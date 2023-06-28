<?php 

namespace App\Notification\Security;

use DateTimeInterface;
use Illuminate\Bus\Queueable;
use App\EcoLearn\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordResetEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance
     *
     * @param User $user
     * @param string $token
     * @param DateTimeInterface $expirationDate
     */
    public function __construct(
        protected User $user,
        protected string $token,
        protected DateTimeInterface $expirationDate
    ) {
        $this->onQueue('email');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return void
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
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
        $name  = $this->user->getFullname();
        $expirationDate = Carbon::createFromInterface($this->expirationDate);

        return (new MailMessage())
            ->subject(__('mail.password.reset.subject', ['name' => $name]))
            ->greeting(__('mail.greeting', ['name' => $name]))
            ->line(__('mail.password.reset.line1'))
            ->line(__('mail.password.reset.line2'))
            ->action(__('mail.password.reset.action'), $url)
            ->line(__('mail.link_expiration', [
                'date' => $expirationDate->format(__('date.format.date.short')),
                'hour' => $expirationDate->format(__('date.format.hour.short')),
            ]));
    }
}