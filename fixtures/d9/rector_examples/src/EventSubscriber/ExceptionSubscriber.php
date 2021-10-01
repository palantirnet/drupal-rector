<?php declare(strict_types=1);

namespace Drupal\rector_examples\EventSubscriber;

use Drupal\Core\EventSubscriber\CustomPageExceptionHtmlSubscriber;
use Drupal\Core\ParamConverter\ParamNotConvertedException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionSubscriber extends CustomPageExceptionHtmlSubscriber {

    public function on404(GetResponseForExceptionEvent $event) {
        $exception = $event->getException();
        $previous = $exception->getPrevious();
        if ($previous instanceof ParamNotConvertedException) {
            // logic
        }
        else {
            parent::on404($event);
        }
    }
}
