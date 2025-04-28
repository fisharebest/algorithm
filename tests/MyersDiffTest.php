<?php

/**
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2025 Greg Roach <greg@subaqua.co.uk>
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

namespace Fisharebest\Tests\Algorithm;

use Fisharebest\Algorithm\MyersDiff;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MyersDiff::class)]
class MyersDiffTest extends TestCase
{
    /**
     * Test empty sequences.
     */
    public function testBothEmpty(): void
    {
        $algorithm = new MyersDiff();
        $x = array();
        $y = array();
        $diff = array();

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test one empty sequence.
     */
    public function testFirstEmpty(): void
    {
        $algorithm = new MyersDiff();
        $x = array();
        $y = array('a', 'b', 'c');
        $diff = array(
            array('a', MyersDiff::INSERT),
            array('b', MyersDiff::INSERT),
            array('c', MyersDiff::INSERT),
        );

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test one empty sequence.
     */
    public function testSecondEmpty(): void
    {
        $algorithm = new MyersDiff();
        $x = array('a', 'b', 'c');
        $y = array();
        $diff = array(
            array('a', MyersDiff::DELETE),
            array('b', MyersDiff::DELETE),
            array('c', MyersDiff::DELETE),
        );

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test identical sequences containing one token.
     */
    public function testIdenticalOne(): void
    {
        $algorithm = new MyersDiff();
        $x = array('a');
        $y = array('a');
        $diff = array(
            array('a', MyersDiff::KEEP),
        );

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test identical sequences containing two tokens.
     */
    public function testIdenticalTwo(): void
    {
        $algorithm = new MyersDiff();
        $x = array('a', 'b');
        $y = array('a', 'b');
        $diff = array(
            array('a', MyersDiff::KEEP),
            array('b', MyersDiff::KEEP),
        );

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test identical sequences containing three tokens.
     */
    public function testIdenticalThree(): void
    {
        $algorithm = new MyersDiff();
        $x = array('a', 'b', 'c');
        $y = array('a', 'b', 'c');
        $diff = array(
            array('a', MyersDiff::KEEP),
            array('b', MyersDiff::KEEP),
            array('c', MyersDiff::KEEP),
        );

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test different strings containing one token.
     */
    public function testSingleDifferentONe(): void
    {
        $algorithm = new MyersDiff();
        $x = array('a');
        $y = array('x');
        $diff = array(
            array('a', MyersDiff::DELETE),
            array('x', MyersDiff::INSERT),
        );

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test different strings containing two token.
     */
    public function testSingleDifferentTwo(): void
    {
        $algorithm = new MyersDiff();
        $x = array('a', 'b');
        $y = array('x', 'y');
        $diff = array(
            array('a', MyersDiff::DELETE),
            array('b', MyersDiff::DELETE),
            array('x', MyersDiff::INSERT),
            array('y', MyersDiff::INSERT),
        );

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test different strings containing three token.
     */
    public function testSingleDifferentThree(): void
    {
        $algorithm = new MyersDiff();
        $x = array('a', 'b', 'c');
        $y = array('x', 'y', 'z');
        $diff = array(
            array('a', MyersDiff::DELETE),
            array('b', MyersDiff::DELETE),
            array('c', MyersDiff::DELETE),
            array('x', MyersDiff::INSERT),
            array('y', MyersDiff::INSERT),
            array('z', MyersDiff::INSERT),
        );

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test two non-empty sequences.
     */
    public function testBothNonEmpty(): void
    {
        $algorithm = new MyersDiff();
        $x = array('a', 'b', 'c', 'a', 'b', 'b', 'a');
        $y = array('c', 'b', 'a', 'b', 'a', 'c');
        $diff = array(
            array('a', MyersDiff::DELETE),
            array('b', MyersDiff::DELETE),
            array('c', MyersDiff::KEEP),
            array('b', MyersDiff::INSERT),
            array('a', MyersDiff::KEEP),
            array('b', MyersDiff::KEEP),
            array('b', MyersDiff::DELETE),
            array('a', MyersDiff::KEEP),
            array('c', MyersDiff::INSERT),
        );

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test delete-before-insert.  Delete/insert gives the same
     * result as insert/delete.  Our algorithm consistently
     * deletes first.
     */
    public function testDeleteBeforeInsert(): void
    {
        $algorithm = new MyersDiff();
        $x = array('a', 'b', 'c');
        $y = array('a', 'd', 'c');
        $diff = array(
            array('a', MyersDiff::KEEP),
            array('b', MyersDiff::DELETE),
            array('d', MyersDiff::INSERT),
            array('c', MyersDiff::KEEP),
        );

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test custom token comparison.
     */
    public function testCustomCompare(): void
    {
        $algorithm = new MyersDiff();
        $ignorecase = function ($x, $y) {
            return strtolower($x) === strtolower($y);
        };
        $x = array('a', 'b', 'c');
        $y = array('A', 'B', 'C');
        $diff = array(
            array('a', MyersDiff::KEEP),
            array('b', MyersDiff::KEEP),
            array('c', MyersDiff::KEEP),
        );

        $this->assertSame($diff, $algorithm->calculate($x, $y, $ignorecase));
    }
}
