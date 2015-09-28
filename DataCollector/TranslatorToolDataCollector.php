<?php

namespace MD\TranslatorToolBundle\DataCollector;

use Symfony\Component\Translation\DataCollector\TranslationDataCollector;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Translation\Writer\TranslationWriter;
use Symfony\Bundle\FrameworkBundle\Translation\TranslationLoader;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\HttpFoundation\Tests\StringableObject;

/**
 * @author Mathieu DUMOUTIER <mathieu@dumoutier.fr>
 */
class TranslatorToolDataCollector extends TranslationDataCollector 
{    
    const MESSAGE_NEW = 4;
    
    /**
     * @var TranslationWriter
     */
    private $translationWriter;
    
    /**
     * @var TranslationLoader
     */
    private $translationLoader;
    
    /**
     * @var string
     */
    private $rootDir;
    
    /**
     * @var boolean
     */
    private $autoCreateMissingEnabled;
    
    /**
     * @var string
     */
    private $autoCreateMissingFormat;
    
    /**
     * @param DataCollectorTranslator $translator
     * @param TranslationWriter $translationWriter
     * @param TranslationLoader $translationLoader
     * @param string $rootDir
     */
    public function __construct(DataCollectorTranslator $translator, TranslationLoader $translationLoader, TranslationWriter $translationWriter, $rootDir, $autoCreateMissingEnabled, $autoCreateMissingFormat)
    {
        parent::__construct($translator);
        
        $this->translationWriter = $translationWriter;
        $this->translationLoader = $translationLoader;
        $this->rootDir = $rootDir;
        $this->autoCreateMissingEnabled = $autoCreateMissingEnabled;
        $this->autoCreateMissingFormat = $autoCreateMissingFormat;
    }
    
    /**
     * {@inheritdoc}
     */
    public function lateCollect()
    {
        parent::lateCollect();
        
        if(true === $this->autoCreateMissingEnabled)
        {
            $this->createMissing($this->data['messages']);
        }
    }
    
    private function createMissing($messages)
    {
        $currentCatalogue = new MessageCatalogue('fr');

        // Define Root Path to App folder
        $transPath = $this->rootDir.'/Resources/translations';

        $this->translationLoader->loadMessages($transPath, $currentCatalogue);
        
        $nbAdded = 0;
        foreach($messages as $key => $message)
        {
            if($message['state'] == DataCollectorTranslator::MESSAGE_MISSING)
            {
                $currentCatalogue->set($message['id'], $message['translation'], $message['domain']);
                $this->data['messages'][$key]['state'] = self::MESSAGE_NEW;
                $nbAdded++;
            }
        }

        if($nbAdded > 0)
        {
            $this->translationWriter->writeTranslations(
                $currentCatalogue, 
                $this->autoCreateMissingFormat, 
                array('path' => $transPath, 'as_tree' => true)
            );
            
            
        }
    }
    
    public function getName()
    {
        return 'translator_tool';
    }
}