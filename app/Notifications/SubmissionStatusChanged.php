<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\HkiSubmission;

class SubmissionStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $submission;
    protected $previousStatus;
    protected $newStatus;
    protected $notes;

    protected $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(HkiSubmission $submission, $previousStatus, $newStatus, $notes = null, $message = null)
    {
        $this->submission = $submission;
        $this->previousStatus = $previousStatus;
        $this->newStatus = $newStatus;
        $this->notes = $notes;
        $this->message = $message;
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
        $statusMessages = [
            'submitted' => 'Submission Anda berhasil diterima',
            'under_review' => 'Submission sedang direview',
            'approved' => 'Submission Anda disetujui! 🎉',
            'rejected' => 'Submission perlu perbaikan',
            'revision_needed' => 'Submission memerlukan revisi'
        ];

        $statusColors = [
            'approved' => 'success',
            'rejected' => 'error',
            'revision_needed' => 'warning',
            'under_review' => 'info',
            'submitted' => 'info'
        ];

        $message = (new MailMessage)
            ->subject('Update Status Pengajuan HKI - ' . $this->submission->title)
            ->greeting('Halo ' . $notifiable->nama . '!')
            ->line('Pengajuan HKI Anda telah diupdate:')
            ->line('**Judul:** ' . $this->submission->title)
            ->line('**Status Baru:** ' . ucfirst($this->newStatus))
            ->line('**Waktu Update:** ' . now()->setTimezone('Asia/Jakarta')->format('d M Y H:i') . ' WIB')
            ->when($this->message, function($mail) {
                return $mail->line($this->message);
            })
            ->action('Lihat Detail', route('user.submissions.show', $this->submission))
            ->line('Terima kasih telah menggunakan SiHaki!')
            ->salutation('Salam, Tim SiHaki AMIKOM Surakarta');

        if ($this->notes) {
            $message->line('**Catatan Admin:** ' . $this->notes);
        }

        // Action button based on status
        switch ($this->newStatus) {
            case 'approved':
                $message->line('Selamat! Pengajuan HKI Anda telah disetujui. Sertifikat akan segera dikirim.')
                        ->action('Lihat Detail Pengajuan', route('user.submissions.show', $this->submission));
                break;
            
            case 'revision_needed':
                $message->line('Silakan lakukan perbaikan sesuai catatan admin.')
                        ->action('Edit Pengajuan', route('user.submissions.edit', $this->submission));
                break;
            
            case 'rejected':
                $message->line('Mohon maaf, pengajuan Anda tidak dapat diproses lebih lanjut.')
                        ->action('Lihat Detail', route('user.submissions.show', $this->submission));
                break;
            
            default:
                $message->action('Lihat Pengajuan', route('user.submissions.show', $this->submission));
                break;
        }

        $message->line('Terima kasih telah menggunakan SiHaki!')
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
            'title' => 'Status Pengajuan Diperbarui',
            'message' => "Pengajuan '{$this->submission->title}' status berubah menjadi " . ucfirst(str_replace('_', ' ', $this->newStatus)),
            'action_url' => route('user.submissions.show', $this->submission),
            'type' => $this->getNotificationType(),
            'icon' => $this->getNotificationIcon(),
        ];
    }

    private function getNotificationType()
    {
        return match($this->newStatus) {
            'approved' => 'success',
            'rejected' => 'danger',
            'revision_needed' => 'warning',
            default => 'info'
        };
    }

    private function getNotificationIcon()
    {
        return match($this->newStatus) {
            'approved' => 'check-circle',
            'rejected' => 'x-circle',
            'revision_needed' => 'exclamation-triangle',
            'under_review' => 'eye',
            default => 'bell'
        };
    }
}
