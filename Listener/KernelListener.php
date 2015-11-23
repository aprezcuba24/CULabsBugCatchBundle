<?php

/**
 * @author: Renier Ricardo Figueredo
 * @mail: aprezcuba24@gmail.com
 */
namespace CULabs\BugCatchBundle\Listener;

use CULabs\BugCatch\ErrorHandler\ErrorHandler;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class KernelListener
{
    protected $errorHandler;
    protected $requestStack;
    protected $tokenStorage;

    public function __construct(ErrorHandler $errorHandler, RequestStack $requestStack, TokenStorageInterface $tokenStorage)
    {
        $this->errorHandler = $errorHandler;
        $this->requestStack = $requestStack;
        $this->tokenStorage = $tokenStorage;
    }

    public function onConsoleException(ConsoleExceptionEvent $event)
    {
        $this->errorHandler->notifyCommandException($event->getException());
    }

    public function onHttpException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if ($exception instanceof HttpException) {
            return;
        }
        $masterRequest = $this->requestStack->getMasterRequest();
        $files = [];
        /**@var $file UploadedFile*/
        foreach ($masterRequest->files->all() as $file) {
            $files[] = $this->processFile($file);
        }
        $this->errorHandler->setCookie($masterRequest->cookies->all());
        $this->errorHandler->setFiles($files);
        $this->errorHandler->setGet($masterRequest->query->all());
        $this->errorHandler->setPost($masterRequest->request->all());
        $roles = [];
        foreach ($this->tokenStorage->getToken()->getRoles() as $role) {
            $roles[] = $role->getRole();
        }
        $user = $this->tokenStorage->getToken()->getUser();
        if ($user instanceof UserInterface) {
            $username = $user->getUsername();
        } else {
            $username = $user;
        }
        $this->errorHandler->setUserData([
            'roles' => $roles,
            'user'  => $username,
        ]);

        $this->errorHandler->notifyException($exception);
    }

    protected function processFile(UploadedFile $file)
    {
        $result = [];
        $reflection = new \ReflectionObject($file);
        foreach ($reflection->getMethods() as $method) {
            try {
                $value = $method->invoke($file);
                if (is_object($value)) {
                    continue;
                }
                $result[$method->getName()] = $value;
            } catch (\Exception $e) {}
        }

        return $result;
    }
}