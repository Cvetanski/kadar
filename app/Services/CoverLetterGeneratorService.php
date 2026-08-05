<?php

namespace App\Services;

use App\Models\CreatorProfile;
use App\Models\Project;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class CoverLetterGeneratorService
{
    /**
     * Draft a cover-letter-style proposal message for a creator applying to
     * a project, grounded only in the project's and creator's real data.
     * Returns null on any failure so the caller can fall back gracefully
     * (the creator can always just type the message themselves).
     */
    public function generate(Project $project, CreatorProfile $creatorProfile): ?string
    {
        $apiKey = config('services.anthropic.api_key');

        if (! $apiKey) {
            return null;
        }

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])
                ->post('https://api.anthropic.com/v1/messages', [
                    'model' => config('services.anthropic.model'),
                    'max_tokens' => 400,
                    'messages' => [
                        ['role' => 'user', 'content' => $this->buildPrompt($project, $creatorProfile)],
                    ],
                ]);

            if (! $response->successful()) {
                Log::warning('Cover letter generation failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $text = $response->json('content.0.text');

            return $text ? trim($text) : null;
        } catch (Throwable $e) {
            Log::warning('Cover letter generation failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    private function buildPrompt(Project $project, CreatorProfile $creatorProfile): string
    {
        $user = $creatorProfile->user;

        $categories = $project->categories->pluck('name')->join(', ') ?: '—';
        $requiredSkills = $project->skills->pluck('name')->join(', ') ?: '—';
        $creatorCategories = $creatorProfile->categories->pluck('name')->join(', ') ?: '—';
        $creatorSkills = $creatorProfile->skills->pluck('name')->join(', ') ?: '—';
        $budget = ($project->budget_min || $project->budget_max)
            ? trim(($project->budget_min ?? '?').'–'.($project->budget_max ?? '?').' EUR')
            : 'по договор';

        $locale = app()->getLocale();

        return <<<PROMPT
            Ти помагаш на фриленсер креативец да напише кратка, професионална и убедлива придружна порака (cover letter) за аплицирање на проект на маркетплејс платформа. Пиши во прво лице во име на креативецот, врз основа САМО на податоците дадени подолу. Не измислувај искуство, вештини или детали што не се наведени подолу. Не додавај формално обраќање надвор од природен почеток и крај на пораката. Одговори ЕДИНСТВЕНО со текстот на пораката, без воведни коментари, без наслов, без markdown форматирање. Пиши на јазик со ISO код "{$locale}". Максимум 130 зборови.

            ПРОЕКТ:
            Наслов: {$project->title}
            Опис: {$project->description}
            Категории: {$categories}
            Потребни вештини: {$requiredSkills}
            Буџет: {$budget}

            КРЕАТИВЕЦ:
            Име: {$user->name}
            Наслов/специјалност: {$creatorProfile->headline}
            Био: {$creatorProfile->bio}
            Категории: {$creatorCategories}
            Вештини: {$creatorSkills}
            Години искуство: {$creatorProfile->experience_years}
            PROMPT;
    }
}
