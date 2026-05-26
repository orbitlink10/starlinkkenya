@extends('layouts.app', ['title' => $title.' | Starlink Kenya'])

@section('content')
    <style>
        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 312px;
            background: #eef1f6;
            border-right: 1px solid #dbe1eb;
            padding: 12px 0 18px;
        }

        .brand {
            display: block;
            margin: 0 14px 14px;
            background: #f8fafd;
            border: 1px solid #d8dfec;
            border-radius: 16px;
            padding: 15px 18px;
            font-size: 18px;
            line-height: 1.15;
            font-weight: 800;
            color: #353f4e;
            letter-spacing: -0.5px;
            text-decoration: none;
        }

        .menu-block {
            margin-bottom: 12px;
        }

        .menu-title {
            margin: 6px 16px 8px;
            font-size: 11px;
            font-weight: 600;
            color: #8b9ab1;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .menu-link,
        .menu-logout {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 4px 0;
            padding: 10px 16px;
            border-radius: 0 12px 12px 0;
            font-size: 15px;
            line-height: 1.2;
            font-weight: 700;
            color: #46556f;
        }

        .menu-link.active {
            background: linear-gradient(90deg, #3b74f3, #2f66f3);
            color: #fff;
            box-shadow: 0 12px 24px rgba(47, 102, 243, .22);
        }

        .menu-icon {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: #dce3ee;
            color: #62738f;
            font-size: 15px;
            flex-shrink: 0;
        }

        .menu-link.active .menu-icon {
            background: rgba(255, 255, 255, .2);
            color: #fff;
        }

        .menu-logout {
            width: 100%;
            border: none;
            background: transparent;
            text-align: left;
            cursor: pointer;
        }

        .main {
            flex: 1;
            padding: 18px 20px;
        }

        .content-card {
            border-radius: 14px;
            background: #fff;
            border: 1px solid #dce3ef;
            padding: 18px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 6px 12px;
            letter-spacing: .14em;
            color: #3f587f;
            background: #e6edf8;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
        }

        .page-title {
            margin: 10px 0 0;
            font-size: 24px;
            letter-spacing: -0.03em;
            color: #1f2a3a;
        }

        .subtitle {
            margin: 6px 0 0;
            color: #586c89;
            font-size: 15px;
            font-weight: 500;
            line-height: 1.45;
        }

        .head-actions {
            margin-top: 12px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-btn {
            border: 1px solid #8d99ae;
            color: #4f607a;
            background: #f3f6fc;
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 700;
        }

        .table-wrap {
            margin-top: 16px;
            overflow-x: auto;
            border: 1px solid #e1e7f1;
            border-radius: 12px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            min-width: 760px;
        }

        .table th,
        .table td {
            text-align: left;
            padding: 10px 12px;
            border-bottom: 1px solid #ebf0f7;
            color: #3a4d6b;
            font-size: 13px;
            white-space: nowrap;
        }

        .table th {
            background: #f5f8fd;
            color: #314562;
            font-weight: 800;
        }

        .empty-note {
            margin-top: 16px;
            color: #607491;
            font-size: 14px;
            line-height: 1.55;
        }

        .pager {
            margin-top: 16px;
        }

        .pager nav {
            display: flex;
            justify-content: center;
        }

        .pager svg {
            width: 14px;
            height: 14px;
        }

        .flash-success {
            margin-top: 14px;
            border: 1px solid #bfe5cd;
            background: #ecfff2;
            color: #1f6c3d;
            border-radius: 10px;
            padding: 9px 11px;
            font-size: 13px;
            font-weight: 700;
        }

        .flash-error {
            margin-top: 14px;
            border: 1px solid #f4b4bd;
            background: #fff1f3;
            color: #9f2536;
            border-radius: 10px;
            padding: 9px 11px;
            font-size: 13px;
            font-weight: 700;
        }

        .form-grid {
            margin-top: 16px;
            display: grid;
            gap: 14px;
        }

        .field-label {
            display: block;
            margin-bottom: 6px;
            color: #223552;
            font-size: 14px;
            font-weight: 800;
        }

        .field-help {
            margin: 4px 0 8px;
            color: #5a6f8e;
            font-size: 12px;
            line-height: 1.5;
        }

        .field-input,
        .field-textarea {
            width: 100%;
            border: 1px solid #d0d8e7;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            color: #2f4564;
            font-family: inherit;
            background: #fff;
        }

        .field-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .file-input {
            width: 100%;
            border: 1px solid #d0d8e7;
            border-radius: 10px;
            padding: 8px;
            background: #fff;
        }

        .hero-preview {
            margin-top: 10px;
            border: 1px solid #d7dfec;
            border-radius: 12px;
            max-width: 640px;
            overflow: hidden;
            background: #f7f9fc;
        }

        .hero-preview img {
            display: block;
            width: 100%;
            height: auto;
        }

        .hero-preview iframe {
            display: block;
            width: 100%;
            aspect-ratio: 16 / 9;
            border: 0;
        }

        .case-study-editor-list {
            display: grid;
            gap: 16px;
        }

        .case-study-editor-card {
            border: 1px solid #d9e3f0;
            border-radius: 16px;
            background: #f8fbff;
            padding: 16px;
        }

        .case-study-editor-title {
            margin: 0 0 12px;
            color: #223552;
            font-size: 16px;
            font-weight: 800;
        }

        .case-study-editor-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .save-btn {
            justify-self: start;
            border: 0;
            background: #1f6ff2;
            color: #fff;
            border-radius: 10px;
            padding: 9px 14px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
        }

        .menu-editor-actions {
            margin-top: 6px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        .menu-items-list {
            display: grid;
            gap: 12px;
        }

        .menu-item-card {
            border: 1px solid #d9e3f0;
            border-radius: 12px;
            background: #f8fbff;
            padding: 14px;
        }

        .menu-item-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: minmax(0, 280px) minmax(0, 1fr);
        }

        .secondary-btn,
        .danger-btn {
            border-radius: 10px;
            padding: 9px 12px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
        }

        .secondary-btn {
            border: 1px solid #b6c5da;
            background: #eef4fc;
            color: #38557f;
        }

        .danger-btn {
            border: 1px solid #efc0c6;
            background: #fff5f6;
            color: #a53848;
        }

        .menu-item-actions {
            margin-top: 14px;
            display: flex;
            justify-content: flex-end;
        }

        @media (max-width: 1200px) {
            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #dbe1eb;
            }

            .menu-item-grid {
                grid-template-columns: 1fr;
            }

            .case-study-editor-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .main {
                padding: 16px;
            }

            .page-title {
                font-size: 22px;
            }

            .subtitle {
                font-size: 14px;
            }
        }
    </style>

    <div class="layout">
        @include('dashboard.partials.sidebar')

        <main class="main">
            <section class="content-card">
                <span class="chip">Admin Section</span>
                <h1 class="page-title">{{ $title }}</h1>
                <p class="subtitle">{{ $description }}</p>

                <div class="head-actions">
                    <a class="action-btn" href="{{ route('dashboard') }}">Back to Dashboard</a>
                    <a class="action-btn" href="{{ route('admin.section', ['section' => $section]) }}">Refresh</a>
                </div>

                @if ($section === 'homepage-content' && isset($homepageContent))
                    @php
                        $youtubeVideoInput = old('youtube_video_url', $homepageContent->youtube_video_url);
                        $youtubeVideoPreviewUrl = \App\Models\HomepageContent::youtubeEmbedUrlFromInput($youtubeVideoInput)
                            ?: ($homepageContent->youtubeEmbedUrl() ?: \App\Models\HomepageContent::defaultYoutubeEmbedUrl());
                    @endphp

                    @if (session('success'))
                        <div class="flash-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="flash-error">{{ $errors->first() }}</div>
                    @endif

                    <form class="form-grid" method="POST" action="{{ route('admin.homepage-content.update') }}" enctype="multipart/form-data">
                        @csrf

                        <div>
                            <label class="field-label" for="hero_header_title">Hero Header Title</label>
                            <input class="field-input" id="hero_header_title" name="hero_header_title" type="text" value="{{ old('hero_header_title', $homepageContent->hero_header_title) }}" required>
                        </div>

                        <div>
                            <label class="field-label" for="hero_header_description">Hero Header Description</label>
                            <textarea class="field-textarea" id="hero_header_description" name="hero_header_description">{{ old('hero_header_description', $homepageContent->hero_header_description) }}</textarea>
                        </div>

                        <div>
                            <label class="field-label" for="hero_image">Hero Image (1280 x 720)</label>
                            <input class="file-input" id="hero_image" name="hero_image" type="file" accept=".jpg,.jpeg,.png,.webp">

                            @if ($homepageContent->hero_image_path)
                                <div class="hero-preview">
                                    <img src="{{ route('media.show', ['path' => $homepageContent->hero_image_path]) }}" alt="Hero image preview">
                                </div>
                            @endif
                        </div>

                        <div>
                            <label class="field-label" for="why_choose_title">Why Choose Title</label>
                            <input class="field-input" id="why_choose_title" name="why_choose_title" type="text" value="{{ old('why_choose_title', $homepageContent->why_choose_title) }}">
                        </div>

                        <div>
                            <label class="field-label" for="why_choose_description">Why Choose Description</label>
                            <textarea class="field-textarea" id="why_choose_description" name="why_choose_description">{{ old('why_choose_description', $homepageContent->why_choose_description) }}</textarea>
                        </div>

                        @if ($supportsYoutubeVideoUrl ?? false)
                            <div>
                                <label class="field-label" for="youtube_video_url">Coverage Section YouTube Link</label>
                                <p class="field-help">Paste a YouTube watch URL, share URL, shorts URL, or the 11-character video ID. This video appears beside the Why Choose section on the homepage.</p>
                                <input class="field-input" id="youtube_video_url" name="youtube_video_url" type="text" value="{{ $youtubeVideoInput }}" placeholder="https://www.youtube.com/watch?v=ZBpsEnxmsG4">

                                <div class="hero-preview">
                                    <iframe
                                        src="{{ $youtubeVideoPreviewUrl }}"
                                        title="Homepage video preview"
                                        loading="lazy"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            </div>
                        @else
                            <div class="flash-error">
                                Run the latest database migration to enable the homepage YouTube link field.
                            </div>
                        @endif

                        @if ($supportsCaseStudies ?? false)
                            @include('dashboard.partials.case-studies-editor', ['caseStudiesConfig' => $caseStudiesConfig])
                        @else
                            <div class="flash-error">
                                Run the latest database migration to enable homepage case studies editing.
                            </div>
                        @endif

                        <div>
                            <label class="field-label" for="products_section_title">Products Section Title</label>
                            <input class="field-input" id="products_section_title" name="products_section_title" type="text" value="{{ old('products_section_title', $homepageContent->products_section_title) }}">
                        </div>

                        <div>
                            <label class="field-label" for="home_page_content">Home Page Content</label>
                            <p class="field-help">Use H2/H3 headings and paragraphs, or paste plain text with blank lines. Numbered sections become headings and short lines after a colon become bullet lists.</p>
                            <textarea class="field-textarea js-home-editor" id="home_page_content" name="home_page_content" style="min-height:260px;">{{ old('home_page_content', $homepageContent->home_page_content) }}</textarea>
                        </div>

                        <button class="save-btn" type="submit">Save Homepage Content</button>
                    </form>

                    <script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>
                    <script>
                        if (window.tinymce) {
                            tinymce.init({
                                selector: '.js-home-editor',
                                height: 360,
                                menubar: 'file edit view insert format tools',
                                plugins: 'lists link table code',
                                toolbar: 'undo redo | blocks | bold italic | bullist numlist | link | code',
                                block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3',
                            });
                        }
                    </script>
                @elseif ($section === 'testimonials' && isset($homepageContent))
                    @if (session('success'))
                        <div class="flash-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="flash-error">{{ $errors->first() }}</div>
                    @endif

                    @if ($supportsCaseStudies ?? false)
                        <form class="form-grid" method="POST" action="{{ route('admin.testimonials.update') }}" enctype="multipart/form-data">
                            @csrf

                            @include('dashboard.partials.case-studies-editor', ['caseStudiesConfig' => $caseStudiesConfig])

                            <button class="save-btn" type="submit">Save Testimonials</button>
                        </form>
                    @else
                        <div class="flash-error">
                            Run the latest database migration to enable homepage case studies editing.
                        </div>
                    @endif
                @elseif ($section === 'menus' && isset($menuItemsConfig))
                    @if (session('success'))
                        <div class="flash-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="flash-error">{{ $errors->first() }}</div>
                    @endif

                    <form class="form-grid" method="POST" action="{{ route('admin.menus.update') }}">
                        @csrf

                        <div>
                            <label class="field-label">Website Menu Items</label>
                            <p class="field-help">Update the menu text and the link each item opens. Use anchors like <code>#packages</code>, relative paths like <code>/cart</code>, or full URLs.</p>
                        </div>

                        <div id="menu-items-list" class="menu-items-list">
                            @foreach ($menuItemsConfig as $index => $item)
                                <div class="menu-item-card" data-menu-item>
                                    <div class="menu-item-grid">
                                        <div>
                                            <label class="field-label" for="menu-label-{{ $index }}">Menu Label</label>
                                            <input class="field-input" id="menu-label-{{ $index }}" name="navigation_menu[{{ $index }}][label]" type="text" value="{{ $item['label'] }}" maxlength="80" required>
                                        </div>

                                        <div>
                                            <label class="field-label" for="menu-href-{{ $index }}">Menu Link</label>
                                            <input class="field-input" id="menu-href-{{ $index }}" name="navigation_menu[{{ $index }}][href]" type="text" value="{{ $item['href'] }}" maxlength="255" required>
                                        </div>
                                    </div>

                                    <div class="menu-item-actions">
                                        <button class="danger-btn js-remove-menu-item" type="button">Remove</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="menu-editor-actions">
                            <button class="secondary-btn js-add-menu-item" type="button">Add Menu Item</button>
                            <button class="save-btn" type="submit">Save Menus</button>
                        </div>
                    </form>

                    <template id="menu-item-template">
                        <div class="menu-item-card" data-menu-item>
                            <div class="menu-item-grid">
                                <div>
                                    <label class="field-label">Menu Label</label>
                                    <input class="field-input" data-menu-label type="text" maxlength="80" required>
                                </div>

                                <div>
                                    <label class="field-label">Menu Link</label>
                                    <input class="field-input" data-menu-href type="text" maxlength="255" required>
                                </div>
                            </div>

                            <div class="menu-item-actions">
                                <button class="danger-btn js-remove-menu-item" type="button">Remove</button>
                            </div>
                        </div>
                    </template>

                    <script>
                        (() => {
                            const list = document.getElementById('menu-items-list');
                            const addButton = document.querySelector('.js-add-menu-item');
                            const template = document.getElementById('menu-item-template');

                            if (!list || !addButton || !template) {
                                return;
                            }

                            const reindexItems = () => {
                                const items = Array.from(list.querySelectorAll('[data-menu-item]'));

                                items.forEach((item, index) => {
                                    const label = item.querySelector('[data-menu-label], input[name*="[label]"]');
                                    const href = item.querySelector('[data-menu-href], input[name*="[href]"]');

                                    if (label) {
                                        label.name = `navigation_menu[${index}][label]`;
                                        label.id = `menu-label-${index}`;
                                        const labelField = item.querySelector('label');

                                        if (labelField) {
                                            labelField.setAttribute('for', label.id);
                                        }
                                    }

                                    if (href) {
                                        href.name = `navigation_menu[${index}][href]`;
                                        href.id = `menu-href-${index}`;
                                        const hrefField = item.querySelectorAll('label')[1];

                                        if (hrefField) {
                                            hrefField.setAttribute('for', href.id);
                                        }
                                    }
                                });

                                const removeButtons = list.querySelectorAll('.js-remove-menu-item');
                                removeButtons.forEach((button) => {
                                    button.disabled = removeButtons.length <= 1;
                                    button.style.opacity = removeButtons.length <= 1 ? '0.55' : '1';
                                    button.style.cursor = removeButtons.length <= 1 ? 'not-allowed' : 'pointer';
                                });
                            };

                            addButton.addEventListener('click', () => {
                                const fragment = template.content.cloneNode(true);
                                list.appendChild(fragment);
                                reindexItems();
                            });

                            list.addEventListener('click', (event) => {
                                const button = event.target.closest('.js-remove-menu-item');

                                if (!button || list.querySelectorAll('[data-menu-item]').length <= 1) {
                                    return;
                                }

                                button.closest('[data-menu-item]')?.remove();
                                reindexItems();
                            });

                            reindexItems();
                        })();
                    </script>
                @elseif ($section === 'settings' && isset($settingsConfig))
                    @php
                        $whatsappPhoneInput = (string) ($settingsConfig['whatsapp_phone'] ?? '');
                        $whatsappPhoneDigits = preg_replace('/\D+/', '', $whatsappPhoneInput);
                        $logoUrl = $settingsConfig['logo_url'] ?? null;
                    @endphp

                    @if (session('success'))
                        <div class="flash-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="flash-error">{{ $errors->first() }}</div>
                    @endif

                    <form class="form-grid" method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                        @csrf

                        <div>
                            <label class="field-label" for="logo">Website Logo</label>
                            <p class="field-help">Upload a JPG, PNG, or WebP logo. It will replace the default header mark on the storefront.</p>
                            <input class="file-input" id="logo" name="logo" type="file" accept=".jpg,.jpeg,.png,.webp">

                            @if ($logoUrl)
                                <div class="image-preview">
                                    <img src="{{ $logoUrl }}" alt="Current website logo">
                                </div>
                            @endif
                        </div>

                        <div>
                            <label class="field-label" for="whatsapp_phone">WhatsApp Phone Number</label>
                            <p class="field-help">Use Kenya international format without a plus sign, for example <code>254700123456</code>. This updates the header, floating WhatsApp button, product order links, and cart checkout link.</p>
                            <input
                                class="field-input"
                                id="whatsapp_phone"
                                name="whatsapp_phone"
                                type="tel"
                                value="{{ $whatsappPhoneInput }}"
                                inputmode="numeric"
                                autocomplete="tel"
                                placeholder="254700123456"
                                required
                            >

                            @if ($whatsappPhoneDigits)
                                <p class="field-help">Current WhatsApp link: <a href="https://wa.me/{{ $whatsappPhoneDigits }}" target="_blank" rel="noopener">https://wa.me/{{ $whatsappPhoneDigits }}</a></p>
                            @endif
                        </div>

                        <button class="save-btn" type="submit">Save Settings</button>
                    </form>
                @elseif ($table)
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    @foreach ($table['headers'] as $header)
                                        <th>{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($table['rows'] as $row)
                                    <tr>
                                        @foreach ($row as $value)
                                            <td>{{ $value }}</td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($table['headers']) }}">No records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="pager">{{ $table['rows']->onEachSide(1)->links() }}</div>
                @else
                    <p class="empty-note">
                        This module is active and routed correctly. Data CRUD screens can be added next based on your workflow requirements.
                    </p>
                @endif
            </section>
        </main>
    </div>
@endsection
