<?php

namespace Symfony\Component\Security\Http\Authentication;

use Symfony\Component\HttpFoundation\Request;

/**
 * Builds the attributes to be set on fresh tokens.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface TokenAttributeSourceInterface
{
    /**
     * Builds attributes and returns them.
     *
     * @param Request $request
     * @return array
     */
    function buildAttributes(Request $request);
}