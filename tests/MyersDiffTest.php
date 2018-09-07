<?php

namespace Fisharebest\Algorithm;

/**
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2015 Greg Roach <greg@subaqua.co.uk>
 * @license   GPL-3.0+
 *s
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
class MyersDiffTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test empty sequences.
     *
     * @return void
     */
    public function testBothEmpty()
    {
        $algorithm = new MyersDiff();
        $x = [];
        $y = [];
        $diff = [];

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test one empty sequence.
     *
     * @return void
     */
    public function testFirstEmpty()
    {
        $algorithm = new MyersDiff();
        $x = [];
        $y = ['a', 'b', 'c'];
        $diff = [
            ['a', MyersDiff::INSERT],
            ['b', MyersDiff::INSERT],
            ['c', MyersDiff::INSERT],
        ];

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test one empty sequence.
     *
     * @return void
     */
    public function testSecondEmpty()
    {
        $algorithm = new MyersDiff();
        $x = ['a', 'b', 'c'];
        $y = [];
        $diff = [
            ['a', MyersDiff::DELETE],
            ['b', MyersDiff::DELETE],
            ['c', MyersDiff::DELETE],
        ];

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test identical sequences containing one token.
     *
     * @return void
     */
    public function testIdenticalOne()
    {
        $algorithm = new MyersDiff();
        $x = ['a'];
        $y = ['a'];
        $diff = [
            ['a', MyersDiff::KEEP],
        ];

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test identical sequences containing two tokens.
     *
     * @return void
     */
    public function testIdenticalTwo()
    {
        $algorithm = new MyersDiff();
        $x = ['a', 'b'];
        $y = ['a', 'b'];
        $diff = [
            ['a', MyersDiff::KEEP],
            ['b', MyersDiff::KEEP],
        ];

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test identical sequences containing three tokens.
     *
     * @return void
     */
    public function testIdenticalThree()
    {
        $algorithm = new MyersDiff();
        $x = ['a', 'b', 'c'];
        $y = ['a', 'b', 'c'];
        $diff = [
            ['a', MyersDiff::KEEP],
            ['b', MyersDiff::KEEP],
            ['c', MyersDiff::KEEP],
        ];

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test different strings containing one token.
     *
     * @return void
     */
    public function testSingleDifferentONe()
    {
        $algorithm = new MyersDiff();
        $x = ['a'];
        $y = ['x'];
        $diff = [
            ['a', MyersDiff::DELETE],
            ['x', MyersDiff::INSERT],
        ];

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test different strings containing two token.
     *
     * @return void
     */
    public function testSingleDifferentTwo()
    {
        $algorithm = new MyersDiff();
        $x = ['a', 'b'];
        $y = ['x', 'y'];
        $diff = [
            ['a', MyersDiff::DELETE],
            ['b', MyersDiff::DELETE],
            ['x', MyersDiff::INSERT],
            ['y', MyersDiff::INSERT],
        ];

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test different strings containing three token.
     *
     * @return void
     */
    public function testSingleDifferentThree()
    {
        $algorithm = new MyersDiff();
        $x = ['a', 'b', 'c'];
        $y = ['x', 'y', 'z'];
        $diff = [
            ['a', MyersDiff::DELETE],
            ['b', MyersDiff::DELETE],
            ['c', MyersDiff::DELETE],
            ['x', MyersDiff::INSERT],
            ['y', MyersDiff::INSERT],
            ['z', MyersDiff::INSERT],
        ];

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test two non-empty sequences.
     *
     * @return void
     */
    public function testBothNonEmpty()
    {
        $algorithm = new MyersDiff();
        $x = ['a', 'b', 'c', 'a', 'b', 'b', 'a'];
        $y = ['c', 'b', 'a', 'b', 'a', 'c'];
        $diff = [
            ['a', MyersDiff::DELETE],
            ['b', MyersDiff::DELETE],
            ['c', MyersDiff::KEEP],
            ['b', MyersDiff::INSERT],
            ['a', MyersDiff::KEEP],
            ['b', MyersDiff::KEEP],
            ['b', MyersDiff::DELETE],
            ['a', MyersDiff::KEEP],
            ['c', MyersDiff::INSERT],
        ];

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }

    /**
     * Test delete-before-insert.  Delete/insert gives the same
     * result as insert/delete.  Our algorithm consistently
     * deletes first.
     *
     * void
     */
    public function testDeleteBeforeInsert()
    {
        $algorithm = new MyersDiff();
        $x = ['a', 'b', 'c'];
        $y = ['a', 'd', 'c'];
        $diff = [
            ['a', MyersDiff::KEEP],
            ['b', MyersDiff::DELETE],
            ['d', MyersDiff::INSERT],
            ['c', MyersDiff::KEEP],
        ];

        $this->assertSame($diff, $algorithm->calculate($x, $y));
    }
}
