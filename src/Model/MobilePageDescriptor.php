<?php

declare(strict_types=1);

namespace Survos\MobileBundle\Model;

final class MobilePageDescriptor
{
    public function __construct(
        public readonly string $route,
        public readonly string $path,
        public readonly string $title,
        public readonly ?string $icon,
        public readonly ?string $tab,
        public readonly int $order,
    ) {
    }
}
