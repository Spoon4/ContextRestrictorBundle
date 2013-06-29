<?php
namespace Sescandell\ContextRestrictorBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Stéphane Escandell
 */
class ContextRestrictorChangedEvent extends Event
{
    /**
     * @var mixed
     */
    protected $new;

    /**
     * @var mixed
     */
    protected $old;

    /**
     * @param mixed $old
     * @param mixed $new
     */
    public function __construct($old, $new)
    {
        $this->new = $new;
        $this->old = $old;
    }

    /**
     * @return mixed
     */
    public function getNewValue()
    {
        return $this->new;
    }

    /**
     * @return mixed
     */
    public function getOldValue()
    {
        return $this->old;
    }
}
