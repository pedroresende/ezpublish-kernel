<?php

namespace eZ\Publish\Core\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use PDO;

abstract class AbstractDoctrineQuery
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * Counter used to create unique ids in the bind methods.
     *
     * @var int
     */
    private $boundCounter = 0;

    /**
     * Stores the list of parameters that will be bound with doBind().
     *
     * Format: array( ':name' => &mixed )
     * @var array(string=>&mixed)
     */
    private $boundParameters = array();

    /**
     * Stores the type of a value which will we used when the value is bound.
     *
     * @var array(string=>int)
     */
    private $boundParametersType = array();

    /**
     * Stores the list of values that will be bound with doBind().
     *
     * Format: array( ':name' => mixed )
     * @var array(string=>mixed)
     */
    private $boundValues = array();

    /**
     * Stores the type of a value which will we used when the value is bound.
     *
     * @var array(string=>int)
     */
    private $boundValuesType = array();

    public $expr;

    public function __construct( Connection $connection )
    {
        $this->connection = $connection;
    }

    /**
     * Create a subselect used with the current query.
     *
     * @return \eZ\Publish\Core\Persistence\Database\SelectQuery
     */
    public function subSelect()
    {
        return new SubselectDoctrineQuery( $this );
    }

    /**
     * @return PDOStatement
     */
    public function prepare()
    {
        $stmt = $this->connection->prepare($this->getQuery());

        return $stmt;
    }

    /**
     * Binds the value $value to the specified variable name $placeHolder.
     *
     * This method provides a shortcut for PDOStatement::bindValue
     * when using prepared statements.
     *
     * The parameter $value specifies the value that you want to bind. If
     * $placeholder is not provided bindValue() will automatically create a
     * placeholder for you. An automatic placeholder will be of the name
     * 'ezcValue1', 'ezcValue2' etc.
     *
     * For more information see {@link http://php.net/pdostatement-bindparam}
     *
     * Example:
     * <code>
     * $value = 2;
     * $q->eq( 'id', $q->bindValue( $value ) );
     * $stmt = $q->prepare(); // the value 2 is bound to the query.
     * $value = 4;
     * $stmt->execute(); // executed with 'id = 2'
     * </code>
     *
     * @param mixed $value
     * @param string $placeHolder the name to bind with. The string must start with a colon ':'.
     * @return string the placeholder name used.
     */
    public function bindValue( $value, $placeHolder = null, $type = PDO::PARAM_STR )
    {
        if ( $placeHolder === null )
        {
            $this->boundCounter++;
            $placeHolder = ":ezcValue{$this->boundCounter}";
        }

        $this->boundValues[$placeHolder] = $value;
        $this->boundValuesType[$placeHolder] = $type;

        return $placeHolder;
    }

    /**
     * Binds the parameter $param to the specified variable name $placeHolder..
     *
     * This method provides a shortcut for PDOStatement::bindParam
     * when using prepared statements.
     *
     * The parameter $param specifies the variable that you want to bind. If
     * $placeholder is not provided bind() will automatically create a
     * placeholder for you. An automatic placeholder will be of the name
     * 'ezcValue1', 'ezcValue2' etc.
     *
     * For more information see {@link http://php.net/pdostatement-bindparam}
     *
     * Example:
     * <code>
     * $value = 2;
     * $q->eq( 'id', $q->bindParam( $value ) );
     * $stmt = $q->prepare(); // the parameter $value is bound to the query.
     * $value = 4;
     * $stmt->execute(); // executed with 'id = 4'
     * </code>
     *
     * @see doBind()
     * @param &mixed $param
     * @param string $placeHolder the name to bind with. The string must start with a colon ':'.
     * @return string the placeholder name used.
     */
    public function bindParam( &$param, $placeHolder = null, $type = PDO::PARAM_STR )
    {
        if ( $placeHolder === null )
        {
            $this->boundCounter++;
            $placeHolder = ":ezcValue{$this->boundCounter}";
        }

        $this->boundParameters[$placeHolder] =& $param;
        $this->boundParametersType[$placeHolder] = $type;

        return $placeHolder;
    }

    /**
     * Return the SQL string for this query.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getQuery();
    }

    /**
     * Parse the arguments and validate for existance of values.
     *
     * @param array $args
     * @return array
     */
    protected function parseArguments( array $args )
    {
        if ( count ( $args ) === 1 && is_array( $args[0] ) )
        {
            $args = $args[0];
        }

        if ( count( $args ) === 0 )
        {
            throw new QueryException('No arguments given');
        }

        return $args;
    }

}