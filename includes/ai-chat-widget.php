<div id="aiChatWidget" class="ai-chat-widget">
    <button id="aiChatToggle" class="ai-chat-toggle" aria-label="Toggle AI Assistant">
        <i data-lucide="bot" style="width:24px;height:24px;"></i>
    </button>
    
    <div id="aiChatPanel" class="ai-chat-panel hidden">
        <div class="ai-chat-header">
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="ai-avatar">
                    <i data-lucide="sparkles" style="width:16px;height:16px;"></i>
                </div>
                <div>
                    <h4 style="font-size:var(--text-base);font-weight:700;color:var(--text-primary);margin:0;">Journey AI</h4>
                    <span style="font-size:var(--text-xs);color:var(--accent-cyan);font-weight:600;letter-spacing:0.05em;text-transform:uppercase;">Online</span>
                </div>
            </div>
            <button id="aiChatClose" class="ai-btn-ghost">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        
        <div id="aiChatMessages" class="ai-chat-messages">
            <div class="ai-message system-msg">
                <div class="msg-bubble">
                    Hi! I'm your Journey AI assistant. Need help planning an itinerary, finding a hidden gem, or translating a menu? Ask away!
                </div>
            </div>
        </div>
        
        <div class="ai-chat-input-area">
            <form id="aiChatForm" style="display:flex;gap:var(--space-2);width:100%;">
                <input type="text" id="aiChatInput" placeholder="Ask anything about your trip..." autocomplete="off">
                <button type="submit" id="aiChatSend" class="ai-btn-primary">
                    <i data-lucide="send" style="width:16px;height:16px;"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
/* AI Chat Widget Styles - Dark Luxury */
.ai-chat-widget {
    position: fixed;
    bottom: var(--space-6);
    right: var(--space-6);
    z-index: var(--z-modal);
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: var(--space-4);
}

.ai-chat-toggle {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: var(--gradient-cyan);
    border: none;
    color: var(--bg-primary);
    cursor: pointer;
    box-shadow: var(--shadow-lg), var(--shadow-glow-cyan);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--transition-spring);
}
.ai-chat-toggle:hover {
    transform: scale(1.05);
}

.ai-chat-panel {
    width: 360px;
    height: 500px;
    background: var(--bg-glass);
    backdrop-filter: var(--glass-blur-heavy);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: var(--radius-2xl);
    box-shadow: var(--shadow-xl);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: all var(--transition-spring);
    transform-origin: bottom right;
}
.ai-chat-panel.hidden {
    opacity: 0;
    transform: scale(0.9) translateY(20px);
    pointer-events: none;
}

.ai-chat-header {
    padding: var(--space-4) var(--space-5);
    border-bottom: 1px solid rgba(255,255,255,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(255,255,255,0.02);
}

.ai-avatar {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    background: rgba(56, 189, 248, 0.1);
    color: var(--accent-cyan);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(56, 189, 248, 0.2);
}

.ai-btn-ghost {
    background: transparent;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    padding: var(--space-1);
    border-radius: var(--radius-md);
    transition: all var(--transition-fast);
}
.ai-btn-ghost:hover {
    color: var(--text-primary);
    background: rgba(255,255,255,0.05);
}

.ai-chat-messages {
    flex: 1;
    padding: var(--space-5);
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: var(--space-4);
}

.ai-message {
    display: flex;
    max-width: 85%;
}
.ai-message.system-msg {
    align-self: flex-start;
}
.ai-message.user-msg {
    align-self: flex-end;
}

.msg-bubble {
    padding: var(--space-3) var(--space-4);
    border-radius: var(--radius-xl);
    font-size: var(--text-sm);
    line-height: var(--leading-relaxed);
}
.system-msg .msg-bubble {
    background: rgba(255,255,255,0.05);
    color: var(--text-secondary);
    border-bottom-left-radius: 4px;
}
.user-msg .msg-bubble {
    background: var(--gradient-cyan);
    color: var(--bg-primary);
    border-bottom-right-radius: 4px;
    font-weight: 500;
}

.ai-chat-input-area {
    padding: var(--space-4);
    border-top: 1px solid rgba(255,255,255,0.05);
    background: rgba(0,0,0,0.2);
}

#aiChatInput {
    flex: 1;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: var(--radius-xl);
    padding: 0 var(--space-4);
    color: var(--text-primary);
    font-size: var(--text-sm);
    outline: none;
    transition: all var(--transition-base);
}
#aiChatInput:focus {
    border-color: var(--accent-cyan);
    background: rgba(255,255,255,0.05);
}

