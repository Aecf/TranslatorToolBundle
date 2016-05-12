<?php

namespace AECF\TranslatorToolBundle\Test;

use AECF\TranslatorToolBundle\Service\TranslatorToolService;
use Mockery as m;
use Symfony\Bundle\FrameworkBundle\Translation\TranslationLoader;
use Symfony\Component\Translation\Writer\TranslationWriter;

class TranslatorToolServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $loader = m::mock(TranslationLoader::class);
        $writer = m::mock(TranslationWriter::class);
        $service = new TranslatorToolService($loader, $writer, 'en', true, '.');
    }
}
