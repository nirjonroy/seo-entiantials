@extends(config('seo.layout', 'seo::layouts.app'))

@push('seo_styles')
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .nirjon-seo-tab-content { display: none; }
        .nirjon-seo-tab-content.active { display: block; }
        .nirjon-seo-tab-btn.active { border-bottom: 2px solid #2563eb; color: #2563eb; }
        .nirjon-seo-editor-wrap .tox-tinymce { border-color: #d1d5db; border-radius: 0.5rem; }
        .nirjon-seo-panel-title { color: #111827; font-size: 0.95rem; font-weight: 800; }
    </style>
@endpush

@section(config('seo.section', 'content'))
    <main class="min-h-screen bg-gray-100 p-8">
        <div class="mx-auto max-w-5xl rounded-lg bg-white p-6 shadow-md">
        <h1 class="mb-6 text-2xl font-bold">PageForge Admin Generator</h1>

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
    </main>
@endsection

@push('seo_scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        (function () {
            'use strict';

            var routes = {
                pages: @json(route('seo.generator.apiPages')),
                generate: @json(route('seo.generator.apiGenerate')),
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
                    var target = event.target;

                    if (target && target.matches('[data-delete-page]')) {
                        deletePage(target.getAttribute('data-delete-page'));
                    }
                });

                fetchAndRenderPages();

            });
        })();
    </script>
@endpush
