<?php

namespace AECF\TranslatorToolBundle\Test;

use AECF\TranslatorToolBundle\Editor\CatalogueEditor;
use AECF\TranslatorToolBundle\Loader\MessageCatalogueLoader;
use AECF\TranslatorToolBundle\Service\TranslatorToolService;
use Mockery as m;
use Symfony\Bundle\FrameworkBundle\Translation\TranslationLoader;
use Symfony\Component\Translation\Writer\TranslationWriter;
use Symfony\Component\Translation\MessageCatalogue;

class TranslatorToolServiceTest extends \PHPUnit_Framework_TestCase
{
    private $service;

    /**
     * @var CatalogueEditor
     */
    private $editor;

    /**
     * @var MessageCatalogue
     */
    private $messageCatalogue;

    public function setUp()
    {
        $this->messageCatalogue = m::mock(MessageCatalogue::class);
        $this->messageCatalogue->shouldReceive('has')->andReturn(false);
        $this->messageCatalogue->shouldReceive('set');

        $loader = m::mock(MessageCatalogueLoader::class);
        $loader->shouldReceive('loadMessageCatalogue')->andReturn($this->messageCatalogue);

        $this->editor = m::mock(CatalogueEditor::class);
        $this->editor->shouldReceive('saveCatalogue');
        $this->service = new TranslatorToolService($loader, $this->editor, 'en', true, '.');
    }

    public function testEdit()
    {
        $this->editor->shouldReceive('edit')->once();//->with($this->messageCatalogue, 'security_login', 'login', 'messages');
        $this->service->edit('security.login', 'login', 'messages');
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
