<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CreatorProfile;
use App\Models\Review;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /**
     * Landing-page marketing copy for each category card.
     * Not modeled in the database — purely presentational content for this page.
     */
    private function categoryDescriptions(): array
    {
        return [
            'video-production' => __('Свадби, реклами, настани, музички спотови — од концепт до финален фајл.'),
            'photography' => __('Продукт, портрет, настан и уредничка фотографија со целосни права.'),
            'digital-marketing' => __('Стратегија за содржина, раст на социјални мрежи, платено рекламирање.'),
            'video-editing' => __('Колор грејдинг, монтажа, моушн графика — испорака подготвена за објава.'),
            'design' => __('Графички дизајн, брендинг, лого и содржина за социјални мрежи.'),
            'content-creator' => __('UGC реклами, TikTok и Instagram содржина — автентичен глас за брендови и производи.'),
        ];
    }

    public function index(): View
    {
        $descriptions = $this->categoryDescriptions();

        $categories = Category::orderBy('id')->get()->map(function ($category) use ($descriptions) {
            $category->description = $descriptions[$category->slug] ?? '';

            return $category;
        });

        $creators = CreatorProfile::where('verified', true)
            ->whereHas('user', fn ($query) => $query->whereNotNull('avatar_url')->where('avatar_url', '!=', ''))
            ->with(['user.country', 'user.city', 'categories'])
            ->inRandomOrder()
            ->limit(6)
            ->get()
            ->values()
            ->map(function ($profile, $index) {
                $reviews = Review::where('reviewee_id', $profile->user_id)->get();
                $profile->avgRating = $reviews->isNotEmpty() ? round($reviews->avg('rating')) : null;
                $profile->reviewCount = $reviews->count();
                $profile->avatarClass = 'cc-a'.(($index % 6) + 1);

                return $profile;
            });

        return view('welcome', compact('categories', 'creators'));
    }
}
