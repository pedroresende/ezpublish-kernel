<?php
/**
 * File containing the ContentList ValueObjectVisitor class
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor;

use eZ\Publish\Core\REST\Common\Output\ValueObjectVisitor;
use eZ\Publish\Core\REST\Common\Output\Generator;
use eZ\Publish\Core\REST\Common\Output\Visitor;

/**
 * CachedValue value object visitor
 */
class CachedValue extends ValueObjectVisitor
{
    protected $options = array();

    public function __construct( array $options = array() )
    {
        $this->options = $options;
    }

    /**
     * @param Visitor   $visitor
     * @param Generator $generator
     * @param \eZ\Publish\Core\REST\Server\Values\CachedValue $data
     */
    public function visit( Visitor $visitor, Generator $generator, $data )
    {
        $visitor->visitValueObject( $data->value );

        if ( $this->options['content.view_cache'] !== true )
        {
            return;
        }

        $visitor->getResponse()->setPublic();
        $visitor->getResponse()->setVary( 'Accept' );

        if ( $data->ttl !== false && $this->options['content.ttl_cache'] === true )
        {
            $visitor->getResponse()->setVary( 'X-User-Hash', false );
            $visitor->getResponse()->setSharedMaxAge(
                $data->ttl !== null ? $data->ttl : $this->options['content.default_ttl']
            );
        }
    }
}
