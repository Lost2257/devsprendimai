<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'title_en',
        'subtitle',
        'subtitle_en',
        'slug',
        'client',
        'industry',
        'industry_en',
        'year',
        'duration',
        'duration_en',
        'url',
        'thumbnail',
        'mobile_thumbnail',
        'gallery',
        'description',
        'description_en',
        'featured',
        'services',
        'services_en',
        'technologies',
        'accent_color',
        'challenge',
        'challenge_en',
        'solution',
        'solution_en',
        'result',
        'result_en',
        'metric_one_value',
        'metric_one_label',
        'metric_two_value',
        'metric_two_label',
        'metric_three_value',
        'metric_three_label',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'gallery' => 'array',
    ];

    public function useLocale(string $locale): static
    {
        if ($locale !== 'en') {
            return $this;
        }

        foreach (['title', 'subtitle', 'industry', 'duration', 'description', 'services', 'challenge', 'solution', 'result'] as $field) {
            if ($this->getAttribute("{$field}_en")) {
                $this->setAttribute($field, $this->getAttribute("{$field}_en"));
            }
        }

        return $this;
    }
}
