<?php

namespace AECF\TranslatorToolBundle\Twig;

use Symfony\Bridge\Twig\Extension\TranslationExtension;

class TranslatorToolExtension extends TranslationExtension
{

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('trans', array($this, 'trans'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('transchoice', array($this, 'transchoice'), array('is_safe' => array('html'))),
        );
    }

    public function trans($message, array $arguments = array(), $domain = null, $locale = null)
    {
        $translated = $this->getTranslator()->trans($message, $arguments, $domain, $locale);
        $uniqid = \uniqid();

        return
            '<span class="aecf-translation" style="cursor: pointer">'.$translated.'</span>
            <input type="text" value="'.$translated.'" name="'.$uniqid.'" id="'.$uniqid.'" data-domain="'.$domain.'" style="display:none" />'
        ;
    }

    public function transchoice($message, $count, array $arguments = array(), $domain = null, $locale = null)
    {
        $translated = $this->getTranslator()->transChoice(
            $message, $count, array_merge(array('%count%' => $count), $arguments), $domain, $locale
        );
        $uniqid = \uniqid();

        return
            '<span class="aecf-translation" style="cursor: pointer">'.$transchoice.'</span>
            <input type="text" value="'.$translated.'" name="'.$uniqid.'" id="'.$uniqid.'" data-domain="'.$domain.'" style="display:none" />'
        ;
    }

    public function getName()
    {
        return 'translator_tool';
    }
}