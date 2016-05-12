<?php

namespace AECF\TranslatorToolBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     *
     * @param string $name
     * @return Response
     */
    public function indexAction($name)
    {
        return $this->render('TranslatorToolBundle:Default:index.html.twig', array('name' => $name));
    }

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
            $catalogue = $service->loadCurrentMessageCatalogue();

            if($catalogue->has($params['id'], $params['domain']))
            {
                $service->edit($catalogue, $params['id'], $params['translation'], $params['domain']);

                // Clear cache
                $kernel = $this->get('kernel');
                $application = new Application($kernel);
                $application->setAutoExit(false);

                $input = new ArrayInput(array(
                    'command' => 'cache:clear',
                    '--env' => 'dev'
                ));
                $output = new NullOutput();
                $application->run($input, $output);

                return JsonResponse::create(array(
                    'params' => $params
                ), 200);
            }
        }

        return JsonResponse::create(array('message' => 'The request could not be understood by the server due to malformed syntax.'), 400);
    }
}
