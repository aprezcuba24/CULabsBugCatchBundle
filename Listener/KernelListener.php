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
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
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
        $files = array();
        /**@var $file UploadedFile*/
        foreach ($masterRequest->files->all() as $file) {
            $files[] = $this->errorHandler->processObject($file, 1);
        }
        $this->errorHandler->setCookie($masterRequest->cookies->all());
        $this->errorHandler->setFiles($files);
        $this->errorHandler->setGet($masterRequest->query->all());
        $this->errorHandler->setPost($masterRequest->request->all());
        $token = $this->tokenStorage->getToken();
        if ($token instanceof TokenInterface) {
            $roles = array();
            foreach ($token->getRoles() as $role) {
                $roles[] = $role->getRole();
            }
            $user = $token->getUser();
            if ($user instanceof UserInterface) {
                $username = $user->getUsername();
            } else {
                $username = $user;
            }
            $this->errorHandler->setUserData(array(
              'roles' => $roles,
              'user'  => $username,
            ));
        }
        $this->errorHandler->notifyException($exception);
    }
}