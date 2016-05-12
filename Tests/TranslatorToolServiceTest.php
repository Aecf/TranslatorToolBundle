<?php

namespace AECF\TranslatorToolBundle\Test;

use AECF\TranslatorToolBundle\Service\TranslatorToolService;
use Mockery as m;
use Symfony\Bundle\FrameworkBundle\Translation\TranslationLoader;
use Symfony\Component\Translation\Writer\TranslationWriter;
use Symfony\Component\Translation\MessageCatalogue;

class TranslatorToolServiceTest extends \PHPUnit_Framework_TestCase
{
    private static $service;

    private static $catalogue;

    public function testCreateService()
    {
        $loader = m::mock(TranslationLoader::class);
        $writer = m::mock(TranslationWriter::class);
        self::$service = new TranslatorToolService($loader, $writer, 'en', true, '.');

        $writer->shouldReceive('writeTranslations');
        $loader->shouldReceive('loadMessages');
        self::$catalogue = self::$service->loadCurrentMessageCatalogue();
    }

    public function testLoadCurrentMessageCatalogue()
    {
        $this->assertTrue(self::$catalogue instanceof MessageCatalogue);
    }

    public function testEdit()
    {
        self::$service->edit(self::$catalogue, 'security.login', 'login', 'messages');
        $this->assertTrue(self::$catalogue->has('security.login', 'messages'));
        $this->assertEquals(self::$catalogue->get('security.login', 'messages'), 'login');
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

        $result = self::$service->createMissing($messages);

        $this->assertEquals($result[0]['state'], 4);

        $messages = array(
            array(
                'state' => 2,
                'id' => 'security.register',
                'translation' => 'register',
                'domain' => 'messages'
            )
        );

        $result = self::$service->createMissing($messages);

        $this->assertEquals($result[0]['state'], 5);
    }
}
