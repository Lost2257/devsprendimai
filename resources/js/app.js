import '../css/app.css';
import './bootstrap';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h, nextTick } from 'vue';
import { analytics, isPublicPath } from './lib/analytics';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'DevSprendimai';
let analyticsTimer;
const updateAnalytics = (page) => nextTick(() => {
    clearTimeout(analyticsTimer);
    // Allow Inertia Head's queued title update to complete before recording a view.
    analyticsTimer = setTimeout(() => analytics.configure(page.props.analytics?.measurementId), 0);
});

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
        updateAnalytics(props.initialPage);
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
    updateAnalytics(event.detail.page);
});
