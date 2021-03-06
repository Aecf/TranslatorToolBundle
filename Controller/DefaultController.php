<?php

namespace AECF\TranslatorToolBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/edit")
     * @Method({"POST"})
     */
    public function editAction(Request $request)
    {
        $params = $request->request->all();
        if(isset($params['id']) && isset($params['translation']) && isset($params['domain']))
        {
            $service = $this->get('translator_tool');
            $catalogue = $this->get('catalogue_loader')->loadMessageCatalogue($request->getLocale(), $this->getParameter('kernel.root_dir'));

            if($catalogue->has($params['id'], $params['domain']))  {
                $service->edit($params['id'], $params['translation'], $params['domain'], $request->getLocale());

                $service->clearCache();

                return JsonResponse::create(array(
                    'params' => $params
                ), 200);
            }
        }

        return JsonResponse::create(array('message' => 'The request could not be understood by the server due to malformed syntax.'), 400);
    }
}
