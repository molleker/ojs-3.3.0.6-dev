<?php

namespace App\Notifications;

use App\AntiplagiatReport;
use App\AntiplagiatReportStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;

class AntiplagiatJobDone extends Notification
{
    use Queueable, SerializesModels;
    /**
     * @var AntiplagiatReport
     */
    private $antiplagiat_report;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(AntiplagiatReport $antiplagiat_report)
    {
        //
        $this->antiplagiat_report = $antiplagiat_report;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function report()
    {
        return "
        Оригинальные блоки: {$this->antiplagiat_report->original_score}
        Заимствованные блоки: {$this->antiplagiat_report->score}
        Заимствования из белых источников: {$this->antiplagiat_report->white_score}
        Итоговая оценка оригинальности: {$this->antiplagiat_report->original_score}
        ";
    }

    public function articleLink($alias)
    {
        $article = [
            'url' => config('app.cochrane_url') . '/index.php/' . $this->antiplagiat_report->file->article->journal->path . '/editor/submission/' . $this->antiplagiat_report->file->article_id,
            'text' => "Проверка статьи \"{$this->antiplagiat_report->file->article->setting('title')}\""
        ];
        return $article[$alias];
    }

    public function antiplagiatLink($alias)
    {
        $link = [
            'text' => 'Ссылка на антиплагиате',
            'url' => $this->antiplagiat_report->link,
        ];
        return $link[$alias];
    }

    public function toText()
    {
        return "{$this->articleLink('text')} : {$this->articleLink('url')} \n {$this->report()} \n{$this->antiplagiatLink('text')}: {$this->antiplagiatLink('url')}" ;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $lines = explode("\n", $this->report());
        $message = (new MailMessage)
            ->subject(config('antiplagiat.email.subject'))
            ->greeting($this->articleLink('text'))
            ->line($this->articleLink('url'))
        ;
        foreach ($lines as $line) {
            $message->line($line);
        }
        $message->action($this->antiplagiatLink('text'), $this->antiplagiatLink('url'));
        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
