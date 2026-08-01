<?php

declare(strict_types=1);

namespace Survos\MobileBundle\Compiler;

use Survos\AtlasBundle\Compiler\ControllerAtlasBuilder;
use Survos\MobileBundle\Attribute\MobilePage;
use Survos\MobileBundle\Model\MobilePageDescriptor;
use Survos\MobileBundle\Registry\MobilePageRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Builds the MobilePageRegistry from atlas-bundle's controller atlas.
 *
 * Routes whose #[Route] lacks an explicit name are skipped by atlas-bundle and
 * therefore silently absent here — same convention as field-bundle's RouteMetaPass.
 */
final class MobilePagePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(MobilePageRegistry::class)) {
            return;
        }

        $descriptors = [];

        foreach (ControllerAtlasBuilder::build($container) as $route) {
            $hits = $route->attributesOf(MobilePage::class);
            if ($hits === []) {
                continue;
            }

            $meta = new MobilePage(...$hits[0]['args']);

            $descriptors[] = new MobilePageDescriptor(
                route: $route->name,
                path: $route->path,
                title: $meta->title,
                icon: $meta->icon,
                tab: $meta->tab,
                order: $meta->order,
            );
        }

        usort(
            $descriptors,
            static fn (MobilePageDescriptor $a, MobilePageDescriptor $b) => [$a->tab, $a->order, $a->title] <=> [$b->tab, $b->order, $b->title],
        );

        $definitions = array_map(self::toDefinition(...), $descriptors);

        $container->getDefinition(MobilePageRegistry::class)
            ->setArgument('$descriptors', $definitions);
    }

    private static function toDefinition(MobilePageDescriptor $d): Definition
    {
        return new Definition(MobilePageDescriptor::class, [
            '$route' => $d->route,
            '$path' => $d->path,
            '$title' => $d->title,
            '$icon' => $d->icon,
            '$tab' => $d->tab,
            '$order' => $d->order,
        ]);
    }
}
