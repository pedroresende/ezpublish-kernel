<?php
/**
 * File containing the RichTextHtml5OutputConverterPass class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass adding converters to HTML5 output converter.
 */
class RichTextHtml5OutputConverterPass implements CompilerPassInterface
{
    public function process( ContainerBuilder $container )
    {
        if ( !$container->hasDefinition( 'ezpublish.fieldType.ezrichtext.converter.output.xhtml5' ) )
        {
            return;
        }

        $html5ConverterDef = $container->getDefinition( 'ezpublish.fieldType.ezrichtext.converter.output.xhtml5' );
        $taggedServiceIds = $container->findTaggedServiceIds( 'ezpublish.ezrichtext.converter.output.xhtml5' );

        foreach ( $taggedServiceIds as $id => $attributes )
        {
            $priority = isset( $attributes[0]['priority'] ) ? (int)$attributes[0]['priority'] : 0;
            $html5ConverterDef->addMethodCall( 'addConverter', array( new Reference( $id ), $priority ) );
        }
    }
}
