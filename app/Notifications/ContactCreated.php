<?php

namespace App\Notifications;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ContactCreated extends Notification
{
    use Queueable;

    protected $contact;

    /**
     * Create a new notification instance.
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
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
        return (new MailMessage)
            ->from('youcefsaker09@gmail.com', 'CRM System')
            ->subject('Bienvenue dans notre CRM')
            ->greeting('Bonjour ' . $this->contact->nom . '!')
            ->line('Vous avez été ajouté à notre système CRM.')
            ->line('Vos informations enregistrées :')
            ->line('Nom : ' . $this->contact->nom)
            ->line('Prénom : ' . $this->contact->prenom)
            ->line('Email : ' . $this->contact->email)
            ->line('Téléphone : ' . $this->contact->telephone)
            ->line('Entreprise : ' . $this->contact->entreprise)
            ->line('Merci de votre confiance!');
    }
}
