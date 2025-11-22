<?php


namespace App\Notifications;

use App\Models\Defense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DefenseScheduled extends Notification implements ShouldQueue
{
    use Queueable;

    protected $defense;
    protected $type; // 'team' or 'jury'

    /**
     * Create a new notification instance.
     */
    public function __construct(Defense $defense, string $type = 'team')
    {
        $this->defense = $defense;
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
        $defenseDate = $this->defense->defense_date->format('d/m/Y');
        $defenseTime = \Carbon\Carbon::parse($this->defense->defense_time)->format('H:i');

        if ($this->type === 'jury') {
            $juryMember = $this->defense->juries()->where('teacher_id', $notifiable->id)->first();
            $role = $juryMember ? ucfirst($juryMember->role) : 'Membre du jury';

            return (new MailMessage)
                ->subject('Soutenance programmée - Jury')
                ->greeting('Bonjour ' . $notifiable->name . ',')
                ->line('Vous avez été désigné(e) comme **' . $role . '** pour une soutenance.')
                ->line('**Sujet:** ' . $this->defense->subject->title)
                ->line('**Date:** ' . $defenseDate . ' à ' . $defenseTime)
                ->line('**Salle:** ' . $this->defense->room->name)
                ->line('**Équipe:** ' . $this->defense->project->team->name)
                ->action('Voir les détails', url('/defenses/' . $this->defense->id))
                ->line('Merci de votre participation!');
        }

        return (new MailMessage)
            ->subject('Votre soutenance est programmée')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre soutenance a été programmée!')
            ->line('**Sujet:** ' . $this->defense->subject->title)
            ->line('**Date:** ' . $defenseDate . ' à ' . $defenseTime)
            ->line('**Salle:** ' . $this->defense->room->name)
            ->line('**Durée:** ' . $this->defense->duration . ' minutes')
            ->action('Voir les détails', url('/defenses/' . $this->defense->id))
            ->line('Bonne chance pour votre soutenance!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'defense_id' => $this->defense->id,
            'subject_title' => $this->defense->subject->title,
            'defense_date' => $this->defense->defense_date->format('Y-m-d'),
            'defense_time' => $this->defense->defense_time,
            'room_name' => $this->defense->room->name,
            'type' => $this->type,
            'message' => $this->type === 'jury'
                ? 'Vous êtes membre du jury pour: ' . $this->defense->subject->title
                : 'Soutenance programmée le ' . $this->defense->defense_date->format('d/m/Y'),
        ];
    }
}
