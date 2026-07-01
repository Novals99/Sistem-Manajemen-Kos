<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;

class SystemNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $description,
        public string $icon,
        public ?string $link = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'link' => $this->link,
        ];
    }

    /**
     * Send notification to Owner and Admin users.
     */
    public static function sendToOwnerAndAdmin(string $title, string $description, string $icon, ?string $link = null): void
    {
        $users = User::whereIn('role', ['owner', 'admin'])->get();
        foreach ($users as $user) {
            $user->notify(new self($title, $description, $icon, $link));
        }
    }

    /**
     * Send notification to Owner users only.
     */
    public static function sendToOwnerOnly(string $title, string $description, string $icon, ?string $link = null): void
    {
        $users = User::where('role', 'owner')->get();
        foreach ($users as $user) {
            $user->notify(new self($title, $description, $icon, $link));
        }
    }

    /**
     * Send notification to a specific User.
     */
    public static function sendToUser($user, string $title, string $description, string $icon, ?string $link = null): void
    {
        if ($user) {
            $user->notify(new self($title, $description, $icon, $link));
        }
    }
}
