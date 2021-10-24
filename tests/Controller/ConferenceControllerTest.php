<?php

namespace App\Tests\Controller;

use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConferenceControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ru/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Оставьте отзыв!');

    }

    public function testConferencePage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ru/');
        echo $client->getResponse();
        $this->assertCount(2, $crawler->filter('h4'));

        $client->clickLink('Посмотреть');

        $this->assertPageTitleContains('Amsterdam');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Amsterdam 2019');
        $this->assertSelectorExists('div:contains("Здесь 1 комментарий")');

        //echo $client->getResponse();

    }

    public function testCommentSubmission()
    {
        $client = static::createClient();
        $client->request('GET', '/ru/conference/amsterdam-2019');
        $client->submitForm('Submit', [
            'comment_form[author]' => 'Sergey',
            'comment_form[text]' => 'Коммент из теста',
            'comment_form[email]' => $email = 's@test.ru',
            'comment_form[photo]' => dirname(__DIR__, 2) . '/public/images/under-construction.gif',
        ]);
        $this->assertResponseRedirects();

		$comment = self::$container->get(CommentRepository::class)->findOneByEmail($email);
		$comment->setState('published');
		self::$container->get(EntityManagerInterface::class)->flush();

        $client->followRedirect();
        $this->assertSelectorExists('div:contains("Здесь 2 комментариев")');

        //echo $client->getResponse();
    }
}
