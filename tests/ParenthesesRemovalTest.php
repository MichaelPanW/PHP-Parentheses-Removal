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
}
