<?php

declare(strict_types=1);

namespace Survos\MobileBundle\Attribute;

/**
 * Declares a controller action as an app page: a title, an optional icon, and
 * an optional tab it belongs to. Composes with #[Route] on the same method —
 * does not replace it.
 *
 * Discovered at compile time by MobilePagePass via atlas-bundle's
 * ControllerAtlasBuilder. The companion #[Route] must carry an explicit name,
 * same requirement as field-bundle's #[RouteMeta].
 *
 * Example:
 *
 *     #[Route('/pages/review', name: 'app_review')]
 *     #[MobilePage(title: 'Review', icon: 'tabler:cards', tab: 'study')]
 *     public function review(): Response { ... }
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final class MobilePage
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $icon = null,
        /** Tab code this page belongs to. Null = not shown in the tab bar (e.g. a detail page). */
        public readonly ?string $tab = null,
        /** Display order within its tab. */
        public readonly int $order = 100,
    ) {
    }
}
