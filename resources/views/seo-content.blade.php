<div class="initial-content">
    <style>
        .initial-content{background:#02060e;color:#edf7ff;min-height:100vh;padding:24px max(20px,calc((100vw - 1080px)/2));font:16px/1.75 system-ui,sans-serif;overflow-wrap:anywhere}
        .initial-content header,.initial-content footer{border-block-end:1px solid #163341;padding-block:20px}
        .initial-content nav{display:flex;gap:12px 24px;flex-wrap:wrap;margin-top:16px}
        .initial-content a{color:#67e8f9;text-decoration:underline;text-underline-offset:4px}
        .initial-content h1{font-size:clamp(2rem,5vw,4rem);line-height:1.13;letter-spacing:-.04em;margin:32px 0 24px;font-weight:800}
        .initial-content h2{font-size:1.6rem;line-height:1.3;margin:32px 0 16px;font-weight:750}
        .initial-content h3{font-size:1.15rem;margin:24px 0 8px;font-weight:700}
        .initial-content p{margin:12px 0;color:#b5c5d8;max-width:78ch}
        .initial-content ul{padding-inline-start:24px;list-style:disc}.initial-content li{margin:8px 0}
        .initial-content img{display:block;max-width:100%;height:auto;border-radius:16px;margin:24px 0}
        .initial-content table{border-collapse:collapse;width:100%;font-size:.9rem}.initial-content th,.initial-content td{border:1px solid #163341;padding:12px;text-align:start;vertical-align:top}
        .initial-content .table-scroll{overflow-x:auto}.initial-content address{font-style:normal}
    </style>
    <header>
        <a href="{{ $english ? $baseUrl.'/en' : $baseUrl.'/' }}" aria-label="{{ $english ? 'DevSprendimai home' : 'DevSprendimai pradžia' }}">DevSprendimai</a>
        <nav aria-label="{{ $english ? 'Services' : 'Paslaugos' }}">
            @foreach ($content['services'] as $slug => $item)
                <a href="{{ $baseUrl.($english ? '/en' : '').'/'.$slug }}">{{ $item['title'] }}</a>
            @endforeach
            <a href="{{ $english ? $ltUrl : $enUrl }}" lang="{{ $english ? 'lt' : 'en' }}">{{ $english ? 'Lietuviškai' : 'English' }}</a>
        </nav>
    </header>
    <main>
        @if ($isHome)
            <h1>{{ $english ? 'Digital solutions that grow your business.' : 'Skaitmeniniai sprendimai, kurie augina verslą.' }}</h1>
            <p>{{ $english ? 'From a distinctive website or application to e-commerce, CRM and automation — I build solutions that look exceptional and deliver measurable value.' : 'Nuo išskirtinės svetainės ar aplikacijos iki el. parduotuvės, CRM ir automatizacijos — kuriu sprendimus, kurie ne tik gerai atrodo, bet ir realiai dirba jūsų naudai.' }}</p>
            <p><a href="#contact">{{ $english ? 'Discuss a project' : 'Aptarkime projektą' }}</a></p>
            <section>
                <h2>{{ $english ? 'Services for your business' : 'Paslaugos jūsų verslui' }}</h2>
                @foreach ($content['services'] as $slug => $item)
                    <article>
                        <h3><a href="{{ $baseUrl.($english ? '/en' : '').'/'.$slug }}">{{ $item['title'] }}</a></h3>
                        <p>{{ $item['description'] }}</p>
                    </article>
                @endforeach
            </section>
            @if (count($projects))
                <section id="projects">
                    <h2>{{ $english ? 'Selected projects' : 'Atlikti projektai' }}</h2>
                    @foreach ($projects as $item)
                        <article>
                            <h3><a href="{{ $baseUrl.($english ? '/en' : '').'/projektai/'.data_get($item, 'slug') }}">{{ data_get($item, 'title') }}</a></h3>
                            <p>{{ data_get($item, 'description') ?: data_get($item, 'subtitle') }}</p>
                        </article>
                    @endforeach
                </section>
            @endif
            <section id="faq">
                <h2>{{ $english ? 'Frequently asked questions' : 'Dažniausiai užduodami klausimai' }}</h2>
                @foreach ($content['faq'] as $item)
                    <h3>{{ $item['q'] }}</h3>
                    <p>{{ $item['a'] }}</p>
                @endforeach
            </section>
        @elseif ($service)
            <h1>{{ $service['title'] }}</h1>
            <p>{{ $service['headline'].' '.$service['highlight'] }}</p>
            <p>{{ $service['description'] }}</p>
            <p><a href="#contact">{{ $english ? 'Discuss this service' : 'Aptarkime šią paslaugą' }}</a></p>
            <section>
                <h2>{{ $service['benefitsTitle'] }}</h2>
                <p>{{ $service['benefitsLead'] }}</p>
                @foreach ($service['benefits'] as $benefit)
                    <h3>{{ $benefit['title'] }}</h3>
                    <p>{{ $benefit['description'] }}</p>
                @endforeach
            </section>
            <section>
                <h2>{{ $service['featureTitle'] }}</h2>
                <p>{{ $service['featureLead'] }}</p>
                <ul>@foreach ($service['features'] as $feature)<li>{{ $feature }}</li>@endforeach</ul>
            </section>
            <section>
                <h2>{{ $english ? 'How we work' : 'Kaip dirbame' }}</h2>
                @foreach ($service['process'] as $step)
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['text'] }}</p>
                @endforeach
            </section>
        @elseif ($project)
            <article>
                <h1>{{ data_get($project, 'title') }}</h1>
                @if (data_get($project, 'subtitle'))<p>{{ data_get($project, 'subtitle') }}</p>@endif
                <p>{{ data_get($project, 'description') }}</p>
                @if (data_get($project, 'services'))<p>{{ $english ? 'Services' : 'Paslaugos' }}: {{ data_get($project, 'services') }}</p>@endif
                @if (data_get($project, 'technologies'))<p>{{ $english ? 'Technologies' : 'Technologijos' }}: {{ data_get($project, 'technologies') }}</p>@endif
                @foreach (['challenge' => ($english ? 'Challenge' : 'Iššūkis'), 'solution' => ($english ? 'Solution' : 'Sprendimas'), 'result' => ($english ? 'Result' : 'Rezultatas')] as $field => $heading)
                    @if (data_get($project, $field))
                        <section><h2>{{ $heading }}</h2><p>{{ data_get($project, $field) }}</p></section>
                    @endif
                @endforeach
                @foreach (app(\App\Support\PageSeo::class)->projectImages($project, $baseUrl) as $projectImage)
                    <img src="{{ $projectImage }}" alt="{{ data_get($project, 'title').' — '.($english ? 'project preview' : 'projekto vaizdas') }}" loading="lazy" decoding="async">
                @endforeach
                @if (preg_match('#^https?://#i', data_get($project, 'url') ?? ''))
                    <p><a href="{{ data_get($project, 'url') }}" rel="noopener noreferrer">{{ $english ? 'Open the live project' : 'Atidaryti gyvą projektą' }}</a></p>
                @endif
            </article>
        @elseif ($legal)
            <h1>{{ str_replace(' | DevSprendimai', '', $seo['title']) }}</h1>
            <p>{{ $seo['description'] }}</p>
            @php
                $legalKey = ['privatumo-politika' => 'privacy', 'slapuku-politika' => 'cookies', 'paslaugu-teikimo-salygos' => 'terms'][$languagePath];
                $document = $content['legal'][$legalKey] ?? null;
            @endphp
            @if ($document)
                <p>{{ $document['intro'] ?? '' }}</p>
                @foreach ($document['sections'] ?? [] as $section)
                    <section>
                        <h2>{{ $section['title'] }}</h2>
                        @foreach ($section['paragraphs'] ?? [] as $paragraph)<p>{{ $paragraph }}</p>@endforeach
                        @if (!empty($section['bullets']))<ul>@foreach ($section['bullets'] as $bullet)<li>{{ $bullet }}</li>@endforeach</ul>@endif
                        @if (!empty($section['table']))
                            <div class="table-scroll"><table>
                                <thead><tr>@foreach ($section['table'][0] as $heading)<th scope="col">{{ $heading }}</th>@endforeach</tr></thead>
                                <tbody>@foreach (array_slice($section['table'], 1) as $row)<tr>@foreach ($row as $cell)<td>{{ $cell }}</td>@endforeach</tr>@endforeach</tbody>
                            </table></div>
                        @endif
                        @foreach ($section['links'] ?? [] as $link)<p><a href="{{ $link['href'] }}" rel="noopener noreferrer">{{ $link['label'] }}</a></p>@endforeach
                    </section>
                @endforeach
            @endif
        @endif
        <section id="contact">
            <h2>{{ $english ? 'Let’s discuss your project' : 'Aptarkime jūsų projektą' }}</h2>
            <p>{{ $english ? 'Tell me what you need — a website, an online store, an application or a simpler business process.' : 'Parašykite, ko reikia jūsų verslui — svetainės, el. parduotuvės, aplikacijos ar paprastesnio darbo proceso.' }}</p>
            <address>
                <a href="mailto:info@devsprendimai.lt">info@devsprendimai.lt</a> · <a href="tel:+37060606227">+370 606 06227</a><br>
                Aivaras Razvodovskis · {{ $english ? 'Individual activity certificate' : 'Individualios veiklos pažyma' }} Nr. 1400067<br>
                Pramonės pr. 28, Kaunas, {{ $english ? 'Lithuania' : 'Lietuva' }}
            </address>
        </section>
    </main>
    <footer>
        <nav aria-label="{{ $english ? 'Legal information' : 'Teisinė informacija' }}">
            <a href="{{ $baseUrl.($english ? '/en' : '').'/privatumo-politika' }}">{{ $english ? 'Privacy Policy' : 'Privatumo politika' }}</a>
            <a href="{{ $baseUrl.($english ? '/en' : '').'/slapuku-politika' }}">{{ $english ? 'Cookie Policy' : 'Slapukų politika' }}</a>
            <a href="{{ $baseUrl.($english ? '/en' : '').'/paslaugu-teikimo-salygos' }}">{{ $english ? 'Terms of Service' : 'Paslaugų teikimo sąlygos' }}</a>
        </nav>
    </footer>
</div>
