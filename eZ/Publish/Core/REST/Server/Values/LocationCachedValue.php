<?php
/**
 * File containing the CachedValue class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */
namespace eZ\Publish\Core\REST\Server\Values;

class LocationCachedValue extends CachedValue
{
    /**
     * Cached location ID
     * @var mixed
     */
    public $locationId;

    public function __construct( $locationId, $value, $userHash, $ttl = null )
    {
        parent::__construct( $value, $userHash, $ttl );
        $this->locationId = $locationId;
    }
}
