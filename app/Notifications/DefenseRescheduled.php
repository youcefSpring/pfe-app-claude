<?php

namespace App\Notifications;

use App\Models\Defense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DefenseRescheduled extends Notification implements ShouldQueue
{
    use Queueable;

    protected $defense;
    protected $oldDate;
    protected $oldTime;

    public function __construct(Defense $defense, $oldDate, $oldTime)
    {
        $this->defense = $defense;
        $this->oldDate = $oldDate;
        $this->oldTime = $oldTime;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $newDate = $this->defense->defense_date->format('d/m/Y');
        $newTime = \Carbon\Carbon::parse($this->defense->defense_time)->format('H:i');
        $oldDateFormatted = \Carbon\Carbon::parse($this->oldDate)->format('d/m/Y');
        $oldTimeFormatted = \Carbon\Carbon::parse($this->oldTime)->format('H:i');

        return (new MailMessage)
            ->subject('Soutenance reprogrammée')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre soutenance a été reprogrammée.')
            ->line('**Ancienne date:** ' . $oldDateFormatted . ' à ' . $oldTimeFormatted)
            ->line('**Nouvelle date:** ' . $newDate . ' à ' . $newTime)
            ->line('**Salle:** ' . $this->defense->room->name)
            ->line('**Sujet:** ' . $this->defense->subject->title)
            ->action('Voir les détails', url('/defenses/' . $this->defense->id))
            ->line('Veuillez noter ce changement.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'defense_id' => $this->defense->id,
            'subject_title' => $this->defense->subject->title,
            'old_date' => $this->oldDate,
            'old_time' => $this->oldTime,
            'new_date' => $this->defense->defense_date->format('Y-m-d'),
            'new_time' => $this->defense->defense_time,
            'room_name' => $this->defense->room->name,
            'message' => 'Soutenance reprogrammée au ' . $this->defense->defense_date->format('d/m/Y'),
        ];
    }
}
