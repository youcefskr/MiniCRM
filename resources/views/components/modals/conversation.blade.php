
<div x-show="showNewConversationModal" 
     x-cloak
     x-transition
     class="fixed inset-0 z-50 overflow-y-auto"
     @keydown.escape.window="showNewConversationModal = false"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-title">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
         @click="showNewConversationModal = false">
    </div>

    <!-- Modal Content -->
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <!-- ...reste du contenu de la modale... -->
    </div>
</div>