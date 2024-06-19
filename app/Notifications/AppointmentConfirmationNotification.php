<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\API\Entity\Appointment;

class AppointmentConfirmationNotification extends Notification
{
    use Queueable;

    protected $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
        $this->appointment->load('users', 'barbers', 'services', 'barbers.barberShop');
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
        $barbershop = $this->appointment->barbers->barberShop->name;
        $user = $this->appointment->users->firstWhere('id', $this->appointment->users_id)->name;
        $barber = $this->appointment->barbers->name;
        $service = $this->appointment->services->description;
        $schedule = Carbon::parse($this->appointment->schedule_time)->format('H:i, d-m-Y');

        return (new MailMessage())
            ->subject("Confirmação de Agendamento em {$barbershop}!")
            ->line("Olá {$user}, esta é uma confirmação do seu agendamento em {$barbershop}!")
            ->line('Detalhes:')
            ->line("Barbeiro: {$barber}")
            ->line("Serviço: {$service}")
            ->line("Horário: {$schedule}")
            ->action('Ver Agendamento', url('/show-appointment/' . $this->appointment->id))
            ->line('Obrigado por usar nossa plataforma!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'user_id' => $this->appointment->users_id,
            'service_id' => $this->appointment->services_id,
            'schedule_time' => Carbon::parse($this->appointment->schedule_time)->format('H:i, d-m-Y'),
        ];
    }
}
