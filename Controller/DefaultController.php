<?php

namespace CULabs\BugCatchBundle\Controller;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name = 'Renier')
    {
        $exception = new \Exception('Mostrar');
        $response = $this->get('cu_labs_bug_catch.client')->request('POST', 'errors.json', [
            'form_params' => [
                'code'          => 4,
                'message'       => $exception->getMessage(),
                'file'          => $exception->getFile(),
                'line'          => $exception->getLine(),
                'traceAsString' => $exception->getTraceAsString(),
            ],
        ]);

        dump($response->getBody()->getContents());

        return $this->render('CULabsBugCatchBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * Throws an exception to test Bugsnag exceptions
     *
     * @throws \Exception
     */
    public function exceptionAction()
    {
        try {
            $this->throwException();
        } catch (\Exception $e) {
            throw new \Exception('Exception for testing BugCatch integration', 0, $e);
        }
        throw new \Exception('Exception for testing BugCatch integration');
    }

    protected function throwException()
    {
        throw new \Exception('First exception.');
    }

    /**
     * Throws a fatal error to test Bugsnag exceptions
     */
    public function errorAction()
    {
        $testObject = new \stdClass();
        $testObject->methodNotExists();
    }
}
