<x-layouts.app :title="__('Conversation')">

        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Messagerie') }}
            </h2>
            <a href="{{ route('messages.index') }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                Liste des conversations
            </a>
        </div>
    

    <div class="py-12" x-data="{
        messages: {{ json_encode($messages) }},
        newMessage: '',
        fileInput: null,
        isLoading: false,
        conversationId: {{ $conversation->id }},
        lastMessageId: {{ $messages->last()?->id ?? 0 }},
        
        init() {
            this.scrollToBottom();
            
            // WebSocket avec Laravel Echo
            if (window.Echo) {
                try {
                    const channel = window.Echo.private(`conversation.${this.conversationId}`);
                    
                    channel.listen('.message.sent', (data) => {
                        console.log('Nouveau message reçu:', data);
                        // Vérifier que le message n'existe pas déjà
                        const messageExists = this.messages.some(m => m.id === data.message.id);
                        if (!messageExists) {
                            this.messages.push(data.message);
                            this.lastMessageId = data.message.id;
                        }
                    })
                    .error((error) => {
                        console.error('Erreur WebSocket:', error);
                        // Activer le polling en cas d'erreur
                        this.enablePolling();
                    });
                    
                    // Vérifier la connexion
                    window.Echo.connector.pusher.connection.bind('connected', () => {
                        console.log('WebSocket connecté');
                    });
                    
                    window.Echo.connector.pusher.connection.bind('error', (error) => {
                        console.error('Erreur de connexion WebSocket:', error);
                        this.enablePolling();
                    });
                } catch (error) {
                    console.error('Erreur lors de la configuration WebSocket:', error);
                    this.enablePolling();
                }
            } else {
                console.warn('Laravel Echo non disponible, utilisation du polling');
                this.enablePolling();
            }
            
            // Auto-scroll quand nouveaux messages
            this.$watch('messages', () => {
                this.$nextTick(() => this.scrollToBottom());
            });
        },
        
        pollingInterval: null,
        
        enablePolling() {
            // Arrêter le polling existant
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
            }
            
            // Démarrer le polling
            this.pollingInterval = setInterval(() => {
                this.checkNewMessages();
            }, 2000); // Toutes les 2 secondes
        },
        
        async checkNewMessages() {
            try {
                const response = await fetch(`/messages/${this.conversationId}/messages?last_message_id=${this.lastMessageId}`);
                if (!response.ok) return;
                
                const newMessages = await response.json();
                if (newMessages && newMessages.length > 0) {
                    newMessages.forEach(msg => {
                        const exists = this.messages.some(m => m.id === msg.id);
                        if (!exists) {
                            this.messages.push(msg);
                            this.lastMessageId = msg.id;
                        }
                    });
                }
            } catch (error) {
                console.error('Error checking messages:', error);
            }
        },
        
        async sendMessage() {
            if (!this.newMessage.trim() && !this.fileInput?.files[0]) return;
            
            this.isLoading = true;
            const formData = new FormData();
            formData.append('content', this.newMessage);
            if (this.fileInput?.files[0]) {
                formData.append('file', this.fileInput.files[0]);
            }
            
            try {
                const response = await fetch(`/messages/${this.conversationId}/send`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: formData
                });
                
                const data = await response.json();
                if (data.success) {
                    this.messages.push(data.message);
                    this.lastMessageId = data.message.id;
                    this.newMessage = '';
                    if (this.fileInput) this.fileInput.value = '';
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Erreur lors de l\'envoi du message');
            } finally {
                this.isLoading = false;
            }
        },
        
        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },
        
        formatTime(date) {
            return new Date(date).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        },
        
        formatDate(date) {
            const d = new Date(date);
            const today = new Date();
            if (d.toDateString() === today.toDateString()) {
                return 'Aujourd\'hui';
            }
            return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long' });
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden h-[calc(100vh-8rem)] flex flex-col">
                <!-- En-tête -->
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        @if($conversation->type === 'private')
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold">
                                {{ $otherParticipant->initials() ?? 'U' }}
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $otherParticipant->name ?? 'Utilisateur' }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">En ligne</p>
                            </div>
                        @else
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
<div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $conversation->name }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $conversation->participants->count() }} membres
                                </p>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($conversation->contact)
                            <a href="{{ route('contacts.show', $conversation->contact) }}" 
                               class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                Voir contact →
                            </a>
                        @endif
                        <a href="{{ route('messages.index') }}" 
                           class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Messages -->
                <div x-ref="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50 dark:bg-gray-900">
                    <template x-for="(message, index) in messages" :key="message.id">
                        <div class="flex" :class="message.user_id === {{ Auth::id() }} ? 'justify-end' : 'justify-start'">
                            <div class="flex items-start space-x-3 max-w-xs lg:max-w-md" :class="message.user_id === {{ Auth::id() }} ? 'flex-row-reverse space-x-reverse' : ''">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xs font-semibold">
                                        <span x-text="message.user.name.charAt(0).toUpperCase()"></span>
                                    </div>
                                </div>
                                <div class="flex flex-col" :class="message.user_id === {{ Auth::id() }} ? 'items-end' : 'items-start'">
                                    <div class="px-4 py-2 rounded-lg"
                                         :class="message.user_id === {{ Auth::id() }} 
                                            ? 'bg-blue-600 text-white' 
                                            : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-700'">
                                        <p class="text-sm whitespace-pre-wrap" x-text="message.content"></p>
                                        <template x-if="message.file_path">
                                            <div class="mt-2">
                                                <a :href="'/storage/' + message.file_path" target="_blank"
                                                   class="inline-flex items-center text-xs text-blue-400 hover:text-blue-300">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    <span x-text="message.file_name"></span>
                                                </a>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="mt-1 flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                                        <span x-text="formatTime(message.created_at)"></span>
                                        <template x-if="message.user_id !== {{ Auth::id() }}">
                                            <span x-text="message.user.name"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
</div>

                <!-- Zone de saisie -->
                <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4">
                    <form @submit.prevent="sendMessage()" class="flex items-end space-x-4">
                        <div class="flex-1">
                            <textarea x-model="newMessage"
                                      @keydown.enter.exact.prevent="sendMessage()"
                                      @keydown.enter.shift.exact="newMessage += '\n'"
                                      rows="1"
                                      placeholder="Tapez votre message..."
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 resize-none"
                                      style="min-height: 40px; max-height: 120px;"></textarea>
                            <input type="file" 
                                   x-ref="fileInput"
                                   @change="fileInput = $el"
                                   class="mt-2 text-sm text-gray-600 dark:text-gray-400"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif">
                        </div>
                        <button type="submit"
                                :disabled="isLoading || (!newMessage.trim() && !fileInput?.files[0])"
                                class="flex-shrink-0 inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
