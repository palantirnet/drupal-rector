<?php declare(strict_types=1);

namespace Drupal\rector_examples;

use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DispatchingService {

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $event_dispatcher) {
        $this->eventDispatcher = $event_dispatcher;
    }

    public function doADispatch() {
        $this->eventDispatcher->dispatch(new Event(), 'sample_event_name');
    }

}
