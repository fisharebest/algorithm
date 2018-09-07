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
class ConnectedComponentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * A graph with no components.
     */
    public function testNoComponents()
    {
        $graph = [];

        $components = [];

        $algorithm = new ConnectedComponent($graph);

        $this->assertSame($components, $algorithm->findConnectedComponents());
    }

    /**
     * A graph with one component.
     *
     *    D----E
     *   / \    \
     *  /   \    \
     * A-----B---C
     *  \   /    /
     *   \ /    /
     *    F----/
     */
    public function testOneComponent()
    {
        $graph = [
            'A' => ['B' => 1, 'D' => 1, 'F' => 1],
            'B' => ['A' => 1, 'C' => 1, 'D' => 1, 'F' => 1],
            'C' => ['B' => 1, 'E' => 1, 'F' => 1],
            'D' => ['A' => 1, 'B' => 1, 'E' => 1],
            'E' => ['C' => 1, 'D' => 1],
            'F' => ['A' => 1, 'B' => 1, 'C' => 1],
        ];

        $components = [
            1 => ['A', 'B', 'C', 'D', 'E', 'F'],
        ];

        $algorithm = new ConnectedComponent($graph);

        $this->assertSame($components, $algorithm->findConnectedComponents());
    }

    /**
     * A graph with two component.
     *
     *    D    E
     *   / \    \
     *  /   \    \
     * A-----B   C
     *  \   /
     *   \ /
     *    F
     */
    public function testTwoComponent()
    {
        $graph = [
            'A' => ['B' => 1, 'D' => 1, 'F' => 1],
            'B' => ['A' => 1, 'D' => 1, 'F' => 1],
            'C' => ['E' => 1],
            'D' => ['A' => 1, 'B' => 1],
            'E' => ['C' => 1],
            'F' => ['A' => 1, 'B' => 1],
        ];

        $components = [
            1 => ['A', 'B', 'D', 'F'],
            2 => ['C', 'E'],
        ];

        $algorithm = new ConnectedComponent($graph);

        $this->assertSame($components, $algorithm->findConnectedComponents());
    }

    /**
     * A graph with two component.
     *
     * A   B
     */
    public function testUnconnected()
    {
        $graph = [
            'A' => [],
            'B' => [],
        ];

        $components = [
            1 => ['A'],
            2 => ['B'],
        ];

        $algorithm = new ConnectedComponent($graph);

        $this->assertSame($components, $algorithm->findConnectedComponents());
    }
}
