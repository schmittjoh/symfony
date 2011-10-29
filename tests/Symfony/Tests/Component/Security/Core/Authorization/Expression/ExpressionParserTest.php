<?php

namespace Symfony\Tests\Component\Security\Core\Authorization\Expression;

use Symfony\Component\Security\Core\Authorization\Expression\Ast\IsEqualExpression;

use Symfony\Component\Security\Core\Authorization\Expression\Ast\ParameterExpression;
use Symfony\Component\Security\Core\Authorization\Expression\Ast\ConstantExpression;
use Symfony\Component\Security\Core\Authorization\Expression\Ast\GetItemExpression;
use Symfony\Component\Security\Core\Authorization\Expression\Ast\ArrayExpression;
use Symfony\Component\Security\Core\Authorization\Expression\Ast\MethodCallExpression;
use Symfony\Component\Security\Core\Authorization\Expression\Ast\GetPropertyExpression;
use Symfony\Component\Security\Core\Authorization\Expression\Ast\VariableExpression;
use Symfony\Component\Security\Core\Authorization\Expression\Ast\OrExpression;
use Symfony\Component\Security\Core\Authorization\Expression\Ast\AndExpression;
use Symfony\Component\Security\Core\Authorization\Expression\Ast\FunctionExpression;
use Symfony\Component\Security\Core\Authorization\Expression\ExpressionParser;

class ExpressionParserTest extends \PHPUnit_Framework_TestCase
{
    private $parser;

    public function testSingleFunction()
    {
        $this->assertEquals(new FunctionExpression('isAnonymous', array()),
            $this->parser->parse('isAnonymous()'));
    }

    public function testSingleFunctionWithOneArgument()
    {
        $this->assertEquals(new FunctionExpression('hasRole', array(
        	new ConstantExpression('ROLE_ADMIN'))),
            $this->parser->parse('hasRole("ROLE_ADMIN")'));
    }

    public function testSingleFunctionWithMultipleArguments()
    {
        $this->assertEquals(new FunctionExpression('hasAnyRole', array(
            new ConstantExpression('FOO'), new ConstantExpression('BAR'))),
            $this->parser->parse('hasAnyRole("FOO", "BAR",)'));
    }

    public function testComplexFunctionExpression()
    {
        $expected = new OrExpression(new FunctionExpression('hasRole', array(
            new ConstantExpression('ADMIN'))),
            new FunctionExpression('hasAnyRole', array(new ConstantExpression('FOO'),
            new ConstantExpression('BAR'))));

        $this->assertEquals($expected, $this->parser->parse('hasRole("ADMIN") or hasAnyRole("FOO", "BAR")'));
    }

    public function testAnd()
    {
        $expected = new AndExpression(
            new FunctionExpression('isAnonymous', array()),
            new FunctionExpression('hasRole', array(new ConstantExpression('FOO'))));

        $this->assertEquals($expected, $this->parser->parse('isAnonymous() && hasRole("FOO")'));
        $this->assertEquals($expected, $this->parser->parse('isAnonymous() and hasRole("FOO")'));
    }

    public function testPrecendence()
    {
        $expected = new OrExpression(
            new AndExpression(new VariableExpression('A'), new VariableExpression('B')),
            new VariableExpression('C')
        );
        $this->assertEquals($expected, $this->parser->parse('A && B || C'));
        $this->assertEquals($expected, $this->parser->parse('(A && B) || C'));

        $expected = new OrExpression(
            new VariableExpression('C'),
            new AndExpression(new VariableExpression('A'), new VariableExpression('B'))
        );
        $this->assertEquals($expected, $this->parser->parse('C || A && B'));
        $this->assertEquals($expected, $this->parser->parse('C || (A && B)'));

        $expected = new AndExpression(
            new AndExpression(new VariableExpression('A'), new VariableExpression('B')),
            new VariableExpression('C')
        );
        $this->assertEquals($expected, $this->parser->parse('A && B && C'));
    }

    public function testGetProperty()
    {
        $expected = new GetPropertyExpression(new VariableExpression('A'), 'foo');
        $this->assertEquals($expected, $this->parser->parse('A.foo'));
    }

    public function testMethodCall()
    {
        $expected = new MethodCallExpression(new VariableExpression('A'), 'foo', array());
        $this->assertEquals($expected, $this->parser->parse('A.foo()'));
    }

    public function testArray()
    {
        $expected = new ArrayExpression(array(
            'foo' => new ConstantExpression('bar'),
        ));
        $this->assertEquals($expected, $this->parser->parse('{"foo":"bar",}'));
        $this->assertEquals($expected, $this->parser->parse('{"foo":"bar"}'));

        $expected = new ArrayExpression(array(
            new ConstantExpression('foo'),
            new ConstantExpression('bar'),
        ));
        $this->assertEquals($expected, $this->parser->parse('["foo","bar",]'));
        $this->assertEquals($expected, $this->parser->parse('["foo","bar"]'));
    }

    public function testGetItem()
    {
        $expected = new GetItemExpression(
            new GetPropertyExpression(new VariableExpression('A'), 'foo'),
            new ConstantExpression('foo')
        );
        $this->assertEquals($expected, $this->parser->parse('A.foo["foo"]'));
    }

    public function testParameter()
    {
        $expected = new ParameterExpression('contact');
        $this->assertEquals($expected, $this->parser->parse('#contact'));
    }

    public function testIsEqual()
    {
        $expected = new IsEqualExpression(new MethodCallExpression(
            new VariableExpression('user'), 'getUsername', array()),
            new ConstantExpression('Johannes'));
        $this->assertEquals($expected, $this->parser->parse('user.getUsername() == "Johannes"'));
    }

    protected function setUp()
    {
        $this->parser = new ExpressionParser;
    }
}