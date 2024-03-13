<?php

namespace Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use App\Classes\Expression;
use PHPUnit\Framework\TestCase;

#[CoversClass(Expression::class)]
class ExpressionTest extends TestCase
{
    public function testRemoveInvalidParenthesisWithComplexExpression()
    {
        $this->assertEquals(
            "1*(2+3*(4+5))",
            (new Expression())->removeUnnecessaryParentheses("1*(2+(3*(4+5)))")
        );
    }

    public function testRemoveInvalidParenthesisWithDivision()
    {
        $this->assertEquals(
            "2 + 3 / -5",
            (new Expression())->removeUnnecessaryParentheses("2 + (3 / -5)")
        );
    }

    public function testRemoveInvalidParenthesisWithVariables()
    {
        $this->assertEquals(
            "x+y+z+t+v+w",
            (new Expression())->removeUnnecessaryParentheses("x+(y+z)+(t+(v+w))")
        );
    }

    public function testRemoveInvalidParenthesisWithOtherCase()
    {
        $expression = new Expression();

        $this->assertEquals(
            "1+2",
            $expression->removeUnnecessaryParentheses("(1+(2))")
        );

        $this->assertEquals(
            "2-(2+3)",
            $expression->removeUnnecessaryParentheses("2-(2+3)")
        );

        $this->assertEquals(
            "2-(2+3)",
            $expression->removeUnnecessaryParentheses("2-(2+3)")
        );

        $this->assertEquals(
            "-2-(2+3)",
            $expression->removeUnnecessaryParentheses("-(2)-(2+3)")
        );

        $this->assertEquals(
            "-(2+3)",
            $expression->removeUnnecessaryParentheses("-(2+3)")
        );

        $this->assertEquals(
            "2*3/5",
            $expression->removeUnnecessaryParentheses("2*(3/5)")
        );

        $this->assertEquals(
            "2/(3/5)",
            $expression->removeUnnecessaryParentheses("2/(3/5)")
        );

        $this->assertEquals(
            "2/3",
            $expression->removeUnnecessaryParentheses("2/(3)")
        );

        $this->assertEquals(
            "5/6",
            $expression->removeUnnecessaryParentheses("(5)/(6)")
        );

        $this->assertEquals(
            "-5/7",
            $expression->removeUnnecessaryParentheses("(-5)/7")
        );

        $this->assertEquals(
            "-5*7",
            $expression->removeUnnecessaryParentheses("(-5)*7")
        );

        $this->assertEquals(
            "5*-3",
            $expression->removeUnnecessaryParentheses("5*(-3)")
        );

        $this->assertEquals(
            "(2 + 2) * 1",
            $expression->removeUnnecessaryParentheses("(2 + 2) * 1")
        );

        $this->assertEquals(
            "1+(-1)",
            $expression->removeUnnecessaryParentheses("1+(-1)")
        );

        $this->assertEquals(
            "2*(2+3-4*6)+8+7*4",
            $expression->removeUnnecessaryParentheses("((2*((2+3)-(4*6))+(8+(7*4))))")
        );

        $this->assertEquals(
            "2*(2*3-(4+6))+8+7*4",
            $expression->removeUnnecessaryParentheses("((2*((2*3)-(4+6))+(8+(7*4))))")
        );
    }
}
