<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\HomepageContent;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminSectionController extends Controller
{
    public function show(string $section, Request $request): View
    {
        $sections = $this->sections();

        abort_unless(isset($sections[$section]), 404);

        $meta = $sections[$section];
        $table = $this->tableForSection($section, $request);
        $homepageContent = null;
        $menuItemsConfig = null;
        $supportsYoutubeVideoUrl = false;
        $supportsCaseStudies = false;
        $caseStudiesConfig = null;
        $settingsConfig = null;

        if (in_array($section, ['homepage-content', 'menus', 'testimonials'], true)) {
            $supportsYoutubeVideoUrl = $this->supportsYoutubeVideoUrl();
            $supportsCaseStudies = $this->supportsCaseStudies();
            $homepageContent = $this->homepageContentRecord();
        }

        if (in_array($section, ['homepage-content', 'testimonials'], true) && $homepageContent) {
            $caseStudiesConfig = HomepageContent::normalizeCaseStudies(
                old('case_studies', $homepageContent->case_studies)
            );
        }

        if ($section === 'menus' && $homepageContent) {
            $menuItemsConfig = HomepageContent::normalizeNavigationMenu(
                old('navigation_menu', $homepageContent->navigation_menu)
            );
        }

        if ($section === 'settings') {
            $logoPath = SiteSetting::value('logo_path');

            $settingsConfig = [
                'whatsapp_phone' => old(
                    'whatsapp_phone',
                    SiteSetting::value('whatsapp_phone', (string) config('seo.whatsapp_phone', '254700123456'))
                ),
                'logo_path' => $logoPath,
                'logo_url' => $logoPath ? route('media.show', ['path' => $logoPath]) : null,
            ];
        }

        return view('dashboard.section', [
            'activeSection' => $section,
            'section' => $section,
            'title' => $meta['title'],
            'description' => $meta['description'],
            'table' => $table,
            'homepageContent' => $homepageContent,
            'menuItemsConfig' => $menuItemsConfig,
            'supportsYoutubeVideoUrl' => $supportsYoutubeVideoUrl,
            'supportsCaseStudies' => $supportsCaseStudies,
            'caseStudiesConfig' => $caseStudiesConfig,
            'settingsConfig' => $settingsConfig,
        ]);
    }

    public function updateHomepageContent(Request $request): RedirectResponse
    {
        $supportsYoutubeVideoUrl = $this->supportsYoutubeVideoUrl();
        $supportsCaseStudies = $this->supportsCaseStudies();

        $rules = [
            'hero_header_title' => ['required', 'string', 'max:255'],
            'hero_header_description' => ['nullable', 'string'],
            'hero_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'why_choose_title' => ['nullable', 'string', 'max:255'],
            'why_choose_description' => ['nullable', 'string'],
            'products_section_title' => ['nullable', 'string', 'max:255'],
            'home_page_content' => ['nullable', 'string'],
        ];

        if ($supportsYoutubeVideoUrl) {
            $rules['youtube_video_url'] = [
                'nullable',
                'string',
                'max:255',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    $youtubeVideoUrl = trim((string) $value);

                    if ($youtubeVideoUrl !== '' && HomepageContent::extractYoutubeVideoId($youtubeVideoUrl) === null) {
                        $fail('Enter a valid YouTube link or 11-character video ID.');
                    }
                },
            ];
        }

        if ($supportsCaseStudies) {
            $rules['case_studies'] = ['required', 'array', 'size:4'];
            $rules['case_studies.*.label'] = ['required', 'string', 'max:80'];
            $rules['case_studies.*.title'] = ['required', 'string', 'max:255'];
            $rules['case_studies.*.excerpt'] = ['required', 'string', 'max:600'];
            $rules['case_studies.*.href'] = ['required', 'string', 'max:255'];
            $rules['case_studies.*.image_alt'] = ['nullable', 'string', 'max:255'];
            $rules['case_study_images'] = ['nullable', 'array', 'max:4'];
            $rules['case_study_images.*'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'];
        }

        $validated = $request->validate($rules);

        if (! $supportsYoutubeVideoUrl && trim((string) $request->input('youtube_video_url')) !== '') {
            return redirect()
                ->route('admin.section', ['section' => 'homepage-content'])
                ->withErrors(['youtube_video_url' => 'Run the latest database migration to enable the homepage YouTube link field.'])
                ->withInput();
        }

        if (! $supportsCaseStudies && is_array($request->input('case_studies'))) {
            return redirect()
                ->route('admin.section', ['section' => 'homepage-content'])
                ->withErrors(['case_studies' => 'Run the latest database migration to enable homepage case studies editing.'])
                ->withInput();
        }

        $content = $this->homepageContentRecord();

        if ($supportsYoutubeVideoUrl) {
            $validated['youtube_video_url'] = trim((string) ($validated['youtube_video_url'] ?? '')) ?: null;
        }

        if ($request->hasFile('hero_image')) {
            if ($content->hero_image_path && Storage::disk('public')->exists($content->hero_image_path)) {
                Storage::disk('public')->delete($content->hero_image_path);
            }

            $validated['hero_image_path'] = $request->file('hero_image')->store('homepage', 'public');
        }

        if ($supportsCaseStudies) {
            $validated['case_studies'] = $this->storeCaseStudies($request, $content, $validated['case_studies'] ?? []);
        }

        unset($validated['hero_image']);
        unset($validated['case_study_images']);

        $content->update($validated);

        return redirect()
            ->route('admin.section', ['section' => 'homepage-content'])
            ->with('success', 'Homepage content saved successfully.');
    }

    public function updateTestimonials(Request $request): RedirectResponse
    {
        if (! $this->supportsCaseStudies()) {
            return redirect()
                ->route('admin.section', ['section' => 'testimonials'])
                ->withErrors(['case_studies' => 'Run the latest database migration to enable homepage case studies editing.'])
                ->withInput();
        }

        $validated = $request->validate([
            'case_studies' => ['required', 'array', 'size:4'],
            'case_studies.*.label' => ['required', 'string', 'max:80'],
            'case_studies.*.title' => ['required', 'string', 'max:255'],
            'case_studies.*.excerpt' => ['required', 'string', 'max:600'],
            'case_studies.*.href' => ['required', 'string', 'max:255'],
            'case_studies.*.image_alt' => ['nullable', 'string', 'max:255'],
            'case_study_images' => ['nullable', 'array', 'max:4'],
            'case_study_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        $content = $this->homepageContentRecord();

        $content->update([
            'case_studies' => $this->storeCaseStudies($request, $content, $validated['case_studies'] ?? []),
        ]);

        return redirect()
            ->route('admin.section', ['section' => 'testimonials'])
            ->with('success', 'Testimonials updated successfully.');
    }

    public function updateMenus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'navigation_menu' => ['required', 'array', 'max:10'],
            'navigation_menu.*.label' => ['nullable', 'string', 'max:80'],
            'navigation_menu.*.href' => ['nullable', 'string', 'max:255'],
        ]);

        $menuItems = collect($validated['navigation_menu'] ?? [])
            ->map(function (array $item): ?array {
                $label = trim((string) ($item['label'] ?? ''));
                $href = trim((string) ($item['href'] ?? ''));

                if ($label === '' || $href === '') {
                    return null;
                }

                return [
                    'label' => $label,
                    'href' => $href,
                ];
            })
            ->filter()
            ->values()
            ->all();

        if ($menuItems === []) {
            return redirect()
                ->route('admin.section', ['section' => 'menus'])
                ->withErrors(['navigation_menu' => 'Add at least one menu item with both a label and a link.'])
                ->withInput();
        }

        $this->homepageContentRecord()->update([
            'navigation_menu' => $menuItems,
        ]);

        return redirect()
            ->route('admin.section', ['section' => 'menus'])
            ->with('success', 'Menus updated successfully.');
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'whatsapp_phone' => [
                'required',
                'string',
                'max:24',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    $digits = preg_replace('/\D+/', '', (string) $value) ?? '';

                    if (! preg_match('/^254\d{9}$/', $digits)) {
                        $fail('Enter the WhatsApp number in Kenya international format, for example 254700123456.');
                    }
                },
            ],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        $whatsappPhone = preg_replace('/\D+/', '', (string) $validated['whatsapp_phone']) ?: '';

        if (! SiteSetting::tableExists()) {
            return redirect()
                ->route('admin.section', ['section' => 'settings'])
                ->withErrors(['whatsapp_phone' => 'Run the latest database migration to enable site settings.'])
                ->withInput();
        }

        SiteSetting::setValue('whatsapp_phone', $whatsappPhone);
        Config::set('seo.whatsapp_phone', $whatsappPhone);

        if ($request->hasFile('logo')) {
            $existingLogoPath = SiteSetting::value('logo_path');

            if ($existingLogoPath && Storage::disk('public')->exists($existingLogoPath)) {
                Storage::disk('public')->delete($existingLogoPath);
            }

            $logoPath = $request->file('logo')->store('site/logo', 'public');

            SiteSetting::setValue('logo_path', $logoPath);
            Config::set('seo.logo_path', $logoPath);
        }

        return redirect()
            ->route('admin.section', ['section' => 'settings'])
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * @return array<string, array{title:string, description:string}>
     */
    private function sections(): array
    {
        return [
            'categories' => ['title' => 'Categories', 'description' => 'Organize products into clear storefront categories.'],
            'sub-categories' => ['title' => 'Sub Categories', 'description' => 'Manage nested product classifications for better browsing.'],
            'products' => ['title' => 'Products', 'description' => 'Manage Starlink kits and accessories listed on the website.'],
            'orders' => ['title' => 'Orders', 'description' => 'Track and manage incoming customer orders.'],
            'invoices' => ['title' => 'Invoices', 'description' => 'Review generated invoices and payment status.'],
            'enquiries' => ['title' => 'Enquiries', 'description' => 'Respond to sales and installation requests from visitors.'],
            'users' => ['title' => 'Users', 'description' => 'Manage admin and customer user accounts.'],
            'homepage-content' => ['title' => 'Homepage Content', 'description' => 'Edit text blocks and homepage messaging.'],
            'sliders' => ['title' => 'Sliders', 'description' => 'Manage homepage and campaign slider items.'],
            'pages' => ['title' => 'Pages', 'description' => 'Create and update custom website pages.'],
            'services' => ['title' => 'Services', 'description' => 'Define installation and networking service offerings.'],
            'testimonials' => ['title' => 'Testimonials', 'description' => 'Manage the four homepage case study and testimonial cards.'],
            'media' => ['title' => 'Media', 'description' => 'Upload and manage website images and files.'],
            'menus' => ['title' => 'Menus', 'description' => 'Configure website navigation menu items.'],
            'settings' => ['title' => 'Settings', 'description' => 'Update global system and website settings.'],
            'profile' => ['title' => 'Profile', 'description' => 'Manage your account profile details.'],
        ];
    }

    /**
     * @return array{headers: array<int, string>, rows: LengthAwarePaginator<array<string, mixed>>}|null
     */
    private function tableForSection(string $section, Request $request): ?array
    {
        return match ($section) {
            'products' => [
                'headers' => ['ID', 'Name', 'Price (KES)', 'Stock', 'Active', 'Created'],
                'rows' => Product::query()
                    ->latest('id')
                    ->paginate(12)
                    ->through(fn (Product $product): array => [
                        'ID' => $product->id,
                        'Name' => $product->name,
                        'Price (KES)' => number_format((float) $product->price, 2),
                        'Stock' => $product->stock,
                        'Active' => $product->is_active ? 'Yes' : 'No',
                        'Created' => $product->created_at?->format('Y-m-d'),
                    ])
                    ->appends($request->query()),
            ],
            'orders' => [
                'headers' => ['ID', 'Order No', 'Customer', 'County', 'Amount (KES)', 'Status'],
                'rows' => Order::query()
                    ->latest('id')
                    ->paginate(12)
                    ->through(fn (Order $order): array => [
                        'ID' => $order->id,
                        'Order No' => $order->order_number,
                        'Customer' => $order->customer_name,
                        'County' => $order->county ?? '-',
                        'Amount (KES)' => number_format((float) $order->amount, 2),
                        'Status' => ucfirst($order->status),
                    ])
                    ->appends($request->query()),
            ],
            'invoices' => [
                'headers' => ['ID', 'Invoice No', 'Order ID', 'Amount (KES)', 'Status', 'Issued'],
                'rows' => Invoice::query()
                    ->latest('id')
                    ->paginate(12)
                    ->through(fn (Invoice $invoice): array => [
                        'ID' => $invoice->id,
                        'Invoice No' => $invoice->invoice_number,
                        'Order ID' => $invoice->order_id ?? '-',
                        'Amount (KES)' => number_format((float) $invoice->amount, 2),
                        'Status' => ucfirst($invoice->status),
                        'Issued' => $invoice->issued_at?->format('Y-m-d'),
                    ])
                    ->appends($request->query()),
            ],
            'users' => [
                'headers' => ['ID', 'Name', 'Email', 'Last Login', 'Created'],
                'rows' => User::query()
                    ->latest('id')
                    ->paginate(12)
                    ->through(fn (User $user): array => [
                        'ID' => $user->id,
                        'Name' => $user->name,
                        'Email' => $user->email,
                        'Last Login' => $user->last_login_at?->format('Y-m-d H:i') ?? '-',
                        'Created' => $user->created_at?->format('Y-m-d'),
                    ])
                    ->appends($request->query()),
            ],
            'enquiries' => [
                'headers' => ['ID', 'Name', 'Email', 'Phone', 'Status', 'Created'],
                'rows' => Enquiry::query()
                    ->latest('id')
                    ->paginate(12)
                    ->through(fn (Enquiry $enquiry): array => [
                        'ID' => $enquiry->id,
                        'Name' => $enquiry->name,
                        'Email' => $enquiry->email,
                        'Phone' => $enquiry->phone ?? '-',
                        'Status' => ucfirst($enquiry->status),
                        'Created' => $enquiry->created_at?->format('Y-m-d'),
                    ])
                    ->appends($request->query()),
            ],
            default => null,
        };
    }

    private function homepageContentRecord(): HomepageContent
    {
        $defaults = [
            'hero_header_title' => 'Starlink Kenya | High-Speed Satellite Internet Across Kenya',
            'hero_header_description' => 'Starlink Kenya offers high-speed satellite internet with affordable packages, hardware, and monthly plans.',
            'why_choose_title' => 'Why Starlink Kenya Is Ideal for You',
            'why_choose_description' => 'Tailored for the Kenyan market.',
            'products_section_title' => 'Hot-Selling Products.',
            'home_page_content' => '<h2>Starlink Kenya: A Comprehensive Guide to Satellite Internet Connectivity</h2><p>Explore the complete guide to STARLINK KENYA.</p>',
            'navigation_menu' => HomepageContent::defaultNavigationMenu(),
        ];

        if ($this->supportsYoutubeVideoUrl()) {
            $defaults['youtube_video_url'] = HomepageContent::defaultYoutubeVideoUrl();
        }

        if ($this->supportsCaseStudies()) {
            $defaults['case_studies'] = HomepageContent::defaultCaseStudies();
        }

        return HomepageContent::query()->firstOrCreate(
            ['id' => 1],
            $defaults
        );
    }

    private function supportsYoutubeVideoUrl(): bool
    {
        return Schema::hasColumn('homepage_contents', 'youtube_video_url');
    }

    /**
     * @param  array<int, array<string, mixed>>  $caseStudies
     * @return array<int, array{label:string, title:string, excerpt:string, href:string, image_path:?string, image_alt:string}>
     */
    private function storeCaseStudies(Request $request, HomepageContent $content, array $caseStudies): array
    {
        $normalizedCaseStudies = HomepageContent::normalizeCaseStudies($caseStudies);
        $existingCaseStudies = HomepageContent::normalizeCaseStudies($content->case_studies);
        $uploadedImages = $request->file('case_study_images', []);

        foreach ($normalizedCaseStudies as $index => &$caseStudy) {
            $existingImagePath = trim((string) ($existingCaseStudies[$index]['image_path'] ?? ''));
            $caseStudy['image_path'] = $existingImagePath !== '' ? $existingImagePath : null;
            $caseStudy['image_alt'] = trim((string) ($caseStudy['image_alt'] ?? '')) ?: $caseStudy['title'];

            if (! isset($uploadedImages[$index])) {
                continue;
            }

            if ($existingImagePath !== '' && Storage::disk('public')->exists($existingImagePath)) {
                Storage::disk('public')->delete($existingImagePath);
            }

            $caseStudy['image_path'] = $uploadedImages[$index]->store('homepage/case-studies', 'public');
        }
        unset($caseStudy);

        return $normalizedCaseStudies;
    }

    private function supportsCaseStudies(): bool
    {
        return Schema::hasColumn('homepage_contents', 'case_studies');
    }
}
