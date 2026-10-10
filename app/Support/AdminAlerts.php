<?php

namespace App\Support;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationSender;
use Throwable;

/**
 * Bell notifications in the admin panel (Filament database notifications) for every admin user.
 */
class AdminAlerts
{
    public static function send(string $title, ?string $body = null, ?string $url = null, string $icon = 'heroicon-o-bell'): void
    {
        try {
            $users = User::all();
            if ($users->isEmpty()) {
                return;
            }

            $notification = Notification::make()
                ->title($title)
                ->body($body)
                ->icon($icon)
                ->actions($url ? [Action::make('open')->label('Open')->url($url)->markAsRead()] : [])
                ->toDatabase();

            // Written straight away rather than queued, so alerts arrive without a queue worker.
            NotificationSender::sendNow($users, $notification);
        } catch (Throwable $e) {
            // A notification is a nice-to-have; never fail the visitor's request over it.
            Log::warning('Admin notification failed', ['title' => $title, 'error' => $e->getMessage()]);
        }
    }
}
