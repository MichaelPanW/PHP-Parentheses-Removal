<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Classes\Expression;

function useRemoveUnnecessaryParentheses(Expression $expression, string $string): void
{
    echo "Input: " . $string . PHP_EOL;
    echo "Output: " . $expression->removeUnnecessaryParentheses($string) . PHP_EOL;
}
$expression = new Expression();
useRemoveUnnecessaryParentheses($expression, "1*(2+(3*(4+5)))");
useRemoveUnnecessaryParentheses($expression, "2 + (3 / -5)");
useRemoveUnnecessaryParentheses($expression, "x+(y+z)+(t+(v+w))");
