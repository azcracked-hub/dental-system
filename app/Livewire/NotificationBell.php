<?php

namespace App\Livewire;

use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public bool $open = false;

    public function markRead(int $id): void
    {
        UserNotification::where('user_id', Auth::id())->where('id', $id)->first()?->markRead();
    }

    public function markAllRead(): void
    {
        UserNotification::where('user_id', Auth::id())->whereNull('read_at')->update(['read_at' => now()]);
        $this->alertSuccess('All notifications marked as read.');
    }

    private function alertSuccess(string $message): void
    {
        session()->flash('success', $message);
        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function render()
    {
        $notifications = UserNotification::where('user_id', Auth::id())
            ->latest()
            ->limit(15)
            ->get();

        $unreadCount = UserNotification::where('user_id', Auth::id())->whereNull('read_at')->count();

        return view('livewire.notification-bell', compact('notifications', 'unreadCount'));
    }
}
