<?php
namespace Sescandell\ContextRestrictorBundle\Storage;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Sescandell\ContextRestrictorBundle\Event\ContextRestrictorStorageEvents;
use Sescandell\ContextRestrictorBundle\Event\ContextRestrictorChangedEvent;
use Sescandell\ContextRestrictorBundle\Event\ContextRestrictorInitializedEvent;
use Sescandell\ContextRestrictorBundle\Exception\NoActiveContextFoundException;

/**
 */
class InMemoryContextRestrictorStorage implements ContextRestrictorStorageInterface
{
    /**
     * @var mixed
     */
    protected $context = null;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Default constructor
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * (non-PHPdoc)
     * @see \Sescandell\ContextRestrictorBundle\Storage\ContextRestrictorStorageInterface::initialize()
     */
    public function initialize()
    {
        $this->sendInitializedEvent();
    }

    /**
     * (non-PHPdoc)
     * @see \Sescandell\ContextRestrictorBundle\Storage\ContextRestrictorStorageInterface::hasActiveContext()
     */
    public function hasActiveContext()
    {
        return !is_null($this->context);
    }

    /**
     * (non-PHPdoc)
     * @see \Sescandell\ContextRestrictorBundle\Storage\ContextRestrictorStorageInterface::setActiveContext()
     */
    public function setActiveContext($context)
    {
        $oldValue = $this->doGetActiveContext();
        $this->context = $context;

        $this->sendChangedEvent($oldValue);
    }

    /**
     * (non-PHPdoc)
     * @see \Sescandell\ContextRestrictorBundle\Storage\ContextRestrictorStorageInterface::getActiveContext()
     */
    public function getActiveContext()
    {
       if (!$this->hasActiveContext()) {
           throw new NoActiveContextFoundException();
       }

       return $this->doGetActiveContext();
    }

    /**
     * (non-PHPdoc)
     * @see \Sescandell\ContextRestrictorBundle\Storage\ContextRestrictorStorageInterface::clearActiveContext()
     */
    public function clearActiveContext()
    {
        $oldValue = $this->doGetActiveContext();
        $this->context = null;

        $this->sendChangedEvent($oldValue);
    }

    /**
     * Returns active context if any, null otherwise.
     *
     * @return mixed
     */
    protected function doGetActiveContext()
    {
        return $this->context;
    }

    /**
     * Create a ContextRestrictorChangedEvent event
     *
     * @param mixed $oldValue
     */
    protected function sendChangedEvent($oldValue)
    {
        $event = new ContextRestrictorChangedEvent($oldValue, $this->doGetActiveContext());

        $this->eventDispatcher->dispatch(ContextRestrictorStorageEvents::CONTEXT_CHANGED, $event);
    }

    /**
     *
     */
    protected function sendInitializedEvent()
    {
        $event = new ContextRestrictorInitializedEvent($this->doGetActiveContext());

        $this->eventDispatcher->dispatch(ContextRestrictorStorageEvents::CONTEXT_INITIALIZED, $event);
    }
}
