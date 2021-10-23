<?php

namespace App\Notification;

use App\Entity\Comment;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

class CommentReviewNotification
{
	private $comment;

	public function __construct(Comment $comment)
	{
		$this->comment = $comment;

		parent::__construct('Новый комментарий размещен');
	}

	public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
	{
		$message = EmailMessage::fromNotification($this, $recipient, $transport);
		$message->getMessage()
			->htmlTemplate('emails/comment_notification.html.twig')
			->context(['comment' => $this->comment]);

		return $message;
	}
}