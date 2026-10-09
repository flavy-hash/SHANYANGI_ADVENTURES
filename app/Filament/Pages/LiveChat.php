<?php

namespace App\Filament\Pages;

use App\Models\ChatConversation;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Url;
use UnitEnum;

/**
 * Inbox for the website's live chat: conversation list + thread, refreshed by polling.
 * Visitors chat through App\Http\Controllers\ChatController.
 */
class LiveChat extends Page
{
    protected static ?string $navigationLabel = 'Live chat';

    protected static ?string $title = 'Live chat';

    protected static ?string $slug = 'live-chat';

    protected static string|UnitEnum|null $navigationGroup = 'Bookings';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected string $view = 'filament.pages.live-chat';

    /** Selected conversation id (kept in the URL so notifications can link to it). */
    #[Url]
    public ?int $conversation = null;

    /** open | closed */
    #[Url]
    public string $show = 'open';

    public string $reply = '';

    public static function getNavigationBadge(): ?string
    {
        $unread = ChatConversation::query()->whereNull('closed_at')->unread()->count();

        return $unread ? (string) $unread : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Unread chats';
    }

    public function mount(): void
    {
        $active = $this->active();
        if ($active?->closed_at) {
            $this->show = 'closed';
        }
        $this->markRead();
    }

    public function select(int $id): void
    {
        $this->conversation = $id;
        $this->reply = '';
        $this->resetErrorBag();
        $this->markRead();
    }

    public function showList(string $show): void
    {
        $this->show = $show === 'closed' ? 'closed' : 'open';
    }

    /** Called by wire:poll; re-rendering fetches new messages. */
    public function poll(): void
    {
        $this->markRead();
    }

    /** Keystroke ping from the reply box so the visitor sees "… is typing". */
    #[Renderless]
    public function typing(): void
    {
        $this->active()?->markTyping('team', strtok((string) auth()->user()?->name, ' ') ?: 'Our team');
    }

    public function sendReply(): void
    {
        $conversation = $this->active();
        if (! $conversation) {
            return;
        }

        $this->validate(['reply' => ['required', 'string', 'max:2000']], [], ['reply' => 'reply']);

        $conversation->messages()->create([
            'sender' => 'team',
            'user_id' => auth()->id(),
            'body' => trim($this->reply),
        ]);
        $conversation->update(['closed_at' => null, 'admin_read_at' => now()]);
        $this->show = 'open';
        $this->reply = '';
    }

    public function close(): void
    {
        $this->active()?->update(['closed_at' => now(), 'admin_read_at' => now()]);
        Notification::make()->title('Chat closed')->body('The visitor can still reopen it by writing again.')->success()->send();
    }

    public function reopen(): void
    {
        $this->active()?->update(['closed_at' => null]);
        $this->show = 'open';
    }

    public function delete(): void
    {
        $this->active()?->delete();
        $this->conversation = null;
        Notification::make()->title('Chat deleted')->success()->send();
    }

    protected function active(): ?ChatConversation
    {
        return $this->conversation ? ChatConversation::find($this->conversation) : null;
    }

    protected function markRead(): void
    {
        $conversation = $this->active();
        if ($conversation && $conversation->isUnread()) {
            $conversation->update(['admin_read_at' => now()]);
        }
    }

    /**
     * @return Collection<int, ChatConversation>
     */
    protected function conversations(): Collection
    {
        return ChatConversation::query()
            ->when($this->show === 'closed', fn ($q) => $q->whereNotNull('closed_at'), fn ($q) => $q->whereNull('closed_at'))
            ->with(['messages' => fn ($q) => $q->reorder()->latest('id')->limit(1)])
            ->withExists(['messages as has_unread' => fn ($q) => $q->where('sender', 'visitor')
                ->where(fn ($q) => $q->whereNull('chat_conversations.admin_read_at')
                    ->orWhereColumn('chat_messages.created_at', '>', 'chat_conversations.admin_read_at'))])
            ->orderByDesc('last_message_at')
            ->limit(100)
            ->get();
    }

    protected function getViewData(): array
    {
        $active = $this->active();
        $conversations = $this->conversations();

        return [
            'conversations' => $conversations,
            'active' => $active,
            'messages' => $active?->messages()->with('user:id,name')->get() ?? collect(),
            'typing' => ChatConversation::typingFor($conversations, 'visitor'), // [conversation id => name]
            'openCount' => ChatConversation::query()->whereNull('closed_at')->count(),
            'closedCount' => ChatConversation::query()->whereNotNull('closed_at')->count(),
        ];
    }
}
