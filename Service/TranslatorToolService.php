<?php

namespace AECF\TranslatorToolBundle\Service;

use AECF\TranslatorToolBundle\Editor\CatalogueEditor;
use AECF\TranslatorToolBundle\Loader\MessageCatalogueLoader;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Writer\TranslationWriter;
use Symfony\Component\HttpFoundation\Tests\StringableObject;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

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
    * @var string[]
    */
    private $formats;

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
     * @param array $formats
     * @param string $rootDir
     */
    public function __construct(MessageCatalogueLoader $catalogueLoader, CatalogueEditor $editor, $enabledLocales, $autoCreateMissingFormat, array $formats, $rootDir, $kernel)
    {
        $this->catalogueLoader = $catalogueLoader;
        $this->editor = $editor;
        $this->enabledLocales = $enabledLocales;
        $this->autoCreateMissingFormat = $autoCreateMissingFormat;
        $this->formats = $formats;
        $this->transDir = $rootDir.'/Resources/translations';
        $this->kernel = $kernel;
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
            $this->editor->saveCatalogue($catalogs[$locale], $this->transDir, $this->formats);
        }

        $this->clearCache();

        return $messages;
    }

    public function edit($id, $translation, $domain, $locale)
    {
        $this->catalogue = $this->catalogueLoader->loadMessageCatalogue($locale, $this->transDir);
        $this->editor->edit($this->catalogue, $id, $translation, $domain, $this->transDir, $this->formats);
    }

    public function clearCache()
    {
        // Clear cache
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'cache:clear',
            '--env' => 'dev'
        ));
        $output = new NullOutput();
        $application->run($input, $output);
    }
 }