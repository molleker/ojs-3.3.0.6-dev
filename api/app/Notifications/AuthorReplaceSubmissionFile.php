<?php

namespace App\Notifications;

use App\Article;

use App\States\ArticleFileState;


use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;

class AuthorReplaceSubmissionFile extends Notification
{
    use Queueable, SerializesModels;
    /**
     * @var Article
     */
    private $article;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function articleLink($alias)
    {
        $article = [
            'url' => config('app.cochrane_url') . '/index.php/' . $this->article->journal->path . '/editor/submission/' . $this->article->article_id,
            'text' => "Статья \"{$this->article->setting('title')}\""
        ];
        return $article[$alias];
    } 

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

         $before = $this->article->submissionFile &&
            $this->article->submissionFile->antiplagiatReport/* &&
            $this->article->submissionFile->antiplagiatReport->status_id === AntiplagiatReportStatus::READY*/;
/*        return
            AntiplagiatReport::whereIn('file_id', $this->submissionAllFilesIds())->count() < 2 &&
            ! $this->edit_submission_file &&
            $this->submissionFile &&
            $this->submissionFile->antiplagiatReport &&
            $this->submissionFile->antiplagiatReport->status_id === AntiplagiatReportStatus::READY &&
            $this->editors()->count() === 0;*/
        return (new MailMessage)
            ->subject('Файл статьи изменён')
            ->greeting($this->article->journal->setting('contactName')->setting_value."!")
            ->line("Автор заменил исходный файл статьи")
//            ->line("После отправки на антиплагиат=".$before)
	    ->action($this->articleLink('text'), $this->articleLink('url'))
	    ->from("mail.mediasphera@gmail.com", "Электронная редакция")
	;
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
            //
        ];
    }
}
