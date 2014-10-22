<?php

namespace Sescandell\ContextRestrictorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Stéphane Escandell
 */
class SescandellContextRestrictorBundle extends Bundle
{
    /**
     * @return \Sescandell\ContextRestrictorBundle\DependencyInjection\SescandellContextRestrictorExtension
     */
    public function getContainerExtension()
    {
        $this->extension = new DependencyInjection\SescandellContextRestrictorExtension();

        return $this->extension;
    }
}
