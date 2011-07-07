<?php

namespace Symfony\Component\Security\Authentication;

use Symfony\Component\HttpFoundation\Request;

/**
 * Default TokenAttributeSource implementation.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class TokenAttributeSource implements TokenAttributeSourceInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildAttributes(Request $request)
    {
        return array(
            'ip' => $request->getClientIp(),
            'session_id' => $request->hasSession() ? $request->getSession()->getId() : null,
        );
    }
}