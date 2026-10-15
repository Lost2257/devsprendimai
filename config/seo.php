<?php

return [
    // Staging/local deployments must not compete with the production domain.
    'indexable' => env('SEO_INDEXABLE', env('APP_ENV') === 'production'),
    'google_site_verification' => env('GOOGLE_SITE_VERIFICATION'),
    'bing_site_verification' => env('BING_SITE_VERIFICATION'),
];
