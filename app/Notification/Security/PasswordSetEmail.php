<?php

namespace App\Notification\Security;

use App\EcoLearn\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordSetEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new instance
     *
     * @param User $user
     * @param boolean $initialization
     */
    public function __construct(
        protected User $user, 
        protected bool $initialization
    ) {
        $this->onQueue('email');
    }

    /**
     * Get the notification's delivery channel
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Generate the email content
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $name = $this->user->getFullname();
        $url = config('app.web.url', '#');
        $name = $notifiable->getFullname();
        $now = Carbon::now();

        if ($this->initialization) {
            return (new MailMessage())
                ->subject(__('mail.password.updated.subject.initialized', ['name' => $name]))
                ->greeting(__('mail.greeting', ['name' => $name]))
                ->line(__('mail.password.updated.content.line1.initialized', [
                    'name' => $name,
                    'date' => $now->format(__('date.format.date.short')),
                    'hour' => $now->format(__('date.format.hour.short')),
                ]))
                ->line(__('mail.password.updated.content.line2'))
                ->action(__('mail.password.updated.action'), $url);
        }

        return (new MailMessage())
            ->subject(__('mail.password.updated.subject.changed', ['name' => $name]))
            ->greeting(__('mail.greeting', ['name' => $name]))
            ->line(__('mail.password.updated.content.line1.changed', [
                'name' => $name,
                'date' => $now->format(__('date.format.date.short')),
                'hour' => $now->format(__('date.format.hour.short')),
            ]))
            ->line(__('mail.password.updated.content.line2'))
            ->action(__('mail.password.updated.action'), $url);
    }
}