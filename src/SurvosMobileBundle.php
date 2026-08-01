<?php

declare(strict_types=1);

namespace Survos\MobileBundle;

use Survos\Kit\AbstractUxBundle;
use Survos\Kit\SurvosKitBundle;
use Survos\MobileBundle\Compiler\MobilePagePass;
use Survos\MobileBundle\Registry\MobilePageRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Kernel\RequiredBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

#[RequiredBundle(SurvosKitBundle::class)]
// Symfony\Component\HttpKernel\Bundle\Bundle <-- Flex auto-registration marker (see Survos\Kit\AbstractSurvosBundle)
final class SurvosMobileBundle extends AbstractUxBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        parent::loadExtension($config, $container, $builder);

        $builder->register(MobilePageRegistry::class)
            ->setAutowired(true)->setAutoconfigured(true)->setPublic(true)
            ->setArgument('$descriptors', []);
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new MobilePagePass());
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        parent::prependExtension($container, $builder);

        if ($builder->hasExtension('twig')) {
            $builder->prependExtensionConfig('twig', [
                'globals' => [
                    'mobile_pages' => '@' . MobilePageRegistry::class,
                ],
            ]);
        }
    }
}
