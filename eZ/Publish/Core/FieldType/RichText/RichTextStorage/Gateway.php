<?php
/**
 * File containing the RichText Gateway abstract class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\FieldType\RichText\RichTextStorage;

use eZ\Publish\Core\FieldType\StorageGateway;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

/**
 * Abstract gateway class for RichText type.
 * Handles data that is not directly included in raw XML value from the field (i.e. URLs)
 */
abstract class Gateway extends StorageGateway
{
    /**
     * Populates $field->value->externalData with external data
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     */
    abstract public function getFieldData( Field $field );

    /**
     * Stores data, external to RichText type
     *
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     *
     * @return boolean
     */
    abstract public function storeFieldData( VersionInfo $versionInfo, Field $field );
}
