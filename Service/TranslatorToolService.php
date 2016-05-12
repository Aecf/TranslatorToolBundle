<?php

namespace AECF\TranslatorToolBundle\Service;

use Symfony\Component\Translation\Writer\TranslationWriter;
use Symfony\Bundle\FrameworkBundle\Translation\TranslationLoader;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\HttpFoundation\Tests\StringableObject;
use Symfony\Component\Translation\DataCollectorTranslator;

class TranslatorToolService
{
    const MESSAGE_NEW_WITHOUT_TRANSLATION = 4;
    const MESSAGE_NEW_FROM_FALLBACK = 5;

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
    private $locale;

    /**
     * @var string
     */
    private $autoCreateMissingFormat;

    /**
     * @var string
     */
    private $rootDir;

    /**
     *
     * @param TranslationLoader $translationLoader
     * @param TranslationWriter $translationWriter
     * @param string $locale
     * @param string $autoCreateMissingFormat
     * @param string $rootDir
     */
    public function __construct(TranslationLoader $translationLoader, TranslationWriter $translationWriter, $locale, $autoCreateMissingFormat, $rootDir)
    {
        $this->translationWriter = $translationWriter;
        $this->translationLoader = $translationLoader;
        $this->locale = $locale;
        $this->autoCreateMissingFormat = $autoCreateMissingFormat;
        $this->rootDir = $rootDir;
    }

    /**
     * Add to MessageCatalogue messages array elements that do not exist (missing or equals_fallback)
     *
     * @param array $messages
     * @return array
     */
    public function createMissing($messages)
    {
        $currentCatalogue = $this->loadCurrentMessageCatalogue();

        $nbAdded = 0;
        foreach($messages as $key => $message)
        {
            if($message['state'] == DataCollectorTranslator::MESSAGE_MISSING
                || $message['state'] == DataCollectorTranslator::MESSAGE_EQUALS_FALLBACK)
            {
                if(!$currentCatalogue->has($message['id'], $message['domain']))
                {
                    $currentCatalogue->set($message['id'], $message['translation'], $message['domain']);
                }

                $messages[$key]['state'] = (
                    $message['state'] == DataCollectorTranslator::MESSAGE_MISSING ?
                    self::MESSAGE_NEW_WITHOUT_TRANSLATION : self::MESSAGE_NEW_FROM_FALLBACK
                    );

                $nbAdded++;
            }
        }

        if($nbAdded > 0)
        {
            $this->writeCurrentMessageCatalogue($currentCatalogue, $this->autoCreateMissingFormat);
        }

//        var_dump($messages);die;

        return $messages;
    }

    public function edit($catalogue, $id, $translation, $domain)
    {
        $catalogue->set($id, $translation, $domain);
        $this->writeCurrentMessageCatalogue($catalogue, $this->getCatalogueMajorFormat($catalogue));
    }

    /**
     * @param string $transPath
     * @param string $locale
     * @return MessageCatalogue
     */
    public function loadCurrentMessageCatalogue()
    {
        $transPath = $this->getTransPath($this->rootDir);

        $currentCatalogue = new MessageCatalogue($this->locale);
        $this->translationLoader->loadMessages($transPath, $currentCatalogue);

        return $currentCatalogue;
    }

    private function writeCurrentMessageCatalogue($catalogue, $format)
    {
        $transPath = $this->getTransPath($this->rootDir);

        $this->translationWriter->writeTranslations(
            $catalogue, $format,
            array(
                'path' => $transPath,
                'as_tree' => true
            )
        );
    }

    private function getCatalogueMajorFormat($catalogue)
    {
        $extensions = array();
        foreach($catalogue->getResources() as $res) {
            $filename = explode('.', $res);
            $ext = $filename[(int)count($filename)-1];

            if(isset($extensions[$ext])) {
                $extensions[$ext] = $extensions[$ext]++;
            }
            else
            {
                $extensions[$ext] = 1;
            }
        }

        asort($extensions);
        $keys = array_keys($extensions);
        return array_pop($keys);
    }


    private function getTransPath($rootDir)
    {
       return $rootDir.'/Resources/translations';
    }
}