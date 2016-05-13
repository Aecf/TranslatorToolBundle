<?php

namespace AECF\TranslatorToolBundle\Editor;

use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Writer\TranslationWriter;

class CatalogueEditor
{
    /**
     * @var TranslationWriter
     */
    private $writer;

    public function __construct(TranslationWriter $writer)
    {
        $this->writer = $writer;
    }

    public function edit(MessageCatalogue $catalogue, $id, $translation, $domain, $path, $formats)
    {
        $catalogue->set($id, $translation, $domain);
        $this->saveCatalogue($catalogue, $path, $formats);
    }

    public function saveCatalogue(MessageCatalogue $catalogue, $path, $formats)
    {
        foreach ($formats as $format) {
            $this->writer->writeTranslations(
                $catalogue, $format,
                array(
                    'as_tree' => true,
                    'path' => $path
                )
            );
        }
    }
}
