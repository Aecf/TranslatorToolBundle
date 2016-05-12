<?php

namespace AECF\TranslatorToolBundle\DataCollector;

use Symfony\Component\Translation\DataCollector\TranslationDataCollector;
use Symfony\Component\Translation\DataCollectorTranslator;
use AECF\TranslatorToolBundle\Service\TranslatorToolService;

class TranslatorToolDataCollector extends TranslationDataCollector
{
    /**
     * @var TranslatorToolService
     */
    private $translatorTool;

    /**
     * @var boolean
     */
    private $autoCreateMissingEnabled;

    /**
     * @param DataCollectorTranslator $translator
     * @param TranslatorToolService $translatorTool
     * @param boolean $autoCreateMissingEnabled
     */
    public function __construct(DataCollectorTranslator $translator, TranslatorToolService $translatorTool, $autoCreateMissingEnabled)
    {
        parent::__construct($translator);
        $this->translatorTool = $translatorTool;
        $this->autoCreateMissingEnabled = $autoCreateMissingEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function lateCollect()
    {
        parent::lateCollect();

        // Automatic creation of missing translation (if enabled)
        if(true === $this->autoCreateMissingEnabled)
        {
            $messages = $this->translatorTool->createMissing($this->data['messages']);
            $this->data = $this->computeCount($messages);
            $this->data['messages'] = $messages;
        }
    }

    /**
     * @return int
     */
    public function getCountNewWithoutTranslation()
    {
        return isset($this->data[TranslatorToolService::MESSAGE_NEW_WITHOUT_TRANSLATION]) ? $this->data[TranslatorToolService::MESSAGE_NEW_WITHOUT_TRANSLATION] : 0;
    }

    /**
     * @return int
     */
    public function getCountNewFromFallback()
    {
        return isset($this->data[TranslatorToolService::MESSAGE_NEW_FROM_FALLBACK]) ? $this->data[TranslatorToolService::MESSAGE_NEW_FROM_FALLBACK] : 0;
    }

    public function getName()
    {
        return 'translator_tool';
    }

    private function computeCount($messages)
    {
        $count = array(
            DataCollectorTranslator::MESSAGE_DEFINED => 0,
            DataCollectorTranslator::MESSAGE_MISSING => 0,
            DataCollectorTranslator::MESSAGE_EQUALS_FALLBACK => 0,
            TranslatorToolService::MESSAGE_NEW_WITHOUT_TRANSLATION => 0,
            TranslatorToolService::MESSAGE_NEW_FROM_FALLBACK => 0
        );

        foreach ($messages as $message) {
            ++$count[$message['state']];
        }

        return $count;
    }
}