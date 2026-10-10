// Live chat widget (resources/views/partials/chat-widget.blade.php).
// The visitor's secret token lives in localStorage and is sent in the X-Chat-Token
// header. Polls for replies every few seconds while open, slowly while closed.

const TOKEN_KEY = 'tm-chat-token';
const SEEN_KEY = 'tm-chat-seen';
const FAST_POLL = 2000;
const TYPING_PING = 2000; // how often to tell the team we're still typing
const SLOW_POLL = 30000;

const store = {
    get(key) {
        try { return window.localStorage.getItem(key); } catch { return null; }
    },
    set(key, value) {
        try { window.localStorage.setItem(key, value); } catch { /* private mode */ }
    },
    remove(key) {
        try { window.localStorage.removeItem(key); } catch { /* private mode */ }
    },
};

const initChat = () => {
    const root = document.querySelector('[data-chat]');
    if (!root) return;

    const panel = root.querySelector('[data-chat-panel]');
    const launcher = root.querySelector('[data-chat-launcher]');
    const badge = root.querySelector('[data-chat-badge]');
    const startForm = root.querySelector('[data-chat-start]');
    const startError = root.querySelector('[data-chat-error]');
    const body = root.querySelector('[data-chat-body]');
    const list = root.querySelector('[data-chat-messages]');
    const composer = root.querySelector('[data-chat-composer]');
    const sendError = root.querySelector('[data-chat-send-error]');
    const subtitle = root.querySelector('[data-chat-sub]');
    const subtitleDefault = subtitle.textContent;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    let token = store.get(TOKEN_KEY);
    let lastId = 0;
    let lastTeamId = 0;
    let closed = false;
    let teamTyping = null;
    let lastTypingPing = 0;
    let timer = null;
    let busy = false;

    const isOpen = () => !panel.hidden;

    const request = async (url, { method = 'GET', data } = {}) => {
        const headers = { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' };
        if (token) headers['X-Chat-Token'] = token;
        if (data) {
            headers['Content-Type'] = 'application/json';
            headers['X-CSRF-TOKEN'] = csrf;
        }
        const response = await fetch(url, { method, headers, body: data ? JSON.stringify(data) : undefined, credentials: 'same-origin' });
        const json = await response.json().catch(() => ({}));
        return { status: response.status, json };
    };

    const errorText = ({ status, json }) => {
        if (status === 419) return 'Your session expired. Please refresh the page and try again.';
        if (status === 429) return 'You are sending messages too quickly. Please wait a moment.';
        if (status === 422 && json.errors) return Object.values(json.errors).flat()[0];
        return 'Sorry, something went wrong. Please try again, or reach us on WhatsApp or email.';
    };

    const showError = (el, text) => {
        el.textContent = text ?? '';
        el.hidden = !text;
    };

    const formatTime = (iso) => {
        try {
            return new Date(iso).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        } catch {
            return '';
        }
    };

    // Closed note at the end of the thread.
    // End of the thread: a typing bubble (three bouncing dots) or the closed note.
    const renderStatus = () => {
        const stuckToBottom = list.scrollHeight - list.scrollTop - list.clientHeight < 40;
        list.querySelector('[data-chat-status]')?.remove();

        const note = document.createElement('li');
        note.dataset.chatStatus = '';

        if (closed) {
            note.className = 'tm-chat-closed';
            note.textContent = 'This chat was closed by our team. Write again if you need anything else.';
        } else if (teamTyping) {
            note.className = 'tm-chat-msg is-team';
            const bubble = document.createElement('p');
            bubble.className = 'tm-chat-bubble tm-typing-bubble';
            bubble.setAttribute('role', 'img');
            bubble.setAttribute('aria-label', `${teamTyping} is typing`);
            bubble.append(...[1, 2, 3].map(() => document.createElement('span')));
            note.append(bubble);
        } else {
            return;
        }

        list.append(note);
        if (stuckToBottom) list.scrollTop = list.scrollHeight;
    };

    // Like WhatsApp: the header subtitle turns into "Amani is typing…".
    const renderTyping = () => {
        subtitle.textContent = teamTyping ? `${teamTyping} is typing…` : subtitleDefault;
        subtitle.classList.toggle('is-typing', Boolean(teamTyping));
    };

    const addMessages = (messages) => {
        messages.forEach((message) => {
            if (message.id <= lastId) return;
            lastId = message.id;
            if (message.from === 'team') lastTeamId = message.id;

            const item = document.createElement('li');
            item.className = `tm-chat-msg is-${message.from === 'team' ? 'team' : 'visitor'}`;

            const bubble = document.createElement('p');
            bubble.className = 'tm-chat-bubble';
            bubble.textContent = message.body;

            const meta = document.createElement('p');
            meta.className = 'tm-chat-meta';
            meta.textContent = [message.from === 'team' ? message.name : 'You', formatTime(message.time)].filter(Boolean).join(' · ');

            item.append(bubble, meta);
            const status = list.querySelector('[data-chat-status]');
            if (status) status.before(item); else list.append(item);
        });
        renderStatus();
        list.scrollTop = list.scrollHeight;
    };

    const updateBadge = (unread) => {
        badge.hidden = !unread;
        launcher.classList.toggle('has-unread', unread);
    };

    const markSeen = () => {
        store.set(SEEN_KEY, String(lastId));
        updateBadge(false);
    };

    const showChat = () => {
        startForm.hidden = Boolean(token);
        body.hidden = !token;
    };

    const reset = () => {
        token = null;
        lastId = 0;
        lastTeamId = 0;
        teamTyping = null;
        renderTyping();
        closed = false;
        list.replaceChildren();
        store.remove(TOKEN_KEY);
        store.remove(SEEN_KEY);
        updateBadge(false);
        showChat();
    };

    const schedule = () => {
        window.clearTimeout(timer);
        if (!token) return;
        timer = window.setTimeout(poll, isOpen() && !document.hidden ? FAST_POLL : SLOW_POLL);
    };

    const poll = async () => {
        if (!token || busy) return schedule();
        try {
            const result = await request(`${root.dataset.messagesUrl}?after=${lastId}`);
            if (result.status === 404) return reset();
            if (result.status === 200) {
                closed = Boolean(result.json.closed);
                teamTyping = result.json.typing || null;
                renderTyping();
                addMessages(result.json.messages ?? []);

                if (isOpen()) {
                    markSeen();
                } else {
                    updateBadge(lastTeamId > Number(store.get(SEEN_KEY) ?? 0));
                }
            }
        } catch {
            // Offline or server hiccup: try again on the next tick.
        }
        schedule();
    };

    const open = () => {
        panel.hidden = false;
        launcher.setAttribute('aria-expanded', 'true');
        root.classList.add('is-open');
        showChat();
        markSeen();
        (token ? composer.querySelector('textarea') : startForm.querySelector('textarea, input:not([tabindex="-1"])'))?.focus({ preventScroll: true });
        list.scrollTop = list.scrollHeight;
        poll();
    };

    const close = () => {
        panel.hidden = true;
        launcher.setAttribute('aria-expanded', 'false');
        root.classList.remove('is-open');
        schedule();
    };

    root.querySelectorAll('[data-chat-toggle]').forEach((button) => {
        button.addEventListener('click', () => (isOpen() ? close() : open()));
    });

    // Other links can open the chat, e.g. <a href="#chat" data-chat-open>.
    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-chat-open]')) {
            event.preventDefault();
            open();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isOpen()) {
            close();
            launcher.focus();
        }
    });

    document.addEventListener('visibilitychange', schedule);

    startForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const data = Object.fromEntries(new FormData(startForm));
        if (!String(data.message ?? '').trim()) {
            showError(startError, 'Please write a message.');
            return;
        }

        const button = startForm.querySelector('button[type="submit"]');
        button.disabled = true;
        showError(startError, null);
        try {
            const result = await request(root.dataset.startUrl, { method: 'POST', data: { ...data, page: window.location.pathname } });
            if (result.status !== 201) {
                showError(startError, errorText(result));
                return;
            }
            token = result.json.token;
            store.set(TOKEN_KEY, token);
            startForm.reset();
            showChat();
            addMessages(result.json.messages ?? []);
            markSeen();
            composer.querySelector('textarea').focus({ preventScroll: true });
            schedule();
        } catch {
            showError(startError, errorText({ status: 0, json: {} }));
        } finally {
            button.disabled = false;
        }
    });

    const textarea = composer.querySelector('textarea');

    const send = async () => {
        const message = textarea.value.trim();
        if (!message || busy) return;

        busy = true;
        showError(sendError, null);
        try {
            const result = await request(root.dataset.sendUrl, { method: 'POST', data: { message } });
            if (result.status === 404) {
                reset();
                showError(startError, 'This chat has ended. Start a new one below.');
                return;
            }
            if (result.status !== 201) {
                showError(sendError, errorText(result));
                return;
            }
            textarea.value = '';
            lastTypingPing = 0;
            closed = false;
            addMessages([result.json.message]);
            markSeen();
        } catch {
            showError(sendError, errorText({ status: 0, json: {} }));
        } finally {
            busy = false;
            schedule();
        }
    };

    composer.addEventListener('submit', (event) => {
        event.preventDefault();
        send();
    });

    // Enter sends, Shift+Enter adds a line.
    textarea.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey && !event.isComposing) {
            event.preventDefault();
            send();
        }
    });

    // Let the team see "… is typing" (a ping every few seconds while typing).
    textarea.addEventListener('input', () => {
        if (!token || !textarea.value.trim() || Date.now() - lastTypingPing < TYPING_PING) return;
        lastTypingPing = Date.now();
        request(root.dataset.typingUrl, { method: 'POST', data: {} }).catch(() => {});
    });

    // Returning visitor: load the existing conversation quietly and watch for replies.
    if (token) {
        showChat();
        poll();
    }
};

initChat();