.ai-btn-primary {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-lg);
    background: rgba(56, 189, 248, 0.1);
    color: var(--accent-cyan);
    border: 1px solid rgba(56, 189, 248, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--transition-fast);
}
.ai-btn-primary:hover {
    background: var(--accent-cyan);
    color: var(--bg-primary);
}
.ai-btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Typing Indicator */
.typing-indicator {
    display: flex;
    gap: 4px;
    padding: var(--space-3) var(--space-4);
    background: rgba(255,255,255,0.02);
    border-radius: var(--radius-xl);
    border-bottom-left-radius: 4px;
    width: fit-content;
    align-items: center;
}
.typing-dot {
    width: 6px;
    height: 6px;
    background: var(--text-muted);
    border-radius: 50%;
    animation: typing 1.4s infinite ease-in-out both;
}
.typing-dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dot:nth-child(2) { animation-delay: -0.16s; }
@keyframes typing {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
}

@media (max-width: 640px) {
    .ai-chat-panel {
        width: calc(100vw - 32px);
        height: 60vh;
        bottom: 80px;
        right: 16px;
        position: fixed;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('aiChatToggle');
    const panel = document.getElementById('aiChatPanel');
    const closeBtn = document.getElementById('aiChatClose');
    const form = document.getElementById('aiChatForm');
    const input = document.getElementById('aiChatInput');
    const messages = document.getElementById('aiChatMessages');
    const sendBtn = document.getElementById('aiChatSend');

    // Toggle Chat
    toggle.addEventListener('click', () => {
        panel.classList.toggle('hidden');
        if (!panel.classList.contains('hidden')) {
            input.focus();
        }
    });

    closeBtn.addEventListener('click', () => {
        panel.classList.add('hidden');
    });

    // Send Message
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = input.value.trim();
        if (!text) return;

        // Append user message
        appendMessage('user', text);
        input.value = '';
        sendBtn.disabled = true;

        // Show typing
        const typingEl = document.createElement('div');
        typingEl.className = 'ai-message system-msg';
        typingEl.innerHTML = `<div class="typing-indicator"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div>`;
        messages.appendChild(typingEl);
        messages.scrollTop = messages.scrollHeight;

        try {
            const res = await fetch('<?= APP_URL ?>/api/external.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'travel_chat', message: text })
            });
            const json = await res.json();
            
            typingEl.remove();

            if (json.success && json.data) {
                // The AI Chat API typically returns an object with a text/response field, or just raw output. 
                // Let's handle generic format.
                let reply = "I'm sorry, I couldn't process that.";
                if (typeof json.data === 'string') {
                    reply = json.data;
                } else if (json.data.response) {
                    reply = json.data.response;
                } else if (json.data.message) {
                    reply = json.data.message;
                } else {
                    reply = JSON.stringify(json.data);
                }
                appendMessage('system', reply);
            } else {
                appendMessage('system', 'Sorry, I encountered an error. Please try again.');
            }
        } catch (err) {
            typingEl.remove();
            appendMessage('system', 'Network error. Please try again.');
        } finally {
            sendBtn.disabled = false;
            input.focus();
        }
    });

    function appendMessage(sender, text) {
        const div = document.createElement('div');
        div.className = `ai-message ${sender}-msg`;
        
        // Very basic markdown to HTML for links/bold
        let formattedText = text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>');
            
        div.innerHTML = `<div class="msg-bubble">${formattedText}</div>`;
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
    }
});
</script>
