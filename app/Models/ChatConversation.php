<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * A live chat between one website visitor and the team.
 *
 * The visitor holds a random secret token (kept in their browser); only its
 * SHA-256 hash is stored, so a database leak can't be used to read chats.
 */
class ChatConversation extends Model
{
    protected $fillable = ['token_hash', 'visitor_name', 'visitor_email', 'started_on', 'last_message_at', 'admin_read_at', 'closed_at'];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'admin_read_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('id');
    }

    /**
     * Create a conversation and return [conversation, plain token for the visitor].
     *
     * @return array{0: ChatConversation, 1: string}
     */
    public static function start(array $attributes): array
    {
        $token = Str::random(48);

        return [static::create($attributes + ['token_hash' => hash('sha256', $token)]), $token];
    }

    public static function findByToken(?string $token): ?self
    {
        return $token ? static::query()->where('token_hash', hash('sha256', $token))->first() : null;
    }

    /**
     * Conversations with visitor messages the team hasn't read yet.
     */
    public function scopeUnread(Builder $query): void
    {
        $query->whereHas('messages', fn (Builder $m) => $m->where('sender', 'visitor')
            ->where(fn (Builder $q) => $q->whereNull('chat_conversations.admin_read_at')
                ->orWhereColumn('chat_messages.created_at', '>', 'chat_conversations.admin_read_at')));
    }

    public function isUnread(): bool
    {
        $lastVisitor = $this->messages()->where('sender', 'visitor')->max('created_at');

        return $lastVisitor && (! $this->admin_read_at || $this->admin_read_at->lt($lastVisitor));
    }

    public function displayName(): string
    {
        return $this->visitor_name ?: 'Visitor #'.$this->id;
    }

    /**
     * "Is typing" flags live briefly in the cache: each keystroke ping keeps
     * the flag alive for a few seconds; sending a message clears it.
     */
    public const TYPING_SECONDS = 5;

    public function markTyping(string $side, string $name): void
    {
        Cache::put($this->typingKey($side), $name, now()->addSeconds(self::TYPING_SECONDS));
    }

    /** Name of whoever on that side (visitor / team) is typing, or null. */
    public function typing(string $side): ?string
    {
        return Cache::get($this->typingKey($side));
    }

    /**
     * Who is typing in each of these conversations, in one cache lookup.
     *
     * @param  iterable<ChatConversation>  $conversations
     * @return array<int, string> conversation id => name
     */
    public static function typingFor(iterable $conversations, string $side): array
    {
        $keys = [];
        foreach ($conversations as $conversation) {
            if (! $conversation->closed_at) {
                $keys[$conversation->typingKey($side)] = $conversation->id;
            }
        }

        $typing = [];
        foreach ($keys ? Cache::many(array_keys($keys)) : [] as $key => $name) {
            if ($name) {
                $typing[$keys[$key]] = $name;
            }
        }

        return $typing;
    }

    public function stopTyping(string $side): void
    {
        Cache::forget($this->typingKey($side));
    }

    protected function typingKey(string $side): string
    {
        return 'chat-typing:'.$this->id.':'.$side;
    }
}
