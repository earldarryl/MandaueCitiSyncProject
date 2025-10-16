<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Filament\Notifications\Notification;
use App\Models\User;
use App\Models\Assignment;
use Illuminate\Support\Facades\Log;

class SendLogoutNotification
{
    public function handle(Logout $event): void
    {
        $authUser = $event->user;

        Log::info('SendLogoutNotification fired', [
            'user_id' => $authUser?->id,
            'user_name' => $authUser?->name,
        ]);

        if (! $authUser instanceof User) {
            Log::warning('Event user is not a valid User instance');
            return;
        }

        $user = User::find($authUser->getAuthIdentifier());

        if (! $user || ! $user->hasRole('citizen')) {
            Log::info('User is not a citizen — skipping notifications', [
                'user_id' => $user?->id,
            ]);
            return;
        }

        $liaisonIds = Assignment::whereHas('grievance', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->pluck('hr_liaison_id')
            ->unique()
            ->filter();

        if ($liaisonIds->isEmpty()) {
            Log::info('No HR liaisons found for citizen', ['citizen_id' => $user->id]);
            return;
        }

        $hrLiaisons = User::whereIn('id', $liaisonIds)->get();

        Log::info('Sending logout notifications to HR liaisons', [
            'citizen' => $user->name,
            'liaison_ids' => $hrLiaisons->pluck('id')->toArray(),
            'liaison_names' => $hrLiaisons->pluck('name')->toArray(),
        ]);

        foreach ($hrLiaisons as $liaison) {
            try {
                Notification::make()
                    ->title('Citizen Logged Out')
                    ->body("{$user->name} has logged out of the system.")
                    ->success()
                    ->broadcast($liaison)
                    ->sendToDatabase($liaison);

                Log::info('Notification sent successfully', [
                    'to_liaison_id' => $liaison->id,
                    'to_liaison_name' => $liaison->name,
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed to send notification', [
                    'to_liaison_id' => $liaison->id,
                    'to_liaison_name' => $liaison->name,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
