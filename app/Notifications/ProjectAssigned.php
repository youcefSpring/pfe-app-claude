<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    protected $project;
    protected $type; // 'team' or 'supervisor'

    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project, string $type = 'team')
    {
        $this->project = $project;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->type === 'supervisor') {
            return (new MailMessage)
                ->subject('Nouveau projet assigné')
                ->greeting('Bonjour ' . $notifiable->name . ',')
                ->line('Un nouveau projet vous a été assigné en tant qu\'encadreur.')
                ->line('**Sujet:** ' . $this->project->subject->title)
                ->line('**Équipe:** ' . $this->project->team->name)
                ->action('Voir le projet', url('/projects/' . $this->project->id))
                ->line('Merci de votre engagement!');
        }

        return (new MailMessage)
            ->subject('Projet assigné à votre équipe')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Félicitations! Un projet a été assigné à votre équipe.')
            ->line('**Sujet:** ' . $this->project->subject->title)
            ->line('**Encadreur:** ' . $this->project->supervisor->name)
            ->action('Voir le projet', url('/projects/' . $this->project->id))
                ->line('Bon courage pour votre projet!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'project_id' => $this->project->id,
            'subject_title' => $this->project->subject->title,
            'team_name' => $this->project->team->name,
            'supervisor_name' => $this->project->supervisor->name ?? null,
            'type' => $this->type,
            'message' => $this->type === 'supervisor' 
                ? 'Nouveau projet assigné: ' . $this->project->subject->title
                : 'Projet assigné à votre équipe: ' . $this->project->subject->title,
        ];
    }
}
