<?php

namespace App\Notifications;

use App\Models\Subject;
use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AllocationResult extends Notification implements ShouldQueue
{
    use Queueable;

    protected $team;
    protected $subject;
    protected $allocated;
    protected $preferenceRank;

    public function __construct(Team $team, ?Subject $subject, bool $allocated, ?int $preferenceRank = null)
    {
        $this->team = $team;
        $this->subject = $subject;
        $this->allocated = $allocated;
        $this->preferenceRank = $preferenceRank;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->allocated && $this->subject) {
            $message = (new MailMessage)
                ->subject('Sujet attribué à votre équipe')
                ->greeting('Bonjour ' . $notifiable->name . ',')
                ->line('Félicitations! Un sujet a été attribué à votre équipe **' . $this->team->name . '**')
                ->line('**Sujet:** ' . $this->subject->title);

            if ($this->preferenceRank) {
                $message->line('Classement de préférence: **' . $this->preferenceRank . '**');
            }

            return $message
                ->action('Voir le projet', url('/teams/' . $this->team->id))
                ->line('Bon courage pour votre projet!');
        }

        return (new MailMessage)
            ->subject('Résultat d\'allocation')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Malheureusement, aucun de vos choix de sujets n\'a pu être attribué.')
            ->line('Veuillez contacter l\'administration pour plus d\'informations.')
            ->action('Voir vos préférences', url('/teams/' . $this->team->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'team_id' => $this->team->id,
            'team_name' => $this->team->name,
            'subject_id' => $this->subject?->id,
            'subject_title' => $this->subject?->title,
            'allocated' => $this->allocated,
            'preference_rank' => $this->preferenceRank,
            'message' => $this->allocated 
                ? 'Sujet attribué: ' . $this->subject->title
                : 'Aucun sujet attribué',
        ];
    }
}
