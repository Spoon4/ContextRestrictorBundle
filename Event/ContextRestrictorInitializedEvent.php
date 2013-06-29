<?php
namespace Sescandell\ContextRestrictorBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Stéphane Escandell
 */
class ContextRestrictorInitializedEvent extends Event
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
