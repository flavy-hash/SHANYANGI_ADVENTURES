<?php

namespace App\Http\Controllers;

use App\Filament\Pages\LiveChat;
use App\Models\ChatConversation;
use App\Support\AdminAlerts;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Visitor side of the live chat (the widget in resources/js/chat.js).
 *
 * The visitor's secret token travels in the X-Chat-Token header (never in the URL,
 * so it stays out of server logs). The team answers in the admin (Filament\Pages\LiveChat).
 */
class ChatController extends Controller
{
    public const MAX_LENGTH = 2000;

    public function start(Request $request): JsonResponse
    {
        // Honeypot: bots fill the hidden "website" field.
        abort_if(filled($request->input('website')), 422);

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'string', 'email:rfc', 'max:255'],
            'message' => ['required', 'string', 'max:'.self::MAX_LENGTH],
            'page' => ['nullable', 'string', 'max:255'],
        ]);

        [$conversation, $token] = ChatConversation::start([
            'visitor_name' => $data['name'] ?? null,
            'visitor_email' => $data['email'] ?? null,
            'started_on' => $this->pagePath($data['page'] ?? null),
        ]);

        $message = $conversation->messages()->create(['sender' => 'visitor', 'body' => trim($data['message'])]);

        AdminAlerts::send(
            'New live chat from '.$conversation->displayName(),
            Str::limit($message->body, 120),
            LiveChat::getUrl(['conversation' => $conversation->id], panel: 'admin'),
            'heroicon-o-chat-bubble-left-right',
        );

        return response()->json([
            'token' => $token,
            'messages' => [$message->toWidgetArray()],
            'closed' => false,
        ], 201);
    }

    public function messages(Request $request): JsonResponse
    {
        $conversation = $this->conversation($request);
        $after = max(0, (int) $request->query('after'));

        $messages = $conversation->messages()->with('user:id,name')->where('id', '>', $after)->limit(100)->get();

        return response()->json([
            'messages' => $messages->map->toWidgetArray()->values(),
            'closed' => $conversation->closed_at !== null,
            'typing' => $conversation->typing('team'), // first name of the team member typing, or null
        ]);
    }

    /**
     * Keystroke ping from the widget so the team sees "… is typing".
     */
    public function typing(Request $request): JsonResponse
    {
        $conversation = $this->conversation($request);
        $conversation->markTyping('visitor', $conversation->displayName());

        return response()->json(['ok' => true]);
    }

    public function send(Request $request): JsonResponse
    {
        $conversation = $this->conversation($request);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:'.self::MAX_LENGTH],
        ]);

        // Writing again reopens a chat the team had closed.
        if ($conversation->closed_at) {
            $conversation->update(['closed_at' => null]);
            AdminAlerts::send(
                $conversation->displayName().' reopened a live chat',
                Str::limit($data['message'], 120),
                LiveChat::getUrl(['conversation' => $conversation->id], panel: 'admin'),
                'heroicon-o-chat-bubble-left-right',
            );
        }

        $message = $conversation->messages()->create(['sender' => 'visitor', 'body' => trim($data['message'])]);

        return response()->json(['message' => $message->toWidgetArray()], 201);
    }

    protected function conversation(Request $request): ChatConversation
    {
        $conversation = ChatConversation::findByToken($request->header('X-Chat-Token'));
        abort_unless($conversation, 404);

        return $conversation;
    }

    /**
     * Keep only the path of the page the chat started on (no query string or other sites).
     */
    protected function pagePath(?string $page): ?string
    {
        $path = $page ? parse_url($page, PHP_URL_PATH) : null;

        return $path ? Str::limit($path, 250, '') : null;
    }
}
