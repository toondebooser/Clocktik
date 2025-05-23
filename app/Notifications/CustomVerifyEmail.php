<?php

namespace App\Notifications;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends Notification
{
    use Queueable;
    protected $companyCode;
    protected $adminEmail;
    /**
     * Create a new notification instance.
     */
    public function __construct($companyCode, $email)
    {
        $this->companyCode = $companyCode;
        $this->adminEmail = $email;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $company= Company::where('company_code', $this->companyCode)->first();
        return (new MailMessage)
        ->subject('Verify Your Email Address')
        ->view('email', ['adminEmail'=> $this->adminEmail, 'url' => $verificationUrl,'company'=>$company, 'companyCode' => $this->companyCode, 'name' => $notifiable->name,]);
    }
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify', Carbon::now()->addMinutes(60), [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
