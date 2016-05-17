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
    private $enabledLocales;

    /**
     * @var string
     */
    private $autoCreateMissingFormat;

    /**
     * @var string
     */
    private $transDir;

    /**
     * @var MessageCatalogue
     */
    private $catalogue;

    /**
     *
     * @param MessageCatalogueLoader $catalogueLoader
     * @param CatalogueEditor $editor
     * @param array $enabledLocales
     * @param string $autoCreateMissingFormat
     * @param string $rootDir
     */
    public function __construct(MessageCatalogueLoader $catalogueLoader, CatalogueEditor $editor, $enabledLocales, $autoCreateMissingFormat, $rootDir)
    {
        $this->catalogueLoader = $catalogueLoader;
        $this->editor = $editor;
        $this->enabledLocales = $enabledLocales;
        $this->autoCreateMissingFormat = $autoCreateMissingFormat;
        $this->transDir = $rootDir.'/Resources/translations';
    }

    /**
     * Add to MessageCatalogue messages array elements that do not exist (missing or equals_fallback)
     *
     * @param array $messages
     * @return array
     */
    public function createMissingTranslations(&$messages)
    {
        $catalogs = array();
        $catalogsToSave = array();

        foreach ($this->enabledLocales as $locale) {
            $catalogs[$locale] = $this->catalogueLoader->loadMessageCatalogue($locale, $this->transDir);
        }

        foreach ($messages as $key => $message) {
            foreach ($catalogs as $locale => $catalog) {
                if(!$catalog->has($message['id'], $message['domain'])) {
                    $catalog->set($message['id'], $message['translation'], $message['domain']);
                    $catalogsToSave[$locale] = true;
                }
            }

            switch($message['state']) {
                case DataCollectorTranslator::MESSAGE_MISSING:
                    $messages[$key]['state'] = self::MESSAGE_NEW_WITHOUT_TRANSLATION;
                    break;
                case DataCollectorTranslator::MESSAGE_EQUALS_FALLBACK:
                    $messages[$key]['state'] = self::MESSAGE_NEW_FROM_FALLBACK;
                    break;
            }
        }

        foreach ($catalogsToSave as $locale => $bool) {
            $this->editor->saveCatalogue($catalogs[$locale], $this->autoCreateMissingFormat, $this->transDir);
        }

        return $messages;
    }

    public function edit($id, $translation, $domain, $locale)
    {
        $this->catalogue = $this->catalogueLoader->loadMessageCatalogue($locale, $this->transDir);
        $this->editor->edit($this->catalogue, $id, $translation, $domain, $this->transDir);
    }

 }
