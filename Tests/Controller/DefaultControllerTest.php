<?php

namespace AECF\TranslatorToolBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $sample;

    public function setUp()
    {
        $this->client = static::createClient();

        $this->sample = array(
            'id' => 'test',
            'translation' => 'This is my translation !',
            'domain' => 'messages'
        );

        // Sample message
        $translatorToolService = $this->client->getContainer()->get('translator_tool');
        $translatorToolService->edit(
            $translatorToolService->loadCurrentMessageCatalogue(),
            $this->sample['id'], $this->sample['translation'],
            $this->sample['domain']
        );
    }

    public function testEdit()
    {
        // Test without params id, translation, domain : error 400
        $crawler = $this->client->request('POST', '/translator_tool/edit');
        $this->assertEquals($this->client->getResponse()->getStatusCode(), Response::HTTP_BAD_REQUEST);

        // Test with params id, translation, domain : ok 200 and the return contains edited translation
        $editTranslation = 'This is my edit translation !';
        $crawler = $this->client->request('POST', '/translator_tool/edit', array(
            'id' => $this->sample['id'],
            'translation' => $editTranslation,
            'domain' => $this->sample['domain']
        ));

        $content = json_decode($this->client->getResponse()->getContent());

        $this->assertEquals($this->client->getResponse()->getStatusCode(), Response::HTTP_OK);
        $this->assertEquals($content->params->translation, $editTranslation);
    }
}
