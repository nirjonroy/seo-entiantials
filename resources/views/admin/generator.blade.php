@extends(config('seo.layout', 'seo::layouts.app'))

@push('seo_styles')
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .nirjon-seo-tab-content { display: none; }
        .nirjon-seo-tab-content.active { display: block; }
        .nirjon-seo-tab-btn.active { border-bottom: 2px solid #2563eb; color: #2563eb; }
        .nirjon-seo-editor-wrap .tox-tinymce { border-color: #d1d5db; border-radius: 0.5rem; }
        .nirjon-seo-panel-title { color: #111827; font-size: 0.95rem; font-weight: 800; }
        .nirjon-seo-modal { display: none; }
        .nirjon-seo-modal.active { display: flex; }
    </style>
@endpush

@section(config('seo.section', 'content'))
    <main class="min-h-screen bg-gray-100 p-8">
        <div class="mx-auto max-w-5xl rounded-lg bg-white p-6 shadow-md">
        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h1 class="text-2xl font-bold">PageForge Admin Generator</h1>
            <div class="flex flex-wrap gap-2">
                <button type="button" data-open-modal="demo-data-modal" onclick="window.nirjonSeoOpenModal && window.nirjonSeoOpenModal('demo-data-modal')" class="rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">Use Demo Data</button>
                <button type="button" data-open-modal="css-help-modal" onclick="window.nirjonSeoOpenModal && window.nirjonSeoOpenModal('css-help-modal')" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">CSS Classes</button>
            </div>
        </div>

        <div class="mb-6 flex border-b">
            <button type="button" id="tab-generator" data-nirjon-tab="generator" class="nirjon-seo-tab-btn active px-4 py-2 font-medium text-gray-600 hover:text-blue-600 focus:outline-none">
                Generator Form
            </button>
            <button type="button" id="tab-pages" data-nirjon-tab="pages" class="nirjon-seo-tab-btn px-4 py-2 font-medium text-gray-600 hover:text-blue-600 focus:outline-none">
                Generated Pages
            </button>
        </div>

        <div id="alert-container" class="mb-4 hidden rounded p-4 font-medium text-white"></div>

        <div id="content-generator" class="nirjon-seo-tab-content active">
            <form id="generator-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Template Title</label>
                    <input type="text" id="templateTitle" class="mt-1 block w-full rounded-md border border-gray-300 p-2 shadow-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Template Slug</label>
                    <input type="text" id="templateSlug" class="mt-1 block w-full rounded-md border border-gray-300 p-2 shadow-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Content</label>
                    <div class="nirjon-seo-editor-wrap mt-1">
                        <textarea id="nirjon_seo_editor" name="content"></textarea>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Meta Title</label>
                    <input type="text" id="meta_title" class="mt-1 block w-full rounded-md border border-gray-300 p-2 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Meta Description</label>
                    <textarea id="meta_description" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 p-2 shadow-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Meta Keywords</label>
                    <input type="text" id="meta_keywords" class="mt-1 block w-full rounded-md border border-gray-300 p-2 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Upload Featured Image</label>
                    <input type="file" id="featured_image" accept="image/*" class="mt-1 block w-full rounded-md border border-gray-300 p-2 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Author</label>
                    <input type="text" id="author" class="mt-1 block w-full rounded-md border border-gray-300 p-2 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Publisher</label>
                    <input type="text" id="publisher" class="mt-1 block w-full rounded-md border border-gray-300 p-2 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Copyright</label>
                    <input type="text" id="copyright" class="mt-1 block w-full rounded-md border border-gray-300 p-2 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Site Name</label>
                    <input type="text" id="site_name" class="mt-1 block w-full rounded-md border border-gray-300 p-2 shadow-sm">
                </div>

                <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                    <h3 class="nirjon-seo-panel-title mb-4">Generated Page Design</h3>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Page Logo</label>
                            <input type="file" id="logo_image" accept="image/*" class="mt-1 block w-full rounded-md border border-gray-300 bg-white p-2 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Font Family</label>
                            <input type="text" id="font_family" value="Inter, Arial, sans-serif" class="mt-1 block w-full rounded-md border border-gray-300 bg-white p-2 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Primary Color</label>
                            <input type="color" id="primary_color" value="#111827" class="mt-1 h-10 w-full rounded-md border border-gray-300 bg-white p-1 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Accent Color</label>
                            <input type="color" id="accent_color" value="#2563eb" class="mt-1 h-10 w-full rounded-md border border-gray-300 bg-white p-1 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Background Color</label>
                            <input type="color" id="background_color" value="#f8fafc" class="mt-1 h-10 w-full rounded-md border border-gray-300 bg-white p-1 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Text Color</label>
                            <input type="color" id="text_color" value="#1f2937" class="mt-1 h-10 w-full rounded-md border border-gray-300 bg-white p-1 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Container Width</label>
                            <input type="text" id="container_width" value="960px" class="mt-1 block w-full rounded-md border border-gray-300 bg-white p-2 shadow-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Custom CSS</label>
                            <textarea id="custom_css" rows="5" class="mt-1 block w-full rounded-md border border-gray-300 bg-white p-2 font-mono text-sm shadow-sm" placeholder=".pf-hero { ... }"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Generated Page Header CSS</label>
                            <textarea id="header_css" rows="5" class="mt-1 block w-full rounded-md border border-gray-300 bg-white p-2 font-mono text-sm shadow-sm" placeholder="body { scroll-behavior: smooth; }"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Generated Page Header Scripts</label>
                            <textarea id="header_js" rows="6" class="mt-1 block w-full rounded-md border border-gray-300 bg-white p-2 font-mono text-sm shadow-sm" placeholder="<script>&#10;console.log('Generated page loaded');&#10;</script>"></textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Keyword Bundle 1 (comma separated)</label>
                    <input type="text" id="bundle1" class="mt-1 block w-full rounded-md border border-gray-300 p-2 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Keyword Bundle 2 (comma separated)</label>
                    <input type="text" id="bundle2" class="mt-1 block w-full rounded-md border border-gray-300 p-2 shadow-sm">
                </div>

                <div class="mb-6 rounded-md border border-blue-200 bg-blue-50 p-5 text-sm text-blue-800 shadow-sm">
                    <h4 class="mb-3 flex text-base font-bold text-blue-900">How to Get the Best Results:</h4>
                    <ul class="list-disc space-y-3 pl-5 text-gray-700">
                        <li>
                            <strong class="text-gray-900">Dynamic Keywords:</strong> Use <code>{0}</code> for words in your first bundle, and <code>{1}</code> for the second.
                            <br><span class="text-xs italic text-gray-500">Example: "Looking for {0} repair in {1}?" will automatically become "Looking for iPhone repair in London?"</span>
                        </li>
                        <li>
                            <strong class="text-gray-900">Keep Content Unique (Spintax):</strong> Prevent duplicate content penalties in SEO by using format like <code>{Best|Top|Reliable}</code>.
                        </li>
                        <li>
                            <strong class="text-gray-900">Add Images & Videos:</strong> Paste standard HTML tags like <code>&lt;img src="..."&gt;</code> or YouTube <code>&lt;iframe src="..."&gt;</code> in the editor using code blocks or source content.
                        </li>
                        <li>
                            <strong class="text-gray-900">Social Media Preview:</strong> Upload a Featured Image for Open Graph sharing previews.
                        </li>
                    </ul>
                </div>

                <button type="button" id="generate-btn" class="rounded bg-blue-600 px-4 py-2 text-white shadow transition-colors hover:bg-blue-700">
                    Generate Pages
                </button>
                <div id="loading-ui" class="mt-2 hidden text-sm text-gray-500">Loading... please wait.</div>
            </form>
        </div>

        <div id="content-pages" class="nirjon-seo-tab-content">
            <div id="generatedPagesContainer" class="space-y-4">
                <div class="text-gray-500">Loading pages...</div>
            </div>
        </div>
        </div>

        <div id="demo-data-modal" class="nirjon-seo-modal fixed inset-0 z-50 items-center justify-center bg-slate-900/60 p-4">
            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-lg bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Demo Data</h2>
                        <p class="text-sm text-slate-500">Fill PageForge with a working demo template.</p>
                    </div>
                    <button type="button" data-close-modal onclick="window.nirjonSeoCloseModals && window.nirjonSeoCloseModals()" class="rounded-md px-2 py-1 text-slate-500 hover:bg-slate-100">Close</button>
                </div>
                <div class="grid gap-3 md:grid-cols-2">
                    <button type="button" data-demo-type="consultancy" class="rounded-lg border border-slate-200 p-4 text-left hover:border-blue-400 hover:bg-blue-50">
                        <strong class="block text-slate-900">Consultancy Landing Page</strong>
                        <span class="text-sm text-slate-500">Professional stress-test template using service + city combinations.</span>
                        <span class="mt-3 block text-xs font-semibold uppercase tracking-wide text-slate-500">Fills</span>
                        <span class="mt-1 block text-sm text-slate-600">Template title, slug, content, meta fields, author, publisher, colors, and keyword bundles.</span>
                        <span class="mt-2 block rounded-md bg-slate-50 p-2 text-xs text-slate-600">Expert {0} Consultancy in {1}<br>Top {0} Services in {1} | BlackTech</span>
                        <span class="mt-2 block text-xs text-slate-500">Bundles: Web Development, SEO Audit, API Integration + Dhaka, Sylhet</span>
                    </button>
                    <button type="button" data-demo-type="restaurant" class="rounded-lg border border-slate-200 p-4 text-left hover:border-blue-400 hover:bg-blue-50">
                        <strong class="block text-slate-900">Restaurant Menu Design</strong>
                        <span class="text-sm text-slate-500">Local landing pages with richer visual content.</span>
                        <span class="mt-3 block text-xs font-semibold uppercase tracking-wide text-slate-500">Fills</span>
                        <span class="mt-1 block text-sm text-slate-600">Menu-design title, slug, HTML content, SEO metadata, design defaults, and keyword bundles.</span>
                        <span class="mt-2 block text-xs text-slate-500">Bundles: Restaurant, Cafe, Bistro + Dhaka, Sylhet, Chittagong</span>
                    </button>
                </div>
            </div>
        </div>

        <div id="css-help-modal" class="nirjon-seo-modal fixed inset-0 z-50 items-center justify-center bg-slate-900/60 p-4">
            <div class="max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-lg bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Demo CSS Customization</h2>
                        <p class="text-sm text-slate-500">Use these classes in the Custom CSS field.</p>
                    </div>
                    <button type="button" data-close-modal onclick="window.nirjonSeoCloseModals && window.nirjonSeoCloseModals()" class="rounded-md px-2 py-1 text-slate-500 hover:bg-slate-100">Close</button>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-lg border border-slate-200 p-4">
                        <h3 class="mb-2 font-bold text-slate-900">Available Classes</h3>
                        <ul class="space-y-1 text-sm text-slate-700">
                            <li><code>.pf-page</code> controls the full generated page background and outer spacing</li>
                            <li><code>.pf-shell</code> controls the centered page width wrapper</li>
                            <li><code>.pf-header</code> controls the logo/header row above the card</li>
                            <li><code>.pf-logo</code> controls uploaded logo size and fit</li>
                            <li><code>.pf-card</code> controls the main page card border, radius, and shadow</li>
                            <li><code>.pf-hero</code> controls the title section background and spacing</li>
                            <li><code>.pf-title</code> controls the generated page H1</li>
                            <li><code>.pf-featured</code> controls the uploaded featured image</li>
                            <li><code>.pf-content</code> controls generated HTML body content</li>
                            <li><code>.pf-related</code> controls the related pages section</li>
                            <li><code>.pf-related-card</code> controls each related page card</li>
                        </ul>
                    </div>
                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <h3 class="font-bold text-slate-900">Sample CSS</h3>
                            <button type="button" id="apply-demo-css" class="rounded-md bg-slate-900 px-3 py-1.5 text-sm font-semibold text-white hover:bg-slate-700">Use This CSS</button>
                        </div>
                        <pre class="overflow-x-auto rounded-lg bg-slate-950 p-4 text-xs text-slate-100"><code id="demo-css-code">.pf-card {
  border-radius: 6px;
  box-shadow: 0 24px 70px rgba(15, 23, 42, 0.14);
}

.pf-hero {
  background: linear-gradient(135deg, #eff6ff, #ffffff);
}

.pf-featured {
  aspect-ratio: 16 / 7;
}

.pf-content h3 {
  border-left: 4px solid var(--pf-accent);
  padding-left: 14px;
}

.pf-related-card {
  background: #f8fafc;
}</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('seo_scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        (function () {
            function openModal(id) {
                closeModals();
                var modal = document.getElementById(id);
                if (modal) {
                    modal.classList.add('active');
                    modal.setAttribute('aria-hidden', 'false');
                }
            }

            function closeModals() {
                document.querySelectorAll('.nirjon-seo-modal').forEach(function (modal) {
                    modal.classList.remove('active');
                    modal.setAttribute('aria-hidden', 'true');
                });
            }

            window.nirjonSeoOpenModal = openModal;
            window.nirjonSeoCloseModals = closeModals;

            document.addEventListener('click', function (event) {
                var opener = event.target.closest('[data-open-modal]');
                if (opener) {
                    event.preventDefault();
                    openModal(opener.getAttribute('data-open-modal'));
                    return;
                }

                if (event.target.closest('[data-close-modal]')) {
                    event.preventDefault();
                    closeModals();
                }
            });
        })();

        (function () {
            'use strict';

            var routes = {
                pages: @json(route('seo.generator.apiPages')),
                generate: @json(route('seo.generator.apiGenerate')),
                pageBase: @json(url('admin/seo-admin/generator/api-pages')),
                destroyPageBase: @json(url('admin/seo-admin/generator/api-pages'))
            };

            var editor = null;

            function byId(id) {
                return document.getElementById(id);
            }

            function getValue(id) {
                var element = byId(id);
                return element ? element.value : '';
            }

            function switchTab(tabName) {
                document.querySelectorAll('.nirjon-seo-tab-content').forEach(function (element) {
                    element.classList.remove('active');
                });

                document.querySelectorAll('.nirjon-seo-tab-btn').forEach(function (element) {
                    element.classList.remove('active');
                });

                var content = byId('content-' + tabName);
                var tab = byId('tab-' + tabName);

                if (content) {
                    content.classList.add('active');
                }

                if (tab) {
                    tab.classList.add('active');
                }

                if (tabName === 'pages') {
                    fetchAndRenderPages();
                }
            }

            function editorHtml() {
                if (editor && typeof editor.getContent === 'function') {
                    return editor.getContent();
                }

                return getValue('nirjon_seo_editor');
            }

            function resetEditor() {
                if (editor && typeof editor.setContent === 'function') {
                    editor.setContent('');
                    return;
                }

                var content = byId('nirjon_seo_editor');
                if (content) {
                    content.value = '';
                }
            }

            function setValue(id, value) {
                var element = byId(id);
                if (element) {
                    element.value = value || '';
                }
            }

            function setEditorHtml(value) {
                if (editor && typeof editor.setContent === 'function') {
                    editor.setContent(value || '');
                    return;
                }

                setValue('nirjon_seo_editor', value || '');
            }

            function openModal(id) {
                window.nirjonSeoOpenModal(id);
            }

            function closeModals() {
                window.nirjonSeoCloseModals();
            }

            function demoPayload(type) {
                if (type === 'restaurant') {
                    return {
                        templateTitle: '{Premium|Modern|Creative} {0} Menu Design in {1}',
                        templateSlug: '{0}-menu-design-{1}',
                        content: '<h3>Professional {0} Menu Design in {1}</h3><p>Your menu is often the first sales tool your customer sees. We create <strong>{premium|modern|conversion-focused}</strong> menu designs that help restaurants, cafes, and food brands present offers clearly.</p><p>Our team combines layout, typography, food imagery, and local SEO structure so your {0} business in <strong>{1}</strong> looks polished online.</p>',
                        meta_title: '{0} Menu Design in {1} | BlackTech',
                        meta_description: 'Professional {0} menu design services in {1}. Get a polished restaurant menu and SEO-ready landing page.',
                        meta_keywords: '{0}, menu design, restaurant branding, {1}',
                        bundle1: 'Restaurant, Cafe, Bistro',
                        bundle2: 'Dhaka, Sylhet, Chittagong'
                    };
                }

                return {
                    templateTitle: 'Expert {0} Consultancy in {1}',
                    templateSlug: 'expert-{0}-consultancy-{1}',
                    content: '<h3>Need {0} Services in {1}?</h3><p>BlackTech Consultancy helps growing businesses plan, build, and optimize professional <strong>{0}</strong> solutions for teams in <strong>{1}</strong> and beyond.</p><p>From discovery and implementation to reporting and continuous improvement, our consultants focus on measurable outcomes, clean delivery, and long-term scalability.</p><p>Get the {premium|fast|reliable} experience your project deserves.</p>',
                    meta_title: 'Top {0} Services in {1} | BlackTech',
                    meta_description: 'Hire expert {0} developers in {1} for your business growth. Professional services by BlackTech.',
                    meta_keywords: '{0}, {1}, expert services, BlackTech',
                    bundle1: 'Web Development, SEO Audit, API Integration',
                    bundle2: 'Dhaka, Sylhet'
                };
            }

            function fillDemoData(type) {
                var demo = demoPayload(type);
                setValue('templateTitle', demo.templateTitle);
                setValue('templateSlug', demo.templateSlug);
                setEditorHtml(demo.content);
                setValue('meta_title', demo.meta_title);
                setValue('meta_description', demo.meta_description);
                setValue('meta_keywords', demo.meta_keywords);
                setValue('author', 'Nirjon Roy');
                setValue('publisher', 'BlackTech Consultancy');
                setValue('copyright', '\u00a9 2026 BlackTech Consultancy');
                setValue('site_name', 'BlackTech Consultancy');
                setValue('bundle1', demo.bundle1);
                setValue('bundle2', demo.bundle2);
                setValue('primary_color', '#111827');
                setValue('accent_color', '#2563eb');
                setValue('background_color', '#f8fafc');
                setValue('text_color', '#1f2937');
                setValue('font_family', 'Inter, Arial, sans-serif');
                setValue('container_width', '960px');
                closeModals();
                switchTab('generator');
            }

            async function editPage(id) {
                if (!id) {
                    return;
                }

                try {
                    var response = await fetch(routes.pageBase + '/' + encodeURIComponent(id), {
                        headers: { 'Accept': 'application/json' }
                    });
                    var data = await response.json();

                    if (!response.ok || !data.template) {
                        alert('Could not load page data for editing.');
                        return;
                    }

                    var template = data.template;
                    setValue('templateTitle', template.title_structure || data.page.final_title || '');
                    setValue('templateSlug', template.slug_structure || data.page.url_slug || '');
                    setEditorHtml(template.content || data.page.final_content || '');
                    setValue('meta_title', template.meta_title || data.page.meta_title || '');
                    setValue('meta_description', template.meta_description || data.page.meta_description || '');
                    setValue('meta_keywords', template.meta_keywords || data.page.meta_keywords || '');
                    setValue('author', template.author || '');
                    setValue('publisher', template.publisher || '');
                    setValue('copyright', template.copyright || '');
                    setValue('site_name', template.site_name || '');
                    setValue('primary_color', template.primary_color || '#111827');
                    setValue('accent_color', template.accent_color || '#2563eb');
                    setValue('background_color', template.background_color || '#f8fafc');
                    setValue('text_color', template.text_color || '#1f2937');
                    setValue('font_family', template.font_family || 'Inter, Arial, sans-serif');
                    setValue('container_width', template.container_width || '960px');
                    setValue('custom_css', template.custom_css || '');
                    setValue('header_css', template.header_css || '');
                    setValue('header_js', template.header_js || '');
                    setValue('bundle1', data.keyword_bundle_1 || '');
                    setValue('bundle2', data.keyword_bundle_2 || '');
                    switchTab('generator');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } catch (error) {
                    alert('Could not load page data for editing.');
                }
            }

            async function fetchAndRenderPages() {
                var container = byId('generatedPagesContainer');

                if (!container) {
                    return;
                }

                container.innerHTML = '<div class="text-gray-500">Loading pages...</div>';

                try {
                    var response = await fetch(routes.pages);
                    var data = await response.json();
                    var pages = Array.isArray(data) ? data : (data.data || []);

                    if (!response.ok) {
                        container.innerHTML = '<div class="text-red-500">Failed to load pages. ' + (data.message || '') + '</div>';
                        return;
                    }

                    if (pages.length === 0) {
                        container.innerHTML = '<div class="text-gray-500">No generated pages found.</div>';
                        return;
                    }

                    var html = '<div class="overflow-x-auto"><table class="min-w-full border border-gray-200 bg-white">';
                    html += '<thead><tr class="bg-gray-100 text-left text-sm uppercase tracking-wider text-gray-600">';
                    html += '<th class="border-b px-4 py-2">ID</th>';
                    html += '<th class="border-b px-4 py-2">Title</th>';
                    html += '<th class="border-b px-4 py-2">Slug</th>';
                    html += '<th class="border-b px-4 py-2">Created At</th>';
                    html += '<th class="border-b px-4 py-2">Actions</th>';
                    html += '</tr></thead><tbody class="text-sm">';

                    pages.forEach(function (page) {
                        var viewUrl = '/' + encodeURIComponent(page.url_slug || '');
                        var date = page.created_at ? new Date(page.created_at).toLocaleString() : 'N/A';

                        html += '<tr class="hover:bg-gray-50">';
                        html += '<td class="border-b px-4 py-2">' + (page.id || '') + '</td>';
                        html += '<td class="border-b px-4 py-2 font-medium">' + (page.final_title || '') + '</td>';
                        html += '<td class="border-b px-4 py-2 text-gray-500">' + (page.url_slug || '') + '</td>';
                        html += '<td class="border-b px-4 py-2">' + date + '</td>';
                        html += '<td class="border-b px-4 py-2">';
                        html += '<div class="flex items-center gap-3">';
                        html += '<a href="' + viewUrl + '" target="_blank" class="text-blue-600 hover:underline">View</a>';
                        html += '<button type="button" data-edit-page="' + encodeURIComponent(page.id) + '" class="text-amber-600 hover:underline">Edit</button>';
                        html += '<button type="button" data-delete-page="' + encodeURIComponent(page.id) + '" class="text-red-600 hover:underline">Delete</button>';
                        html += '</div>';
                        html += '</td>';
                        html += '</tr>';
                    });

                    html += '</tbody></table></div>';
                    container.innerHTML = html;
                } catch (error) {
                    container.innerHTML = '<div class="text-red-500">An error occurred while fetching pages.</div>';
                }
            }

            async function deletePage(id) {
                if (!id || !confirm('Delete this generated page?')) {
                    return;
                }

                var csrfToken = document.querySelector('meta[name="csrf-token"]');

                try {
                    var response = await fetch(routes.destroyPageBase + '/' + encodeURIComponent(id), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
                            'Accept': 'application/json'
                        }
                    });
                    var data = await response.json();

                    if (response.ok && data.success) {
                        fetchAndRenderPages();
                        return;
                    }

                    alert('Could not delete page.');
                } catch (error) {
                    alert('Could not delete page.');
                }
            }

            async function generatePages() {
                var formData = new FormData();
                var featuredImage = byId('featured_image');
                var bundle1Input = getValue('bundle1');
                var bundle2Input = getValue('bundle2');
                var bundle1 = bundle1Input ? bundle1Input.split(',').map(function (item) { return item.trim(); }).filter(Boolean) : [];
                var bundle2 = bundle2Input ? bundle2Input.split(',').map(function (item) { return item.trim(); }).filter(Boolean) : [];
                var bundles = [];

                if (bundle1.length > 0) {
                    bundles.push({ name: 'Bundle 1', keywords: bundle1 });
                }

                if (bundle2.length > 0) {
                    bundles.push({ name: 'Bundle 2', keywords: bundle2 });
                }

                formData.append('title', getValue('templateTitle'));
                formData.append('slug', getValue('templateSlug'));
                formData.append('content', editorHtml());
                formData.append('metaTitle', getValue('meta_title'));
                formData.append('metaDescription', getValue('meta_description'));
                formData.append('metaKeywords', getValue('meta_keywords'));
                formData.append('author', getValue('author'));
                formData.append('publisher', getValue('publisher'));
                formData.append('copyright', getValue('copyright'));
                formData.append('siteName', getValue('site_name'));
                formData.append('primaryColor', getValue('primary_color'));
                formData.append('accentColor', getValue('accent_color'));
                formData.append('backgroundColor', getValue('background_color'));
                formData.append('textColor', getValue('text_color'));
                formData.append('fontFamily', getValue('font_family'));
                formData.append('containerWidth', getValue('container_width'));
                formData.append('customCss', getValue('custom_css'));
                formData.append('headerCss', getValue('header_css'));
                formData.append('headerJs', getValue('header_js'));
                formData.append('keyword_bundle_1', bundle1Input);
                formData.append('keyword_bundle_2', bundle2Input);
                formData.append('bundle1', bundle1Input);
                formData.append('bundle2', bundle2Input);
                formData.append('bundles', JSON.stringify(bundles));

                if (featuredImage && featuredImage.files.length > 0) {
                    formData.append('featured_image', featuredImage.files[0]);
                }

                var logoImage = byId('logo_image');
                if (logoImage && logoImage.files.length > 0) {
                    formData.append('logo_image', logoImage.files[0]);
                }

                var btn = byId('generate-btn');
                var loading = byId('loading-ui');
                var alertContainer = byId('alert-container');

                if (btn) {
                    btn.disabled = true;
                }

                if (loading) {
                    loading.classList.remove('hidden');
                }

                if (alertContainer) {
                    alertContainer.classList.add('hidden');
                }

                try {
                    var csrfToken = document.querySelector('meta[name="csrf-token"]');
                    var response = await fetch(routes.generate, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken ? csrfToken.content : ''
                        },
                        body: formData
                    });
                    var data = await response.json();

                    if (response.ok && data.success) {
                        alertContainer.textContent = data.message || 'Generation successful!';
                        alertContainer.className = 'mb-4 rounded p-4 font-medium text-white bg-green-500 block';
                        byId('generator-form').reset();
                        resetEditor();
                        setTimeout(function () {
                            switchTab('pages');
                        }, 1500);
                    } else {
                        alertContainer.textContent = data.message || 'Error occurred';
                        alertContainer.className = 'mb-4 rounded p-4 font-medium text-white bg-red-500 block';
                    }
                } catch (error) {
                    alertContainer.textContent = 'A network error occurred.';
                    alertContainer.className = 'mb-4 rounded p-4 font-medium text-white bg-red-500 block';
                } finally {
                    if (btn) {
                        btn.disabled = false;
                    }

                    if (loading) {
                        loading.classList.add('hidden');
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                var editorElement = byId('nirjon_seo_editor');
                var generateButton = byId('generate-btn');

                if (editorElement && window.tinymce) {
                    window.tinymce.init({
                        selector: '#nirjon_seo_editor',
                        height: 460,
                        menubar: 'file edit view insert format tools table help',
                        branding: false,
                        promotion: false,
                        placeholder: 'Write page content...',
                        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table wordcount help emoticons codesample',
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table blockquote codesample | removeformat code fullscreen preview',
                        toolbar_mode: 'sliding',
                        setup: function (tinyEditor) {
                            editor = tinyEditor;
                            tinyEditor.on('change keyup', function () {
                                tinyEditor.save();
                            });
                        }
                    });
                }

                document.querySelectorAll('[data-nirjon-tab]').forEach(function (tab) {
                    tab.addEventListener('click', function () {
                        switchTab(tab.getAttribute('data-nirjon-tab'));
                    });
                });

                if (generateButton) {
                    generateButton.addEventListener('click', generatePages);
                }

                document.addEventListener('click', function (event) {
                    var target = event.target.closest('[data-open-modal], [data-close-modal], [data-demo-type], #apply-demo-css, [data-edit-page], [data-delete-page]');

                    if (!target) {
                        return;
                    }

                    if (target.matches('[data-open-modal]')) {
                        openModal(target.getAttribute('data-open-modal'));
                        return;
                    }

                    if (target.matches('[data-close-modal]')) {
                        closeModals();
                        return;
                    }

                    if (target.matches('[data-demo-type]')) {
                        fillDemoData(target.getAttribute('data-demo-type'));
                        return;
                    }

                    if (target.matches('#apply-demo-css')) {
                        setValue('custom_css', byId('demo-css-code') ? byId('demo-css-code').textContent : '');
                        closeModals();
                        return;
                    }

                    if (target.matches('[data-edit-page]')) {
                        editPage(target.getAttribute('data-edit-page'));
                        return;
                    }

                    if (target.matches('[data-delete-page]')) {
                        deletePage(target.getAttribute('data-delete-page'));
                    }
                });

                fetchAndRenderPages();

            });
        })();
    </script>
@endpush
