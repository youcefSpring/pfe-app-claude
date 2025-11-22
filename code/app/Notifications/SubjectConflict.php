<?php

namespace App\Notifications;

use App\Models\Subject;
use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubjectConflict extends Notification implements ShouldQueue
{
    use Queueable;

    protected $subject;
    protected $team;
    protected $conflictingTeams;

    public function __construct(Subject $subject, Team $team, $conflictingTeams)
    {
        $this->subject = $subject;
        $this->team = $team;
        $this->conflictingTeams = $conflictingTeams;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Conflit de choix de sujet')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre équipe **' . $this->team->name . '** a un conflit pour le sujet:')
            ->line('**' . $this->subject->title . '**')
            ->line('Nombre d\'équipes en conflit: ' . count($this->conflictingTeams))
            ->line('Le système procédera à l\'allocation selon les critères de priorité.')
            ->action('Voir les détails', url('/teams/' . $this->team->id))
            ->line('Vous serez notifié du résultat.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'subject_id' => $this->subject->id,
            'subject_title' => $this->subject->title,
            'team_id' => $this->team->id,
            'team_name' => $this->team->name,
            'conflicting_teams_count' => count($this->conflictingTeams),
            'message' => 'Conflit pour le sujet: ' . $this->subject->title,
        ];
    }
}
