<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $translations = [
            'autoritmu' => [
                'title_en' => 'Autoritmu',
                'subtitle_en' => 'An automotive events platform connecting the community, organisers and partners in one place.',
                'industry_en' => 'Automotive events',
                'duration_en' => 'Custom project',
                'description_en' => 'A bold platform for car culture, combining event discovery, organiser content and partner visibility in one mobile-first system.',
                'services_en' => 'UX/UI design, WordPress, Event platform, Mobile design',
                'challenge_en' => 'Information about automotive events was scattered across social networks and separate channels. Visitors needed one place to find relevant events quickly, organisers needed a clear presentation tool and partners needed direct access to the right audience.',
                'solution_en' => 'We created an energetic platform inspired by car culture, with clear event cards, categories and organiser actions. A dark visual direction, orange accent and responsive layout preserve the character on both desktop and mobile.',
                'result_en' => 'Autoritmu.lt became a unified digital hub for the automotive community. Content is easy to discover, events are simple to promote and the modular system supports future categories and partner formats.',
            ],
            'e-akademija' => [
                'title_en' => 'E-akademija',
                'subtitle_en' => 'An entrepreneurship learning ecosystem where knowledge, experts and community form a clear growth journey.',
                'industry_en' => 'Business education',
                'duration_en' => 'Custom project',
                'description_en' => 'A bright and trustworthy learning platform with course sales, expert content and a simple learning journey for new and growing businesses.',
                'services_en' => 'UX/UI design, WordPress, E-commerce, Learning platform',
                'challenge_en' => 'Different courses, expert content and the sales process needed to become one trustworthy system. The platform had to feel simple for beginners while clearly presenting a growing catalogue and community content.',
                'solution_en' => 'We created a clean learning platform with a clear course hierarchy, e-commerce functionality and a consistent journey from discovering a programme to active learning. A light visual language and confident colour accents keep attention on the content.',
                'result_en' => 'E-akademija.lt gained a unified digital foundation for selling and expanding its learning programmes. The platform works smoothly on mobile and is ready for new courses and community functionality.',
            ],
            'devsprendimai' => [
                'title_en' => 'DevSprendimai',
                'subtitle_en' => 'A digital studio turning complex business ideas into clear, fast and distinctive products.',
                'industry_en' => 'Digital products',
                'duration_en' => 'Continuously evolving',
                'description_en' => 'The new-generation DevSprendimai platform presents websites, applications, e-commerce, CRM and automation through one coherent modern experience.',
                'services_en' => 'UX/UI design, Website development, Application development, CRM systems, Process automation',
                'challenge_en' => 'A broad range of digital services needed to be united in one clear platform. Visitors had to understand both what we build and the business value behind each solution, without compromising visual quality, performance or mobile usability.',
                'solution_en' => 'We built a modular Laravel and Vue.js platform with a unified design system, clear service paths and smooth Inertia transitions. Mobile was designed as an equal experience, while the portfolio gained immersive 3D case studies controlled through scrolling.',
                'result_en' => 'The result is a distinctive and scalable DevSprendimai platform where services, process, projects and enquiries work as one system. The admin area supports independent case-study creation, content management and website activity monitoring.',
            ],
        ];

        foreach ($translations as $slug => $content) {
            DB::table('projects')->where('slug', $slug)->update($content);
        }
    }

    public function down(): void
    {
        DB::table('projects')->whereIn('slug', ['autoritmu', 'e-akademija', 'devsprendimai'])->update([
            'title_en' => null, 'subtitle_en' => null, 'industry_en' => null, 'duration_en' => null,
            'description_en' => null, 'services_en' => null, 'challenge_en' => null, 'solution_en' => null, 'result_en' => null,
        ]);
    }
};
