<?php

namespace AECF\TranslatorToolBundle\Test;

use AECF\TranslatorToolBundle\Service\TranslatorToolService;
use Mockery as m;
use Symfony\Bundle\FrameworkBundle\Translation\TranslationLoader;
use Symfony\Component\Translation\Writer\TranslationWriter;
use Symfony\Component\Translation\MessageCatalogue;

class TranslatorToolServiceTest extends \PHPUnit_Framework_TestCase
{
    private $service;
    private $catalogue;

    public function setUp()
    {
        $loader = m::mock(TranslationLoader::class);
        $writer = m::mock(TranslationWriter::class);
        $this->service = new TranslatorToolService($loader, $writer, 'en', true, '.');

        $writer->shouldReceive('writeTranslations');
        $loader->shouldReceive('loadMessages');
        $this->catalogue = $this->service->loadCurrentMessageCatalogue();
    }

    public function testEdit()
    {
        $this->service->edit($this->catalogue, 'security.login', 'login', 'messages');
        $this->assertTrue($this->catalogue->has('security.login', 'messages'));
        $this->assertEquals($this->catalogue->get('security.login', 'messages'), 'login');
    }

    public function testCreateMissing()
    {
        $messages = array(
            array(
                'state' => 1,
                'id' => 'security.logout',
                'translation' => 'logout',
                'domain' => 'messages'
            )
        );

        $result = $this->service->createMissing($messages);

        $this->assertEquals($result[0]['state'], 4);

        $messages = array(
            array(
                'state' => 2,
                'id' => 'security.register',
                'translation' => 'register',
                'domain' => 'messages'
            )
        );

        $result = $this->service->createMissing($messages);

        $this->assertEquals($result[0]['state'], 5);
    }
}
