<?php

namespace App\EventListener;

use App\Exception\RedirectException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * Class RedirectExceptionListener
 * @package App\EventListener
 */
class RedirectExceptionListener
{

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        if (($exception = $event->getThrowable()) instanceof RedirectException) {
            $event->setResponse($exception->getResponse());
        }
    }

}