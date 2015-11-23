<?php

namespace CULabs\BugCatchBundle\Controller;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function formAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            throw new \Exception();
        }

        return $this->render('@CULabsBugCatch/Default/form.html.twig');
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
