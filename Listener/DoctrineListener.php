<?php
namespace Sescandell\ContextRestrictorBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sescandell\ContextRestrictorBundle\Event\ContextRestrictorChangedEvent;
use Sescandell\ContextRestrictorBundle\Event\ContextRestrictorInitializedEvent;

/**
 * @author Stéphane Escandell
 * @TODO:
 *  > Manage cache
 *  > Manage multy entities
 *
 */
class DoctrineListener implements RegistryListenerInterface
{
    /**
     * @var boolean
     */
    protected $enabled = true;

    /**
     * @var mixed
     */
    protected $value = null;

    /**
     * @var string
     */
    protected $targetEntity = null;

    /**
     * @param string $restrictedEntityName
     */
    public function __construct($targetEntity)
    {
        $this->targetEntity = $targetEntity;
    }

    /**
     * Enable listener
     *
     * @return \Sescandell\ContextRestrictorBundle\Listener\RestrictedListener
     */
    public function enable()
    {
        $this->enabled = true;

        return $this;
    }

    /**
     * Disable listener
     *
     * @return \Sescandell\ContextRestrictorBundle\Listener\RestrictedListener
     */
    public function disable()
    {
        $this->enabled = false;

        return $this;
    }

    /**
     * onChangeHandler
     *
     * @param ContextRestrictorChangedEvent $event
     */
    public function onContextRestrictorChange(ContextRestrictorChangedEvent $event)
    {
        $this->value = $event->getNewValue();
    }

    /**
     * onInitializedHandler
     *
     * @param ContextRestrictorInitializedEvent $event
     */
    public function onContextRestrictorInitialize(ContextRestrictorInitializedEvent $event)
    {
        $this->value = $event->getValue();
    }

    /**
     * onPrePersistHandler
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        if (!$this->enabled || is_null($this->value)) {
            return;
        }

        $entity = $event->getEntity();
        $classMetadata = $event->getEntityManager()->getClassMetadata(get_class($entity));

        foreach ($classMetadata->associationMappings as $am) {
            if ($this->targetEntity === $am['targetEntity']
                && in_array($am['type'], array(ClassMetadata::ONE_TO_ONE, ClassMetadata::MANY_TO_ONE))) {
                $setter = 'set'.ucfirst($am['fieldName']);
                $entity->{$setter}($event->getEntityManager()->getReference($am['targetEntity'], $this->value));

                return;
            }
        }
    }
}
