<?php
namespace Sescandell\ContextRestrictorBundle\Manager;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Sescandell\ContextRestrictorBundle\Event\ContextRestrictorChangedEvent;
use Sescandell\ContextRestrictorBundle\Event\ContextRestrictorInitializedEvent;
use Sescandell\ContextRestrictorBundle\Listener\RegistryListenerInterface;

/**
 * @author Stéphane Escandell
 */
class ContextRestrictorManager
{
    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @var string
     */
    protected $filterName;

    /**
     * @var mixed
     */
    protected $value = null;

    /**
     * @var boolean
     */
    protected $enabled = true;

    /**
     * @var RegistryListenerInterface
     */
    protected $registryListener;

    /**
     *
     * @param RegistryInterface $doctrine
     * @param string $filterName
     */
    public function setRegistry(RegistryInterface $registry, $filterName)
    {
        // TODO: currently, work only with Doctrine registry
        $this->registry = $registry;
        $this->filterName = $filterName;
    }

    /**
     *
     * @param DoctrineListener $listener
     */
    public function setRegistryListener(RegistryListenerInterface $listener)
    {
        $this->registryListener = $listener;
    }

    /**
     * onChangeHandler
     *
     * @param ContextRestrictorChangedEvent $event
     * @throws \InvalidArgumentException
     */
    public function onContextRestrictorChange(ContextRestrictorChangedEvent $event)
    {
        $this->setValue($event->getNewValue());
    }

    /**
     * onInitializedHandler
     *
     * @param ContextRestrictorInitializedEvent $event
     */
    public function onContextRestrictorInitialize(ContextRestrictorInitializedEvent $event)
    {
        $this->setValue($event->getValue());
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function setValue($value)
    {
        $this->value = $value;
        // Pourquoi ne pas se baser sur $this->enabled ????
        // Il y a certainement une raison... mais je ne sais plus laquelle
        // ou pas d'ailleurs... :D
        if ($this->isFilterEnabled()) {
            $this->registry->getManager()->getFilters()->getFilter($this->filterName)->setRestrictedValue($this->value);

            return true;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function enable()
    {
        if (!$this->enabled) {
            $this->registry->getManager()->getFilters()->enable($this->filterName)->setRestrictedValue($this->value);
            $this->registryListener->enable();
            $this->enabled = true;

            return true;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function disable()
    {
        if ($this->enabled) {
            $this->registry->getManager()->getFilters()->disable($this->filterName);
            $this->registryListener->disable();
            $this->enabled = false;

            return true;
        }

        return false;
    }

    /**
     * Check if registry filter is enabled.
     * Returns true if enabled, false otherwise.
     *
     * @return boolean
     */
    protected function isFilterEnabled()
    {
        return in_array($this->filterName, array_keys($this->registry->getManager()->getFilters()->getEnabledFilters()));
    }
}
