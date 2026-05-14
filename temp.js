
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

                // Handle both { data: [...] } and directly [...] format
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
            const content = document.getElementById('content').value;
            
            const bundle1Input = document.getElementById('bundle1').value;
            const bundle2Input = document.getElementById('bundle2').value;
            
            const bundle1 = bundle1Input ? bundle1Input.split(',').map(s => s.trim()).filter(s => s) : [];
            const bundle2 = bundle2Input ? bundle2Input.split(',').map(s => s.trim()).filter(s => s) : [];

            const payload = {
                title: templateTitle,
                slug: templateSlug,
                content: content,
                keyword_bundles: []
            };

            if (bundle1.length > 0) {
                payload.keyword_bundles.push({ name: 'Bundle 1', keywords: bundle1 });
            }
            if (bundle2.length > 0) {
                payload.keyword_bundles.push({ name: 'Bundle 2', keywords: bundle2 });
            }

            const btn = document.getElementById('generate-btn');
            const loading = document.getElementById('loading-ui');
            const alertContainer = document.getElementById('alert-container');

            btn.disabled = true;
            loading.classList.remove('hidden');
            alertContainer.classList.add('hidden');

            try {
                const response = await fetch('/seo-admin/generator/api-generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    alertContainer.textContent = data.message || 'Generation started in background!';
                    alertContainer.className = 'mb-4 p-4 rounded text-white bg-green-500 block';
                    // Reset form
                    document.getElementById('generator-form').reset();
                    // Switch to pages tab after successful generation request
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
    
