<?php

namespace App\MessageHandler;

use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use App\SpamChecker;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CommentMessageHandler implements MessageHandlerInterface
{
	private $spamChecker;
	private $entityManager;
	private $commentRepository;
//	private $bus;
//	private $workflow;
//	private $notifier;
//	private $imageOptimizer;
//	private $photoDir;
//	private $logger;

	public function __construct(EntityManagerInterface $entityManager, SpamChecker $spamChecker, CommentRepository $commentRepository)
	{
		$this->entityManager = $entityManager;
		$this->spamChecker = $spamChecker;
		$this->commentRepository = $commentRepository;
	}

	public function __invoke(CommentMessage $message)
	{
		$comment = $this->commentRepository->find($message->getId());
		if (!$comment) {
			return;
		}

		if (2 === $this->spamChecker->getSpamScore($comment, $message->getContext())) {
			$comment->setState('spam');
		} else {
			$comment->setState('published');
		}

		$this->entityManager->flush();
	}
}