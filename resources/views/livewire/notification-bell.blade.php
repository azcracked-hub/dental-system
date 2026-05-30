<div class="relative" x-data="{ open: @entangle('open') }">
    <button type="button" @click="open = !open" class="relative p-2 rounded-lg hover:bg-gray-800 text-gray-300 hover:text-white transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
        @endif
    </button>

    <div x-show="open" x-cloak @click.outside="open = false" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllRead" class="text-xs text-yellow-600 hover:underline">Mark all read</button>
            @endif
        </div>
        <div class="max-h-80 overflow-y-auto">
            @forelse($notifications as $note)
                <div wire:click="markRead({{ $note->id }})" class="px-4 py-3 border-b border-gray-50 cursor-pointer hover:bg-gray-50 {{ $note->read_at ? 'opacity-60' : 'bg-yellow-50/40' }}">
                    <p class="text-sm font-semibold text-gray-800">{{ $note->title }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $note->message }}</p>
                    <p class="text-[10px] text-gray-400 mt-1">{{ $note->created_at->diffForHumans() }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-8">No notifications yet.</p>
            @endforelse
        </div>
    </div>
</div>
