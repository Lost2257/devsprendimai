<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Http\Request;

/** Metadata and readable initial HTML for the same public pages served by Vue. */
final class PageSeo
{
    public function forPage(Request $request, array $page): array
    {
        $baseUrl = rtrim((string) config('app.url'), '/');
        $english = app()->isLocale('en');
        $locale = $english ? 'en' : 'lt';
        $path = trim($request->path(), '/');
        $languagePath = preg_replace('#^en(?:/|$)#', '', $path) ?: '/';
        $ltUrl = $baseUrl.($languagePath === '/' ? '/' : '/'.$languagePath);
        $enUrl = $baseUrl.'/en'.($languagePath === '/' ? '' : '/'.$languagePath);
        $canonical = $english ? $enUrl : $ltUrl;
        $settings = cache()->remember('seo.settings', 300, fn () => Setting::query()->first());
        $content = json_decode(file_get_contents(resource_path('data/seo-content.json')), true, 512, JSON_THROW_ON_ERROR)[$locale];
        $service = $content['services'][$languagePath] ?? null;
        $project = str_starts_with($languagePath, 'projektai/') ? data_get($page, 'props.project') : null;
        $legalKey = ['privatumo-politika' => 'privacy', 'slapuku-politika' => 'cookies', 'paslaugu-teikimo-salygos' => 'terms'][$languagePath] ?? null;
        $legalDocument = $content['legal'][$legalKey] ?? null;
        $legal = $legalDocument ? [$legalDocument['title'].' | DevSprendimai', $legalDocument['description']] : null;
        $isHome = $languagePath === '/';
        $public = $isHome || $service || $project || $legal;
        $seo = [
            'title' => $english ? 'Website & e-commerce development | DevSprendimai' : ($settings?->site_title ?: 'Svetainių ir el. parduotuvių kūrimas | DevSprendimai'),
            'description' => $english ? 'Custom websites, online stores, CRM systems and applications. Based in Kaunas, Lithuania, working with businesses remotely. Discuss your project.' : ($settings?->site_description ?: 'Individualios interneto svetainės, el. parduotuvės, CRM ir aplikacijos. Kūrėjas Kaune, dirbantis su verslais visoje Lietuvoje. Aptarkime jūsų projektą.'),
            'image' => $baseUrl.'/og.png',
            'image_alt' => $english ? 'DevSprendimai digital product studio' : 'DevSprendimai skaitmeninių sprendimų studija',
            'type' => 'website',
        ];
        if ($isHome && $settings?->og_image) {
            $seo['image'] = $baseUrl.'/storage/'.ltrim($settings->og_image, '/');
        }
        if ($service) {
            $seo['title'] = $service['title'].' | DevSprendimai';
            $seo['description'] = $service['seoDescription'];
            $seo['image_alt'] = $service['title'];
        }
        if ($legal) {
            [$seo['title'], $seo['description']] = $legal;
        }
        if ($project) {
            $projectTitle = data_get($project, 'title');
            $seo['title'] = $projectTitle.' — '.($english ? 'project' : 'projektas').' | DevSprendimai';
            $seo['description'] = data_get($project, 'description') ?: data_get($project, 'subtitle') ?: ($english ? "$projectTitle, a digital project by DevSprendimai." : "$projectTitle projektas, sukurtas DevSprendimai.");
            $seo['type'] = 'article';
            $seo['image_alt'] = $projectTitle.($english ? ' project preview' : ' projekto vizualizacija');
            $images = $this->projectImages($project, $baseUrl);
            $seo['image'] = $images[0] ?? $seo['image'];
        }
        $indexable = $public && config('seo.indexable');
        $seo['robots'] = $indexable ? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' : 'noindex, nofollow, noarchive';
        $organizationId = $baseUrl.'/#organization';
        $globalGraph = [
            [
                '@type' => ['Organization', 'ProfessionalService'], '@id' => $organizationId,
                'name' => 'DevSprendimai', 'legalName' => 'Aivaras Razvodovskis', 'url' => $baseUrl.'/',
                'logo' => $baseUrl.'/favicon.png', 'image' => $baseUrl.'/og.png',
                'email' => 'info@devsprendimai.lt', 'telephone' => '+37060606227',
                'identifier' => ['@type' => 'PropertyValue', 'propertyID' => 'Individualios veiklos pažyma', 'value' => '1400067'],
                'address' => ['@type' => 'PostalAddress', 'streetAddress' => 'Pramonės pr. 28', 'addressLocality' => 'Kaunas', 'addressCountry' => 'LT'],
                'areaServed' => ['@type' => 'Country', 'name' => 'Lithuania'],
                'contactPoint' => ['@type' => 'ContactPoint', 'contactType' => 'customer service', 'telephone' => '+37060606227', 'email' => 'info@devsprendimai.lt', 'availableLanguage' => ['lt', 'en']],
            ],
            ['@type' => 'WebSite', '@id' => $baseUrl.'/#website', 'url' => $baseUrl.'/', 'name' => 'DevSprendimai', 'publisher' => ['@id' => $organizationId], 'inLanguage' => ['lt-LT', 'en']],
        ];
        $webPage = [
            '@type' => 'WebPage', '@id' => $canonical.'#webpage', 'url' => $canonical,
            'name' => $seo['title'], 'description' => $seo['description'],
            'inLanguage' => $english ? 'en' : 'lt-LT', 'isPartOf' => ['@id' => $baseUrl.'/#website'],
        ];
        $pageGraph = [];
        if (! $isHome && $public) {
            $webPage['breadcrumb'] = ['@id' => $canonical.'#breadcrumb'];
            $pageGraph[] = [
                '@type' => 'BreadcrumbList', '@id' => $canonical.'#breadcrumb',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => $english ? 'Home' : 'Pradžia', 'item' => $english ? $baseUrl.'/en' : $baseUrl.'/'],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => $service['title'] ?? data_get($project, 'title') ?? str_replace(' | DevSprendimai', '', $seo['title']), 'item' => $canonical],
                ],
            ];
        }
        if ($service) {
            $webPage['mainEntity'] = ['@id' => $canonical.'#service'];
            $pageGraph[] = [
                '@type' => 'Service', '@id' => $canonical.'#service', 'name' => $service['title'],
                'serviceType' => $service['eyebrow'], 'description' => $service['seoDescription'], 'url' => $canonical,
                'areaServed' => ['@type' => 'Country', 'name' => 'Lithuania'], 'provider' => ['@id' => $organizationId],
            ];
        }
        if ($project) {
            $webPage['mainEntity'] = ['@id' => $canonical.'#project'];
            $pageGraph[] = array_filter([
                '@type' => 'CreativeWork', '@id' => $canonical.'#project', 'name' => data_get($project, 'title'),
                'headline' => data_get($project, 'subtitle') ?: data_get($project, 'title'),
                'description' => $seo['description'], 'url' => $canonical, 'image' => $images,
                'dateModified' => data_get($project, 'updated_at'), 'creator' => ['@id' => $organizationId],
                'inLanguage' => $english ? 'en' : 'lt-LT',
            ], fn ($value) => $value !== null && $value !== []);
        }
        if ($isHome) {
            $webPage['about'] = ['@id' => $organizationId];
            $pageGraph[] = [
                '@type' => 'FAQPage', '@id' => $canonical.'#faq',
                'mainEntity' => array_map(fn ($item) => ['@type' => 'Question', 'name' => $item['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['a']]], $content['faq']),
            ];
        }
        $pageGraph[] = $webPage;

        return compact('baseUrl', 'english', 'locale', 'languagePath', 'ltUrl', 'enUrl', 'canonical', 'seo', 'service', 'project', 'legal', 'isHome', 'public', 'content', 'globalGraph', 'pageGraph') + [
            'googleVerification' => $settings?->google_site_verification ?: config('seo.google_site_verification'),
            'bingVerification' => $settings?->bing_site_verification ?: config('seo.bing_site_verification'),
            'projects' => data_get($page, 'props.projects', []),
        ];
    }

    public function projectImages(mixed $project, string $baseUrl): array
    {
        $paths = [data_get($project, 'thumbnail'), ...((array) data_get($project, 'gallery', [])), data_get($project, 'mobile_thumbnail')];

        return array_values(array_unique(array_map(
            fn ($path) => $baseUrl.'/storage/'.ltrim($path, '/'),
            array_filter($paths, fn ($path) => is_string($path) && $path !== ''),
        )));
    }
}
