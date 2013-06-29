<?php
namespace Sescandell\ContextRestrictorBundle\Storage;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Sescandell\ContextRestrictorBundle\Event\ContextRestrictorStorageEvents;
use Sescandell\ContextRestrictorBundle\Event\ContextRestrictorChangedEvent;
use Sescandell\ContextRestrictorBundle\Event\ContextRestrictorInitializedEvent;
use Sescandell\ContextRestrictorBundle\Exception\NoActiveContextFoundException;

/**
 * @author Stéphane Escandell
 */
class SessionContextRestrictorStorage implements ContextRestrictorStorageInterface
{
    const CONTEXT_KEY_NAME = 'context_restrictor.storage';

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(SessionInterface $session, EventDispatcherInterface $eventDispatcher)
    {
        $this->session = $session;
        $this->eventDispatcher = $eventDispatcher;

        $this->sendInitializedEvent();
    }

    /**
     * (non-PHPdoc)
     * @see \Sescandell\ContextRestrictorBundle\Storage\ContextRestrictorStorageInterface::hasActiveContext()
     */
    public function hasActiveContext()
    {
        return $this->session->has(self::CONTEXT_KEY_NAME);
    }

    /**
     * (non-PHPdoc)
     * @see \Sescandell\ContextRestrictorBundle\Storage\ContextRestrictorStorageInterface::setActiveContext()
     */
    public function setActiveContext($context)
    {
        $oldValue = $this->doGetActiveContext();
        $this->session->set(self::CONTEXT_KEY_NAME, $context);

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
        $this->session->remove(self::CONTEXT_KEY_NAME);

        $this->sendChangedEvent($oldValue);
    }

    /**
     * Returns active context if any, null otherwise.
     *
     * @return mixed
     */
    protected function doGetActiveContext()
    {
        return $this->session->get(self::CONTEXT_KEY_NAME, null);
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
