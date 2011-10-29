<?php

namespace Symfony\Component\Security\Core\Authorization\Expression;

final class Expression
{
    /** READ-ONLY */
    public $expression;

    public function __construct($expression)
    {
        $this->expression = $expression;
    }

    public function getHashCode()
    {
        return sha1($this->expression);
    }

    public function __toString()
    {
        return 'EXPRESSION('.$this->expression.')';
    }
}