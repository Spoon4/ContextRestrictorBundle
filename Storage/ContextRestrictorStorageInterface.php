<?php
namespace Sescandell\ContextRestrictorBundle\Storage;

/**
 * @author Stéphane Escandell
 */
interface ContextRestrictorStorageInterface
{
    /**
     * Check if a context is already registered.
     * Returns true if any, false otherwise
     *
     * @return boolean
     */
    public function hasActiveContext();

    /**
     * Register $context as active context.
     *
     * @param mixed $context
     */
    public function setActiveContext($context);

    /**
     * Get registered $context
     *
     * @return mixed
     * @throws \Sescandell\ContextRestrictorBundle\Exception\NoActiveContextFoundException
     */
    public function getActiveContext();

    /**
     * Remove active context
     */
    public function clearActiveContext();
}
