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
        $this->saveCatalogue($catalogue, $this->getCatalogueMajorFormat($catalogue), $path, $formats);
    }

    public function saveCatalogue(MessageCatalogue $catalogue, $path, $formats)
    {
        foreach ($formats as $format) {
            $this->writer->writeTranslations(
                $catalogue, $format,
                array(
                    'path'    => $path . '/Resources/translations',
                    'as_tree' => true
                )
            );
        }
    }

    private function getCatalogueMajorFormat(MessageCatalogue $catalogue)
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
}
