<?php

namespace App\Classes;

class Expression
{
    /**
     * Remove unnecessary parentheses from the given expression.
     *
     * @param string $expression
     * @return string
     */
    public function removeUnnecessaryParentheses(string $expression): string
    {
        $expression = $this->removeParenthesesWithNumbers($expression);
        foreach (['*', '/', '+', '-'] as $value) {
            $shift = 0;
            $found = false;
            $centers = $this->findMultiplicationPositions($expression, $value);
            foreach ($centers as $center) {
                $center -= $shift;
                $left = $this->findLeftBracketPosition($expression, $center);
                $right = $this->findRightBracketPosition($expression, $center);
                if (($left !== null) && ($right !== null)) {
                    // Check if the left bracket is preceded by '/' or '-'
                    if ($left > 0 && in_array($expression[$left - 1], ['/', '-'])) {
                        continue;
                    }
                    $expression = $this->removeCharAtPosition($expression, $right);
                    $expression = $this->removeCharAtPosition($expression, $left);
                    $shift += 2;
                    $found = true;
                }
            }
            // Break the loop if the most prioritized operator is found.
            if ($found) {
                break;
            }
        }
        return $expression;
    }

    /**
     * Find positions of multiplication operations in the given string.
     *
     * @param string $str
     * @param string $target
     * @return array
     */
    private function findMultiplicationPositions(string $str, string $target = '*'): array
    {
        $positions = [];
        $startPosition = 0;

        while (($position = strpos($str, $target, $startPosition)) !== false) {
            $positions[] = $position;
            $startPosition = $position + 1;
        }

        return $positions;
    }

    /**
     * Find the position of the left bracket at the specified position in the expression.
     *
     * @param string $expression
     * @param int $position
     * @return int|null
     */
    private function findLeftBracketPosition(string $expression, int $position): ?int
    {
        $stack = [];
        for ($i = $position; $i >= 0; $i--) {
            if ($expression[$i] == ')') {
                array_push($stack, $i);
            } elseif ($expression[$i] == '(') {
                if (empty($stack)) {
                    return $i;
                }
                array_pop($stack);
            }
        }
        return null;
    }

    /**
     * Find the position of the right bracket at the specified position in the expression.
     *
     * @param string $expression
     * @param int $position
     * @return int|null
     */
    private function findRightBracketPosition(string $expression, int $position): ?int
    {
        $stack = [];
        for ($i = $position; $i < strlen($expression); $i++) {
            if ($expression[$i] == '(') {
                array_push($stack, $i);
            } elseif ($expression[$i] == ')') {
                if (empty($stack)) {
                    return $i;
                }
                array_pop($stack);
            }
        }
        return null;
    }

    /**
     * Remove a character at the specified position in the expression.
     *
     * @param string $expression
     * @param int $position
     * @return string
     */
    private function removeCharAtPosition(string $expression, int $position): string
    {
        if ($position < 0 || $position >= strlen($expression)) {
            return $expression;
        }

        $leftPart = substr($expression, 0, $position);
        $rightPart = substr($expression, $position + 1);

        return $leftPart . $rightPart;
    }

    /**
     * Remove parentheses enclosing numbers or letters from the expression.
     * 
     * @param string $expression
     * @return string
     */
    private function removeParenthesesWithNumbers(string $expression): string
    {
        $shift = 0;
        $matches = [];
        // Find all substrings matching the pattern (letters or numbers inside parentheses)
        preg_match_all('/\([a-zA-Z0-9]+\)/', $expression, $matches, PREG_OFFSET_CAPTURE);
        foreach (current($matches) as $value) {
            $value[1] = $value[1] - $shift;
            $expression = $this->removeCharAtPosition($expression, strlen($value[0]) + $value[1] - 1);
            $expression = $this->removeCharAtPosition($expression, $value[1]);
            $shift = $shift + 2;
        }
        return $expression;
    }
}
