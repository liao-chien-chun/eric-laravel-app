<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpiredShortUrlDeletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param array{original_url:string,short_code:string,expired_at:mixed} $shortUrl
     */
    public function __construct(private array $shortUrl)
    {
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
            ->subject('短網址已過期並刪除')
            ->greeting('您好，' . ($notifiable->name ?? '使用者'))
            ->line('您的短網址已過期，系統已自動刪除。')
            ->line('短網址代碼：' . $this->shortUrl['short_code'])
            ->line('原始網址：' . $this->shortUrl['original_url'])
            ->line('過期時間：' . optional($this->shortUrl['expired_at'])->format('Y-m-d H:i:s'))
            ->line('如有需要，請重新建立短網址。');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->shortUrl;
    }
}
