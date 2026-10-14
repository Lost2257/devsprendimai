<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PortfolioProjectsSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'slug' => 'gorilla-group',
                'media' => [
                    'thumbnail' => ['gorilla-group/hero.png', 'projects/gorilla-group/hero.png'],
                    'mobile_thumbnail' => ['gorilla-group/mobile.png', 'projects/gorilla-group/mobile.png'],
                    'gallery' => [
                        ['gorilla-group/login-desktop.png', 'projects/gorilla-group/login-desktop.png'],
                        ['gorilla-group/login-mobile.png', 'projects/gorilla-group/login-mobile.png'],
                    ],
                ],
                'attributes' => [
                    'title' => 'Gorilla Group',
                    'title_en' => 'Gorilla Group',
                    'subtitle' => 'Maistas, lojalumas ir užsakymas vienoje vietoje',
                    'subtitle_en' => 'Food, rewards and ordering in one place',
                    'client' => 'Gorilla Group',
                    'industry' => 'Maisto užsakymas ir lojalumo sprendimai',
                    'industry_en' => 'Food ordering and loyalty',
                    'year' => '2026',
                    'duration' => 'Individuali apimtis',
                    'duration_en' => 'Custom scope',
                    'url' => 'https://app.gorillagroup.lt/',
                    'description' => 'Vieninga skaitmeninė platforma trims „Gorilla Group“ meniu: nuo patiekalų atradimo ir paieškos iki kliento paskyros, lojalumo taškų bei greito užsakymo skirtinguose ekranuose.',
                    'description_en' => 'A unified digital platform for three Gorilla Group menus, covering food discovery and search, customer accounts, loyalty rewards and fast ordering across devices.',
                    'featured' => true,
                    'services' => 'UX/UI dizainas, responsyvi aplikacija, užsakymo patirtis, lojalumo sistema',
                    'services_en' => 'UX/UI design, responsive application, ordering experience, loyalty system',
                    'technologies' => 'Expo, React Native Web, Google Identity, Responsive UI',
                    'accent_color' => '#FF5A0A',
                    'challenge' => 'Sujungti tris skirtingus restoranų meniu į vieną aiškią patirtį, kurioje lankytojas greitai pasirenka prekės ženklą, atranda patiekalus ir gali naudotis ta pačia paskyra bei lojalumo nauda.',
                    'challenge_en' => 'Bring three restaurant menus into one clear experience where visitors can quickly choose a brand, discover dishes and use the same account and loyalty benefits.',
                    'solution' => 'Sukurta tamsi, išraiškinga ir mobiliam naudojimui pritaikyta sąsaja su vieninga navigacija, kategorijų filtrais, paieška, dvikalbyste, kliento paskyra ir aiškiai matomais užsakymo veiksmais.',
                    'solution_en' => 'A bold, mobile-first dark interface was created with unified navigation, category filters, search, bilingual content, customer accounts and prominent ordering actions.',
                    'result' => 'Vienoje platformoje veikia trys „Gorilla Group“ meniu, o klientas gauna nuoseklią patirtį nuo pirmo patiekalo peržiūros iki prisijungimo, lojalumo pasiūlymų ir užsakymo.',
                    'result_en' => 'Three Gorilla Group menus now live in one platform, giving customers a consistent journey from discovering a dish to signing in, using loyalty offers and ordering.',
                    'metric_one_value' => '3',
                    'metric_one_label' => 'Meniu vienoje platformoje',
                    'metric_two_value' => '2',
                    'metric_two_label' => 'Kalbos',
                    'metric_three_value' => '1',
                    'metric_three_label' => 'Lojalumo ekosistema',
                ],
            ],
            [
                'slug' => 'moteris',
                'media' => [
                    'thumbnail' => ['moteris/hero.png', 'projects/moteris/hero.png'],
                    'mobile_thumbnail' => ['moteris/mobile.png', 'projects/moteris/mobile.png'],
                    'gallery' => [
                        ['moteris/desktop.png', 'projects/moteris/desktop.png'],
                    ],
                ],
                'attributes' => [
                    'title' => 'Moteris.lt',
                    'title_en' => 'Moteris.lt',
                    'subtitle' => 'Šimtametė leidybos tradicija moderniame skaitmeniniame žurnale',
                    'subtitle_en' => 'A century of publishing tradition in a modern digital magazine',
                    'client' => 'GM Žurnalų leidybos grupė',
                    'industry' => 'Žiniasklaida ir skaitmeninė leidyba',
                    'industry_en' => 'Media and digital publishing',
                    'year' => '2026',
                    'duration' => 'Individuali apimtis',
                    'duration_en' => 'Custom scope',
                    'url' => 'https://www.moteris.lt/lt',
                    'description' => 'Didelės apimties skaitmeninio žurnalo patirtis, jungianti aktualų redakcinį turinį, aiškią kategorijų architektūrą, prenumeratą, reklamos integracijas ir patogų skaitymą telefone.',
                    'description_en' => 'A large-scale digital magazine experience combining timely editorial content, clear category architecture, subscriptions, advertising integrations and comfortable mobile reading.',
                    'featured' => true,
                    'services' => 'UX/UI dizainas, front-end programavimas, turinio architektūra, reklamos integracijos',
                    'services_en' => 'UX/UI design, front-end development, content architecture, advertising integrations',
                    'technologies' => 'Responsive Web, Vite, Google Tag Manager, Google Ad Manager',
                    'accent_color' => '#C4474D',
                    'challenge' => 'Didelį kasdien atnaujinamo turinio kiekį pateikti taip, kad skaitytoja greitai rastų aktualias temas, o ilgametė žurnalo tapatybė neprarastų savo išskirtinumo skirtinguose ekranuose.',
                    'challenge_en' => 'Present a large volume of frequently updated content so readers can quickly find relevant topics while preserving the magazine’s long-established identity across devices.',
                    'solution' => 'Sukurta aiški redakcinė hierarchija su ryškia tipografija, teminėmis kategorijomis, aktualijų blokais, paieška, prenumeratos keliais ir responsyviomis istorijų kortelėmis, kurios telefone tampa vientisa skaitymo srove.',
                    'solution_en' => 'A clear editorial hierarchy was built with expressive typography, topic categories, featured stories, search, subscription journeys and responsive story cards that become a seamless mobile reading feed.',
                    'result' => 'Vienoje platformoje sujungtas žurnalo autoritetas ir šiuolaikinė turinio vartojimo patirtis: nuo greito naujienų atradimo iki gilesnio skaitymo, prenumeratos ir šviesios arba tamsios temos pasirinkimo.',
                    'result_en' => 'The platform combines the authority of the magazine with a modern content experience, from fast story discovery to deeper reading, subscription and light or dark viewing modes.',
                    'metric_one_value' => '10+',
                    'metric_one_label' => 'Turinio kategorijų',
                    'metric_two_value' => '1920',
                    'metric_two_label' => 'Leidybos tradicijos pradžia',
                    'metric_three_value' => '2',
                    'metric_three_label' => 'Skaitymo temos',
                ],
            ],
        ];

        foreach ($projects as $projectData) {
            $media = $this->publishMedia($projectData['media']);

            Project::updateOrCreate(
                ['slug' => $projectData['slug']],
                [...$projectData['attributes'], ...$media],
            );
        }
    }

    private function publishMedia(array $media): array
    {
        [$thumbnailSource, $thumbnailPath] = $media['thumbnail'];
        [$mobileSource, $mobilePath] = $media['mobile_thumbnail'];

        $this->copyAsset($thumbnailSource, $thumbnailPath);
        $this->copyAsset($mobileSource, $mobilePath);

        $gallery = [];
        foreach ($media['gallery'] as [$source, $destination]) {
            $this->copyAsset($source, $destination);
            $gallery[] = $destination;
        }

        return [
            'thumbnail' => $thumbnailPath,
            'mobile_thumbnail' => $mobilePath,
            'gallery' => $gallery,
        ];
    }

    private function copyAsset(string $source, string $destination): void
    {
        $sourcePath = database_path("seeders/assets/portfolio-projects/{$source}");

        if (! File::exists($sourcePath)) {
            throw new \RuntimeException("Trūksta projekto vaizdo: {$sourcePath}");
        }

        Storage::disk('public')->put($destination, File::get($sourcePath));
    }
}
