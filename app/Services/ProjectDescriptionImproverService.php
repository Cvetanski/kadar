<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ProjectDescriptionImproverService
{
    /**
     * Polish a client's project title and description (grammar, clarity,
     * appeal) without inventing new requirements, budget or deadlines.
     * Returns null on any failure so the caller can leave the client's
     * original text untouched.
     *
     * @return array{title: string, description: string}|null
     */
    public function improve(string $title, string $description, array $categoryNames): ?array
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
                    'max_tokens' => 500,
                    'messages' => [
                        ['role' => 'user', 'content' => $this->buildPrompt($title, $description, $categoryNames)],
                    ],
                ]);

            if (! $response->successful()) {
                Log::warning('Project description improvement failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $text = $response->json('content.0.text');

            return $text ? $this->parse($text) : null;
        } catch (Throwable $e) {
            Log::warning('Project description improvement failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    private function parse(string $text): ?array
    {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?|```$/m', '', $text);
        $text = trim($text);

        $decoded = json_decode($text, true);

        if (! is_array($decoded) || ! isset($decoded['title'], $decoded['description'])) {
            return null;
        }

        return [
            'title' => trim((string) $decoded['title']),
            'description' => trim((string) $decoded['description']),
        ];
    }

    private function buildPrompt(string $title, string $description, array $categoryNames): string
    {
        $locale = app()->getLocale();
        $categories = $categoryNames !== [] ? implode(', ', $categoryNames) : '—';

        return <<<PROMPT
            Ти помагаш на клиент да го подобри насловот и описот на оглас за проект на маркетплејс платформа што поврзува клиенти со креативци (видеографи, фотографи, дизајнери, дигитални маркетери, едитори). Подобри ги граматички, стилски и структурно, направи го текстот појасен, попрофесионален и попривлечен за креативци. НЕ измислувај нови барања, буџет, рокови, локации или детали што не се спомнати во оригиналот, задржи ја истата суштина и намера. Одговори ЕДИНСТВЕНО со валиден JSON во следниов формат, без markdown ограда, без дополнителен текст пред или по него: {"title": "...", "description": "..."}. Пиши на јазик со ISO код "{$locale}".

            Категории: {$categories}

            ОРИГИНАЛЕН НАСЛОВ: {$title}
            ОРИГИНАЛЕН ОПИС: {$description}
            PROMPT;
    }
}
