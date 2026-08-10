<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="dark">

        @php
            $privateSeoPath = request()->is('dashboard*', 'login', 'register', 'forgot-password', 'reset-password*', 'verify-email', 'confirm-password', 'profile*');
            $isEnglish = app()->isLocale('en');
            $seo = [
                'title' => $isEnglish ? 'DevSprendimai — digital products for business growth' : 'DevSprendimai — skaitmeniniai sprendimai verslui',
                'description' => $isEnglish ? 'Websites, applications, e-commerce, CRM systems and business process automation.' : 'Interneto svetainės, aplikacijos, el. parduotuvės, CRM sistemos ir verslo procesų automatizacija.',
                'image' => url('/og.png'),
                'image_alt' => 'DevSprendimai skaitmeninių sprendimų studija',
                'type' => 'website',
            ];
            $serviceSeo = [
                'interneto-svetaines' => ['Interneto svetainių kūrimas | DevSprendimai', 'Modernios, greitos ir pardavimams paruoštos interneto svetainės Lietuvos verslui.'],
                'el-parduotuves' => ['El. parduotuvių kūrimas | DevSprendimai', 'Greitos, patogios ir pardavimams paruoštos el. parduotuvės su reikalingomis integracijomis.'],
                'crm-sistemos' => ['CRM sistemų kūrimas | DevSprendimai', 'Individualios CRM sistemos klientams, pardavimams ir komandiniams procesams valdyti.'],
                'automatizacija' => ['Verslo procesų automatizacija | DevSprendimai', 'Procesų automatizavimas, API integracijos ir mažiau rankinio darbo jūsų komandai.'],
                'aplikaciju-kurimas' => ['Aplikacijų kūrimas | DevSprendimai', 'Individualių web ir mobiliųjų aplikacijų kūrimas nuo MVP iki pilnai veikiančios verslo platformos.'],
                'svetainiu-prieziura' => ['Svetainių priežiūra | DevSprendimai', 'Svetainių atnaujinimai, saugumas, atsarginės kopijos ir greita techninė pagalba.'],
                'privatumo-politika' => ['Privatumo politika | DevSprendimai', 'Informacija apie tai, kaip DevSprendimai renka, naudoja ir saugo asmens duomenis.'],
                'slapuku-politika' => ['Slapukų politika | DevSprendimai', 'Informacija apie devsprendimai.lt naudojamus būtinuosius slapukus.'],
                'paslaugu-teikimo-salygos' => ['Paslaugų teikimo sąlygos | DevSprendimai', 'Bendros DevSprendimai skaitmeninių paslaugų teikimo sąlygos.'],
            ];
            $currentPath = request()->path();
            $languagePath = preg_replace('#^en/?#', '', $currentPath) ?: '/';
            $serviceSeoEn = [
                'interneto-svetaines' => ['Website development | DevSprendimai', 'Fast, modern and conversion-ready websites for ambitious businesses.'],
                'el-parduotuves' => ['E-commerce development | DevSprendimai', 'Fast, user-friendly online stores with the integrations your business needs.'],
                'crm-sistemos' => ['Custom CRM development | DevSprendimai', 'Custom CRM systems for managing customers, sales and team workflows.'],
                'automatizacija' => ['Business process automation | DevSprendimai', 'Workflow automation, API integrations and less manual work for your team.'],
                'aplikaciju-kurimas' => ['Application development | DevSprendimai', 'Custom web and mobile applications from MVP to a complete business platform.'],
                'svetainiu-prieziura' => ['Website maintenance | DevSprendimai', 'Updates, security, backups and responsive technical support.'],
                'privatumo-politika' => ['Privacy Policy | DevSprendimai', 'How DevSprendimai collects, uses and protects personal data.'],
                'slapuku-politika' => ['Cookie Policy | DevSprendimai', 'Information about essential cookies used by devsprendimai.lt.'],
                'paslaugu-teikimo-salygos' => ['Terms of Service | DevSprendimai', 'General terms for DevSprendimai digital services.'],
            ];
            $activeServiceSeo = $isEnglish ? $serviceSeoEn : $serviceSeo;
            if (isset($activeServiceSeo[$languagePath])) {
                [$seo['title'], $seo['description']] = $activeServiceSeo[$languagePath];
            }
            if (!$isEnglish && $languagePath === '/') {
                $settings = data_get($page, 'props.settings', []);
                $seo['title'] = data_get($settings, 'site_title') ?: $seo['title'];
                $seo['description'] = data_get($settings, 'site_description') ?: $seo['description'];
                if ($ogImage = data_get($settings, 'og_image')) {
                    $seo['image'] = url('/storage/'.ltrim($ogImage, '/'));
                }
            }
            if (request()->is('projektai/*') && ($project = data_get($page, 'props.project'))) {
                $seo['title'] = data_get($project, 'title').' — projektas | DevSprendimai';
                $seo['description'] = data_get($project, 'description') ?: data_get($project, 'subtitle') ?: 'DevSprendimai sukurtas skaitmeninis projektas.';
                $seo['type'] = 'article';
                $seo['image_alt'] = data_get($project, 'title').' projekto vizualizacija';
                if ($thumbnail = data_get($project, 'thumbnail')) {
                    $seo['image'] = url('/storage/'.ltrim($thumbnail, '/'));
                }
            }
            if (request()->is('en/projektai/*') && ($project = data_get($page, 'props.project'))) {
                $seo['title'] = data_get($project, 'title').' — project | DevSprendimai';
                $seo['description'] = data_get($project, 'description') ?: data_get($project, 'subtitle') ?: 'A digital project created by DevSprendimai.';
                $seo['type'] = 'article';
                $seo['image_alt'] = data_get($project, 'title').' project preview';
                if ($thumbnail = data_get($project, 'thumbnail')) $seo['image'] = url('/storage/'.ltrim($thumbnail, '/'));
            }
            $ltPath = $languagePath === '/' ? '' : '/'.$languagePath;
            $enPath = '/en'.($languagePath === '/' ? '' : '/'.$languagePath);
        @endphp
        <link data-seo-canonical rel="canonical" href="{{ url()->current() }}">
        <link data-seo-canonical rel="alternate" hreflang="lt-LT" href="{{ url($ltPath) }}">
        <link data-seo-canonical rel="alternate" hreflang="en" href="{{ url($enPath) }}">
        <link data-seo-canonical rel="alternate" hreflang="x-default" href="{{ url($ltPath) }}">

        <meta data-seo-meta="description" name="description" content="{{ $seo['description'] }}">
        <meta data-seo-meta="robots" name="robots" content="{{ $privateSeoPath ? 'noindex, nofollow, noarchive' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' }}">
        <meta data-seo-meta="googlebot" name="googlebot" content="{{ $privateSeoPath ? 'noindex, nofollow, noarchive' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' }}">
        <meta name="author" content="DevSprendimai">
        <meta name="theme-color" content="#02060e">
        <meta data-seo-meta="og:locale" property="og:locale" content="{{ $isEnglish ? 'en_US' : 'lt_LT' }}">
        <meta data-seo-meta="og:locale:alternate" property="og:locale:alternate" content="{{ $isEnglish ? 'lt_LT' : 'en_US' }}">
        <meta property="og:site_name" content="DevSprendimai">
        <meta data-seo-meta="og:type" property="og:type" content="{{ $seo['type'] }}">
        <meta data-seo-meta="og:title" property="og:title" content="{{ $seo['title'] }}">
        <meta data-seo-meta="og:description" property="og:description" content="{{ $seo['description'] }}">
        <meta data-seo-meta="og:url" property="og:url" content="{{ url()->current() }}">
        <meta data-seo-meta="og:image" property="og:image" content="{{ $seo['image'] }}">
        <meta data-seo-meta="og:image:alt" property="og:image:alt" content="{{ $seo['image_alt'] }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta data-seo-meta="twitter:title" name="twitter:title" content="{{ $seo['title'] }}">
        <meta data-seo-meta="twitter:description" name="twitter:description" content="{{ $seo['description'] }}">
        <meta data-seo-meta="twitter:image" name="twitter:image" content="{{ $seo['image'] }}">
        <meta data-seo-meta="twitter:image:alt" name="twitter:image:alt" content="{{ $seo['image_alt'] }}">

        @if (config('seo.google_site_verification'))
            <meta name="google-site-verification" content="{{ config('seo.google_site_verification') }}">
        @endif
        @if (config('seo.bing_site_verification'))
            <meta name="msvalidate.01" content="{{ config('seo.bing_site_verification') }}">
        @endif

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <title inertia>{{ $seo['title'] }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
