<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPasswordNotification
{
    /**
     * The password broker name.
     *
     * @var string
     */
    public $broker;

    /**
     * Create a notification instance.
     *
     * @param string $token
     * @param string $broker
     */
    public function __construct($token, $broker = 'users')
    {
        parent::__construct($token);
        $this->broker = $broker;
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
            'broker' => $this->broker,
        ], false));

        return (new MailMessage())
            ->subject(__('passwords.reset.subject'))
            ->line(__('passwords.reset.reason'))
            ->action(__('passwords.reset.action'), $url)
            ->line(__('passwords.reset.expire', ['count' => config('auth.passwords.' . $this->broker . '.expire')]))
            ->line(__('passwords.reset.no_action'));
    }
}
