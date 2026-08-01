# Survos Mobile Bundle

Declarative app pages for Framework7/Dexie Symfony PWAs — sits on top of
[`survos/fw-bundle`](../fw-bundle) (F7 chrome) and
[`survos/js-twig-bundle`](../js-twig-bundle) (client-side Twig render + Dexie
sync). Rewritten 2026-08 — the old OnsenUI version is gone; nothing here was
reused.

## `#[MobilePage]`

Pair it with a normal Symfony `#[Route]` (which must carry an explicit
`name:` — routes without one are invisible to the registry, same rule as
`survos/field-bundle`'s `#[RouteMeta]`):

```php
use Survos\MobileBundle\Attribute\MobilePage;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/pages/review', name: 'app_review')]
#[MobilePage(title: 'Review', icon: 'tabler:cards', tab: 'study')]
public function review(): Response
{
    return $this->render('pages/review.html.twig');
}
```

`MobilePagePass` (a compiler pass, built on `survos/atlas-bundle`'s
`ControllerAtlasBuilder` — see `field-bundle`'s `RouteMetaPass` for the same
pattern) collects every `#[Route]` + `#[MobilePage]` pair at compile time into
`MobilePageRegistry`, exposed to Twig as the `mobile_pages` global. Render the
tab bar with:

```twig
{% include '@SurvosMobile/tabbar.html.twig' %}
```

which iterates `mobile_pages.tabPages` — no more hand-written `KnpMenuEvent`
listeners or a raw `config.tabs` loop of unclear origin.

## App shell

Extend `mobile_app_controller.js` (imported via
`{{ stimulus_controller(survos_stimulus('mobile-bundle', 'app'), {...}) }}`,
per `AGENTS.md`'s Stimulus-naming rule — never hard-code the identifier) on
your `#app` element instead of copy-pasting Framework7 + `DbUtilities`
bootstrapping into every app's own `app_controller.js`:

```twig
<div id="app" {{ stimulus_controller(survos_stimulus('mobile-bundle', 'app'), {
    name: 'MyApp',
    configCode: 'myproject',
    config: survos_fw_config,
}) }}>
```

It boots Framework7 on that element and, from the same `survos_fw.yaml`
`projects[].stores` config `fw-bundle` already defines (no separate/duplicate
store config here — see fw-bundle's `AGENTS.md`), constructs
`js-twig-bundle`'s `DbUtilities` once, dispatching the usual `dbready` event
your page controllers already listen for.

## Rendering data

Use `js-twig-bundle`'s decoupled `compileTwigBlocks`/`twigRender` API
directly in your page's own Stimulus controller — not `<twig:dexie>`, which
carries PHP-side schema plumbing that's unused in every app surveyed so far.
See `js-twig-bundle`'s README for that API, and `~/sites/dadjokes` for a full
worked example (a page controller reading `window.db`, rendering a
precompiled `<twig:block>`, and writing back Leitner-scheduling state).
