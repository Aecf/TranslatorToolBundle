<?php

namespace AECF\TranslatorToolBundle\Loader;
use Symfony\Bundle\FrameworkBundle\Translation\TranslationLoader;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Loads the catalogue from translations files
 */
class MessageCatalogueLoader
{
    /**
     * @var TranslationLoader
     */
    private $translationLoader;

    /**
     * @param TranslationLoader $translationLoader
     */
    public function __construct(TranslationLoader $translationLoader)
    {
        $this->translationLoader = $translationLoader;
    }

    /**
     * @return MessageCatalogue
     */
    public function loadMessageCatalogue($locale, $path)
    {
        $catalogue = new MessageCatalogue($locale);
        $this->translationLoader->loadMessages($path, $catalogue);

        return $catalogue;
    }
}