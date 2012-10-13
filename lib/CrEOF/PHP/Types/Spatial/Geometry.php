<?php
/**
 * Copyright (C) 2012 Derek J. Lambert
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace CrEOF\PHP\Types\Spatial;

use CrEOF\Exception\InvalidValueException;
use CrEOF\PHP\Types\Spatial\Geometry\LineString;
use CrEOF\PHP\Types\Spatial\Geometry\Point;
use CrEOF\PHP\Types\Spatial\Geometry\Polygon;


/**
 * Abstract geometry object for geometry type
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
abstract class Geometry
{
    const POINT      = 'POINT';
    const LINESTRING = 'LINESTRING';
    const POLYGON    = 'POLYGON';

    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @return string
     */
    public function __toString()
    {
        $type   = $this->getType();
        $method = 'toString' . $type;

        return sprintf('%s(%s)', $type, $this->$method($this));
    }

    /**
     * @param Point[] $points
     *
     * @throws InvalidValueException
     */
    protected  function validateLineStringValue(array $points)
    {
        foreach ($points as $point) {
            if ( ! ($point instanceof Point)) {
                throw InvalidValueException::invalidType('Point', $point);
            }
        }
    }

    /**
     * @param LineString[] $rings
     *
     * @throws InvalidValueException
     */
    protected  function validatePolygonValue(array $rings)
    {
        foreach ($rings as $ring) {
            if ( ! ($ring instanceof LineString)) {
                throw InvalidValueException::invalidType('LineString', $ring);
            }

            if ( ! $ring->isClosed()) {
                throw InvalidValueException::ringNotClosed($ring);
            }
        }
    }

    /**
     * @param Point $point
     *
     * @return string
     */
    private function toStringPoint(Point $point)
    {
        return sprintf('%s %s', $point->getLatitude(), $point->getLongitude());
    }

    /**
     * @param LineString $lineString
     *
     * @return null|string
     */
    private function toStringLineString(LineString $lineString)
    {
        $string = null;

        foreach ($lineString->getPoints() as $point) {
            $string .= ($string ? ',': null) . $this->toStringPoint($point);
        }

        return $string;
    }

    /**
     * @param Polygon $polygon
     *
     * @return null|string
     */
    private function toStringPolygon(Polygon $polygon)
    {
        $string = null;

        foreach ($polygon->getRings() as $lineString) {
            $string .= ($string ? ',': null) . '(' . $this->toStringLineString($lineString) . ')';
        }

        return $string;
    }
}
