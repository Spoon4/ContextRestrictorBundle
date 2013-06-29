<?php
namespace Sescandell\ContextRestrictorBundle\Listener;

/**
 * @author Stéphane Escandell
 */
interface RegistryListenerInterface
{
    /**
     * Enable listener
     */
    public function enable();

    /**
     * Disable listener
     */
    public function disable();
}
