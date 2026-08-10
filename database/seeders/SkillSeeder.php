<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skillsByCategorySlug = [
            'video-production' => [
                'svadbeno-snimanje' => ['mk' => 'Свадбено снимање', 'sr' => 'Снимање свадби', 'hr' => 'Snimanje vjenčanja', 'sq' => 'Xhirim dasmash', 'bg' => 'Заснемане на сватби', 'el' => 'Βιντεοσκόπηση γάμων', 'en' => 'Wedding videography'],
                'nastan-snimanje' => ['mk' => 'Настан снимање', 'sr' => 'Снимање догађаја', 'hr' => 'Snimanje događanja', 'sq' => 'Xhirim eventesh', 'bg' => 'Заснемане на събития', 'el' => 'Βιντεοσκόπηση εκδηλώσεων', 'en' => 'Event videography'],
                'video-snimanje' => ['mk' => 'Видео снимање', 'sr' => 'Видео снимање', 'hr' => 'Video snimanje', 'sq' => 'Xhirim video', 'bg' => 'Видео заснемане', 'el' => 'Βιντεοσκόπηση', 'en' => 'Video filming'],
                'reklama-snimanje' => ['mk' => 'Реклама снимање', 'sr' => 'Снимање реклама', 'hr' => 'Snimanje reklama', 'sq' => 'Xhirim reklamash', 'bg' => 'Заснемане на реклами', 'el' => 'Βιντεοσκόπηση διαφημίσεων', 'en' => 'Ad filming'],
                'dron-snimanje' => ['mk' => 'Дрон снимање', 'sr' => 'Снимање дроном', 'hr' => 'Snimanje dronom', 'sq' => 'Xhirim me dron', 'bg' => 'Заснемане с дрон', 'el' => 'Βιντεοσκόπηση με drone', 'en' => 'Drone filming'],
                'muzicki-spotovi' => ['mk' => 'Музички спотови', 'sr' => 'Музички спотови', 'hr' => 'Glazbeni spotovi', 'sq' => 'Video-klipe muzikore', 'bg' => 'Музикални клипове', 'el' => 'Μουσικά βίντεο κλιπ', 'en' => 'Music videos'],
                'vlog-produkcija' => ['mk' => 'Влог продукција', 'sr' => 'Влог продукција', 'hr' => 'Vlog produkcija', 'sq' => 'Produksion vlogesh', 'bg' => 'Влог продукция', 'el' => 'Παραγωγή vlog', 'en' => 'Vlog production'],
                'podkast-produkcija' => ['mk' => 'Подкаст продукција', 'sr' => 'Подкаст продукција', 'hr' => 'Podcast produkcija', 'sq' => 'Produksion podkastesh', 'bg' => 'Подкаст продукция', 'el' => 'Παραγωγή podcast', 'en' => 'Podcast production'],
                'adobe-premiere-pro' => ['mk' => 'Adobe Premiere Pro', 'sr' => 'Adobe Premiere Pro', 'hr' => 'Adobe Premiere Pro', 'sq' => 'Adobe Premiere Pro', 'bg' => 'Adobe Premiere Pro', 'el' => 'Adobe Premiere Pro', 'en' => 'Adobe Premiere Pro'],
                'capcut' => ['mk' => 'CapCut', 'sr' => 'CapCut', 'hr' => 'CapCut', 'sq' => 'CapCut', 'bg' => 'CapCut', 'el' => 'CapCut', 'en' => 'CapCut'],
                'davinci-resolve' => ['mk' => 'DaVinci Resolve', 'sr' => 'DaVinci Resolve', 'hr' => 'DaVinci Resolve', 'sq' => 'DaVinci Resolve', 'bg' => 'DaVinci Resolve', 'el' => 'DaVinci Resolve', 'en' => 'DaVinci Resolve'],
            ],
            'photography' => [
                'svadbena-fotografija' => ['mk' => 'Свадбена фотографија', 'sr' => 'Свадбена фотографија', 'hr' => 'Vjenčana fotografija', 'sq' => 'Fotografi dasme', 'bg' => 'Сватбена фотография', 'el' => 'Φωτογραφία γάμου', 'en' => 'Wedding photography'],
                'muzicki-spot-fotografija' => ['mk' => 'Музички спот фотографија', 'sr' => 'Фотографија музичких спотова', 'hr' => 'Fotografija glazbenih spotova', 'sq' => 'Fotografi video-klipesh muzikore', 'bg' => 'Фотография на музикални клипове', 'el' => 'Φωτογραφία μουσικών βίντεο κλιπ', 'en' => 'Music video photography'],
                'nastan-fotografija' => ['mk' => 'Настан фотографија', 'sr' => 'Фотографија догађаја', 'hr' => 'Fotografija događanja', 'sq' => 'Fotografi eventesh', 'bg' => 'Фотография на събития', 'el' => 'Φωτογραφία εκδηλώσεων', 'en' => 'Event photography'],
                'rodendenska-fotografija' => ['mk' => 'Роденденска фотографија', 'sr' => 'Рођенданска фотографија', 'hr' => 'Rođendanska fotografija', 'sq' => 'Fotografi ditëlindjesh', 'bg' => 'Фотография на рожден ден', 'el' => 'Φωτογραφία γενεθλίων', 'en' => 'Birthday photography'],
                'krstevka-fotografija' => ['mk' => 'Крштевка фотографија', 'sr' => 'Фотографија крштења', 'hr' => 'Fotografija krštenja', 'sq' => 'Fotografi pagëzimi', 'bg' => 'Фотография на кръщене', 'el' => 'Φωτογραφία βάφτισης', 'en' => 'Christening photography'],
                'vencavka-fotografija' => ['mk' => 'Венчавка фотографија', 'sr' => 'Фотографија венчања', 'hr' => 'Fotografija vjenčanja', 'sq' => 'Fotografi martese', 'bg' => 'Фотография на венчавка', 'el' => 'Φωτογραφία τελετής γάμου', 'en' => 'Wedding ceremony photography'],
                'klasicna-fotosesija' => ['mk' => 'Класична фотосесија', 'sr' => 'Класична фото-сесија', 'hr' => 'Klasična fotosesija', 'sq' => 'Sesion klasik fotografik', 'bg' => 'Класическа фотосесия', 'el' => 'Κλασική φωτογράφιση', 'en' => 'Classic photo shoot'],
                'portret-fotografija' => ['mk' => 'Портрет фотографија', 'sr' => 'Портретна фотографија', 'hr' => 'Portretna fotografija', 'sq' => 'Fotografi portreti', 'bg' => 'Портретна фотография', 'el' => 'Πορτραίτο', 'en' => 'Portrait photography'],
                'studiska-fotografija' => ['mk' => 'Студиска фотографија', 'sr' => 'Студијска фотографија', 'hr' => 'Studijska fotografija', 'sq' => 'Fotografi studioje', 'bg' => 'Студийна фотография', 'el' => 'Φωτογράφιση στούντιο', 'en' => 'Studio photography'],
                'produkt-fotografija' => ['mk' => 'Продукт фотографија', 'sr' => 'Продукт фотографија', 'hr' => 'Produktna fotografija', 'sq' => 'Fotografi produktesh', 'bg' => 'Продуктова фотография', 'el' => 'Φωτογράφιση προϊόντων', 'en' => 'Product photography'],
                'urednicka-fotografija' => ['mk' => 'Уредничка фотографија', 'sr' => 'Уреднička фотографија', 'hr' => 'Urednička fotografija', 'sq' => 'Fotografi editoriale', 'bg' => 'Редакционна фотография', 'el' => 'Συντακτική φωτογραφία', 'en' => 'Editorial photography'],
                'adobe-lightroom' => ['mk' => 'Adobe Lightroom', 'sr' => 'Adobe Lightroom', 'hr' => 'Adobe Lightroom', 'sq' => 'Adobe Lightroom', 'bg' => 'Adobe Lightroom', 'el' => 'Adobe Lightroom', 'en' => 'Adobe Lightroom'],
                'adobe-photoshop' => ['mk' => 'Adobe Photoshop', 'sr' => 'Adobe Photoshop', 'hr' => 'Adobe Photoshop', 'sq' => 'Adobe Photoshop', 'bg' => 'Adobe Photoshop', 'el' => 'Adobe Photoshop', 'en' => 'Adobe Photoshop'],
            ],
            'digital-marketing' => [
                'social-media-menadzment' => ['mk' => 'Social media менаџмент', 'sr' => 'Social media менаџмент', 'hr' => 'Social media menadžment', 'sq' => 'Menaxhim i social media', 'bg' => 'Social media мениджмънт', 'el' => 'Διαχείριση social media', 'en' => 'Social media management'],
                'seo-optimizacija' => ['mk' => 'SEO оптимизација', 'sr' => 'SEO оптимизација', 'hr' => 'SEO optimizacija', 'sq' => 'Optimizim SEO', 'bg' => 'SEO оптимизация', 'el' => 'Βελτιστοποίηση SEO', 'en' => 'SEO optimization'],
                'google-ads' => ['mk' => 'Google Ads', 'sr' => 'Google Ads', 'hr' => 'Google Ads', 'sq' => 'Google Ads', 'bg' => 'Google Ads', 'el' => 'Google Ads', 'en' => 'Google Ads'],
                'facebookinstagram-reklami' => ['mk' => 'Facebook/Instagram реклами', 'sr' => 'Facebook/Instagram рекламе', 'hr' => 'Facebook/Instagram reklame', 'sq' => 'Reklama Facebook/Instagram', 'bg' => 'Facebook/Instagram реклами', 'el' => 'Διαφημίσεις Facebook/Instagram', 'en' => 'Facebook/Instagram ads'],
                'kopirajting' => ['mk' => 'Копирајтинг', 'sr' => 'Копирајтинг', 'hr' => 'Copywriting', 'sq' => 'Copywriting', 'bg' => 'Копирайтинг', 'el' => 'Copywriting', 'en' => 'Copywriting'],
            ],
            'video-editing' => [
                'video-montaza' => ['mk' => 'Видео монтажа', 'sr' => 'Видео монтажа', 'hr' => 'Video montaža', 'sq' => 'Montazh video', 'bg' => 'Видео монтаж', 'el' => 'Μοντάζ βίντεο', 'en' => 'Video editing'],
                'color-grading' => ['mk' => 'Color Grading', 'sr' => 'Color Grading', 'hr' => 'Color Grading', 'sq' => 'Color Grading', 'bg' => 'Color Grading', 'el' => 'Color Grading', 'en' => 'Color Grading'],
                'meta-ads-editing' => ['mk' => 'Meta ADs editing', 'sr' => 'Meta ADs editing', 'hr' => 'Meta ADs editing', 'sq' => 'Meta ADs editing', 'bg' => 'Meta ADs editing', 'el' => 'Meta ADs editing', 'en' => 'Meta ADs editing'],
                'socijalni-mrezi-rils-video-editing' => ['mk' => 'Социјални Мрежи рилс-видео editing', 'sr' => 'Друштвене мреже рилс-видео editing', 'hr' => 'Društvene mreže reels-video editing', 'sq' => 'Editim video-reels për rrjete sociale', 'bg' => 'Социални мрежи рийлс-видео editing', 'el' => 'Επεξεργασία βίντεο reels για social media', 'en' => 'Social media reels video editing'],
                'graphinc-motion' => ['mk' => 'Graphinc Motion', 'sr' => 'Graphinc Motion', 'hr' => 'Graphinc Motion', 'sq' => 'Graphinc Motion', 'bg' => 'Graphinc Motion', 'el' => 'Graphinc Motion', 'en' => 'Graphinc Motion'],
                'zvucen-dizajn' => ['mk' => 'Звучен дизајн', 'sr' => 'Звучни дизајн', 'hr' => 'Zvučni dizajn', 'sq' => 'Dizajn zëri', 'bg' => 'Звуков дизайн', 'el' => 'Ηχητικός σχεδιασμός', 'en' => 'Sound design'],
                'animacija' => ['mk' => 'Анимација', 'sr' => 'Анимација', 'hr' => 'Animacija', 'sq' => 'Animacion', 'bg' => 'Анимация', 'el' => 'Κινούμενα σχέδια', 'en' => 'Animation'],
                'storibording' => ['mk' => 'Сторибординг', 'sr' => 'Сторибординг', 'hr' => 'Storyboarding', 'sq' => 'Storyboarding', 'bg' => 'Сторибординг', 'el' => 'Storyboarding', 'en' => 'Storyboarding'],
                'captions' => ['mk' => 'Captions', 'sr' => 'Captions', 'hr' => 'Captions', 'sq' => 'Captions', 'bg' => 'Captions', 'el' => 'Captions', 'en' => 'Captions'],
                // Legacy skills kept for backward compatibility — creators already have
                // these attached to their profiles. Superseded going forward by
                // 'color-grading', 'graphinc-motion' and 'captions' above.
                'kolor-grejding' => ['mk' => 'Колор грејдинг', 'sr' => 'Колор грејдинг', 'hr' => 'Color grading', 'sq' => 'Color grading', 'bg' => 'Цветова корекция', 'el' => 'Διαβάθμιση χρώματος', 'en' => 'Color grading'],
                'mousn-grafik' => ['mk' => 'Моушн график', 'sr' => 'Моушн график', 'hr' => 'Motion graphics', 'sq' => 'Motion graphics', 'bg' => 'Моушън графика', 'el' => 'Motion graphics', 'en' => 'Motion graphics'],
                'titluvanje' => ['mk' => 'Титлување', 'sr' => 'Титловање', 'hr' => 'Titlovanje', 'sq' => 'Titrim', 'bg' => 'Субтитриране', 'el' => 'Υποτιτλισμός', 'en' => 'Subtitling'],
            ],
            'design' => [
                'logo-dizajn' => ['mk' => 'Лого дизајн', 'sr' => 'Лого дизајн', 'hr' => 'Dizajn logotipa', 'sq' => 'Dizajn logoje', 'bg' => 'Лого дизайн', 'el' => 'Σχεδιασμός λογότυπου', 'en' => 'Logo design'],
                'brending' => ['mk' => 'Брендинг', 'sr' => 'Брендинг', 'hr' => 'Brendiranje', 'sq' => 'Brendim', 'bg' => 'Брандинг', 'el' => 'Branding', 'en' => 'Branding'],
                'uiux-dizajn' => ['mk' => 'UI/UX дизајн', 'sr' => 'UI/UX дизајн', 'hr' => 'UI/UX dizajn', 'sq' => 'Dizajn UI/UX', 'bg' => 'UI/UX дизайн', 'el' => 'Σχεδιασμός UI/UX', 'en' => 'UI/UX design'],
                'enterier-dizajn' => ['mk' => 'Ентериер Дизајн', 'sr' => 'Дизајн ентеријера', 'hr' => 'Dizajn interijera', 'sq' => 'Dizajn i brendshëm', 'bg' => 'Дизайн на интериор', 'el' => 'Σχεδιασμός εσωτερικών χώρων', 'en' => 'Interior design'],
                'ilustracija' => ['mk' => 'Илустрација', 'sr' => 'Илустрација', 'hr' => 'Ilustracija', 'sq' => 'Ilustrim', 'bg' => 'Илюстрация', 'el' => 'Εικονογράφηση', 'en' => 'Illustration'],
                'grafiki-za-socijalni-mrezi' => ['mk' => 'Графики за социјални мрежи', 'sr' => 'Графике за друштвене мреже', 'hr' => 'Grafike za društvene mreže', 'sq' => 'Grafika për rrjete sociale', 'bg' => 'Графики за социални мрежи', 'el' => 'Γραφικά για social media', 'en' => 'Social media graphics'],
                'grafichki-dizajn' => ['mk' => 'Графички дизајн', 'sr' => 'Графички дизајн', 'hr' => 'Grafički dizajn', 'sq' => 'Dizajn grafik', 'bg' => 'Графичен дизайн', 'el' => 'Γραφιστικός σχεδιασμός', 'en' => 'Graphic design'],
            ],
            'content-creator' => [
                'ugc-reklami' => ['mk' => 'UGC реклами', 'sr' => 'UGC рекламе', 'hr' => 'UGC reklame', 'sq' => 'Reklama UGC', 'bg' => 'UGC реклами', 'el' => 'Διαφημίσεις UGC', 'en' => 'UGC ads'],
                'tiktok-sodrzina' => ['mk' => 'TikTok содржина', 'sr' => 'TikTok садржај', 'hr' => 'TikTok sadržaj', 'sq' => 'Përmbajtje TikTok', 'bg' => 'TikTok съдържание', 'el' => 'Περιεχόμενο TikTok', 'en' => 'TikTok content'],
                'instagram-reels' => ['mk' => 'Instagram Reels', 'sr' => 'Instagram Reels', 'hr' => 'Instagram Reels', 'sq' => 'Instagram Reels', 'bg' => 'Instagram Reels', 'el' => 'Instagram Reels', 'en' => 'Instagram Reels'],
                'unboxing-pregled-produkt' => ['mk' => 'Unboxing / преглед на производ', 'sr' => 'Unboxing / преглед производа', 'hr' => 'Unboxing / recenzija proizvoda', 'sq' => 'Unboxing / vlerësim produkti', 'bg' => 'Unboxing / преглед на продукт', 'el' => 'Unboxing / αξιολόγηση προϊόντος', 'en' => 'Unboxing / product review'],
                'sponsored-content' => ['mk' => 'Sponsored content', 'sr' => 'Sponsored content', 'hr' => 'Sponsored content', 'sq' => 'Sponsored content', 'bg' => 'Sponsored content', 'el' => 'Sponsored content', 'en' => 'Sponsored content'],
                'raskazuvanje-kratki-videa' => ['mk' => 'Раскажување за кратки видеа', 'sr' => 'Приповедање за кратке видео снимке', 'hr' => 'Pripovijedanje za kratke videe', 'sq' => 'Rrëfim për video të shkurtra', 'bg' => 'Разказвачество за кратки видеа', 'el' => 'Αφήγηση για σύντομα βίντεο', 'en' => 'Short-video storytelling'],
                'trendovi-predizvici' => ['mk' => 'Трендови и предизвици', 'sr' => 'Трендови и изазови', 'hr' => 'Trendovi i izazovi', 'sq' => 'Trende dhe sfida', 'bg' => 'Тенденции и предизвикателства', 'el' => 'Τάσεις και προκλήσεις', 'en' => 'Trends and challenges'],
            ],
        ];

        foreach ($skillsByCategorySlug as $categorySlug => $skills) {
            $category = Category::where('slug', $categorySlug)->first();

            if (! $category) {
                continue;
            }

            foreach ($skills as $slug => $name) {
                $skill = Skill::updateOrCreate(['slug' => $slug], ['name' => $name]);
                $skill->categories()->syncWithoutDetaching([$category->id]);
            }
        }

        // Skills that are also relevant to a category beyond the primary one
        // they were defined under above — this is exactly what the
        // skill_category pivot exists for, no duplicate skill needed.
        $additionalCategoryAttachments = [
            'vlog-produkcija' => ['content-creator'],
            'adobe-premiere-pro' => ['video-editing'],
            'capcut' => ['video-editing'],
            'davinci-resolve' => ['video-editing'],
        ];

        foreach ($additionalCategoryAttachments as $skillSlug => $categorySlugs) {
            $skill = Skill::where('slug', $skillSlug)->first();

            if (! $skill) {
                continue;
            }

            $categoryIds = Category::whereIn('slug', $categorySlugs)->pluck('id');
            $skill->categories()->syncWithoutDetaching($categoryIds);
        }
    }
}
