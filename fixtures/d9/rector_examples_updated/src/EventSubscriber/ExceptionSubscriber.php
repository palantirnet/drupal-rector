<?php declare(strict_types=1);

namespace Drupal\rector_examples\EventSubscriber;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Drupal\Core\EventSubscriber\CustomPageExceptionHtmlSubscriber;
use Drupal\Core\ParamConverter\ParamNotConvertedException;

class ExceptionSubscriber extends CustomPageExceptionHtmlSubscriber {

    public function on404(ExceptionEvent $event) {
        $exception = $event->getThrowable();
        $previous = $exception->getPrevious();
        if ($previous instanceof ParamNotConvertedException) {
            // logic
        }
        else {
            parent::on404($event);
        }
    }
}
