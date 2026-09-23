<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Country;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeds 50 realistic-but-fictional open projects across all 6 categories
 * and a wide spread of countries, so the marketplace doesn't look empty
 * before real client demand ramps up. Each project gets its own fake
 * client account (role=client, is_legacy_free=true so it never hits the
 * freemium limits and never shows an upgrade prompt to real creators
 * browsing). Safe to re-run — skipped per-entry if the client email
 * already exists.
 */
class FakeProjectSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::pluck('id', 'slug');
        $countryIds = Country::pluck('id', 'code');

        foreach ($this->projects() as $data) {
            $email = 'client.'.Str::slug($data['client']).'.'.$data['id'].'@seed.creatorspot.internal';

            if (User::where('email', $email)->exists()) {
                continue;
            }

            $countryId = $countryIds[$data['country']] ?? null;

            $client = User::create([
                'name' => $data['client'],
                'email' => $email,
                'password' => Hash::make(Str::random(40)),
                'email_verified_at' => now(),
                'role' => 'client',
                'is_legacy_free' => true,
                'country_id' => $countryId,
            ]);

            $project = Project::create([
                'client_id' => $client->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'budget_min' => $data['budget_min'] ?? null,
                'budget_max' => $data['budget_max'] ?? null,
                'deadline' => isset($data['deadline_days']) ? now()->addDays($data['deadline_days']) : null,
                'country_id' => $countryId,
                'remote_ok' => $data['remote_ok'],
                'status' => 'open',
            ]);

            $project->categories()->sync([$categoryIds[$data['category']]]);

            $createdAt = now()->subDays($data['created_days_ago']);
            $project->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();
        }
    }

    private function projects(): array
    {
        return [
            // Video Production
            ['id' => 1, 'client' => 'Lukas Hoffmann', 'country' => 'DE', 'category' => 'video-production', 'remote_ok' => false, 'budget_min' => 1500, 'budget_max' => 3500, 'deadline_days' => 30, 'created_days_ago' => 2,
                'title' => 'Corporate Brand Video for a Berlin Fintech Startup',
                'description' => "We're a fast-growing fintech startup based in Berlin and we need a polished 2-3 minute brand video to use on our website and at investor meetings. The video should mix interviews with our founding team, b-roll of our office and product in action, and a clean voiceover tying it all together. We're open to your creative direction on structure and pacing, but we want something that feels premium, not corporate-stiff. Ideally you have experience filming in office/startup environments and can handle both filming and a first-pass edit. We can provide a shot list, but we're also happy to lean on your expertise. Please share examples of similar brand videos you've made, along with your day rate and estimated turnaround."],
            ['id' => 2, 'client' => 'Giulia Romano', 'country' => 'IT', 'category' => 'video-production', 'remote_ok' => false, 'budget_min' => 2000, 'budget_max' => 4500, 'deadline_days' => 60, 'created_days_ago' => 9,
                'title' => 'Destination Wedding Videography in Tuscany',
                'description' => "We're getting married at a vineyard estate outside Florence in the fall and are looking for a videographer to capture the full day — getting ready, ceremony, and reception. We'd love a highlight reel of 4-6 minutes plus a longer 20-30 minute documentary-style cut we can watch years from now. Drone shots of the venue and surrounding hills would be a huge bonus if you have the equipment and permits sorted. We're flexible on style but lean toward warm, cinematic color grading rather than anything too stylized. Travel and accommodation for the weekend would be covered separately from your fee. Please send a couple of full wedding videos you've delivered recently, not just highlight reels."],
            ['id' => 3, 'client' => 'Emily Carter', 'country' => 'US', 'category' => 'video-production', 'remote_ok' => false, 'budget_min' => 800, 'budget_max' => 2000, 'deadline_days' => 14, 'created_days_ago' => 0,
                'title' => '60-Second Product Launch Ad for a DTC Brand',
                'description' => "Launching a new product next month and need a punchy 60-second ad for social and paid media. Should feel fast-paced and scroll-stopping, similar to the ads you'd see from a DTC brand on Instagram. We'll provide the product and rough messaging, you handle the filming and edit."],
            ['id' => 4, 'client' => 'Oliver Bennett', 'country' => 'GB', 'category' => 'video-production', 'remote_ok' => false, 'budget_min' => 1200, 'budget_max' => 3000, 'deadline_days' => 45, 'created_days_ago' => 5,
                'title' => "Music Video for an Indie Artist's Debut Single",
                'description' => "I'm an independent artist releasing my debut single and I'm looking for someone to direct and shoot a music video that matches the mood of the track — moody, a bit cinematic, shot mostly at night with practical lighting. I have a rough concept in mind involving abandoned industrial locations but I'm very open to your creative input since this is your craft, not mine. Budget covers your time and equipment; I can help scout locations and coordinate a small crew of friends as extras if needed. Turnaround doesn't need to be rushed — quality over speed here. If you've directed music videos before, please share 2-3 links along with a rough treatment idea after we chat."],
            ['id' => 5, 'client' => 'Ahmed Al Mansouri', 'country' => 'AE', 'category' => 'video-production', 'remote_ok' => false, 'budget_min' => 500, 'budget_max' => 1500, 'deadline_days' => 21, 'created_days_ago' => 13,
                'title' => 'Real Estate Walkthrough Videos — 10 Luxury Listings',
                'description' => 'Need smooth walkthrough videos for 10 luxury properties in Dubai, roughly 60-90 seconds each. Gimbal or stabilized footage only, please — no handheld. Quick turnaround needed since listings go live on a rolling basis.'],
            ['id' => 6, 'client' => 'Wei Ling Tan', 'country' => 'SG', 'category' => 'video-production', 'remote_ok' => false, 'budget_min' => 1800, 'budget_max' => 4000, 'deadline_days' => 30, 'created_days_ago' => 4,
                'title' => '2-Day Tech Conference Coverage in Singapore',
                'description' => "We're organizing a two-day tech conference in Singapore with around 400 attendees and need full video coverage — keynote sessions, panel discussions, and candid crowd/networking shots. We'd like a same-day recap reel each evening for social media, plus a polished highlight video within a week after the event wraps. You'll need your own gear including at least two cameras for multi-angle keynote coverage and a wireless mic setup for speakers. We can provide a run-of-show in advance and a badge for venue access. Experience covering conferences or corporate events is a must — please include relevant work samples when you apply."],
            ['id' => 7, 'client' => 'Dimitris Papadopoulos', 'country' => 'GR', 'category' => 'video-production', 'remote_ok' => false, 'budget_min' => 600, 'budget_max' => 1800, 'deadline_days' => 30, 'created_days_ago' => 18,
                'title' => 'Drone Footage for a Greek Islands Tourism Campaign',
                'description' => 'Looking for a licensed drone operator to capture aerial footage of coastline and villages across two Greek islands for a tourism board campaign. Need someone comfortable traveling between islands over about a week. Please confirm you hold a valid drone operator license for Greece.'],
            ['id' => 8, 'client' => 'Camille Dubois', 'country' => 'FR', 'category' => 'video-production', 'remote_ok' => false, 'budget_min' => 700, 'budget_max' => 1600, 'deadline_days' => 20, 'created_days_ago' => 1,
                'title' => 'Promo Video Series for a Paris Restaurant Group',
                'description' => 'We run a small group of restaurants in Paris and want 3-4 short promo videos, one per location, showing the food, atmosphere, and chef in action. Roughly 30-45 seconds each, optimized for Instagram and TikTok. Filming can happen during off-peak hours across two weeks.'],
            ['id' => 9, 'client' => 'Haruto Sato', 'country' => 'JP', 'category' => 'video-production', 'remote_ok' => false, 'budget_min' => 300, 'budget_max' => 800, 'deadline_days' => 14, 'created_days_ago' => 7,
                'title' => 'Anniversary Video Montage from Old Home Footage',
                'description' => "I have several hours of old home videos and photos I'd like turned into a 10-minute montage for my parents' 40th anniversary. Looking for someone with a good eye for pacing and music selection. Footage will be shared digitally, no on-location filming needed."],

            // Photography
            ['id' => 10, 'client' => 'Sanne de Vries', 'country' => 'NL', 'category' => 'photography', 'remote_ok' => false, 'budget_min' => 400, 'budget_max' => 1200, 'deadline_days' => 21, 'created_days_ago' => 3,
                'title' => 'Product Photography for an E-commerce Store — 50 SKUs',
                'description' => "We're relaunching our online store and need clean, consistent product photography for about 50 SKUs — mostly home goods and small accessories. We need both plain white-background shots for the product listings and a handful of styled lifestyle shots per hero product. You'll have access to a small studio space in Amsterdam, or you're welcome to bring your own setup. Fast turnaround matters here since our launch date is fixed, so please only apply if you can realistically deliver within three weeks. Retouching should be included — clean backgrounds, no dust or scratches, accurate color matching to the physical products. Please share your e-commerce photography portfolio when applying."],
            ['id' => 11, 'client' => 'Michael Turner', 'country' => 'US', 'category' => 'photography', 'remote_ok' => false, 'budget_min' => 500, 'budget_max' => 1000, 'deadline_days' => 14, 'created_days_ago' => 0,
                'title' => 'Corporate Headshots for a Law Firm — 15 People',
                'description' => 'Our firm needs professional headshots for 15 attorneys and staff, ideally shot in one day at our downtown office. Clean, neutral background, consistent lighting across all subjects. Quick delivery of edited files preferred — within a week if possible.'],
            ['id' => 12, 'client' => 'Marta Fernández', 'country' => 'ES', 'category' => 'photography', 'remote_ok' => false, 'budget_min' => 1500, 'budget_max' => 3000, 'deadline_days' => 90, 'created_days_ago' => 11,
                'title' => 'Full-Day Wedding Photography Coverage',
                'description' => "We're getting married outside Barcelona next spring and need a photographer for the full day, from getting-ready shots through the reception dance floor. We love a natural, documentary style over heavily posed shots, though we'll want a short window for family portraits too. Around 400-500 edited photos delivered within a few weeks after the wedding would be ideal, with a smaller sneak-peek gallery within 48 hours if possible. The venue is a countryside estate with beautiful natural light, so experience shooting outdoors is a plus. Please send a couple of full wedding galleries, not just highlight selections, so we can see your full range."],
            ['id' => 13, 'client' => 'Ji-hoon Park', 'country' => 'KR', 'category' => 'photography', 'remote_ok' => false, 'budget_min' => 800, 'budget_max' => 2000, 'deadline_days' => 21, 'created_days_ago' => 6,
                'title' => 'Fashion Lookbook Photoshoot for a Seoul Streetwear Brand',
                'description' => "We're shooting a lookbook for our next streetwear drop, roughly 20 looks on one model against both studio and urban backdrops in Seoul. Looking for a photographer who can also direct posing and styling on set. Please share recent lookbook or editorial work."],
            ['id' => 14, 'client' => 'Ryan Mitchell', 'country' => 'CA', 'category' => 'photography', 'remote_ok' => false, 'budget_min' => 300, 'budget_max' => 900, 'deadline_days' => 30, 'created_days_ago' => 16,
                'title' => 'Real Estate Photography — Luxury Listings in Toronto',
                'description' => 'Ongoing need for a photographer to shoot 3-4 luxury home listings per month around the Toronto area. Interior, exterior, and twilight shots preferred where possible. Looking to build a regular working relationship if the first few shoots go well.'],
            ['id' => 15, 'client' => 'Elin Karlsson', 'country' => 'SE', 'category' => 'photography', 'remote_ok' => false, 'budget_min' => 400, 'budget_max' => 1000, 'deadline_days' => 21, 'created_days_ago' => 2,
                'title' => 'Food Photography for a Stockholm Restaurant Menu Relaunch',
                'description' => "We're relaunching our menu and need appetizing, well-lit photos of around 25 dishes for both print menus and our website. Styling experience is a big plus since we don't have a dedicated food stylist on staff, though our chef can help plate dishes on shoot day. We'd like a mix of top-down and 45-degree angle shots, natural light preferred if our dining room windows cooperate, otherwise your own lighting setup. Shoot would take place over one full day at the restaurant before opening hours. Please include food photography specifically in your portfolio when you apply, not just general product work."],
            ['id' => 16, 'client' => 'Yuki Tanaka', 'country' => 'JP', 'category' => 'photography', 'remote_ok' => false, 'budget_min' => 900, 'budget_max' => 2200, 'deadline_days' => 30, 'created_days_ago' => 8,
                'title' => 'Editorial Portrait Series for a Tokyo Magazine',
                'description' => 'We need an editorial-style portrait series of a local musician for an upcoming magazine feature, roughly 15-20 final images across a few different setups. Comfortable working with art direction from our editorial team. Studio access can be arranged if you don\'t have your own.'],
            ['id' => 17, 'client' => 'Priya Nair', 'country' => 'SG', 'category' => 'photography', 'remote_ok' => false, 'budget_min' => 500, 'budget_max' => 1200, 'deadline_days' => 21, 'created_days_ago' => 19,
                'title' => 'Startup Team Headshots and Office Photos',
                'description' => 'Small startup team of about 12 people needs professional headshots plus a handful of candid office/culture shots for our website and LinkedIn. Half-day shoot at our office in Singapore. Quick turnaround appreciated since we\'re updating our site soon.'],

            // Digital Marketing
            ['id' => 18, 'client' => 'David Reynolds', 'country' => 'US', 'category' => 'digital-marketing', 'remote_ok' => true, 'budget_min' => 1000, 'budget_max' => 3000, 'deadline_days' => 30, 'created_days_ago' => 1,
                'title' => 'Full-Funnel Google Ads Campaign for a B2B SaaS Company',
                'description' => "We're a B2B SaaS company looking to scale our Google Ads spend and need someone experienced with full-funnel campaign structure — search, display retargeting, and possibly YouTube. We currently run a small campaign in-house but haven't seen the ROAS we need to justify scaling further, so we want a fresh set of eyes on our account structure, keyword strategy, and landing page alignment. Ongoing management over a few months is the goal, not a one-off audit, though we're happy to start with an audit and strategy phase first. You'd be working closely with our small marketing team on messaging and creative. Please share relevant case studies or results from similar B2B accounts you've managed."],
            ['id' => 19, 'client' => 'Chloe Anderson', 'country' => 'AU', 'category' => 'digital-marketing', 'remote_ok' => true, 'budget_min' => 600, 'budget_max' => 1800, 'deadline_days' => 14, 'created_days_ago' => 10,
                'title' => 'Instagram and Facebook Ads Management — 3 Month Engagement',
                'description' => "Looking for someone to manage our Meta ads for the next three months, including creative testing and weekly optimization. We'll provide product photos and basic brand guidelines. Experience with e-commerce brands specifically is a plus."],
            ['id' => 20, 'client' => 'James Whitfield', 'country' => 'GB', 'category' => 'digital-marketing', 'remote_ok' => true, 'budget_min' => 500, 'budget_max' => 1500, 'deadline_days' => 21, 'created_days_ago' => 4,
                'title' => 'SEO Audit and Strategy for an E-commerce Site',
                'description' => "Our organic traffic has been flat for the past year and we suspect there are technical and content issues holding us back. We're looking for a thorough SEO audit covering technical health, site structure, keyword gaps, and competitor comparison, followed by a prioritized action plan we can actually execute against. We're not necessarily looking for ongoing management right away — a strong audit and strategy document is the main deliverable for now, though we may bring you on longer-term if it goes well. Access to our Google Search Console and Analytics can be arranged once we agree on scope. Please share an example of a past audit (anonymized is fine) so we can see your approach and level of detail."],
            ['id' => 21, 'client' => 'Rohan Mehta', 'country' => 'IN', 'category' => 'digital-marketing', 'remote_ok' => true, 'budget_min' => 300, 'budget_max' => 900, 'deadline_days' => 14, 'created_days_ago' => 0,
                'title' => 'Social Media Management for a Restaurant Chain',
                'description' => 'We run a small chain of casual dining restaurants and need consistent Instagram and Facebook posting, roughly 4-5 posts a week across platforms. We can supply food and location photos; you handle captions, scheduling, and light community management. Looking for someone who can start relatively soon.'],
            ['id' => 22, 'client' => 'Anna Schmidt', 'country' => 'DE', 'category' => 'digital-marketing', 'remote_ok' => true, 'budget_min' => 400, 'budget_max' => 1200, 'deadline_days' => 21, 'created_days_ago' => 14,
                'title' => 'Email Marketing Automation Setup for an Online Store',
                'description' => 'We need someone to set up automated email flows — welcome series, abandoned cart, and post-purchase — in Klaviyo for our online store. We have brand assets ready to go, just need the strategy and build-out. Experience with Klaviyo specifically is preferred over general email tools.'],
            ['id' => 23, 'client' => 'Mei Lin Goh', 'country' => 'SG', 'category' => 'digital-marketing', 'remote_ok' => true, 'budget_min' => 700, 'budget_max' => 2000, 'deadline_days' => 21, 'created_days_ago' => 6,
                'title' => 'TikTok Ads Strategy for a D2C Beauty Brand',
                'description' => "We're a direct-to-consumer beauty brand looking to seriously invest in TikTok ads for the first time and need someone who understands the platform's native ad style, not just repurposed Instagram creative. Deliverables would include a content strategy, a handful of ad concepts or scripts, and guidance on our creative testing process once we start running spend. We're open to bringing you on for ongoing creative strategy afterward if this initial phase goes well. We have UGC creators lined up already for filming, so this role is more strategy and campaign structure than production. Please share any TikTok ad results or case studies you can share, even under NDA-safe general terms."],
            ['id' => 24, 'client' => "Sean O'Brien", 'country' => 'IE', 'category' => 'digital-marketing', 'remote_ok' => true, 'budget_min' => 500, 'budget_max' => 1400, 'deadline_days' => 21, 'created_days_ago' => 17,
                'title' => 'Content Marketing Strategy and Copywriting for a B2B Blog',
                'description' => 'We want to start publishing regular blog content targeting our B2B audience but need help with both strategy and the actual writing. Ideally 4 articles a month to start, roughly 1200-1500 words each. SEO awareness is a plus but strong, clear writing matters most.'],
            ['id' => 25, 'client' => 'Fatima Al Zaabi', 'country' => 'AE', 'category' => 'digital-marketing', 'remote_ok' => true, 'budget_min' => 1000, 'budget_max' => 2500, 'deadline_days' => 30, 'created_days_ago' => 3,
                'title' => 'Influencer Marketing Campaign Management for a Skincare Launch',
                'description' => "We're launching a new skincare line in the Gulf region and want to run a coordinated micro-influencer campaign across Instagram and TikTok. We're looking for someone to handle influencer sourcing, outreach, negotiation, and campaign tracking — we have a budget set aside for influencer fees separate from your management fee. Ideally you already have relationships with beauty and lifestyle micro-influencers in the region, or a solid process for vetting and onboarding new ones quickly. Reporting on reach, engagement, and any trackable sales impact would be expected at the end of the campaign. Please share results from a past influencer campaign you managed, including rough scale (number of influencers, reach) if you're able to."],

            // Video Editing
            ['id' => 26, 'client' => 'Brandon Lee', 'country' => 'US', 'category' => 'video-editing', 'remote_ok' => true, 'budget_min' => 800, 'budget_max' => 2000, 'created_days_ago' => 5,
                'title' => 'Weekly YouTube Video Editing — Ongoing Retainer',
                'description' => "We publish two YouTube videos a week and need a reliable editor to take raw footage and turn it into a polished final cut — trimming, pacing, basic color correction, captions, and simple motion graphics for lower thirds and transitions. We'll provide a style guide and a couple of reference edits so you understand our pacing preferences early on. This is meant to be an ongoing weekly arrangement rather than a one-off project, so we're looking for someone reliable who can commit to a consistent turnaround time each week. Raw footage would be shared via cloud storage shortly after each recording session. Please share a recent YouTube edit you've done, ideally in a similar style — fast-paced talking head content."],
            ['id' => 27, 'client' => 'Antoine Moreau', 'country' => 'FR', 'category' => 'video-editing', 'remote_ok' => true, 'budget_min' => 500, 'budget_max' => 1200, 'deadline_days' => 21, 'created_days_ago' => 12,
                'title' => 'Color Grading for an Independent Short Film',
                'description' => "We've finished shooting a 15-minute short film and need a colorist to grade it — going for a warm, slightly desaturated look inspired by a couple of reference films we can share. DaVinci Resolve project files can be provided. Festival submission deadline means we need this within three weeks."],
            ['id' => 28, 'client' => 'Jordan Campbell', 'country' => 'CA', 'category' => 'video-editing', 'remote_ok' => true, 'budget_min' => 300, 'budget_max' => 800, 'created_days_ago' => 1,
                'title' => 'Weekly Podcast Video Editing',
                'description' => 'We record a weekly video podcast and need someone to edit the raw multi-camera footage into a clean final cut with basic graphics and captions. Roughly 45-60 minutes of raw footage per episode. Looking for a consistent weekly turnaround, ideally within 2-3 days of receiving the files.'],
            ['id' => 29, 'client' => 'Tomas Bakker', 'country' => 'NL', 'category' => 'video-editing', 'remote_ok' => true, 'budget_min' => 600, 'budget_max' => 1500, 'deadline_days' => 21, 'created_days_ago' => 8,
                'title' => 'Motion Graphics for a Product Explainer Video',
                'description' => "We have a voiceover script and rough storyboard for a 90-second product explainer video and need someone to bring it to life with motion graphics — think clean, modern animated icons, text, and simple character or object animation rather than a fully illustrated 2D style. We can provide our brand colors, fonts, and logo files, along with the finished voiceover audio. Open to your input on pacing and how literally to follow the storyboard versus improving it. This will be used on our homepage and in sales calls, so quality matters more than speed here, though we do have a rough three-week window we're hoping to hit. Please share 1-2 explainer or motion graphics reels in a similar clean, modern style."],
            ['id' => 30, 'client' => 'Francesca Bianchi', 'country' => 'IT', 'category' => 'video-editing', 'remote_ok' => true, 'budget_min' => 400, 'budget_max' => 1000, 'deadline_days' => 30, 'created_days_ago' => 15,
                'title' => 'Wedding Video Editing from Raw Footage',
                'description' => "I filmed my sister's wedding myself but need help editing the raw footage into a proper 15-20 minute video with music and some basic color correction. Roughly 4 hours of footage across two cameras. Not looking for anything overly stylized, just clean and well-paced."],
            ['id' => 31, 'client' => 'Khalid Rashid', 'country' => 'AE', 'category' => 'video-editing', 'remote_ok' => true, 'budget_min' => 500, 'budget_max' => 1300, 'created_days_ago' => 3,
                'title' => '30 Instagram Reels per Month for a Fitness Brand',
                'description' => 'We shoot raw workout and lifestyle footage regularly and need someone to turn it into short, punchy Instagram Reels — roughly 30 per month. Trending audio, captions, and quick cuts are the style we\'re going for. Looking for an ongoing monthly arrangement, not a one-off.'],
            ['id' => 32, 'client' => 'Felix Wagner', 'country' => 'DE', 'category' => 'video-editing', 'remote_ok' => true, 'budget_min' => 1200, 'budget_max' => 3000, 'deadline_days' => 45, 'created_days_ago' => 9,
                'title' => 'Documentary Rough Cut and Sound Design',
                'description' => "We've shot roughly 20 hours of footage for a short documentary about a local community project and need an experienced editor to help shape it into a 25-30 minute rough cut, followed by sound design and mixing once the picture edit is locked. This is a story-driven project, so strong instincts for pacing and narrative structure matter more than flashy effects. We have interview transcripts available to help with the selection process, though you'll need to review footage yourself to really get a feel for it. Sound design should include cleaning up on-location audio, light music scoring or licensed tracks, and a final mix suitable for online and small festival screenings. Please share a documentary or narrative editing sample, ideally something character or story-driven rather than commercial work."],
            ['id' => 33, 'client' => 'Nathalie Girard', 'country' => 'FR', 'category' => 'video-editing', 'remote_ok' => true, 'budget_min' => 800, 'budget_max' => 2000, 'deadline_days' => 30, 'created_days_ago' => 20,
                'title' => 'Corporate Explainer Animation from Existing Script',
                'description' => "We have a finished script and need a 60-90 second animated explainer video built from scratch, clean flat-design style. Voiceover recording can be arranged separately if you don't offer that. Looking for someone who can share a clear timeline before we commit."],

            // Design
            ['id' => 34, 'client' => 'Nathan Walsh', 'country' => 'US', 'category' => 'design', 'remote_ok' => true, 'budget_min' => 1500, 'budget_max' => 4000, 'deadline_days' => 30, 'created_days_ago' => 2,
                'title' => 'Complete Brand Identity for a Fintech Startup',
                'description' => "We're a pre-launch fintech startup and need a full brand identity package — logo, color palette, typography system, and a basic brand guidelines document we can hand to future contractors and employees. We have a working name and a rough sense of our brand personality (trustworthy but modern, not stiff-corporate), but no visual direction locked in yet, so we're relying heavily on your creative process here. Deliverables should include source files (Figma or Adobe) plus exported assets in common formats for web and print use. We'd also love a few mockups showing the identity applied to a website header, business card, and app icon so we can visualize it in context. Please share 2-3 brand identity projects in your portfolio, ideally for startups or fintech if you have them."],
            ['id' => 35, 'client' => 'Kasia Nowak', 'country' => 'PL', 'category' => 'design', 'remote_ok' => true, 'budget_min' => 300, 'budget_max' => 900, 'deadline_days' => 21, 'created_days_ago' => 11,
                'title' => 'Logo Design and Style Guide for a New Bakery',
                'description' => 'Opening a small artisan bakery and need a warm, hand-crafted feeling logo along with a simple one-page style guide covering colors and fonts. Nothing overly modern or corporate — we want it to feel cozy and a little rustic. Looking for 2-3 initial concepts before we narrow down.'],
            ['id' => 36, 'client' => 'Mart Tamm', 'country' => 'EE', 'category' => 'design', 'remote_ok' => true, 'budget_min' => 2000, 'budget_max' => 5000, 'deadline_days' => 45, 'created_days_ago' => 6,
                'title' => 'UI/UX Design for a Mobile App — iOS and Android',
                'description' => "We're building a habit-tracking mobile app and need a designer to take our rough wireframes and functional spec and turn them into a polished, fully designed UI ready for development — covering onboarding, main tracking screens, settings, and a few empty/error states. We use Figma internally so deliverables should be a well-organized Figma file with a basic component library, not just static screens. This is a fairly information-dense app, so experience designing for clarity and simplicity with lots of small data points would be ideal. We're happy to hop on a call to walk through our current wireframes and functional requirements before you start. Please share 1-2 mobile app projects you've designed end-to-end, ideally something in a similar utility/productivity space."],
            ['id' => 37, 'client' => 'Soo-jin Lee', 'country' => 'KR', 'category' => 'design', 'remote_ok' => true, 'budget_min' => 700, 'budget_max' => 1800, 'deadline_days' => 30, 'created_days_ago' => 4,
                'title' => 'Packaging Design for a Skincare Product Line',
                'description' => "Launching a small skincare line — three products to start — and need packaging design including box and label artwork print-ready for our manufacturer. Clean, minimal aesthetic similar to what's popular in K-beauty branding right now. Please confirm experience with print-ready file preparation, not just digital mockups."],
            ['id' => 38, 'client' => 'Sarah Mitchell', 'country' => 'US', 'category' => 'design', 'remote_ok' => true, 'budget_min' => 500, 'budget_max' => 1500, 'deadline_days' => 14, 'created_days_ago' => 0,
                'title' => 'Pitch Deck Design for a Seed Fundraising Round',
                'description' => 'We have the content and structure for our pitch deck already written out but need a designer to make it visually compelling for investor meetings. Roughly 15-18 slides. Quick turnaround needed since we have meetings scheduled in the next couple of weeks.'],
            ['id' => 39, 'client' => 'Henry Clarke', 'country' => 'GB', 'category' => 'design', 'remote_ok' => true, 'budget_min' => 1200, 'budget_max' => 3000, 'deadline_days' => 30, 'created_days_ago' => 13,
                'title' => 'Website Redesign — Figma to Webflow Handoff',
                'description' => "Our current website feels dated and we want a full visual redesign, then built out in Webflow so our team can make small content updates ourselves going forward. We have rough content and sitemap ready (About, Services, Case Studies, Contact) but no design direction yet, so we're looking for someone who can handle both the visual design in Figma and the Webflow build, or at minimum a design that's clean and realistic to implement well in Webflow. Around 6-8 pages total, nothing overly complex in terms of custom interactions. We'd like to review a design concept before moving into full build-out so we can give feedback early. Please share Webflow builds you've done previously, ideally with live links we can click through."],
            ['id' => 40, 'client' => 'Maria Santos', 'country' => 'PH', 'category' => 'design', 'remote_ok' => true, 'budget_min' => 200, 'budget_max' => 600, 'deadline_days' => 14, 'created_days_ago' => 7,
                'title' => 'Social Media Templates Kit for a Coaching Business',
                'description' => "Need a set of reusable Canva templates for Instagram posts and stories — quote graphics, carousel templates, and a couple of promo formats. Roughly 10-12 templates total, on-brand with colors and fonts we'll provide. Should be easy for a non-designer (me) to edit going forward."],
            ['id' => 41, 'client' => 'Linh Nguyen', 'country' => 'VN', 'category' => 'design', 'remote_ok' => false, 'budget_min' => 800, 'budget_max' => 2000, 'deadline_days' => 30, 'created_days_ago' => 10,
                'title' => 'Interior Design Concept for a Small Café',
                'description' => "We're opening a small specialty coffee shop in Hanoi and want a cohesive interior design concept before we start construction — layout suggestions, material and color palette, lighting direction, and a few reference mood boards we can share with our contractor. The space is roughly 60 square meters with decent natural light from one side. We're aiming for a warm, minimal aesthetic, plenty of natural materials like wood and stone rather than anything too industrial or stark. A site visit would be ideal if you're local to Hanoi, though we're open to remote collaboration with photos and measurements if not. Please share past interior design or café/retail space projects, even if they're renderings rather than built spaces."],
            ['id' => 42, 'client' => 'Tyler Brooks', 'country' => 'US', 'category' => 'design', 'remote_ok' => true, 'budget_min' => 400, 'budget_max' => 1000, 'deadline_days' => 14, 'created_days_ago' => 16,
                'title' => 'YouTube Channel Branding Package',
                'description' => 'Starting a new YouTube channel and need a full branding package — channel banner, logo/avatar, and a thumbnail template I can reuse for future videos. Looking for something bold and easy to read at small sizes. Channel topic is personal finance, so a trustworthy but modern feel would fit well.'],

            // Content Creator / UGC
            ['id' => 43, 'client' => 'Ashley Kim', 'country' => 'US', 'category' => 'content-creator', 'remote_ok' => true, 'budget_min' => 500, 'budget_max' => 1500, 'deadline_days' => 21, 'created_days_ago' => 1,
                'title' => 'UGC Videos for a Skincare Brand — 10 Videos',
                'description' => "We're looking for a UGC creator to film 10 short, authentic-feeling videos featuring our skincare products — think casual talking-head reviews, get-ready-with-me style content, and a couple of before/after style clips. Product will be shipped to you in advance, no need to travel or visit a studio. We're not looking for a highly polished, overly produced look — the whole point is that it feels native to how real people post on social media. A rough script outline can be provided for each video, but we're open to you adapting it to sound natural in your own voice. Usage rights for organic posting and paid ads would need to be included in your rate — please mention your usage terms when you apply."],
            ['id' => 44, 'client' => 'Jasmine Reid', 'country' => 'GB', 'category' => 'content-creator', 'remote_ok' => true, 'budget_min' => 600, 'budget_max' => 1600, 'created_days_ago' => 8,
                'title' => 'TikTok Content Creator — Monthly Retainer',
                'description' => "Looking for an ongoing TikTok content creator to post 3-4 times a week promoting our small fashion brand, mixing product features with lifestyle content. You'd have creative freedom within loose brand guidelines. Ideally someone with an existing audience or strong understanding of what performs well on the platform."],
            ['id' => 45, 'client' => 'Jonas Richter', 'country' => 'DE', 'category' => 'content-creator', 'remote_ok' => true, 'budget_min' => 300, 'budget_max' => 900, 'deadline_days' => 14, 'created_days_ago' => 0,
                'title' => 'Unboxing Videos for a Tech Gadget Launch',
                'description' => 'Launching a new tech accessory and want a handful of authentic unboxing and first-impressions videos, roughly 60-90 seconds each. Product would be shipped to you ahead of the launch date. Looking for a genuine, enthusiastic tone rather than a scripted read.'],
            ['id' => 46, 'client' => "Liam O'Sullivan", 'country' => 'AU', 'category' => 'content-creator', 'remote_ok' => true, 'budget_min' => 400, 'budget_max' => 1100, 'deadline_days' => 21, 'created_days_ago' => 6,
                'title' => 'Instagram Reels for a Fitness App Launch',
                'description' => "We're launching a new fitness tracking app and want a series of Instagram Reels showing real workouts paired with the app in use — think quick workout clips with on-screen app footage cut in, rather than a straight product demo. We can provide app screen recordings and a rough messaging brief, but we're looking for someone who can bring their own workout content and personality to the videos rather than following a rigid script. Roughly 8-10 reels to start, with the possibility of an ongoing arrangement if engagement looks good. Comfortable being on camera yourself is a requirement here, not just editing skills. Please share examples of fitness or app-related content you've created previously, along with any engagement numbers you're comfortable sharing."],
            ['id' => 47, 'client' => 'Nicha Suwan', 'country' => 'TH', 'category' => 'content-creator', 'remote_ok' => false, 'budget_min' => 500, 'budget_max' => 1400, 'deadline_days' => 30, 'created_days_ago' => 14,
                'title' => 'Sponsored Content Creator — Travel Niche',
                'description' => 'We run a boutique hotel in Phuket and want a travel content creator to stay with us for a few days and produce a set of Instagram and TikTok content in exchange for a fee plus complimentary stay. Looking for genuine, high-quality photo and video content showing the property and local area. Please share your existing travel content and any relevant audience size.'],
            ['id' => 48, 'client' => 'Natalie Brown', 'country' => 'CA', 'category' => 'content-creator', 'remote_ok' => true, 'budget_min' => 250, 'budget_max' => 700, 'deadline_days' => 14, 'created_days_ago' => 4,
                'title' => 'Product Review Videos — 5 Products',
                'description' => 'We sell a small range of home organization products and want honest-feeling review videos for 5 of them, roughly 60 seconds each. Products will be shipped to you at no cost, video fee is separate. Looking for a natural, conversational tone rather than a hard sell.'],
            ['id' => 49, 'client' => 'Iris Jansen', 'country' => 'NL', 'category' => 'content-creator', 'remote_ok' => false, 'budget_min' => 300, 'budget_max' => 900, 'deadline_days' => 21, 'created_days_ago' => 19,
                'title' => 'Short-Form Video Storytelling for a Nonprofit Campaign',
                'description' => "We're a small nonprofit running a fundraising campaign and want a series of short, emotionally resonant videos to share our impact story across social media. We can connect you with a couple of people directly involved in our programs for interviews, along with existing photo and video assets from past events. We're looking for someone with a good sense for storytelling and pacing in a short-form format, roughly 60-90 seconds per video, rather than a highly produced commercial style. Budget is modest since we're a small organization, but we're hoping to find someone who connects with the mission and is excited to help tell this story well. Please share any past nonprofit or cause-driven content you've created, even if it was volunteer work."],
            ['id' => 50, 'client' => 'Pablo Ruiz', 'country' => 'ES', 'category' => 'content-creator', 'remote_ok' => false, 'budget_min' => 500, 'budget_max' => 1300, 'deadline_days' => 30, 'created_days_ago' => 12,
                'title' => 'Behind-the-Scenes Content for a Music Festival',
                'description' => "We're organizing a mid-sized music festival outside Madrid and want a content creator on-site for the full weekend capturing behind-the-scenes moments — artist arrivals, soundchecks, crowd energy, and candid backstage content for our socials during and after the event. We need someone comfortable working fast in a chaotic live-event environment, turning around a few posts per day during the festival itself, followed by a wrap-up recap video within a week after. Festival credentials and backstage access would be provided. This is a physically demanding gig — long hours, lots of walking, unpredictable schedule — so please only apply if you're genuinely comfortable with live event work. Share any past festival, concert, or live event content you've created."],
        ];
    }
}
