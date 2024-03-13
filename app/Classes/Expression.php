<?php

namespace App\Classes;

class Expression
{
    /**
     * Array containing the supported operators.
     * @var array
     */
    const OPERATORS = ['*', '/', '+', '-'];

    /**
     * Remove unnecessary parentheses from the given expression.
     *
     * @param string $expression
     * @return string
     */
    public function removeUnnecessaryParentheses(string $expression): string
    {
        $expression = $this->removeParenthesesWithNumbers($expression);

        foreach ($this::OPERATORS as $operatorIndex => $operator) {
            do {
                $restart = false;
                $centers = $this->findMultiplicationPositions($expression, $operator);
                foreach ($centers as $center) {
                    $operator = $this->findLowerPriorityOperator($expression, $center, $operatorIndex) ?: $operator;
                    $leftPosition = $this->findLeftBracketPosition($expression, $center);
                    $rightPosition = $this->findRightBracketPosition($expression, $center);
                    if ($this->isBracketCanRemove($expression, $leftPosition, $rightPosition, $operator)) {
                        $expression = $this->removeCharAtPosition($expression, $rightPosition);
                        $expression = $this->removeCharAtPosition($expression, $leftPosition);
                        $restart = true;
                        break;
                    }
                }
            } while ($restart);
        }

        return $expression;
    }

    /**
     * Find the previous character in the expression string.
     *
     * @param string $expression
     * @param integer $point
     * @return string|null
     */
    private function findPrevChar(string $expression, int $point): ?string
    {
        if ($point >= 0 && isset($expression[$point])) {
            if ($expression[$point] != ' ') {
                return $expression[$point];
            } else {
                return $this->findPrevChar($expression, $point - 1);
            }
        }
        return null;
    }

    /**
     * Find the next character in the expression string.
     *
     * @param string $expression
     * @param integer $point
     * @return string|null
     */
    private function findNextChar(string $expression, int $point): ?string
    {
        if (isset($expression[$point])) {
            if ($expression[$point] != ' ') {
                return $expression[$point];
            } else {
                return $this->findNextChar($expression, $point + 1);
            }
        }
        return null;
    }

    /**
     * Find the lower priority operator in the expression based on the given center.
     *
     * @param string $expression
     * @param integer $center
     * @param integer $operatorIndex
     * @return string|null
     */
    private function findLowerPriorityOperator(string $expression, int $center, int $operatorIndex): ?string
    {
        $operator = null;
        for ($dir = -1; $dir <= 1; $dir += 2) {
            for ($i = $center; $i >= 0 && $i < strlen($expression); $i += $dir) {
                if (in_array($expression[$i], ['(', ')'])) break;
                if (in_array($expression[$i], $this::OPERATORS)) {
                    if (array_search($expression[$i], $this::OPERATORS) > $operatorIndex) {
                        $operator = $expression[$i];
                    }
                }
            }
        }
        return $operator;
    }

    /**
     * Determine whether the bracket can be removed based on the surrounding characters and the operator.
     *
     * @param string $expression
     * @param integer|null $left
     * @param integer|null $right
     * @param string $operator
     * @return bool
     */
    private function isBracketCanRemove(string $expression, ?int $leftPosition, ?int $rightPosition, string $operator): bool
    {
        if ($leftPosition !== null && $rightPosition !== null) {
            $insideLeftChar = $this->findPrevChar($expression, $leftPosition + 1);
            $outsideLeftChar = $this->findPrevChar($expression, $leftPosition - 1);
            $outsideRightChar = $this->findNextChar($expression, $rightPosition + 1);
            // Check whether both ends meet the conditions for removing brackets
            if (((!$this->isOperator($outsideLeftChar)) ||
                    ($this->checkOperatorLogic($insideLeftChar, $outsideRightChar, $outsideLeftChar, $operator, true))) &&
                ((!$this->isOperator($outsideRightChar)) ||
                    ($this->checkOperatorLogic($insideLeftChar, $outsideRightChar, $outsideRightChar, $operator, false)))
            ) {
                return true;
            }
            // Check if both characters position are ends (case: '(1+2)')
            if ($outsideLeftChar == null &&  $outsideRightChar == null) {
                return true;
            }
            // Check if both characters position are brackets (case: '((1+2))')
            if ($outsideLeftChar == '(' &&  $outsideRightChar == ')') {
                return true;
            }
        }
        return false;
    }

    /**
     * Find positions of multiplication operations in the given expression.
     *
     * @param string $expression
     * @param string $operator
     * @return array
     */
    private function findMultiplicationPositions(string $expression, string $operator = '*'): array
    {
        $positions = [];
        $startPosition = 0;

        while (($position = strpos($expression, $operator, $startPosition)) !== false) {
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

    /**
     * Determine the logical result of the given pair of operators.
     *
     * @param string $operator1
     * @param string $operator2
     * @param bool $direction
     * @return bool
     */
    private function logicTable(string $operator1, string $operator2, bool $direction = true): bool
    {
        $logicTable = [
            "+" => ["+" => true, "-" => true, "*" => true, "/" => true],
            "-" => ["+" => false, "-" => false, "*" => true, "/" => true],
            "*" => ["+" => false, "-" => !$direction, "*" => true, "/" => true],
            "/" => ["+" => false, "-" => !$direction, "*" => true, "/" => false]
        ];

        return $logicTable[$operator1][$operator2] ?? false;
    }

    /**
     * Check the logical result of operator combination
     *
     * @param string $insideLeftChar
     * @param string|null $outsideRightChar
     * @param string $operator1
     * @param string $operator2
     * @param bool $direction
     * @return bool
     */
    function checkOperatorLogic(string $insideLeftChar, ?string $outsideRightChar, string $operator1, string $operator2, bool $direction): bool
    {
        if ($insideLeftChar !== '-' && $outsideRightChar !== '-') {
            return $this->logicTable($operator1, $operator2, $direction);
        } else {
            return $this->logicTable($operator2, $operator1, $direction);
        }
    }

    /**
     * Check if the given character is an operator.
     *
     * @param string|null $char
     * @return bool
     */
    function isOperator(?string $char): bool
    {
        return in_array($char, $this::OPERATORS);
    }
}
