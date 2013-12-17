<?php
/**
 * This file contains the ValidatorDispatcher class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\FieldType\RichText;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use DOMDocument;

/**
 * Dispatcher for various validators depending on the XML document namespace.
 */
class ValidatorDispatcher
{
    /**
     * Mapping of namespaces to validators.
     *
     * @var \eZ\Publish\Core\FieldType\RichText\Validator[]
     */
    protected $mapping = array();

    /**
     * @param \eZ\Publish\Core\FieldType\RichText\Validator[] $validatorMap
     */
    public function __construct( $validatorMap )
    {
        foreach ( $validatorMap as $namespace => $validator )
        {
            $this->addValidator( $namespace, $validator );
        }
    }

    /**
     * Adds validator mapping.
     *
     * @param string $namespace
     * @param \eZ\Publish\Core\FieldType\RichText\Validator $validator
     */
    public function addValidator( $namespace, Validator $validator = null )
    {
        $this->mapping[$namespace] = $validator;
    }

    /**
     * Dispatches DOMDocument to the namespace mapped validator.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @param \DOMDocument $document
     *
     * @return string[]
     */
    public function dispatch( DOMDocument $document )
    {
        $documentNamespace = $document->documentElement->lookupNamespaceURI( null );
        // checking for null as ezxml has no default namespace...
        if ( $documentNamespace === null )
        {
            $documentNamespace = $document->documentElement->lookupNamespaceURI( "xhtml" );
        }

        foreach ( $this->mapping as $namespace => $validator )
        {
            if ( $documentNamespace === $namespace )
            {
                if ( $validator === null )
                {
                    return array();
                }
                return $validator->validate( $document );
            }
        }

        throw new NotFoundException( "Validator", $documentNamespace );
    }
}
