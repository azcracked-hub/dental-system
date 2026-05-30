{{-- Fixed toast stack — listens for Livewire notify events (no browser alert()) --}}
<div
    x-data="{
        toasts: [],
        add(detail) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, type: detail.type || 'info', message: detail.message || '' });
            setTimeout(() => this.remove(id), 5000);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    @notify.window="add($event.detail)"
    class="fixed top-4 right-4 z-[300] flex flex-col gap-2 w-full max-w-sm pointer-events-none"
    aria-live="polite"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-x-4"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0 translate-x-4"
            class="pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-xl shadow-lg border text-sm"
            :class="{
                'bg-green-50 border-green-200 text-green-800': toast.type === 'success',
                'bg-red-50 border-red-200 text-red-800': toast.type === 'error',
                'bg-amber-50 border-amber-200 text-amber-900': toast.type === 'warning',
                'bg-blue-50 border-blue-200 text-blue-800': toast.type === 'info',
            }"
        >
            <span class="flex-1" x-text="toast.message"></span>
            <button type="button" @click="remove(toast.id)" class="opacity-60 hover:opacity-100 leading-none shrink-0">&times;</button>
        </div>
    </template>
</div>
