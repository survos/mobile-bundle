# Survos Mobile Bundle

A collection of tools to help create Symfony-based mobile apps.

* OnsenUI 
* pwa-bundle
* Dexie

Work in Progress.  See survos-sites/pokemon to see this in action.

## Notes

These need to be cleaned up, but they're useful to me during development.

### Twig

The application can be run as an SPA.  The initial page must extend the base page

    {% extends "@SurvosMobile/base.html.twig" %}

create app_controller and extend it from 

If using the OnsenUI documentation, replace 

    onclick="loadPage('whatever')"
 
with 

      {{ stimulus_action(_app_sc, 'loadPage', 'click', {
          route: 'whatever'
      }) }}

_app_sc should be set to 'app', someday this may change (https://github.com/hotwired/stimulus/issues/641)

### Tabs and Pages

Two fundamental concepts: the tabs at the bottom of the screen, and everything else.

All pages, though, are pre-loaded as twig templates in OnsenController.  

To create the tabs, the following, where id is the name of the tab template

```php
#[AsEventListener(event: KnpMenuEvent::MOBILE_TAB_MENU)]
public function tabMenu(KnpMenuEvent $event): void
{
    $menu = $event->getMenu();
        $this->add($menu, id: 'projects', label: 'projects', icon: 'fa-list');
        $this->add($menu, id: 'tours', label: 'tours', icon: 'fa-list', badge: 'x');
        $this->add($menu, id: 'share', label: 'share', icon: 'fa-qrcode');

```

## Events

When a tab is clicked, a 'prechange' event is dispatched, with  event.tabItem as the tab that's about to become active.  We intercept  

### Dynamic Data

To load dynamic data into a page, you must first put the data into dixie.  The basic way is to set up "stores" and define the indexable fields, eg..

```yaml
survos_js_twig:
  debug: true
  db: omar-db
  version: 7
  stores:
    -
      name: items
      schema: "++id,code,title,projectCode"
      url: /api/items
    -
      name: projects
      schema: "code"
      url: /api/projects
```

### Stimulus Helpers

https://github.com/symfony/ux/blob/2.x/src/StimulusBundle/src/Dto/StimulusAttributes.php

tests: https://github.com/symfony/ux/blob/2.x/src/StimulusBundle/tests/Twig/StimulusTwigExtensionTest.php

I need a create and publish an es6 package that exports 3 functions produce the exact same result as their PHP counterpart.

For example,


```js
import {stimulus_controller, stimulus_target, stimulus_action} from 'stimulus-twig';

let str = stimulus_controller('my-controller',  {myValue: 'scalar-value'});
console.assert(str == 'data-controller="my-controller" data-my-controller-my-value-value="scalar-value"');
```

All of the tests can be found (in PHP) at  https://github.com/symfony/ux/blob/2.x/src/StimulusBundle/tests/Twig/StimulusTwigExtensionTest.php
The PHP code is at https://github.com/symfony/ux/blob/2.x/src/StimulusBundle/src/Dto/StimulusAttributes.php 
The methods should be able to return an array and a string, exactly as the PHP code does.  Fortunately, the PHP code is very well written

This job consists of the following:

* Convert the PHP unit tests to javascript, using Jest or another testing packing.
* Convert the PHP code to an es6 class that exports the 3 methods and passes the tests
* Push the code to a github repo
* Publish the package to npmjs, and make sure it works with package bundlers like jsdelivr and unpkg 

Please point to a package on npmjs that you've written

## Requirement

bin/console importmap:require stimulus-attributes
bin/console importmap:require fos-routing
composer req friendsofsymfony/jsrouting-bundle

