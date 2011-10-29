<?php

namespace Symfony\Tests\Component\Security\Core\Authorization\Expression;

use Symfony\Component\Security\Core\Authorization\Expression\ExpressionLexer;

class ExpressionLexerTest extends \PHPUnit_Framework_TestCase
{
    private $lexer;

    public function testParameter()
    {
        $this->lexer->initialize('#contact');

        $this->assertEquals(array(
            'type' =>  ExpressionLexer::T_PARAMETER,
            'value' => 'contact',
            'position' => 0,
        ), $this->lexer->lookahead);
        $this->assertFalse($this->lexer->next());
    }

    protected function setUp()
    {
        $this->lexer = new ExpressionLexer();
    }
}