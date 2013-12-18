<?php
/**
 * File containing the RichTextTest class
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\Persistence\Legacy\Tests\Content\FieldValue\Converter;

use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue;
use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter\RichText as RichTextConverter;
use PHPUnit_Framework_TestCase;
use DOMDocument;

/**
 * Test case for RichText converter in Legacy storage
 *
 * @group fieldType
 * @group ezrichtext
 */
class RichTextTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter\RichText
     */
    protected $converter;

    /**
     * @var string
     */
    private $ezxmlString;

    /**
     * @var string
     */
    private $docbookString;

    protected function setUp()
    {
        parent::setUp();
        $this->converter = new RichTextConverter(
            new RichTextConverter\XsltConverter(
                $this->getAbsolutePath( "eZ/Publish/Core/Persistence/Legacy/Content/FieldValue/Converter/RichText/Resources/stylesheets/docbook_ezxml.xsl" )
            ),
            new RichTextConverter\XsltConverter(
                $this->getAbsolutePath( "eZ/Publish/Core/Persistence/Legacy/Content/FieldValue/Converter/RichText/Resources/stylesheets/ezxml_docbook.xsl" )
            ),
            new RichTextConverter\XsdValidator(
                $this->getAbsolutePath( "eZ/Publish/Core/Persistence/Legacy/Content/FieldValue/Converter/RichText/Resources/schemas/ezxml.xsd" )
            )
        );
        $this->ezxmlString = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<section xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">
  <header>This is a heading.</header>
  <paragraph>This is a paragraph.</paragraph>
</section>

EOT;
        $this->docbookString = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<section xmlns="http://docbook.org/ns/docbook" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:ezxhtml="http://ez.no/xmlns/ezpublish/docbook/xhtml" xmlns:ezcustom="http://ez.no/xmlns/ezpublish/docbook/custom" version="5.0-variant ezpublish-1.0">
  <title>This is a heading.</title>
  <para>This is a paragraph.</para>
</section>

EOT;
    }

    protected function tearDown()
    {
        unset( $this->xmlText );
        parent::tearDown();
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter\RichText::toStorageValue
     */
    public function testToStorageValue()
    {
        $value = new FieldValue;
        $value->data = $this->docbookString;
        $storageFieldValue = new StorageFieldValue;

        $this->converter->toStorageValue( $value, $storageFieldValue );
        self::assertSame( $this->ezxmlString, $storageFieldValue->dataText );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter\RichText::toFieldValue
     */
    public function testToFieldValue()
    {
        $storageFieldValue = new StorageFieldValue;
        $storageFieldValue->dataText = $this->ezxmlString;
        $fieldValue = new FieldValue;

        $this->converter->toFieldValue( $storageFieldValue, $fieldValue );
        self::assertSame( $this->docbookString, $fieldValue->data );
    }

    /**
     * @param string $relativePath
     *
     * @return string
     */
    protected function getAbsolutePath( $relativePath )
    {
        return self::getInstallationDir() . "/" . $relativePath;
    }

    /**
     * @return string
     */
    static protected function getInstallationDir()
    {
        static $installDir = null;
        if ( $installDir === null )
        {
            $config = require 'config.php';
            $installDir = $config['service']['parameters']['install_dir'];
        }
        return $installDir;
    }
}
