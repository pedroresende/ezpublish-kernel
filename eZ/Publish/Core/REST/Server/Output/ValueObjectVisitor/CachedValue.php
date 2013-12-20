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
use Symfony\Component\HttpFoundation\Request;

/**
 * CachedValue value object visitor
 */
class CachedValue extends ValueObjectVisitor
{
    protected $options = array();

    protected $request;

    public function __construct( array $options = array() )
    {
        $this->options = $options;
    }

    public function setRequest( Request $request = null )
    {
        return $this->request = $request;
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

        $response = $visitor->getResponse();
        $response->setPublic();
        $response->setVary( 'Accept' );

        if ( $this->options['content.ttl_cache'] === true )
        {
            $response->setSharedMaxAge( $data->ttl ? : $this->options['content.default_ttl'] );
            if ( isset( $this->request ) && $this->request->headers->has( 'X-User-Hash' ) )
            {
                $response->setVary( 'X-User-Hash', false );
            }
        }
    }
}
