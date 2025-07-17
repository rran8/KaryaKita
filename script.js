// script.js

document.addEventListener('DOMContentLoaded', () => {
    const path = window.location.pathname;

    // Fungsi untuk memuat header dan footer
    const loadCommonElements = async () => {
        const headerPlaceholder = document.getElementById('header-placeholder');
        const footerPlaceholder = document.getElementById('footer-placeholder');

        if (headerPlaceholder) {
            headerPlaceholder.innerHTML = `
                <header>
                    <div class="container">
                        <h1 onclick="window.location.href='index.html'">KaryaKita</h1>
                        <nav>
                            <ul>
                                <li><a href="index.html">Beranda</a></li>
                                <li><a href="gallery.html">Galeri Karya</a></li>
                                <li><a href="submit_work.html">Kirim Karya</a></li>
                                <li><a href="login.html">Admin Login</a></li>
                            </ul>
                        </nav>
                    </div>
                </header>
            `;
        }

        if (footerPlaceholder) {
            footerPlaceholder.innerHTML = `
                <footer>
                    <div class="container">
                        <p>&copy; 2024 KaryaKita. All rights reserved.</p>
                    </div>
                </footer>
            `;
        }
    };

    loadCommonElements();

    // Fungsi untuk menampilkan pesan (menggantikan alert)
    const showMessage = (elementId, message, type) => {
        const messageElement = document.getElementById(elementId);
        if (messageElement) {
            messageElement.textContent = message;
            messageElement.className = `message ${type}`; // Add class for styling (success/error)
            messageElement.style.display = 'block';
            setTimeout(() => {
                messageElement.style.display = 'none';
                messageElement.textContent = '';
            }, 5000); // Sembunyikan setelah 5 detik
        }
    };

    // --- Halaman Beranda (index.html) ---
    if (path.includes('index.html') || path === '/') {
        const latestWorksContainer = document.getElementById('latest-works-grid');
        const popularWorksContainer = document.getElementById('popular-works-grid');

        const fetchWorks = async () => {
            try {
                const response = await fetch('works_api.php', { method: 'GET' });
                const data = await response.json();

                if (data.success) {
                    const works = data.works;

                    // Karya Terbaru (3 terbaru)
                    const sortedByDate = [...works].sort((a, b) => new Date(b.upload_date) - new Date(a.upload_date));
                    renderWorks(sortedByDate.slice(0, 3), latestWorksContainer);

                    // Karya Terpopuler (3 terpopuler berdasarkan views)
                    const sortedByViews = [...works].sort((a, b) => b.views - a.views);
                    renderWorks(sortedByViews.slice(0, 3), popularWorksContainer);

                } else {
                    console.error('Gagal mengambil karya:', data.message);
                    if (latestWorksContainer) latestWorksContainer.innerHTML = '<p class="text-center text-gray-600">Gagal memuat karya terbaru.</p>';
                    if (popularWorksContainer) popularWorksContainer.innerHTML = '<p class="text-center text-gray-600">Gagal memuat karya terpopuler.</p>';
                }
            } catch (error) {
                console.error('Error fetching works:', error);
                if (latestWorksContainer) latestWorksContainer.innerHTML = '<p class="text-center text-gray-600">Terjadi kesalahan saat memuat karya.</p>';
                if (popularWorksContainer) popularWorksContainer.innerHTML = '<p class="text-center text-gray-600">Terjadi kesalahan saat memuat karya.</p>';
            }
        };

        const renderWorks = (works, container) => {
            if (!container) return;
            container.innerHTML = '';
            if (works.length === 0) {
                container.innerHTML = '<p class="text-center text-gray-600 col-span-full">Belum ada karya.</p>';
                return;
            }
            works.forEach(work => {
                const workCard = document.createElement('div');
                workCard.className = 'work-card';
                workCard.innerHTML = `
                    <img src="${getThumbnail(work.media_type, work.media_url, work.title)}" alt="${work.title}" onerror="this.src='https://placehold.co/400x250/A78BFA/ffffff?text=${encodeURIComponent(work.title)}'">
                    <div class="work-card-content">
                        <h4>${work.title}</h4>
                        <p>Oleh: ${work.student_name}</p>
                        <p class="date">${new Date(work.upload_date).toLocaleDateString()}</p>
                    </div>
                `;
                workCard.addEventListener('click', () => {
                    localStorage.setItem('selectedWorkId', work.id);
                    window.location.href = 'detail.html';
                });
                container.appendChild(workCard);
            });
        };

        // Helper untuk thumbnail
        const getThumbnail = (mediaType, mediaUrl, title) => {
            if (mediaType === 'image' && mediaUrl) {
                return mediaUrl;
            }
            if (mediaType === 'youtube') {
                const youtubeIdMatch = mediaUrl.match(/(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=|embed\/|v\/|)([\w-]{11})(?:\S+)?/);
                if (youtubeIdMatch && youtubeIdMatch[1]) {
                    return `https://img.youtube.com/vi/${youtubeIdMatch[1]}/hqdefault.jpg`;
                }
            }
            return `https://placehold.co/400x250/A78BFA/ffffff?text=${encodeURIComponent(title)}`;
        };

        fetchWorks();
    }

    // --- Halaman Galeri Karya (gallery.html) ---
    if (path.includes('gallery.html')) {
        const galleryGrid = document.getElementById('gallery-grid');
        const searchInput = document.getElementById('search-input');
        const categoryFilter = document.getElementById('category-filter');
        const studentFilter = document.getElementById('student-filter');
        let allWorks = []; // Menyimpan semua karya yang diambil dari API

        const fetchCategories = async () => {
            try {
                const response = await fetch('categories_api.php', { method: 'GET' });
                const data = await response.json();
                if (data.success) {
                    categoryFilter.innerHTML = '<option value="all">Semua Kategori</option>';
                    data.categories.forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.name;
                        option.textContent = cat.name;
                        categoryFilter.appendChild(option);
                    });
                } else {
                    console.error('Gagal memuat kategori:', data.message);
                }
            } catch (error) {
                console.error('Error fetching categories:', error);
            }
        };

        const fetchAndRenderGalleryWorks = async () => {
            try {
                const response = await fetch('works_api.php', { method: 'GET' });
                const data = await response.json();

                if (data.success) {
                    allWorks = data.works;
                    applyFilters();
                } else {
                    console.error('Gagal mengambil karya galeri:', data.message);
                    if (galleryGrid) galleryGrid.innerHTML = '<p class="text-center text-gray-600 col-span-full">Gagal memuat karya galeri.</p>';
                }
            } catch (error) {
                console.error('Error fetching gallery works:', error);
                if (galleryGrid) galleryGrid.innerHTML = '<p class="text-center text-gray-600 col-span-full">Terjadi kesalahan saat memuat karya galeri.</p>';
            }
        };

        const applyFilters = () => {
            let filtered = [...allWorks];
            const searchTerm = searchInput.value.toLowerCase();
            const selectedCategory = categoryFilter.value;
            const studentNameFilter = studentFilter.value.toLowerCase();

            if (selectedCategory !== 'all') {
                filtered = filtered.filter(work => work.category === selectedCategory);
            }

            if (studentNameFilter) {
                filtered = filtered.filter(work => work.student_name.toLowerCase().includes(studentNameFilter));
            }

            if (searchTerm) {
                filtered = filtered.filter(work =>
                    work.title.toLowerCase().includes(searchTerm) ||
                    work.student_name.toLowerCase().includes(searchTerm)
                );
            }

            renderGalleryWorks(filtered);
        };

        const renderGalleryWorks = (works) => {
            if (!galleryGrid) return;
            galleryGrid.innerHTML = '';
            if (works.length === 0) {
                galleryGrid.innerHTML = '<p class="text-center text-gray-600 col-span-full">Tidak ada karya yang ditemukan.</p>';
                return;
            }
            works.forEach(work => {
                const workCard = document.createElement('div');
                workCard.className = 'work-card';
                workCard.innerHTML = `
                    <img src="${getThumbnail(work.media_type, work.media_url, work.title)}" alt="${work.title}" onerror="this.src='https://placehold.co/400x250/A78BFA/ffffff?text=${encodeURIComponent(work.title)}'">
                    <div class="work-card-content">
                        <h4>${work.title}</h4>
                        <p>Oleh: ${work.student_name}</p>
                        <p class="date">${new Date(work.upload_date).toLocaleDateString()}</p>
                    </div>
                `;
                workCard.addEventListener('click', () => {
                    localStorage.setItem('selectedWorkId', work.id);
                    window.location.href = 'detail.html';
                });
                galleryGrid.appendChild(workCard);
            });
        };

        // Helper untuk thumbnail (duplikasi dari index.html, bisa dipisah ke file util)
        const getThumbnail = (mediaType, mediaUrl, title) => {
            if (mediaType === 'image' && mediaUrl) {
                return mediaUrl;
            }
            if (mediaType === 'youtube') {
                const youtubeIdMatch = mediaUrl.match(/(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=|embed\/|v\/|)([\w-]{11})(?:\S+)?/);
                if (youtubeIdMatch && youtubeIdMatch[1]) {
                    return `https://img.youtube.com/vi/${youtubeIdMatch[1]}/hqdefault.jpg`;
                }
            }
            return `https://placehold.co/400x250/A78BFA/ffffff?text=${encodeURIComponent(title)}`;
        };

        searchInput.addEventListener('input', applyFilters);
        categoryFilter.addEventListener('change', applyFilters);
        studentFilter.addEventListener('input', applyFilters);

        fetchCategories();
        fetchAndRenderGalleryWorks();
    }

    // --- Halaman Detail Karya (detail.html) ---
    if (path.includes('detail.html')) {
        const workDetailContainer = document.getElementById('work-detail-container');

        const fetchWorkDetail = async () => {
            const workId = localStorage.getItem('selectedWorkId');
            if (!workId) {
                if (workDetailContainer) workDetailContainer.innerHTML = '<p class="text-center text-gray-700">Karya tidak ditemukan. Silakan kembali ke galeri.</p>';
                return;
            }

            try {
                const response = await fetch(`works_api.php?id=${workId}`, { method: 'GET' });
                const data = await response.json();

                if (data.success && data.works && data.works.length > 0) {
                    const work = data.works.find(w => w.id == workId); // Find the specific work by ID
                    if (work) {
                        renderWorkDetail(work);
                        // Increment views
                        const formData = new FormData();
                        formData.append('action', 'increment_views');
                        formData.append('id', work.id);
                        await fetch('works_api.php', {
                            method: 'POST',
                            body: formData
                        });
                    } else {
                        if (workDetailContainer) workDetailContainer.innerHTML = '<p class="text-center text-gray-700">Karya tidak ditemukan.</p>';
                    }
                } else {
                    console.error('Gagal mengambil detail karya:', data.message);
                    if (workDetailContainer) workDetailContainer.innerHTML = '<p class="text-center text-gray-700">Gagal memuat detail karya.</p>';
                }
            } catch (error) {
                console.error('Error fetching work detail:', error);
                if (workDetailContainer) workDetailContainer.innerHTML = '<p class="text-center text-gray-700">Terjadi kesalahan saat memuat detail karya.</p>';
            }
        };

        const renderWorkDetail = (work) => {
            if (!workDetailContainer) return;

            let mediaHtml = '';
            if (work.media_type === 'image') {
                mediaHtml = `<img src="${work.media_url}" alt="${work.title}" onerror="this.src='https://placehold.co/800x600/A78BFA/ffffff?text=Gambar+Tidak+Tersedia'">`;
            } else if (work.media_type === 'pdf') {
                mediaHtml = `<iframe src="${work.media_url}" title="PDF Viewer"></iframe>`;
            } else if (work.media_type === 'youtube') {
                const youtubeIdMatch = work.media_url.match(/(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=|embed\/|v\/|)([\w-]{11})(?:\S+)?/);
                const youtubeId = youtubeIdMatch ? youtubeIdMatch[1] : null;
                if (youtubeId) {
                    mediaHtml = `<iframe src="https://www.youtube.com/embed/${youtubeId}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
                } else {
                    mediaHtml = `<p class="text-red-500">URL YouTube tidak valid atau tidak dapat di-embed.</p>`;
                }
            } else if (work.media_type === 'link') {
                mediaHtml = `<a href="${work.media_url}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline text-lg">Lihat Karya (Link Eksternal)</a>`;
            } else {
                mediaHtml = `<p class="text-gray-600">Tipe media tidak didukung.</p>`;
            }

            workDetailContainer.innerHTML = `
                <h2 class="text-4xl font-extrabold text-gray-900 mb-4">${work.title}</h2>
                <div class="detail-meta">
                    <span>Oleh: <strong>${work.student_name}</strong></span>
                    <span>Tanggal Upload: <strong>${new Date(work.upload_date).toLocaleDateString()}</strong></span>
                </div>

                <div class="detail-media">
                    ${mediaHtml}
                </div>

                <div class="detail-description">
                    <h3>Deskripsi Karya:</h3>
                    <p>${work.description}</p>
                </div>
                <div class="detail-stats">
                    <p>Views: ${work.views}</p>
                    <p>Kategori: ${work.category}</p>
                </div>
            `;
        };

        fetchWorkDetail();
    }

    // --- Halaman Kirim Karya (submit_work.html) ---
    if (path.includes('submit_work.html')) {
        const submitWorkForm = document.getElementById('submit-work-form');
        const submitMessage = document.getElementById('submit-message');
        const categorySelect = document.getElementById('category');

        const fetchCategoriesForSubmit = async () => {
            try {
                const response = await fetch('categories_api.php', { method: 'GET' });
                const data = await response.json();
                if (data.success) {
                    categorySelect.innerHTML = ''; // Clear existing options
                    data.categories.forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.name;
                        option.textContent = cat.name;
                        categorySelect.appendChild(option);
                    });
                } else {
                    console.error('Gagal memuat kategori:', data.message);
                    showMessage('submit-message', 'Gagal memuat kategori untuk form.', 'error');
                }
            } catch (error) {
                console.error('Error fetching categories:', error);
                showMessage('submit-message', 'Terjadi kesalahan saat memuat kategori.', 'error');
            }
        };

        if (submitWorkForm) {
            submitWorkForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(submitWorkForm);
                formData.append('action', 'submit'); // Aksi untuk mengirim karya

                const submitButton = submitWorkForm.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.textContent = 'Mengirim...';

                try {
                    const response = await fetch('submissions_api.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        showMessage('submit-message', data.message, 'success');
                        submitWorkForm.reset(); // Reset form setelah berhasil
                    } else {
                        showMessage('submit-message', data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error submitting work:', error);
                    showMessage('submit-message', 'Terjadi kesalahan saat mengirim karya.', 'error');
                } finally {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Kirim Karya';
                }
            });
        }
        fetchCategoriesForSubmit();
    }
});
