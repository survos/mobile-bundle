import MobileController from '@survos-mobile/mobile';
import Framework7 from 'framework7/bundle';
import 'framework7/css/bundle';
import { DbUtilities } from '@survos-js-twig/database';

/*
* Generic app-shell controller: boots Framework7 and seeds the app's single
* Dexie database from its `survos_fw.yaml` project config, once, so every
* app stops hand-copying this from framework7-bundle-demo's app_controller.js.
* Extend this (not MobileController directly) for the top-level `#app` element.
*/
export default class extends MobileController {
    static values = {
        name: { type: String, default: 'FwApp' },
        theme: { type: String, default: 'auto' },
        locale: { type: String, default: 'en' },
        configCode: String,
        config: Object,
        // Plain {path, url}[] entries only (no beforeEnter callbacks — those
        // aren't JSON-serializable into a Stimulus value). Apps needing more
        // than url-based routes should extend this controller instead.
        routes: { type: Array, default: [] },
        // Initial URL fetched into the main view on boot, if set.
        mainUrl: { type: String, default: '' },
    };

    initialize() {
        super.initialize();

        const view = { el: '.view-main' };
        if (this.mainUrlValue) {
            view.url = this.mainUrlValue;
        }

        const app = new Framework7({
            name: this.nameValue,
            theme: this.themeValue,
            el: this.element,
            routes: this.routesValue,
            view: { main: view },
        });
        window.app = app;

        const projectConfig = (this.configValue?.projects ?? {})[this.configCodeValue];
        if (projectConfig) {
            new DbUtilities(projectConfig, this.localeValue);
        }
    }
}
