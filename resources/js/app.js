import '../css/app.css';
import './bootstrap';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { analytics, isPublicPath } from './lib/analytics';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'DevSprendimai';

createInertiaApp({
    title: (title) => title?.toLowerCase().includes(appName.toLowerCase())
        ? title
        : `${title} | ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#22d3ee',
    },
});

router.on('before', (event) => {
    if (!isPublicPath(event.detail.visit.url.pathname)) analytics.pause();
});
router.on('navigate', (event) => {
    // Public views are recorded by SeoHead after the new page actually renders.
    if (!event.detail.page.props.analytics?.measurementId) analytics.configure(null);
});
