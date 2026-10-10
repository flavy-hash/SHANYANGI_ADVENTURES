<x-filament-panels::page>
    @php($tz = \Filament\Support\Facades\FilamentTimezone::get())
    <style>
        .sa-chat { display: grid; gap: 1rem; min-height: 32rem; }
        @media (min-width: 1024px) { .sa-chat { grid-template-columns: 20rem minmax(0, 1fr); height: calc(100dvh - 15rem); min-height: 32rem; } }

        .sa-chat-panel { display: flex; flex-direction: column; min-height: 0; overflow: hidden; border-radius: 0.75rem; background: #fff; box-shadow: 0 1px 2px rgb(0 0 0 / 0.05); outline: 1px solid rgb(3 7 18 / 0.06); }
        .dark .sa-chat-panel { background: var(--gray-900); outline-color: rgb(255 255 255 / 0.1); }

        .sa-chat-tabs { display: flex; gap: 0.25rem; padding: 0.5rem; border-bottom: 1px solid var(--gray-200); }
        .dark .sa-chat-tabs { border-color: rgb(255 255 255 / 0.1); }
        .sa-chat-tab { flex: 1; border-radius: 0.5rem; padding: 0.45rem 0.75rem; font-size: 0.85rem; font-weight: 600; color: var(--gray-600); }
        .sa-chat-tab:hover { background: var(--gray-50); }
        .sa-chat-tab.is-active { background: var(--primary-50); color: var(--primary-700); }
        .dark .sa-chat-tab { color: var(--gray-400); }
        .dark .sa-chat-tab:hover { background: rgb(255 255 255 / 0.05); }
        .dark .sa-chat-tab.is-active { background: rgb(255 255 255 / 0.08); color: var(--primary-400); }

        .sa-chat-list { flex: 1; overflow-y: auto; max-height: 24rem; }
        @media (min-width: 1024px) { .sa-chat-list { max-height: none; } }
        .sa-chat-item { display: grid; gap: 0.15rem; width: 100%; padding: 0.8rem 1rem; text-align: left; border-bottom: 1px solid var(--gray-100); }
        .sa-chat-item:hover { background: var(--gray-50); }
        .sa-chat-item.is-active { background: var(--primary-50); box-shadow: inset 3px 0 0 var(--primary-600); }
        .dark .sa-chat-item { border-color: rgb(255 255 255 / 0.05); }
        .dark .sa-chat-item:hover { background: rgb(255 255 255 / 0.04); }
        .dark .sa-chat-item.is-active { background: rgb(255 255 255 / 0.07); }
        .sa-chat-item-top { display: flex; align-items: center; gap: 0.5rem; }
        .sa-chat-item-name { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 600; font-size: 0.9rem; color: var(--gray-950); }
        .dark .sa-chat-item-name { color: #fff; }
        .sa-chat-item-time { flex-shrink: 0; font-size: 0.75rem; color: var(--gray-500); }
        .sa-chat-item-preview { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.82rem; color: var(--gray-500); }
        .sa-chat-item.is-unread .sa-chat-item-preview { color: var(--gray-900); font-weight: 600; }
        .dark .sa-chat-item.is-unread .sa-chat-item-preview { color: var(--gray-100); }
        .sa-chat-dot { width: 0.55rem; height: 0.55rem; flex-shrink: 0; border-radius: 9999px; background: var(--danger-500); }

        .sa-chat-empty { margin: auto; padding: 2.5rem 1.5rem; text-align: center; font-size: 0.9rem; color: var(--gray-500); }
        .sa-chat-empty svg { width: 2.5rem; height: 2.5rem; margin: 0 auto 0.75rem; color: var(--gray-400); }

        .sa-chat-head { display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; padding: 0.85rem 1rem; border-bottom: 1px solid var(--gray-200); }
        .dark .sa-chat-head { border-color: rgb(255 255 255 / 0.1); }
        .sa-chat-head-info { flex: 1; min-width: 12rem; }
        .sa-chat-head-name { font-weight: 700; color: var(--gray-950); }
        .dark .sa-chat-head-name { color: #fff; }
        .sa-chat-head-meta { display: flex; flex-wrap: wrap; gap: 0.25rem 0.9rem; margin-top: 0.15rem; font-size: 0.8rem; color: var(--gray-500); }
        .sa-chat-head-meta a { color: var(--primary-600); text-decoration: underline; }
        .sa-chat-head-actions { display: flex; flex-wrap: wrap; gap: 0.5rem; }

        .sa-chat-thread { flex: 1; min-height: 16rem; overflow-y: auto; display: flex; flex-direction: column; gap: 0.6rem; padding: 1.25rem 1rem; background: var(--gray-50); }
        .dark .sa-chat-thread { background: rgb(0 0 0 / 0.2); }
        .sa-chat-msg { max-width: min(36rem, 85%); }
        .sa-chat-msg.is-team { align-self: flex-end; }
        .sa-chat-bubble { border-radius: 1rem; padding: 0.6rem 0.9rem; font-size: 0.9rem; line-height: 1.5; white-space: pre-wrap; overflow-wrap: anywhere; background: #fff; color: var(--gray-900); box-shadow: 0 1px 2px rgb(0 0 0 / 0.06); border-bottom-left-radius: 0.3rem; }
        .dark .sa-chat-bubble { background: var(--gray-800); color: var(--gray-100); }
        .sa-chat-msg.is-team .sa-chat-bubble { background: var(--primary-600); color: #fff; border-bottom-left-radius: 1rem; border-bottom-right-radius: 0.3rem; }
        .sa-chat-msg-meta { margin-top: 0.2rem; padding-inline: 0.3rem; font-size: 0.72rem; color: var(--gray-500); }
        .sa-chat-msg.is-team .sa-chat-msg-meta { text-align: right; }
        .sa-chat-closed-note { align-self: center; border-radius: 9999px; padding: 0.3rem 0.9rem; font-size: 0.75rem; background: var(--gray-200); color: var(--gray-600); }
        .dark .sa-chat-closed-note { background: rgb(255 255 255 / 0.08); color: var(--gray-300); }

        .sa-chat-reply { display: flex; align-items: flex-end; gap: 0.6rem; padding: 0.75rem; border-top: 1px solid var(--gray-200); }
        .dark .sa-chat-reply { border-color: rgb(255 255 255 / 0.1); }
        .sa-chat-reply textarea { flex: 1; min-height: 2.75rem; max-height: 9rem; resize: vertical; border-radius: 0.6rem; border: 1px solid var(--gray-300); background: #fff; padding: 0.6rem 0.8rem; font-size: 0.9rem; color: var(--gray-950); }
        .sa-chat-reply textarea:focus { outline: 2px solid var(--primary-600); outline-offset: -1px; border-color: transparent; }
        .dark .sa-chat-reply textarea { background: rgb(255 255 255 / 0.05); border-color: rgb(255 255 255 / 0.15); color: #fff; }
        .sa-chat-error { padding: 0 0.75rem 0.6rem; font-size: 0.8rem; color: var(--danger-600); }
        .sa-chat-typing-text { color: var(--success-600); font-weight: 600; font-style: normal; }
        .dark .sa-chat-typing-text { color: var(--success-400); }
        .sa-typing-bubble { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.8rem 1rem; }
        .sa-typing-bubble span { width: 0.5rem; height: 0.5rem; border-radius: 9999px; background: var(--gray-400); animation: sa-typing-bounce 1.3s infinite ease-in-out; }
        .sa-typing-bubble span:nth-child(2) { animation-delay: 0.16s; }
        .sa-typing-bubble span:nth-child(3) { animation-delay: 0.32s; }
        @keyframes sa-typing-bounce { 0%, 60%, 100% { transform: translateY(0); opacity: 0.45; } 30% { transform: translateY(-6px); opacity: 1; } }
        @media (prefers-reduced-motion: reduce) { .sa-typing-bubble span { animation: none; opacity: 0.7; } }
        .sa-sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
        .sa-chat-hint { padding: 0 0.75rem 0.6rem; font-size: 0.75rem; color: var(--gray-500); }
    </style>

    <div class="sa-chat" wire:poll.2s="poll">
        {{-- Conversations --}}
        <section class="sa-chat-panel" aria-label="Conversations">
            <div class="sa-chat-tabs" role="tablist">
                <button type="button" role="tab" wire:click="showList('open')" @class(['sa-chat-tab', 'is-active' => $show === 'open']) aria-selected="{{ $show === 'open' ? 'true' : 'false' }}">
                    Open ({{ $openCount }})
                </button>
                <button type="button" role="tab" wire:click="showList('closed')" @class(['sa-chat-tab', 'is-active' => $show === 'closed']) aria-selected="{{ $show === 'closed' ? 'true' : 'false' }}">
                    Closed ({{ $closedCount }})
                </button>
            </div>

            <div class="sa-chat-list">
                @forelse ($conversations as $item)
                    @php($last = $item->messages->first())
                    <button type="button" wire:key="conversation-{{ $item->id }}" wire:click="select({{ $item->id }})"
                            @class(['sa-chat-item', 'is-active' => $active?->is($item), 'is-unread' => $item->has_unread && ! $active?->is($item)])>
                        <span class="sa-chat-item-top">
                            @if ($item->has_unread && ! $active?->is($item))
                                <span class="sa-chat-dot" title="Unread"></span>
                            @endif
                            <span class="sa-chat-item-name">{{ $item->displayName() }}</span>
                            <span class="sa-chat-item-time">{{ $item->last_message_at?->diffForHumans(short: true) }}</span>
                        </span>
                        @if (isset($typing[$item->id]))
                            <span class="sa-chat-item-preview sa-chat-typing-text">typing…</span>
                        @else
                            <span class="sa-chat-item-preview">
                                @if ($last?->sender === 'team') You: @endif{{ $last?->body }}
                            </span>
                        @endif
                    </button>
                @empty
                    <div class="sa-chat-empty">
                        {{ $show === 'open' ? 'No open chats. New messages from the website appear here.' : 'No closed chats.' }}
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Thread --}}
        <section class="sa-chat-panel" aria-label="Conversation">
            @if ($active)
                <header class="sa-chat-head">
                    <div class="sa-chat-head-info">
                        <p class="sa-chat-head-name">{{ $active->displayName() }}</p>
                        @if (isset($typing[$active->id]))
                            <p class="sa-chat-head-meta sa-chat-typing-text" aria-live="polite">typing…</p>
                        @else
                        <p class="sa-chat-head-meta">
                            @if ($active->visitor_email)
                                <a href="mailto:{{ $active->visitor_email }}">{{ $active->visitor_email }}</a>
                            @else
                                <span>No email given</span>
                            @endif
                            <span>Started {{ $active->created_at->timezone($tz)->format('j M Y, H:i') }}</span>
                            @if ($active->started_on)
                                <span>from <a href="{{ url($active->started_on) }}" target="_blank" rel="noopener">{{ $active->started_on === '/' ? 'Home page' : $active->started_on }}</a></span>
                            @endif
                        </p>
                        @endif
                    </div>
                    <div class="sa-chat-head-actions">
                        @if ($active->closed_at)
                            <x-filament::button size="sm" color="gray" icon="heroicon-m-arrow-uturn-left" wire:click="reopen">Reopen</x-filament::button>
                        @else
                            <x-filament::button size="sm" color="gray" icon="heroicon-m-check" wire:click="close">Close chat</x-filament::button>
                        @endif
                        <x-filament::button size="sm" color="danger" outlined icon="heroicon-m-trash" wire:click="delete"
                                            wire:confirm="Delete this chat and all its messages? This cannot be undone.">Delete</x-filament::button>
                    </div>
                </header>

                @php($lastId = $messages->last()?->id ?? 0)
                <div class="sa-chat-thread" wire:key="thread-{{ $active->id }}-{{ $lastId }}" x-data x-init="$el.scrollTop = $el.scrollHeight">
                    @foreach ($messages as $message)
                        <div @class(['sa-chat-msg', 'is-team' => $message->sender === 'team'])>
                            <div class="sa-chat-bubble">{{ $message->body }}</div>
                            <p class="sa-chat-msg-meta">
                                {{ $message->sender === 'team' ? ($message->user?->name ?? 'Team') : $active->displayName() }}
                                · <time datetime="{{ $message->created_at->toIso8601String() }}" title="{{ $message->created_at->timezone($tz)->format('j M Y, H:i:s') }}">{{ $message->created_at->timezone($tz)->format('H:i') }}</time>
                            </p>
                        </div>
                    @endforeach
                    @if (isset($typing[$active->id]))
                        <div class="sa-chat-msg" wire:key="typing-{{ $active->id }}" x-data x-init="$el.parentElement.scrollTop = $el.parentElement.scrollHeight">
                            <div class="sa-chat-bubble sa-typing-bubble" role="img" aria-label="{{ $typing[$active->id] }} is typing"><span></span><span></span><span></span></div>
                        </div>
                    @endif
                    @if ($active->closed_at)
                        <p class="sa-chat-closed-note">Closed {{ $active->closed_at->timezone($tz)->format('j M, H:i') }}</p>
                    @endif
                </div>

                <form class="sa-chat-reply" wire:submit="sendReply">
                    <label for="sa-chat-reply" class="sa-sr-only">Reply</label>
                    <textarea id="sa-chat-reply" wire:model="reply" rows="1" maxlength="2000" placeholder="Write a reply..."
                              x-data="{ pinged: 0 }"
                              x-on:input="if ($el.value.trim() && Date.now() - pinged > 2000) { pinged = Date.now(); $wire.typing() }"
                              x-on:keydown.enter="if (! $event.shiftKey) { $event.preventDefault(); $wire.sendReply() }"></textarea>
                    <x-filament::button type="submit" icon="heroicon-m-paper-airplane" wire:loading.attr="disabled" wire:target="sendReply">Send</x-filament::button>
                </form>
                @error('reply')
                    <p class="sa-chat-error">{{ $message }}</p>
                @else
                    <p class="sa-chat-hint">Enter sends, Shift + Enter adds a new line. The visitor sees your first name.</p>
                @enderror
            @else
                <div class="sa-chat-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/></svg>
                    Choose a conversation to read and reply.
                </div>
            @endif
        </section>
    </div>
</x-filament-panels::page>
