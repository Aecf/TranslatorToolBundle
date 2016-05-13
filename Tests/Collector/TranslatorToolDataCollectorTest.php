<?php

namespace AECF\TranslatorToolBundle\Collector;

use Mockery as m;
use AECF\TranslatorToolBundle\Service\TranslatorToolService;
use Symfony\Component\Translation\DataCollectorTranslator;

class TranslatorToolDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testLateCollectTriggersCreationOfMissingTranslations()
    {
        $dataCollectorTranslator = m::mock(DataCollectorTranslator::class);
        $dataCollectorTranslator->shouldReceive('getCollectedMessages')
            ->andReturn(array(
                $this->createMessage(DataCollectorTranslator::MESSAGE_DEFINED),
                $this->createMessage(DataCollectorTranslator::MESSAGE_EQUALS_FALLBACK),
                $this->createMessage(DataCollectorTranslator::MESSAGE_MISSING),
            ));

        $translatorTool = m::mock(TranslatorToolService::class);
        $returnedMessages = array($this->createMessage(DataCollectorTranslator::MESSAGE_MISSING));
        $translatorTool->shouldReceive('createMissingTranslations')
            ->once()
            ->andReturn($returnedMessages);

        $dataCollector = new TranslatorToolDataCollector($dataCollectorTranslator, $translatorTool, true);
        $dataCollector->lateCollect();
        $this->assertEquals($returnedMessages, $dataCollector->getMessages());
    }

    public function testLateCollectDoesNotTriggerCreationOfMissingTranslationsIfDisabled()
    {
        $dataCollectorTranslator = m::mock(DataCollectorTranslator::class);
        $dataCollectorTranslator->shouldReceive('getCollectedMessages')
            ->andReturn(array(
                $this->createMessage(TranslatorToolService::MESSAGE_NEW_FROM_FALLBACK),
            ));

        $translatorTool = m::mock(TranslatorToolService::class);
        $translatorTool->shouldReceive('createMissingTranslations')->never();

        $dataCollector = new TranslatorToolDataCollector($dataCollectorTranslator, $translatorTool, false);
        $dataCollector->lateCollect();
    }

    private function createMessage($state)
    {
        return array(
            'state' => $state,
        );
    }
}
