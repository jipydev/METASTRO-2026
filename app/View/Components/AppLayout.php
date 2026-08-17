<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * The layout title
     */
    public string $title;

    public function __construct(string $title = '')
    {
        $this->title = $title;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        $user = auth()->user();

        return view('layouts.app', [
            'appNotifications' => $user
                ? $user->notifications()->latest()->limit(20)->get()
                : collect(),
            'appUnreadNotificationCount' => $user
                ? $user->unreadNotifications()->count()
                : 0,
        ]);
    }
}
