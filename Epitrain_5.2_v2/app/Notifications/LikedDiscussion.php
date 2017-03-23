<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LikedDiscussion extends Notification
{
    use Queueable;

    //private $url;
    private $userWhoLiked;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($userWhoLiked)
    {
        //$this->url = $url;
        $this->userWhoLiked = $userWhoLiked;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line($this->userWhoLiked)
                    ->line(' liked your discussion.');
                    //->action('View Liked Discussion', $this->url)
                    //->line('Enjoy Yourself!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
