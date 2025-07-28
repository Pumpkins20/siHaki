<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\HkiSubmission;
use App\Models\User;

class KtpUpdated extends Notification
{
    use Queueable;

    protected $submission;
    protected $updatedMembers;
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(HkiSubmission $submission, array $updatedMembers, User $user)
    {
        $this->submission = $submission;
        $this->updatedMembers = $updatedMembers;
        $this->user = $user;
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
        $memberNames = collect($this->updatedMembers)->pluck('name')->join(', ');

        return (new MailMessage)
            ->subject('KTP Anggota Diperbarui - Submission #' . str_pad($this->submission->id, 4, '0', STR_PAD_LEFT))
            ->greeting('Halo Admin,')
            ->line('User telah memperbarui KTP anggota untuk submission berikut:')
            ->line('**Submission:** ' . $this->submission->title)
            ->line('**User:** ' . $this->user->nama . ' (' . $this->user->nidn . ')')
            ->line('**Status Submission:** ' . ucfirst(str_replace('_', ' ', $this->submission->status)))
            ->line('**Anggota yang diperbarui:** ' . $memberNames)
            ->line('**Total File:** ' . count($this->updatedMembers) . ' file KTP')
            ->action('Lihat Submission', route('admin.submissions.show', $this->submission))
            ->line('KTP baru telah mengganti file yang lama. Status submission tidak berubah.');
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
            'type' => 'ktp_updated',
            'submission_id' => $this->submission->id,
            'submission_title' => $this->submission->title,
            'user_name' => $this->user->nama,
            'updated_members_count' => count($this->updatedMembers),
            'message' => $this->user->nama . ' memperbarui KTP untuk ' . count($this->updatedMembers) . ' anggota di submission "' . $this->submission->title . '"'
        ];
    }
}
