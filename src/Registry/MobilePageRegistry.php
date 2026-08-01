<?php

declare(strict_types=1);

namespace Survos\MobileBundle\Registry;

use Survos\MobileBundle\Model\MobilePageDescriptor;

/**
 * Runtime service holding all MobilePageDescriptors compiled by MobilePagePass.
 * Injected with the compiled descriptor list — no runtime scanning. Exposed to
 * Twig as the `mobile_pages` global (see SurvosMobileBundle::prependExtension()).
 */
final class MobilePageRegistry
{
    /** @var array<string, MobilePageDescriptor[]> */
    private readonly array $byTab;

    /** @param MobilePageDescriptor[] $descriptors */
    public function __construct(
        private readonly array $descriptors = [],
    ) {
        $byTab = [];
        foreach ($descriptors as $descriptor) {
            if ($descriptor->tab !== null) {
                $byTab[$descriptor->tab][] = $descriptor;
            }
        }
        $this->byTab = $byTab;
    }

    /** @return MobilePageDescriptor[] */
    public function getAll(): array
    {
        return $this->descriptors;
    }

    public function getByRoute(string $route): ?MobilePageDescriptor
    {
        foreach ($this->descriptors as $d) {
            if ($d->route === $route) {
                return $d;
            }
        }

        return null;
    }

    /** @return string[] tab codes, in first-seen order */
    public function getTabs(): array
    {
        return array_keys($this->byTab);
    }

    /** One representative page per tab (the lowest-order page) — used to render the tab bar link. */
    public function getTabPages(): array
    {
        return array_map(static fn (array $pages) => $pages[0], $this->byTab);
    }

    /** @return MobilePageDescriptor[] */
    public function getByTab(string $tab): array
    {
        return $this->byTab[$tab] ?? [];
    }
}
