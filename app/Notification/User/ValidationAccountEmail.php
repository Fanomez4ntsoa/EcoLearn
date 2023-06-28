<?php

namespace App\Notification\User;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ValidationAccountEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a notification instance
     *
     * @param string $token
     */
    public function __construct(
        protected string $token
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
        $email = $notifiable->routeNotificationForMail();
        $token = $this->token;
        $hash  = encrypt(json_encode(compact('email','token')));
        $url   = config('app.web.url', '#') . '/account/password/' . $hash;
        $expirationDate = Carbon::now()->addMinutes(config('ecolearn.security.password.token.expiration'));

        return (new MailMessage())
            ->subject(__('mail.signed_up.subject', ['name' => $notifiable->getFullname()]))
            ->greeting(__('mail.greeting', ['name' => $notifiable->getFullname()]))
            ->line(__('mail.signed_up.content.confirmation'))
            ->action(__('mail.signed_up.action'), $url)
            ->line(__('mail.link_expiration', [
                'date' => $expirationDate->format(__('date.format.date.short')),
                'hour' => $expirationDate->format(__('date.format.hour.short')),
        ]));
    }
}