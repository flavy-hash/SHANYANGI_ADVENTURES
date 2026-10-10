<?php

namespace Tests\Feature;

use App\Filament\Pages\LiveChat;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Start a chat as a visitor and return the secret token.
     */
    protected function startChat(array $data = []): string
    {
        $response = $this->postJson(route('chat.start'), [
            'name' => 'Jane',
            'email' => 'jane@example.com',
            'message' => 'Hello! Is the Serengeti busy in July?',
            'page' => 'https://elsewhere.example/safaris/serengeti?utm=x',
            ...$data,
        ])->assertCreated();

        return $response->json('token');
    }

    public function test_widget_is_on_every_page_and_can_be_switched_off(): void
    {
        $this->seed(ContentSeeder::class);

        $this->get('/')->assertOk()->assertSee('data-chat', false)->assertSee('name="csrf-token"', false);

        config(['site.live_chat' => false]);
        $this->get('/')->assertOk()->assertDontSee('data-chat-panel', false);
    }

    public function test_visitor_starts_a_chat_and_admins_are_notified(): void
    {
        Queue::fake(); // alerts must not depend on a queue worker
        $admin = User::factory()->create();

        $token = $this->startChat();

        $conversation = ChatConversation::sole();
        $this->assertSame('Jane', $conversation->visitor_name);
        $this->assertSame('/safaris/serengeti', $conversation->started_on); // path only
        $this->assertNotSame($token, $conversation->token_hash);          // only the hash is stored
        $this->assertTrue($conversation->is(ChatConversation::findByToken($token)));
        $this->assertSame('visitor', $conversation->messages->sole()->sender);
        $this->assertNotNull($conversation->last_message_at);

        $this->assertSame(1, $admin->notifications()->count());
        $this->assertStringContainsString('Jane', $admin->notifications()->first()->data['title']);
    }

    public function test_visitor_sends_and_receives_messages_with_their_token(): void
    {
        $team = User::factory()->create(['name' => 'Amani Mushi']);
        $token = $this->startChat();
        $conversation = ChatConversation::sole();

        $this->postJson(route('chat.send'), ['message' => 'We are a family of four.'], ['X-Chat-Token' => $token])
            ->assertCreated()
            ->assertJsonPath('message.from', 'visitor');

        $reply = $conversation->messages()->create(['sender' => 'team', 'user_id' => $team->id, 'body' => 'July is great!']);

        $first = $conversation->messages()->first();
        $this->getJson(route('chat.messages', ['after' => $first->id]), ['X-Chat-Token' => $token])
            ->assertOk()
            ->assertJsonCount(2, 'messages')
            ->assertJsonPath('messages.1.id', $reply->id)
            ->assertJsonPath('messages.1.from', 'team')
            ->assertJsonPath('messages.1.name', 'Amani') // first name only
            ->assertJsonPath('closed', false);

        $this->getJson(route('chat.messages', ['after' => $reply->id]), ['X-Chat-Token' => $token])
            ->assertOk()
            ->assertJsonCount(0, 'messages');
    }

    public function test_chats_cannot_be_read_without_the_right_token(): void
    {
        $this->startChat();

        $this->getJson(route('chat.messages'))->assertNotFound();
        $this->getJson(route('chat.messages'), ['X-Chat-Token' => 'wrong'])->assertNotFound();
        $this->postJson(route('chat.send'), ['message' => 'Hi'], ['X-Chat-Token' => 'wrong'])->assertNotFound();
        $this->assertSame(1, ChatMessage::count());
    }

    public function test_messages_are_validated_and_bots_are_ignored(): void
    {
        $this->postJson(route('chat.start'), ['message' => ''])->assertUnprocessable()->assertJsonValidationErrors('message');
        $this->postJson(route('chat.start'), ['message' => str_repeat('a', 2001)])->assertUnprocessable();
        $this->postJson(route('chat.start'), ['message' => 'Hi', 'email' => 'not-an-email'])->assertJsonValidationErrors('email');
        $this->postJson(route('chat.start'), ['message' => 'Buy now', 'website' => 'http://spam.example'])->assertUnprocessable();

        $this->assertSame(0, ChatConversation::count());
    }

    public function test_writing_again_reopens_a_closed_chat(): void
    {
        $token = $this->startChat();
        ChatConversation::sole()->update(['closed_at' => now()]);

        $this->getJson(route('chat.messages'), ['X-Chat-Token' => $token])->assertJsonPath('closed', true);
        $this->postJson(route('chat.send'), ['message' => 'One more question'], ['X-Chat-Token' => $token])->assertCreated();

        $this->assertNull(ChatConversation::sole()->closed_at);
    }

    public function test_chat_requests_are_not_logged_as_page_views(): void
    {
        $token = $this->startChat();
        $this->get(route('chat.messages'), ['X-Chat-Token' => $token, 'Accept' => 'text/html']);

        $this->assertDatabaseCount('page_views', 0);
    }

    public function test_admin_reads_and_replies(): void
    {
        $admin = User::factory()->create(['name' => 'Amani Mushi']);
        $this->startChat();
        $conversation = ChatConversation::sole();

        $this->actingAs($admin);
        $this->assertSame('1', LiveChat::getNavigationBadge());

        $this->get(LiveChat::getUrl())->assertOk()->assertSee('Jane');

        Livewire::withQueryParams(['conversation' => $conversation->id])
            ->test(LiveChat::class)
            ->assertSee('Is the Serengeti busy in July?')
            ->set('reply', 'Hi Jane, July is peak migration season!')
            ->call('sendReply')
            ->assertHasNoErrors()
            ->assertSet('reply', '')
            ->assertSee('Hi Jane, July is peak migration season!');

        $reply = $conversation->messages()->where('sender', 'team')->sole();
        $this->assertSame($admin->id, $reply->user_id);
        $this->assertNull(LiveChat::getNavigationBadge()); // opened = read

        Livewire::withQueryParams(['conversation' => $conversation->id])
            ->test(LiveChat::class)
            ->set('reply', '')
            ->call('sendReply')
            ->assertHasErrors('reply')
            ->call('close');
        $this->assertNotNull($conversation->fresh()->closed_at);

        Livewire::withQueryParams(['conversation' => $conversation->id])
            ->test(LiveChat::class)
            ->assertSet('show', 'closed')
            ->call('delete')
            ->assertSet('conversation', null);
        $this->assertSame(0, ChatConversation::count());
        $this->assertSame(0, ChatMessage::count());
    }

    public function test_typing_indicators_both_ways(): void
    {
        $admin = User::factory()->create(['name' => 'Amani Mushi']);
        $token = $this->startChat();
        $conversation = ChatConversation::sole();
        $headers = ['X-Chat-Token' => $token];

        // Visitor types -> the team sees it; sending the message clears it.
        $this->postJson(route('chat.typing'), [], $headers)->assertOk();
        $this->postJson(route('chat.typing'), [], ['X-Chat-Token' => 'wrong'])->assertNotFound();

        $this->actingAs($admin);
        Livewire::withQueryParams(['conversation' => $conversation->id])
            ->test(LiveChat::class)
            ->assertSeeHtml('sa-chat-head-meta sa-chat-typing-text') // "typing…" under the name
            ->assertSeeHtml('sa-chat-item-preview sa-chat-typing-text') // and in the chat list
            ->assertSeeHtml('class="sa-chat-bubble sa-typing-bubble"') // three bouncing dots in the thread
            ->call('typing'); // team member types

        $this->getJson(route('chat.messages'), $headers)->assertJsonPath('typing', 'Amani');

        $this->postJson(route('chat.send'), ['message' => 'Thanks!'], $headers)->assertCreated();
        $this->assertNull($conversation->typing('visitor'));

        // The flag expires on its own if the team stops typing.
        $this->travel(ChatConversation::TYPING_SECONDS + 1)->seconds();
        $this->getJson(route('chat.messages'), $headers)->assertJsonPath('typing', null);
    }

    public function test_inbox_shows_who_is_typing_without_waiting_labels(): void
    {
        $this->actingAs(User::factory()->create());
        $token = $this->startChat();
        $other = ChatConversation::sole();
        $this->startChat(['name' => 'Sam']);
        $this->travel(7)->minutes();

        // Sam's chat is open; Jane (another chat) is typing -> shown in the list only.
        $this->postJson(route('chat.typing'), [], ['X-Chat-Token' => $token])->assertOk();

        Livewire::withQueryParams(['conversation' => ChatConversation::where('visitor_name', 'Sam')->value('id')])
            ->test(LiveChat::class)
            ->assertSeeHtml('sa-chat-item-preview sa-chat-typing-text')
            ->assertDontSeeHtml('sa-chat-head-meta sa-chat-typing-text')
            ->assertDontSeeHtml('class="sa-chat-bubble sa-typing-bubble"')
            ->assertDontSee('Waiting');

        $this->assertSame([$other->id => 'Jane'], ChatConversation::typingFor(ChatConversation::all(), 'visitor'));
    }

    public function test_live_chat_requires_login(): void
    {
        $this->get(LiveChat::getUrl())->assertRedirect('/admin/login');
    }
}
