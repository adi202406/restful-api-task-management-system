<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkspaceInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $workspace;
    protected $inviter;
    protected $role;
    protected $token;

    public function __construct(
        $workspace,
        $inviter,
        $role,
        $token
    ) {
        $this->workspace = $workspace;
        $this->inviter = $inviter;
        $this->role = $role;
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $frontendUrl = config('app.frontend_url');

        $inviteUrl =
            $frontendUrl .
            '/invitations/accept?token=' .
            $this->token;

        return (new MailMessage)
            ->subject('Undangan Bergabung ke Workspace')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line(
                $this->inviter->name .
                ' mengundang Anda untuk bergabung ke workspace:'
            )
            ->line('**' . $this->workspace->title . '**')
            ->line(
                'Role Anda: **' .
                ucfirst($this->role) .
                '**'
            )
            ->line(
                'Klik tombol di bawah untuk menerima undangan.'
            )
            ->action('Terima Undangan', $inviteUrl)
            ->line(
                'Link undangan ini memiliki batas waktu.'
            )
            ->line(
                'Jika Anda tidak merasa menerima undangan ini, abaikan email ini.'
            );
    }
}