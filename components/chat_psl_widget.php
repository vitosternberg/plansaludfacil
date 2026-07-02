<?php
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isLocalHost = strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false;
$omnilamaBaseUrl = $isLocalHost
    ? $scheme . '://' . $host . '/Omniflow'
    : 'https://www.omnilama.cl';

$chatWidgetConfig = [
    'tenantId' => 'psl',
    'brandName' => 'Plan Salud Fácil',
    'assistantName' => 'Asistente PSL',
    'apiUrl' => $omnilamaBaseUrl . '/api/chat_assistant.php',
    'messagesUrl' => $omnilamaBaseUrl . '/api/chat/messages.php',
    'sessionStorageKey' => 'psl_chat_session_id',
    'welcomeMessage' => 'Hola, soy el Asistente PSL. Te puedo orientar sobre cambio de isapre, Fonasa, preexistencias o planes para tu empresa.',
    'quickPrompts' => [
        'Quiero cambiarme de isapre',
        'Vengo desde Fonasa',
        'Tengo preexistencias',
        'Necesito un plan para mi empresa',
    ],
];
?>

<div id="psl-chat-widget-root" class="fixed bottom-5 right-5 z-[9999]">
    <button
        id="psl-chat-toggle"
        type="button"
        class="flex items-center gap-3 rounded-full bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-sky-700"
        aria-expanded="false"
        aria-controls="psl-chat-panel"
    >
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-lg">AI</span>
        <span>Habla con el Asistente PSL</span>
    </button>

    <div id="psl-chat-panel" class="mt-4 hidden w-[min(92vw,380px)] overflow-hidden rounded-3xl border border-sky-100 bg-white shadow-2xl">
        <div class="bg-gradient-to-r from-sky-600 to-cyan-500 px-5 py-4 text-white">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] opacity-90">Plan Salud Fácil</p>
            <div class="mt-1 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-bold">Asistente PSL</h3>
                    <p class="text-sm text-sky-50">Con la potencia de Omnilama y foco 100% PSL.</p>
                </div>
                <span id="psl-chat-status-badge" class="rounded-full bg-white/15 px-3 py-1 text-xs font-semibold">IA activa</span>
            </div>
        </div>

        <div id="psl-chat-messages" class="max-h-[420px] space-y-3 overflow-y-auto bg-slate-50 px-4 py-4"></div>

        <div class="border-t border-slate-200 bg-white px-4 py-4">
            <div id="psl-chat-quick-prompts" class="mb-3 flex flex-wrap gap-2"></div>
            <form id="psl-chat-form" class="space-y-3">
                <textarea
                    id="psl-chat-input"
                    rows="3"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                    placeholder="Cuéntame tu caso y te orientaré de inmediato..."
                ></textarea>
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs text-slate-500">Si necesitas, te puedo derivar con un asesor humano.</p>
                    <button
                        id="psl-chat-send"
                        type="submit"
                        class="rounded-full bg-sky-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-700"
                    >
                        Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const config = <?= json_encode($chatWidgetConfig, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const root = document.getElementById('psl-chat-widget-root');
    if (!root) return;

    const toggleButton = document.getElementById('psl-chat-toggle');
    const panel = document.getElementById('psl-chat-panel');
    const messagesContainer = document.getElementById('psl-chat-messages');
    const form = document.getElementById('psl-chat-form');
    const input = document.getElementById('psl-chat-input');
    const statusBadge = document.getElementById('psl-chat-status-badge');
    const quickPromptsContainer = document.getElementById('psl-chat-quick-prompts');
    let pollIntervalId = null;

    const getSessionId = () => {
        let sessionId = localStorage.getItem(config.sessionStorageKey);
        if (!sessionId) {
            sessionId = `psl_${Date.now()}_${Math.random().toString(36).slice(2, 10)}`;
            localStorage.setItem(config.sessionStorageKey, sessionId);
        }
        return sessionId;
    };

    const setStatus = (statusText, style = 'ai') => {
        statusBadge.textContent = statusText;
        statusBadge.className = 'rounded-full px-3 py-1 text-xs font-semibold';

        if (style === 'human') {
            statusBadge.classList.add('bg-amber-100', 'text-amber-700');
            return;
        }

        if (style === 'sending') {
            statusBadge.classList.add('bg-sky-100', 'text-sky-700');
            return;
        }

        statusBadge.classList.add('bg-white/15', 'text-white');
    };

    const updateStatusFromSessionState = (sessionState) => {
        if (sessionState === 'human_requested') {
            setStatus('Conectando con asesor', 'human');
            return;
        }

        if (sessionState === 'human_active') {
            setStatus('Conversando con asesor', 'human');
            return;
        }

        setStatus('IA activa', 'ai');
    };

    const appendBubble = (role, content, isTemporary = false) => {
        const wrapper = document.createElement('div');
        const bubble = document.createElement('div');
        const label = document.createElement('p');
        const text = document.createElement('p');

        wrapper.className = 'flex';
        bubble.className = 'max-w-[85%] rounded-2xl px-4 py-3 text-sm shadow-sm';
        label.className = 'mb-1 text-[11px] font-semibold uppercase tracking-wide opacity-70';
        text.className = 'whitespace-pre-wrap leading-relaxed';
        text.textContent = content;

        if (role === 'user') {
            wrapper.classList.add('justify-end');
            bubble.classList.add('bg-sky-600', 'text-white');
            label.textContent = 'Tú';
        } else if (role === 'human') {
            bubble.classList.add('bg-amber-100', 'text-amber-900');
            label.textContent = 'Asesor humano';
        } else if (role === 'system') {
            wrapper.classList.add('justify-center');
            bubble.classList.add('bg-slate-200', 'text-slate-700');
            label.textContent = 'Sistema';
        } else {
            bubble.classList.add('bg-white', 'text-slate-800', 'border', 'border-slate-200');
            label.textContent = config.assistantName;
        }

        if (isTemporary) {
            wrapper.dataset.temporary = 'true';
            bubble.classList.add('animate-pulse');
        }

        bubble.appendChild(label);
        bubble.appendChild(text);
        wrapper.appendChild(bubble);
        messagesContainer.appendChild(wrapper);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };

    const renderWelcomeState = () => {
        messagesContainer.innerHTML = '';
        appendBubble('assistant', config.welcomeMessage);
    };

    const renderQuickPrompts = () => {
        quickPromptsContainer.innerHTML = '';
        config.quickPrompts.forEach((prompt) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'rounded-full border border-sky-200 bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700 transition hover:bg-sky-100';
            button.textContent = prompt;
            button.addEventListener('click', () => {
                input.value = prompt;
                input.focus();
            });
            quickPromptsContainer.appendChild(button);
        });
    };

    const refreshMessages = async () => {
        try {
            const response = await fetch(`${config.messagesUrl}?session_hash=${encodeURIComponent(getSessionId())}`);
            const data = await response.json();

            if (data.status !== 'success') {
                return;
            }

            messagesContainer.innerHTML = '';
            if (!data.messages.length) {
                appendBubble('assistant', config.welcomeMessage);
            } else {
                data.messages.forEach((message) => appendBubble(message.role, message.content));
            }

            updateStatusFromSessionState(data.session_estado);
        } catch (error) {
            console.error('PSL chat refresh failed:', error);
        }
    };

    const sendMessage = async (message) => {
        const trimmed = message.trim();
        if (!trimmed) return;

        appendBubble('user', trimmed);
        appendBubble('assistant', 'Estoy revisando tu caso...', true);
        input.value = '';
        setStatus('Respondiendo...', 'sending');

        try {
            const response = await fetch(config.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    session_id: getSessionId(),
                    message: trimmed,
                    tenant_id: config.tenantId,
                    source_site: 'plansaludfacil',
                    current_url: window.location.href
                })
            });

            const data = await response.json();
            const temporary = messagesContainer.querySelector('[data-temporary="true"]');
            if (temporary) {
                temporary.remove();
            }

            if (data.status !== 'success') {
                appendBubble('system', 'Perdón, no pude procesar tu mensaje en este momento.');
                setStatus('Error temporal', 'human');
                return;
            }

            if (data.reply) {
                appendBubble('assistant', data.reply);
            }

            if (data.human_requested) {
                setStatus('Conectando con asesor', 'human');
            } else {
                setStatus('IA activa', 'ai');
            }

            await refreshMessages();
        } catch (error) {
            const temporary = messagesContainer.querySelector('[data-temporary="true"]');
            if (temporary) {
                temporary.remove();
            }
            appendBubble('system', 'Perdón, ahora mismo el chat no está disponible.');
            setStatus('Error temporal', 'human');
            console.error('PSL chat send failed:', error);
        }
    };

    toggleButton.addEventListener('click', async () => {
        const isOpen = !panel.classList.contains('hidden');
        panel.classList.toggle('hidden', isOpen);
        toggleButton.setAttribute('aria-expanded', String(!isOpen));

        if (!isOpen) {
            await refreshMessages();
            if (!pollIntervalId) {
                pollIntervalId = window.setInterval(refreshMessages, 5000);
            }
        } else if (pollIntervalId) {
            window.clearInterval(pollIntervalId);
            pollIntervalId = null;
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        await sendMessage(input.value);
    });

    renderWelcomeState();
    renderQuickPrompts();
});
</script>
