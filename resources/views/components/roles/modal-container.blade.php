
<div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-2xl border-t-4 border-{{ $color }}-500"
     x-show="{{ $show }}"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     @click.away="{{ $show }} = false">
    <!-- ... contenu ... -->
</div>