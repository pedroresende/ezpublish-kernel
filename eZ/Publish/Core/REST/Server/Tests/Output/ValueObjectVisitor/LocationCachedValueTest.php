<?php
/**
 * File containing a test class
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\REST\Server\Tests\Output\ValueObjectVisitor;

use eZ\Publish\Core\REST\Common\Tests\Output\ValueObjectVisitorBaseTest;
use eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor;
use eZ\Publish\Core\REST\Server\Exceptions;
use eZ\Publish\Core\REST\Common;
use eZ\Publish\Core\REST\Server\Values\CachedValue;
use stdClass;

class LocationCachedValueTest extends ValueObjectVisitorBaseTest
{
    protected $options;

    protected $defaultOptions = array(
        'content.view_cache' => true,
        'content.ttl_cache' => true,
        'content.default_ttl' => 60
    );

    public function testVisit()
    {
        self::markTestSkipped( "@todo implement, need better Response testing/mocking" );
    }

    /**
     * Must return an instance of the tested visitor object
     *
     * @return \eZ\Publish\Core\REST\Common\Output\ValueObjectVisitor
     */
    protected function internalGetVisitor()
    {
        return new ValueObjectVisitor\LocationCachedValue(
            $this->options ?: $this->defaultOptions
        );
    }
}
