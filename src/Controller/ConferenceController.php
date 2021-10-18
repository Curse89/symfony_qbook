<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ConferenceController extends AbstractController
{
	private $twig;

	public function __construct(Environment $twig)
	{
		$this->twig = $twig;
	}
    /**
     * @Route("/", name="homepage")
     */
    public function index(ConferenceRepository $conferenceRepository): Response
    {
        return new Response($this->twig->render('conference/index.html.twig', [
            //'conferences' => $conferenceRepository->findAll(),
        ]));
    }
    /**
     * @Route("/conference/{id}", name="conference")
     */
    public function show(LoggerInterface $log, ConferenceRepository $conferenceRepository, Request $request, Conference $conference, CommentRepository $commentRepository): Response
    {
		$log->critical('PRIVET!');
		$offset = max(0, $request->query->getInt('offset', 0));
		$paginator = $commentRepository->getCommentPaginator($conference, $offset);
		//dump($offset - CommentRepository::PAGINATOR_PER_PAGE);
        return new Response($this->twig->render('conference/show.html.twig', [
			//'conferences' => $conferenceRepository->findAll(),
            'conference' => $conference,
			'comments' => $paginator,
			'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
			'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
        ]));
    }
}
