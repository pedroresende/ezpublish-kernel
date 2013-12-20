<?php
/**
 * File containing the CachedValue class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */
namespace eZ\Publish\Core\REST\Server\Values;

use eZ\Publish\Core\REST\Common\Value as RestValue;

class CachedValue extends RestValue
{
    /**
     * Cache TTL. Set to false to disable.
     * @var int|bool
     */
    public $ttl;

    /**
     * Actual value object
     * @var mixed
     */
    public $value;

    /**
     * Vary cache user hash
     * @var string
     */
    public $userHash;

    public function __construct( $value, $ttl = null )
    {
        $this->value = $value;
        $this->ttl = $ttl;
    }
}
