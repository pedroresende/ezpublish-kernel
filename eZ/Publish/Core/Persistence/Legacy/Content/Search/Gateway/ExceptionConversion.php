<?php
/**
 * File containing the Content Search Gateway class
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\Persistence\Legacy\Content\Search\Gateway;

use eZ\Publish\Core\Persistence\Legacy\Content\Search\Gateway;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Doctrine\DBAL\DBALException;
use PDOException;
use RuntimeException;

/**
 * The Content Search Gateway provides the implementation for one database to
 * retrieve the desired content objects.
 */
class ExceptionConversion extends Gateway
{
    /**
     * The wrapped gateway
     *
     * @var Gateway
     */
    protected $innerGateway;

    /**
     * Creates a new exception conversion gateway around $innerGateway
     *
     * @param Gateway $innerGateway
     */
    public function __construct( Gateway $innerGateway )
    {
        $this->innerGateway = $innerGateway;
    }

    /**
     * Returns a list of object satisfying the $criterion.
     *
     * @param Criterion $criterion
     * @param int $offset
     * @param int|null $limit
     * @param \eZ\Publish\API\Repository\Values\Content\Query\SortClause[] $sort
     * @param string[] $translations
     *
     * @return mixed[][]
     */
    public function find( Criterion $criterion, $offset = 0, $limit = null, array $sort = null, array $translations = null )
    {
        try
        {
            return $this->innerGateway->find( $criterion, $offset, $limit, $sort, $translations );
        }
        catch ( ezcDbException $e )
        {
            throw new RuntimeException( 'Database error', 0, $e );
        }
        catch ( PDOException $e )
        {
            throw new RuntimeException( 'Database error', 0, $e );
        }
    }
}

