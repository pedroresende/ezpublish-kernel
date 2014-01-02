<?php
/**
 * File containing the RichText LegacyStorage class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\FieldType\RichText\RichTextStorage\Gateway;

use eZ\Publish\Core\FieldType\RichText\RichTextStorage\Gateway;
use eZ\Publish\Core\Persistence\Legacy\EzcDbHandler;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use DOMDocument;
use RuntimeException;

abstract class BaseStorage extends Gateway
{
    protected $dbHandler;

    /**
     * Returns the active connection
     *
     * @throws \RuntimeException if no connection has been set, yet.
     *
     * @return \eZ\Publish\Core\Persistence\Legacy\EzcDbHandler|\ezcDbHandler
     */
    protected function getConnection()
    {
        if ( $this->dbHandler === null )
        {
            throw new \RuntimeException( "Missing database connection." );
        }
        return $this->dbHandler;
    }

    /**
     * Populates $field->value with external data
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     */
    public function getFieldData( Field $field )
    {
        $document = new DOMDocument;
        $document->loadXML( $field->value->data );

        $xpath = new \DOMXPath( $document );
        $xpath->registerNamespace( "docbook", "http://docbook.org/ns/docbook" );
        $xpathExpression = "//docbook:link[starts-with( @xlink:href, 'ezurl://' )]";

        $links = $xpath->query( $xpathExpression );

        if ( empty( $links ) )
        {
            return;
        }

        $linkIdSet = array();
        $linksInfo = array();

        /** @var \DOMElement $link */
        foreach ( $links as $index => $link )
        {
            preg_match(
                "~^ezurl://([^#]*)?(#.*|\\s*)?$~",
                $link->getAttribute( "xlink:href" ),
                $matches
            );
            $linksInfo[$index] = $matches;

            if ( !empty( $matches[1] ) )
            {
                $linkIdSet[$matches[1]] = true;
            }
        }

        $linkUrls = $this->getLinkUrls( array_keys( $linkIdSet ) );

        foreach ( $links as $index => $link )
        {
            list( , $urlId, $fragment ) = $linksInfo[$index];

            if ( isset( $linkUrls[$urlId] ) )
            {
                $href = $linkUrls[$urlId] . $fragment;
            }
            else
            {
                // URL id is empty or not in the DB
                // @TODO log error
                $href = "#";
            }

            $link->setAttribute( "xlink:href", $href );
        }

        $field->value->data = $document->saveXML();
    }

    /**
     * Stores data, external to RichText type
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     *
     * @return boolean
     */
    public function storeFieldData( VersionInfo $versionInfo, Field $field )
    {
        $document = new DOMDocument;
        $document->loadXML( $field->value->data );

        $xpath = new \DOMXPath( $document );
        $xpath->registerNamespace( "docbook", "http://docbook.org/ns/docbook" );
        // This will select only links with non-empty 'xlink:href' attribute value
        $xpathExpression = "//docbook:link[string( @xlink:href ) and not( starts-with( @xlink:href, 'ezurl://' )" .
            "or starts-with( @xlink:href, 'ezcontent://' )" .
            "or starts-with( @xlink:href, 'ezlocation://' )" .
            "or starts-with( @xlink:href, '#' ) )]";

        $links = $xpath->query( $xpathExpression );

        if ( empty( $links ) )
        {
            return false;
        }

        $urlSet = array();
        $remoteIdSet = array();
        $linksInfo = array();

        /** @var \DOMElement $link */
        foreach ( $links as $index => $link )
        {
            preg_match(
                "~^(ezremote://)?([^#]*)?(#.*|\\s*)?$~",
                $link->getAttribute( "xlink:href" ),
                $matches
            );
            $linksInfo[$index] = $matches;

            if ( empty( $matches[1] ) )
            {
                $urlSet[$matches[2]] = true;
            }
            else
            {
                $remoteIdSet[$matches[2]] = true;
            }
        }

        $linksIds = $this->getLinkIds( array_keys( $urlSet ) );
        $contentIds = $this->getContentIds( array_keys( $remoteIdSet ) );

        foreach ( $links as $index => $link )
        {
            list( , $protocol, $url, $fragment ) = $linksInfo[$index];

            if ( empty( $protocol ) )
            {
                if ( !isset( $linksIds[$url] ) )
                {
                    $linksIds[$url] = $this->insertLink( $url );
                }
                $href = "ezurl://{$linksIds[$url]}{$fragment}";
            }
            else
            {
                if ( !isset( $contentIds[$url] ) )
                {
                    throw new NotFoundException( "Content", $url );
                }
                $href = "ezcontent://{$contentIds[$url]}{$fragment}";
            }

            $link->setAttribute( "xlink:href", $href );
        }

        $field->value->data = $document->saveXML();

        return true;
    }

    /**
     * Fetches rows in ezurl table referenced by IDs in $linkIds.
     * Returns as hash with URL id as key and corresponding URL as value.
     *
     * @param array $linkIds Array of link Ids
     *
     * @return array
     */
    abstract protected function getLinkUrls( array $linkIds );

    /**
     * Fetches rows in ezurl table referenced by URLs in $linksUrls array.
     * Returns as hash with URL as key and corresponding URL id as value.
     *
     * @param array $linksUrls
     *
     * @return array
     */
    abstract protected function getLinkIds( array $linksUrls );

    /**
     * Fetches rows in ezcontentobject table referenced by remoteIds in $linksRemoteIds array.
     * Returns as hash with remote id as key and corresponding id as value.
     *
     * @param array $linksRemoteIds
     *
     * @return array
     */
    abstract protected function getContentIds( array $linksRemoteIds );

    /**
     * Inserts a new entry in ezurl table and returns the table last insert id
     *
     * @param string $url The URL to insert in the database
     *
     * @return mixed
     */
    abstract protected function insertLink( $url );
}
