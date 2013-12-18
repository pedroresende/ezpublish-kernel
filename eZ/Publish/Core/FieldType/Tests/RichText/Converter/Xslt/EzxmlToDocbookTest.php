<?php
/**
 * File containing the EzxmlToDocbookTest conversion test
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\FieldType\Tests\RichText\Converter\Xslt;

/**
 * Tests conversion from legacy ezxml to docbook format
 */
class EzxmlToDocbookTest extends BaseTest
{
    /**
     * Provider for conversion test.
     *
     * @return array
     */
    public function providerForTestConvert()
    {
        $map = array();

        foreach ( glob( __DIR__ . "/_fixtures/ezxml/*.xml" ) as $xmlFile )
        {
            $map[] = array(
                $xmlFile,
                __DIR__ . "/_fixtures/docbook/" . basename( $xmlFile )
            );
        }

        return $map;
    }

    /**
     * Return the absolute path to conversion transformation stylesheet.
     *
     * @return string
     */
    protected function getConversionTransformationStylesheet()
    {
        return __DIR__ . "/../../../../RichText/Resources/stylesheets/ezxml/docbook.xsl";
    }

    /**
     * Return custom XSLT stylesheets configuration.
     *
     * Stylesheet paths must be absolute.
     *
     * Code example:
     *
     * <code>
     *  array(
     *      array(
     *          "path" => __DIR__ . "/core.xsl",
     *          "priority" => 100
     *      ),
     *      array(
     *          "path" => __DIR__ . "/custom.xsl",
     *          "priority" => 99
     *      ),
     *  )
     * </code>
     *
     * @return array
     */
    protected function getCustomConversionTransformationStylesheets()
    {
        return array(
        );
    }

    /**
     * Return the absolute path to conversion result validation schema, or null if no validation is to be performed.
     *
     * @return null|string
     */
    protected function getConversionValidationSchema()
    {
        return __DIR__ . "/../../../../RichText/Resources/schemas/docbook/ezpublish.rng";
    }
}
