<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['chat_conversation_id', 'sender', 'user_id', 'body', 'created_at'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (ChatMessage $message) {
            $message->conversation()->update(['last_message_at' => $message->created_at]);
            $message->conversation?->stopTyping($message->sender);
        });
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class, 'chat_conversation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Shape sent to the visitor's chat widget (no internal ids beyond the message id).
     */
    public function toWidgetArray(): array
    {
        return [
            'id' => $this->id,
            'from' => $this->sender,
            'name' => $this->sender === 'team' ? ($this->user?->name ? strtok($this->user->name, ' ') : 'Shanyangi team') : null,
            'body' => $this->body,
            'time' => $this->created_at->toIso8601String(),
        ];
    }
}
