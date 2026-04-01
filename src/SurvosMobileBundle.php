<?php

namespace Survos\MobileBundle;

use Survos\CoreBundle\Bundle\AssetMapperBundle;
use Survos\MobileBundle\Event\KnpMenuEvent;
use Survos\MobileBundle\Components\MenuComponent;
use Survos\MobileBundle\Menu\MenuService;
use Survos\MobileBundle\Twig\TwigExtension;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
class SurvosMobileBundle extends AssetMapperBundle implements CompilerPassInterface
{
    public const ASSET_PACKAGE = 'mobile';

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass($this);
    }


    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {

        $builder->register(MenuComponent::class)->setAutowired(true)->setAutoconfigured(true)
            ->setArgument('$menuOptions', []) // $config['menu_options'])
            ->setArgument('$helper', new Reference('knp_menu.helper'))
            ->setArgument('$factory', new Reference('knp_menu.factory'))
            ->setArgument('$eventDispatcher', new Reference('event_dispatcher'));
        ;

        $builder->register(TwigExtension::class)
            ->addTag('twig.extension');

        $builder->register(MenuService::class)
            ->setAutowired(true)
            ->setArgument(
                '$authorizationChecker',
                new Reference('security.authorization_checker', ContainerInterface::NULL_ON_INVALID_REFERENCE)
            )
            ->setArgument(
                '$security',
                new Reference('security.helper', ContainerInterface::NULL_ON_INVALID_REFERENCE)
            );

    }

    public function process(ContainerBuilder $container): void
    {
//        if (false === $container->hasDefinition('twig')) {
//            throw new \RuntimeException('Twig service not found, composer require twig/twig');
//            assert(false, "missing twig");
//            return;
//        }
        $def = $container->getDefinition('twig');

        // add the constants to twig to make calling the menu easier.

        $eventClass = (new \ReflectionClass(KnpMenuEvent::class));

        foreach ($eventClass->getConstants() as $name => $value) {
            $def->addMethodCall('addGlobal', [$name, $value]);
        }

    }


        public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
            ->scalarNode('theme')->defaultValue('pagestack')->end()
            ->booleanNode('enabled')->defaultTrue()->end()
            ->end();
    }
}
