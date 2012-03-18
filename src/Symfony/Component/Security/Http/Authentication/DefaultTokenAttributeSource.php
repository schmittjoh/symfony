<?php

namespace Symfony\Component\Security\Http\Authentication;

use Symfony\Component\HttpFoundation\Request;

/**
 * Default TokenAttributeSource implementation.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class DefaultTokenAttributeSource implements TokenAttributeSourceInterface
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