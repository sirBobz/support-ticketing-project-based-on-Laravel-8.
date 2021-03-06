<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TicketConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;


    public $user;
    public $ticket;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $ticket)
    {
        $this->user = $user;
        $this->ticket = $ticket;
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
            ->subject('You created a new Ticket')
            ->greeting('Hi ' . $this->ticket->author_name . ',')
            ->line('You created a new Ticket')
            ->line('You are in position: ' .  $this->ticket->queue_number . '  in the queue.')
            ->line("Your name: " . $this->ticket->author_name)
            ->line("Ticket title: " . $this->ticket->title)
            ->line("Internal review deadline: " . $this->ticket->review_deadline)
            ->line("Editorial requests description: " . Str::limit($this->ticket->editorial_requests, 500))
            ->line("Brief description: " . Str::limit($this->ticket->content, 500))
            ->action('View full ticket', route('admin.tickets.show', $this->ticket->id))
            ->line('Thank you')
            ->line(config('app.name') . ' Team')
            ->salutation(' ');
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
