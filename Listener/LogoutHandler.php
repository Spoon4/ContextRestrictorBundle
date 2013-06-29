<?php
namespace Sescandell\ContextRestrictorBundle\Listener;

use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Sescandell\ContextRestrictorBundle\Storage\ContextRestrictorStorageInterface;

/**
 * @author Stéphane Escandell
 */
class LogoutHandler implements LogoutHandlerInterface
{
    /**
     * @var ContextRestrictorStorageInterface
     */
    protected $contextRestrictorStorage;

    /**
     * Default constructor
     *
     * @param ContextRestrictorStorageInterface $contextRestrictorStorage
     */
    public function __construct(ContextRestrictorStorageInterface $contextRestrictorStorage)
    {
        $this->contextRestrictorStorage = $contextRestrictorStorage;
    }

    /* (non-PHPdoc)
     * @see \Symfony\Component\Security\Http\Logout\LogoutHandlerInterface::logout()
     */
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $this->contextRestrictorStorage->clearActiveContext();
    }

}
