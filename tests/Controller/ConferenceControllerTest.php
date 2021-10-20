<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConferenceControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Give your feedback!');

    }

    public function testConferencePage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        echo $client->getResponse();
        $this->assertCount(2, $crawler->filter('h4'));

        $client->clickLink('Посмотреть');

        $this->assertPageTitleContains('Amsterdam');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Amsterdam 2019');
        $this->assertSelectorExists('div:contains("Здесь 1 комментариев")');

        //echo $client->getResponse();

    }

    public function testCommentSubmission()
    {
        $client = static::createClient();
        $client->request('GET', '/conference/amsterdam-2019');
        $client->submitForm('Submit', [
            'comment_form[author]' => 'Sergey',
            'comment_form[text]' => 'Коммент из теста',
            'comment_form[email]' => 's@test.ru',
            'comment_form[photo]' => dirname(__DIR__, 2) . '/public/images/under-construction.gif',
        ]);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("Здесь 2 комментариев")');

        //echo $client->getResponse();
    }
}
