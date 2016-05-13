<?php

namespace AECF\TranslatorToolBundle\Service;

use AECF\TranslatorToolBundle\Editor\CatalogueEditor;
use AECF\TranslatorToolBundle\Loader\MessageCatalogueLoader;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Writer\TranslationWriter;
use Symfony\Component\HttpFoundation\Tests\StringableObject;
use Symfony\Component\Translation\DataCollectorTranslator;

class TranslatorToolService
{
    const MESSAGE_NEW_WITHOUT_TRANSLATION = 4;
    const MESSAGE_NEW_FROM_FALLBACK = 5;

    /**
     * @var CatalogueEditor
     */
    private $editor;

    /**
     * @var MessageCatalogueLoader
     */
    private $catalogueLoader;

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
     * @var MessageCatalogue
     */
    private $catalogue;

    /**
     *
     * @param MessageCatalogueLoader $catalogueLoader
     * @param CatalogueEditor $editor
     * @param string $locale
     * @param string $autoCreateMissingFormat
     * @param string $rootDir
     */
    public function __construct(MessageCatalogueLoader $catalogueLoader, CatalogueEditor $editor, $locale, $autoCreateMissingFormat, $rootDir)
    {
        $this->catalogueLoader = $catalogueLoader;
        $this->editor = $editor;
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
    public function createMissingTranslations($messages)
    {
        $this->catalogue = $this->catalogueLoader->loadMessageCatalogue($this->locale, $this->rootDir);

        $nbAdded = 0;
        foreach($messages as $key => $message)
        {
            if($message['state'] == DataCollectorTranslator::MESSAGE_MISSING
                || $message['state'] == DataCollectorTranslator::MESSAGE_EQUALS_FALLBACK)
            {
                if(!$this->catalogue->has($message['id'], $message['domain']))
                {
                    $this->catalogue->set($message['id'], $message['translation'], $message['domain']);
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
            $this->editor->saveCatalogue($this->catalogue, $this->autoCreateMissingFormat);
        }

        return $messages;
    }

    public function edit($id, $translation, $domain)
    {
        $this->catalogue = $this->catalogueLoader->loadMessageCatalogue($this->locale, $this->rootDir);
        $this->editor->edit($this->catalogue, $id, $translation, $domain);
    }

 }