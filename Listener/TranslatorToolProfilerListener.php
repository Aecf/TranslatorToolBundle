<?php

namespace AECF\TranslatorToolBundle\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class TranslatorToolProfilerListener implements EventSubscriberInterface
{
    protected $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        if (!$event->isMasterRequest()) {
            return;
        }

        if ($request->isXmlHttpRequest()) {
            return;
        }

        $content = $response->getContent();
        $pos = strripos($content, '</body>');

        if (false !== $pos) {
            $ajax = "\n".str_replace("\n", '', $this->twig->render(
                'TranslatorToolBundle:DataCollector:ajax_translator_tool.html.twig'
            ))."\n";
            $content = substr($content, 0, $pos).$ajax.substr($content, $pos);
            $response->setContent($content);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse', -128),
        );
    }
}
