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
     * @var array
     */
    protected $targetRestrictions = array();

    /**
     * @param array $restrictions
     * @param string $filterName
     * @param string $enabled
     */
    public function __construct(array $restrictions, $filterName, $enabled = true)
    {
        $this->targetRestrictions = $restrictions;
        $this->filterName = $filterName;
        $this->enabled = $enabled;
    }

    /**
     *
     * @param RegistryInterface $doctrine
     * @param string $filterName
     */
    public function setRegistry(RegistryInterface $registry)
    {
        // TODO: currently, work only with Doctrine registry
        $this->registry = $registry;

        if ($this->enabled) {
            $this->configureFilter();
        }
    }

    /**
     *
     * @param DoctrineListener $listener
     */
    public function setRegistryListener(RegistryListenerInterface $listener)
    {
        $this->registryListener = $listener;

        if ($this->enabled) {
            $this->registryListener->enable();
        }
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
        if ($this->enabled) {
            $this->configureFilter();

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
            $this->configureFilter();

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
     * Configure the filter with filtered value and
     * targeted class.
     */
    protected function configureFilter()
    {
        $filter = $this->registry->getManagerForClass($this->targetRestrictions['targetClass'])->getFilters()->getFilter($this->filterName);
        $filter->setRestrictedValue($this->value);
        $filter->configure($this->targetRestrictions);
    }
}
