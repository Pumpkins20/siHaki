<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\HkiSubmission;
use App\Models\SubmissionDocument;

class CertificateSent extends Notification implements ShouldQueue
{
    use Queueable;

    protected $submission;
    protected $certificate;
    protected $notes;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(HkiSubmission $submission, SubmissionDocument $certificate, $notes = null)
    {
        $this->submission = $submission;
        $this->certificate = $certificate;
        $this->notes = $notes;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('🎉 Sertifikat HKI Sudah Tersedia - ' . $this->submission->title)
            ->greeting('Selamat ' . $notifiable->nama . '!')
            ->line('Sertifikat HKI untuk pengajuan Anda sudah tersedia!')
            ->line('**Judul HKI:** ' . $this->submission->title)
            ->line('**Jenis Ciptaan:** ' . ucfirst(str_replace('_', ' ', $this->submission->creation_type)));

        if ($this->notes) {
            $message->line('**Catatan:** ' . $this->notes);
        }

        $message->line('Anda dapat mengunduh sertifikat melalui sistem SiHaki.')
                ->action('Unduh Sertifikat', route('user.submissions.show', $this->submission))
                ->line('Terima kasih atas partisipasi Anda dalam program HKI AMIKOM!')
                ->salutation('Salam, Tim SiHaki AMIKOM Surakarta');

        return $message;
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
            'submission_id' => $this->submission->id,
            'title' => 'Sertifikat HKI Tersedia',
            'message' => "Sertifikat untuk pengajuan '{$this->submission->title}' sudah dapat diunduh",
            'action_url' => route('user.submissions.show', $this->submission),
            'type' => 'success',
            'icon' => 'award',
        ];
    }
}
