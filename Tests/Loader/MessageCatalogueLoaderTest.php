<?php

namespace AECF\TranslatorToolBundle\Test\Loader;

use AECF\TranslatorToolBundle\Loader\MessageCatalogueLoader;
use Mockery as m;
use Symfony\Bundle\FrameworkBundle\Translation\TranslationLoader;

class MessageCatalogueLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $loader;

    public function setUp()
    {
        $loader = m::mock(TranslationLoader::class);
        $loader->shouldReceive('loadMessages')
            ->andReturn(array("first", "second", "third"))
            ->once();

        $this->loader = new MessageCatalogueLoader($loader);
    }

    public function testMessageCatalogueLocale()
    {
        $catalogue = $this->loader->loadMessageCatalogue('en', 'app/Ressources/translations');
        $this->assertEquals($catalogue->getLocale(), 'en');
    }
}
