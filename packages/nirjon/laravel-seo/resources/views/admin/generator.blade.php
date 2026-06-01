<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PageForge Generator</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tab-btn.active { border-bottom: 2px solid #2563eb; color: #2563eb; }
        .note-editor.note-frame { border-color: #d1d5db; border-radius: 0.375rem; margin-top: 0.25rem; }
        .note-toolbar { background: #f9fafb; border-top-left-radius: 0.375rem; border-top-right-radius: 0.375rem; }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6">PageForge Admin Generator</h1>
        
        <!-- Tabs -->
        <div class="flex border-b mb-6">
            <button id="tab-generator" class="tab-btn active px-4 py-2 font-medium text-gray-600 hover:text-blue-600 focus:outline-none" onclick="switchTab('generator')">
                Generator Form
            </button>
            <button id="tab-pages" class="tab-btn px-4 py-2 font-medium text-gray-600 hover:text-blue-600 focus:outline-none" onclick="switchTab('pages')">
                Generated Pages
            </button>
        </div>

        <div id="alert-container" class="hidden mb-4 p-4 rounded text-white font-medium"></div>

        <!-- Tab Content: Generator Form -->
        <div id="content-generator" class="tab-content active">
            <form id="generator-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Template Title</label>
                    <input type="text" id="templateTitle" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Template Slug</label>
                    <input type="text" id="templateSlug" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Content</label>
                    <textarea id="content" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border" required></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Meta Title</label>
                    <input type="text" id="meta_title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Meta Description</label>
                    <textarea id="meta_description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Meta Keywords</label>
                    <input type="text" id="meta_keywords" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Upload Featured Image</label>
                    <input type="file" id="featured_image" accept="image/*" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Author</label>
                    <input type="text" id="author" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Publisher</label>
                    <input type="text" id="publisher" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Copyright</label>
                    <input type="text" id="copyright" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Site Name</label>
                    <input type="text" id="site_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Keyword Bundle 1 (comma separated)</label>
                    <input type="text" id="bundle1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Keyword Bundle 2 (comma separated)</label>
                    <input type="text" id="bundle2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border">
                </div>

                <div class="bg-blue-50 border border-blue-200 text-blue-800 p-5 rounded-md mb-6 text-sm shadow-sm">
    <h4 class="text-base font-bold mb-3 flex items-center text-blue-900">
        <span class="mr-2 text-lg"></span> How to Get the Best Results:
    </h4>
    <ul class="list-disc pl-5 space-y-3 text-gray-700">
        <li>
            <strong class="text-gray-900">Dynamic Keywords:</strong> Use <code>{0}</code> for words in your first bundle, and <code>{1}</code> for the second. 
            <br><span class="text-xs text-gray-500 italic">Example: "Looking for {0} repair in {1}?" will automatically become "Looking for iPhone repair in London?"</span>
        </li>
        <li>
            <strong class="text-gray-900">Keep Content Unique (Spintax):</strong> Prevent duplicate content penalties in SEO by using format like <code>{Best|Top|Reliable}</code>. The generator will randomly pick one word for every new page.
        </li>
        <li>
            <strong class="text-gray-900">Add Images & Videos:</strong> Make your content engaging! Feel free to paste standard HTML tags like <code>&lt;img src="..."&gt;</code> or YouTube <code>&lt;iframe src="..."&gt;</code> directly into the Content box.
        </li>
        <li>
            <strong class="text-gray-900">Social Media Preview:</strong> Upload a catchy Featured Image. This will automatically generate Open Graph (OG) tags so your pages look professional when shared on Facebook, Twitter, or LinkedIn.
        </li>
    </ul>
</div>

                <button type="button" id="generate-btn" onclick="generatePages()" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition-colors">
                    Generate Pages
                </button>
                <div id="loading-ui" class="hidden text-sm text-gray-500 mt-2">Loading... please wait.</div>
            </form>
        </div>

        <!-- Tab Content: Generated Pages -->
        <div id="content-pages" class="tab-content">
            <div id="generatedPagesContainer" class="space-y-4">
                <!-- Pages will be dynamically rendered here -->
                <div class="text-gray-500">Loading pages...</div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            $('#content').summernote({
                height: 220,
                placeholder: 'Write page content...',
                toolbar: [
                    ['style', ['bold', 'italic']],
                    ['insert', ['link']],
                    ['view', ['codeview']]
                ]
            });
        });

        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            
            document.getElementById('content-' + tabName).classList.add('active');
            document.getElementById('tab-' + (tabName === 'generator' ? 'generator' : 'pages')).classList.add('active');

            if (tabName === 'pages') {
                fetchAndRenderPages();
            }
        }

        async function fetchAndRenderPages() {
            const container = document.getElementById('generatedPagesContainer');
            container.innerHTML = '<div class="text-gray-500">Loading pages...</div>';

            try {
                const response = await fetch('/seo-admin/generator/api-pages');
                const data = await response.json();

                const pages = Array.isArray(data) ? data : (data.data || []);

                if (!response.ok) {
                    container.innerHTML = `<div class="text-red-500">Failed to load pages. ${data.message || ''}</div>`;
                    return;
                }

                if (pages.length === 0) {
                    container.innerHTML = '<div class="text-gray-500">No generated pages found.</div>';
                    return;
                }

                let html = '<div class="overflow-x-auto"><table class="min-w-full bg-white border border-gray-200">';
                html += `
                    <thead>
                        <tr class="bg-gray-100 text-left text-sm uppercase tracking-wider text-gray-600">
                            <th class="py-2 px-4 border-b">ID</th>
                            <th class="py-2 px-4 border-b">Title</th>
                            <th class="py-2 px-4 border-b">Slug</th>
                            <th class="py-2 px-4 border-b">Created At</th>
                            <th class="py-2 px-4 border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                `;

                pages.forEach(page => {
                    const viewUrl = '/' + page.url_slug;
                    const date = page.created_at ? new Date(page.created_at).toLocaleString() : 'N/A';
                    html += `
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 border-b">${page.id || ''}</td>
                            <td class="py-2 px-4 border-b font-medium">${page.final_title || ''}</td>
                            <td class="py-2 px-4 border-b text-gray-500">${page.url_slug || ''}</td>
                            <td class="py-2 px-4 border-b">${date}</td>
                            <td class="py-2 px-4 border-b">
                                <a href="${viewUrl}" target="_blank" class="text-blue-600 hover:underline">View</a>
                            </td>
                        </tr>
                    `;
                });

                html += '</tbody></table></div>';
                container.innerHTML = html;
            } catch (error) {
                container.innerHTML = '<div class="text-red-500">An error occurred while fetching pages.</div>';
                console.error(error);
            }
        }

        async function generatePages() {
            const templateTitle = document.getElementById('templateTitle').value;
            const templateSlug = document.getElementById('templateSlug').value;
            const content = $('#content').summernote('code');
            
            const metaTitle = document.getElementById('meta_title').value;
            const metaDescription = document.getElementById('meta_description').value;
            const metaKeywords = document.getElementById('meta_keywords').value;
            const author = document.getElementById('author').value;
            const publisher = document.getElementById('publisher').value;
            const copyright = document.getElementById('copyright').value;
            const siteName = document.getElementById('site_name').value;
            
            const bundle1Input = document.getElementById('bundle1').value;
            const bundle2Input = document.getElementById('bundle2').value;
            
            const bundle1 = bundle1Input ? bundle1Input.split(',').map(s => s.trim()).filter(s => s) : [];
            const bundle2 = bundle2Input ? bundle2Input.split(',').map(s => s.trim()).filter(s => s) : [];

            let bundles = [];
            if (bundle1.length > 0) {
                bundles.push({ name: 'Bundle 1', keywords: bundle1 });
            }
            if (bundle2.length > 0) {
                bundles.push({ name: 'Bundle 2', keywords: bundle2 });
            }

            const formData = new FormData();
            formData.append('title', templateTitle);
            formData.append('slug', templateSlug);
            formData.append('content', content);
            formData.append('metaTitle', metaTitle);
            formData.append('metaDescription', metaDescription);
            formData.append('metaKeywords', metaKeywords);
            formData.append('author', author);
            formData.append('publisher', publisher);
            formData.append('copyright', copyright);
            formData.append('siteName', siteName);
            
            if (document.getElementById('featured_image').files.length > 0) {
                formData.append('featured_image', document.getElementById('featured_image').files[0]);
            }
            formData.append('bundles', JSON.stringify(bundles));

            const btn = document.getElementById('generate-btn');
            const loading = document.getElementById('loading-ui');
            const alertContainer = document.getElementById('alert-container');

            btn.disabled = true;
            loading.classList.remove('hidden');
            alertContainer.classList.add('hidden');

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const tokenValue = csrfToken ? csrfToken.content : '';

                const response = await fetch('/seo-admin/generator/api-generate', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': tokenValue
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    alertContainer.textContent = data.message || 'Generation successful!';
                    alertContainer.className = 'mb-4 p-4 rounded text-white bg-green-500 block';
                    document.getElementById('generator-form').reset();
                    $('#content').summernote('reset');
                    setTimeout(() => switchTab('pages'), 1500);
                } else {
                    alertContainer.textContent = data.message || 'Error occurred';
                    alertContainer.className = 'mb-4 p-4 rounded text-white bg-red-500 block';
                }
            } catch (error) {
                alertContainer.textContent = 'A network error occurred.';
                alertContainer.className = 'mb-4 p-4 rounded text-white bg-red-500 block';
            } finally {
                btn.disabled = false;
                loading.classList.add('hidden');
            }
        }
        
        // Add window object exposure just to be completely safe in some environments
        window.generatePages = generatePages;
        window.fetchAndRenderPages = fetchAndRenderPages;
        window.switchTab = switchTab;
    </script>
</body>
</html>
