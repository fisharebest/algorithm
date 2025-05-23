<?php

namespace Fisharebest\Algorithm;

/**
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2021 Greg Roach <greg@subaqua.co.uk>
 * @license   GPL-3.0+
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses>.
 */

/**
 * Class MyersDiff - find the shortest edit sequence to transform one string into another.
 *
 * Based on "An O(ND) Difference Algorithm and Its Variations" by Eugene W Myers.
 *
 * http://www.xmailserver.org/diff2.pdf
 * http://www.codeproject.com/Articles/42279/Investigating-Myers-diff-algorithm-Part-of
 */
class MyersDiff
{
    /** Instruction to delete a token which only appears in the first sequence */
    const DELETE = -1;

    /** Instruction to keep a token which is common to both sequences */
    const KEEP = 0;

    /** Instruction to insert a token which only appears in the last sequence */
    const INSERT = 1;

    /**
     * Backtrack through the intermediate results to extract the "snakes" that
     * are visited on the chosen "D-path".
     *
     * @param string[] $v_save Intermediate results
     * @param int      $x      End position
     * @param int      $y      End position
     *
     * @return list<array{int, int}>
     */
    private function extractSnakes(array $v_save, $x, $y)
    {
        $snakes = array();
        for ($d = count($v_save) - 1; $x >= 0 && $y >= 0; $d--) {
            array_unshift($snakes, array($x, $y));

            $v = $v_save[$d];
            $k = $x - $y;

            if ($k === -$d || $k !== $d && $v[$k - 1] < $v[$k + 1]) {
                $k_prev = $k + 1;
            } else {
                $k_prev = $k - 1;
            }

            $x = $v[$k_prev];
            $y = $x - $k_prev;
        }

        return $snakes;
    }

    /**
     * Convert a list of "snakes" into a set of insert/keep/delete instructions.
     *
     * @template T
     *
     * @param list<array{int, int}> $snakes Common subsequences
     * @param list<T>               $a      First sequence
     * @param list<T>               $b      Second sequence
     *
     * @return list<array{T, -1|0|1}> - pairs of token and edit (-1 for delete, 0 for keep, +1 for insert)
     */
    private function formatSolution(array $snakes, array $a, array $b)
    {
        $solution = array();
        $x = 0;
        $y = 0;
        foreach ($snakes as $snake) {
            // Horizontals
            while ($snake[0] - $snake[1] > $x - $y) {
                $solution[] = array($a[$x], self::DELETE);
                $x++;
            }
            // Verticals
            while ($snake[0] - $snake[1] < $x - $y) {
                $solution[] = array($b[$y], self::INSERT);
                $y++;
            }
            // Diagonals
            while ($x < $snake[0]) {
                $solution[] = array($a[$x], self::KEEP);
                $x++;
                $y++;
            }
        }

        return $solution;
    }

    /**
     * Calculate the shortest edit sequence to convert $x into $y.
     *
     * @template T
     *
     * @param list<T> $a - List of values to compare.
     * @param list<T> $b - List of values to compare.
     * @param (callable(T, T): bool)|null $compare - comparison function for tokens, or NULL to use === comparison.
     *
     * @return list<array{T, -1|0|1}> - pairs of token and edit (-1 for delete, 0 for keep, +1 for insert)
     */
    public function calculate(array $a, array $b, $compare = null)
    {
        if ($compare === null) {
            $compare = function ($x, $y) {
                return $x === $y;
            };
        }

        // The algorithm uses array keys numbered from zero.
        $n = count($a);
        $m = count($b);
        $a = array_values($a);
        $b = array_values($b);
        $max = $m + $n;

        // Keep a copy of $v after each iteration of $d.
        $v_save = array();

        // Find the shortest "D-path".
        $v = array(1 => 0);
        for ($d = 0; $d <= $max; $d++) {
            // Examine all possible "K-lines" for this "D-path".
            for ($k = -$d; $k <= $d; $k += 2) {
                if ($k === -$d || $k !== $d && $v[$k - 1] < $v[$k + 1]) {
                    // Move down.
                    $x = $v[$k + 1];
                } else {
                    // Move right.
                    $x = $v[$k - 1] + 1;
                }
                // Derive Y from X.
                $y = $x - $k;
                // Follow the diagonal.
                while ($x < $n && $y < $m && $compare($a[$x], $b[$y])) {
                    $x++;
                    $y++;
                }
                // Just store X, as we can calculate Y (from X + K).
                $v[$k] = $x;
                $v_save[$d] = $v;
                // Solution found?
                if ($x === $n && $y === $m) {
                    break 2;
                }
            }
        }

        // Extract the solution by back-tracking through the saved results.
        $snakes = $this->extractSnakes($v_save, $n, $m);

        // Format the snakes as a set of instructions.
        return $this->formatSolution($snakes, $a, $b);
    }
}
