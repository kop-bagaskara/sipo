<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>E-Book PKB 2026 - PT. Krisanthium Offset Printing</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            font-family: 'Georgia', 'Times New Roman', serif;
            background: #f5f5f5;
        }

        .ebook-container {
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 0;
        }

        .flipbook-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            background: #fff;
            display: flex;
            flex-direction: column;
        }

        .header-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            gap: 15px;
        }

        .header-brand {
            color: white;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            flex-shrink: 0;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            max-width: 400px;
        }

        .search-input {
            flex: 1;
            padding: 8px 15px;
            border: none;
            border-radius: 20px;
            font-size: 14px;
            outline: none;
            background: rgba(255, 255, 255, 0.95);
            color: #333;
        }

        .search-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .search-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .search-results {
            position: absolute;
            top: 60px;
            right: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            padding: 10px;
            max-width: 300px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 200;
            display: none;
        }

        .search-result-item {
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px;
            margin-bottom: 4px;
            font-size: 13px;
            transition: background 0.2s;
        }

        .search-result-item:hover {
            background: #f0f0f0;
        }

        .search-highlight {
            background: yellow;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: 600;
        }

        .controls {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            background: #fff;
            padding: 10px 15px;
            border-bottom: 1px solid #e0e0e0;
            flex-shrink: 0;
        }

        .btn-nav {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-nav:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.4);
        }

        .btn-nav:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .page-info {
            display: flex;
            align-items: center;
            padding: 0 10px;
            font-weight: 600;
            color: #333;
        }

        .flipbook-container {
            width: 100%;
            flex: 1;
            position: relative;
            overflow: hidden;
            min-height: 0;
        }

        .page {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: #fff;
            transition: transform 0.4s ease-in-out;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .page.slide-left {
            transform: translateX(-100%);
        }

        .page.slide-right {
            transform: translateX(100%);
        }

        .page.active {
            transform: translateX(0);
            z-index: 2;
        }

        .page-content {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
            min-height: 100%;
            font-size: 16px;
            line-height: 1.8;
            color: #333;
        }

        /* E-book Content Styles */
        .ebook-page-content {
            width: 100%;
        }

        /* Cover Page Styles */
        .ebook-cover {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 60px 40px;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            position: relative;
            overflow: hidden;
        }

        .ebook-cover::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
            border-radius: 50%;
            opacity: 0.1;
            z-index: 0;
        }

        .cover-header {
            position: relative;
            z-index: 1;
        }

        .cover-logo {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 40px;
        }

        .logo-square {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #ffa726 0%, #fb8c00 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 20px;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
        }

        .company-name {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .company-sub {
            font-size: 12px;
            color: #666;
            text-transform: lowercase;
        }

        .cover-title-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            z-index: 1;
            margin: 60px 0;
        }

        .cover-main-title {
            font-size: 72px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .title-word-1, .title-word-3 {
            color: #333;
        }

        .title-word-2 {
            color: #ff6b35;
            font-size: 80px;
        }

        .cover-company {
            display: flex;
            flex-direction: column;
            gap: 5px;
            font-size: 24px;
        }

        .cover-company .company-name {
            font-weight: 600;
            color: #333;
        }

        .cover-company .company-sub {
            color: #ff6b35;
            font-size: 20px;
            font-weight: 500;
        }

        .cover-period {
            background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
            padding: 20px 40px;
            border-radius: 12px;
            text-align: center;
            color: white;
            position: relative;
            z-index: 1;
            margin-top: auto;
        }

        .period-label {
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .period-years {
            font-size: 32px;
            font-weight: 700;
        }

        /* Standard Page Styles */
        .ebook-page-standard {
            width: 100%;
            padding: 40px 0;
        }

        .page-header-official {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        .official-logo {
            flex-shrink: 0;
        }

        .gov-logo {
            max-width: 80px;
            height: auto;
        }

        .official-title h2 {
            font-size: 18px;
            font-weight: 700;
            color: #1a237e;
            margin-bottom: 5px;
        }

        .official-title h3 {
            font-size: 14px;
            font-weight: 600;
            color: #283593;
            margin-bottom: 10px;
        }

        .official-address {
            font-size: 12px;
            color: #666;
            line-height: 1.6;
        }

        .page-content-official {
            line-height: 1.8;
        }

        .decision-title {
            font-size: 16px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 15px;
            color: #1a237e;
        }

        .decision-number {
            text-align: center;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .decision-about {
            text-align: center;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .decision-subject {
            text-align: center;
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 30px;
            color: #1a237e;
        }

        .decision-content {
            margin-top: 30px;
        }

        .decision-header {
            font-weight: 700;
            margin-top: 20px;
            margin-bottom: 10px;
            color: #333;
        }

        .decision-content ol, .decision-content ul {
            margin-left: 30px;
            margin-bottom: 20px;
        }

        .decision-content li {
            margin-bottom: 10px;
            text-align: justify;
        }

        .page-content-text {
            line-height: 1.8;
            color: #333;
        }

        /* PKB Content Styling */
        .article {
            margin: 30px 0;
        }

        .article-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a237e;
            margin-bottom: 15px;
            text-align: center;
        }

        .paragraph {
            margin: 15px 0;
            text-align: justify;
            text-indent: 30px;
        }

        .paragraph-number {
            font-weight: 600;
            color: #667eea;
        }

        .list-item {
            margin: 10px 0;
            padding-left: 20px;
            text-align: justify;
        }

        .list-item-numbered {
            margin: 10px 0;
            padding-left: 40px;
            text-align: justify;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }

        table th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #5568d3;
        }

        table td {
            padding: 10px 12px;
            border: 1px solid #e0e0e0;
            text-align: left;
        }

        table tr:nth-child(even) {
            background: #f8f9fa;
        }

        table tr:hover {
            background: #f0f0f0;
        }

        .signature-section {
            margin-top: 60px;
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
        }

        .signature-box {
            text-align: center;
            min-width: 200px;
        }

        .signature-name {
            font-weight: 600;
            margin-top: 60px;
            border-top: 2px solid #333;
            padding-top: 5px;
            display: inline-block;
            min-width: 200px;
        }

        .content-placeholder {
            text-align: center;
            color: #999;
            font-style: italic;
            padding: 40px;
        }

        /* Loading */
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 1000;
        }

        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-bar {
                flex-direction: column;
                gap: 10px;
            }

            .search-box {
                max-width: 100%;
                width: 100%;
            }

            .page-content {
                padding: 20px;
                font-size: 14px;
            }

            .cover-main-title {
                font-size: 48px;
            }

            .title-word-2 {
                font-size: 56px;
            }
        }
    </style>
</head>
<body>
    <div class="ebook-container">
        <div class="flipbook-wrapper">
            <div class="header-bar">
                <div class="header-brand">SiPO - Krisanthium</div>
                <div class="search-box">
                    <input type="text" class="search-input" id="search-input" placeholder="Cari kata kunci..." onkeypress="handleSearchKeyPress(event)">
                    <button class="search-btn" onclick="performSearch()">Cari</button>
                </div>
                <div class="search-results" id="search-results"></div>
            </div>

            <div class="controls">
                <button class="btn-nav" id="btn-prev" onclick="previousPage()">
                    <span>←</span> Sebelumnya
                </button>
                <div class="page-info">
                    <span id="page-info">Halaman {{ $currentPage }} / {{ $totalPages }}</span>
                </div>
                <button class="btn-nav" id="btn-next" onclick="nextPage()">
                    Selanjutnya <span>→</span>
                </button>
            </div>

            <div class="flipbook-container" id="flipbook">
                <div class="page active" id="page-container">
                    <div class="page-content">
                        @include($pageView, ['pageNumber' => $currentPage])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const currentPage = {{ $currentPage }};
        const totalPages = {{ $totalPages }};
        let isNavigating = false;

        function nextPage() {
            if (isNavigating || currentPage >= totalPages) return;
            window.location.href = '{{ route("ebook-pkb.index") }}?page=' + (currentPage + 1);
        }

        function previousPage() {
            if (isNavigating || currentPage <= 1) return;
            window.location.href = '{{ route("ebook-pkb.index") }}?page=' + (currentPage - 1);
        }

        function handleSearchKeyPress(event) {
            if (event.key === 'Enter') {
                performSearch();
            }
        }

        // Search across all pages via AJAX
        async function performSearch() {
            const keyword = document.getElementById('search-input').value.trim();
            if (!keyword) {
                document.getElementById('search-results').style.display = 'none';
                return;
            }

            const resultsDiv = document.getElementById('search-results');
            resultsDiv.innerHTML = '<div style="padding: 10px; text-align: center;">Mencari...</div>';
            resultsDiv.style.display = 'block';

            try {
                const response = await fetch('{{ route("ebook-pkb.search") }}?q=' + encodeURIComponent(keyword));
                const data = await response.json();
                displaySearchResults(data.results, keyword);
            } catch (error) {
                // Fallback: search in current page only
                const pageContent = document.getElementById('page-container');
                const content = pageContent.innerText.toLowerCase();
                const keywordLower = keyword.toLowerCase();

                if (content.includes(keywordLower)) {
                    highlightKeyword(keyword);
                    resultsDiv.innerHTML =
                        '<div style="padding: 10px; text-align: center; color: #667eea; font-weight: 600;">Ditemukan di halaman ini</div>';
                } else {
                    resultsDiv.innerHTML =
                        '<div style="padding: 10px; text-align: center; color: #999;">Tidak ditemukan di halaman ini</div>';
                }
            }
        }

        function displaySearchResults(results, keyword) {
            const resultsDiv = document.getElementById('search-results');

            if (!results || results.length === 0) {
                resultsDiv.innerHTML = '<div style="padding: 10px; text-align: center; color: #999;">Tidak ditemukan</div>';
                return;
            }

            let html = '<div style="padding: 8px 12px; font-weight: 600; border-bottom: 1px solid #eee; font-size: 12px;">Ditemukan di ' + results.length + ' halaman</div>';

            results.forEach((result, index) => {
                html += `
                    <div class="search-result-item" onclick="goToPage(${result.page})">
                        <div style="font-weight: 600;">Halaman ${result.page}</div>
                        <div style="font-size: 11px; color: #666; margin-top: 2px;">${result.matches} ditemukan</div>
                    </div>
                `;
            });

            resultsDiv.innerHTML = html;
        }

        function highlightKeyword(keyword) {
            const pageContent = document.getElementById('page-container');
            const regex = new RegExp(`(${keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            const html = pageContent.innerHTML;
            const highlighted = html.replace(regex, '<span class="search-highlight">$1</span>');
            pageContent.innerHTML = highlighted;
        }

        function goToPage(pageNum) {
            window.location.href = '{{ route("ebook-pkb.index") }}?page=' + pageNum;
        }

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                e.preventDefault();
                nextPage();
            } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                e.preventDefault();
                previousPage();
            }
        });

        // Touch swipe handling
        let touchStartX = 0;
        let touchEndX = 0;

        document.getElementById('flipbook').addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        document.getElementById('flipbook').addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            const diff = touchStartX - touchEndX;
            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    nextPage();
                } else {
                    previousPage();
                }
            }
        }, { passive: true });

        // Scroll to top on page load
        window.addEventListener('load', () => {
            document.getElementById('page-container').scrollTop = 0;
        });

        // ============ EBOOK READING TRACKING ============
        // Track user reading behavior for analytics
        let readingTracker = {
            sessionStartTime: Date.now(),
            currentPage: {{ $currentPage }},
            totalPages: {{ $totalPages }},
            pageViewDurations: {},
            pageEnteredTime: Date.now(),
            updateInterval: null,
            trackingEnabled: {{ Auth::check() ? 1 : 0 }} === 1,
            csrfToken: document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '',

            init() {
                if (!this.trackingEnabled) {
                    console.warn('⚠️ Tracking disabled (user not authenticated)');
                    return;
                }

                if (!this.csrfToken) {
                    console.error('❌ CSRF token not found!');
                    return;
                }

                console.log('📚 Reading tracker initialized - Tracking ENABLED');
                console.log('   User tracking enabled:', this.trackingEnabled);
                console.log('   Current page:', this.currentPage);
                console.log('   Total pages:', this.totalPages);
                console.log('   CSRF token:', this.csrfToken.substring(0, 10) + '...');

                this.pageViewDurations[this.currentPage] = 0;
                this.startTracking();
                this.trackPageView();
                this.setupEventListeners();
            },

            startTracking() {
                // Update time spent every 10 seconds
                this.updateInterval = setInterval(() => {
                    this.trackCurrentPageDuration();
                }, 10000);

                // Track on page unload/close
                window.addEventListener('beforeunload', () => {
                    this.trackCurrentPageDuration();
                    this.endSession();
                });
            },

            trackPageView() {
                if (!this.trackingEnabled) return;

                const totalSeconds = Math.round((Date.now() - this.sessionStartTime) / 1000);

                const payload = {
                    current_page: this.currentPage,
                    time_spent_seconds: totalSeconds,
                    interaction_type: 'page_view',
                };

                console.log('📤 Sending tracking data:', payload);

                fetch('/api/ebook-pkb/tracking/update-progress', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => {
                    console.log('✅ API Response status:', res.status);
                    return res.json();
                })
                .then(data => {
                    console.log('✅ API Response:', data);
                })
                .catch(err => {
                    console.error('❌ Tracking error:', err);
                });

                if (this.pageViewDurations[this.currentPage] === undefined) {
                    this.pageViewDurations[this.currentPage] = duration;
                } else {
                    this.pageViewDurations[this.currentPage] += duration;
                }

                this.pageEnteredTime = now;
            },

            onPageChanged(newPage) {
                if (!this.trackingEnabled) return;

                this.trackCurrentPageDuration();
                this.currentPage = newPage;
                if (!this.pageViewDurations[newPage]) {
                    this.pageViewDurations[newPage] = 0;
                }
                this.trackPageView();
            },

            setupEventListeners() {
                // Track scroll events
                const pageContainer = document.getElementById('page-container');
                if (pageContainer) {
                    pageContainer.addEventListener('scroll', () => {
                        this.trackInteraction('scroll');
                    }, { passive: true });
                }

                // Track search interactions
                const searchBtn = document.querySelector('.search-btn');
                if (searchBtn) {
                    searchBtn.addEventListener('click', () => {
                        this.trackInteraction('search');
                    });
                }
            },

            trackInteraction(type) {
                if (!this.trackingEnabled) return;

                const totalSeconds = Math.round((Date.now() - this.sessionStartTime) / 1000);

                fetch('/api/ebook-pkb/tracking/update-progress', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                    body: JSON.stringify({
                        current_page: this.currentPage,
                        time_spent_seconds: totalSeconds,
                        interaction_type: type,
                    })
                }).catch(err => console.error('Tracking error:', err));
            },

            // Mark reading as complete (user manually marks or reaches end)
            markAsComplete() {
                if (!this.trackingEnabled) return;

                const totalSeconds = Math.round((Date.now() - this.sessionStartTime) / 1000);

                fetch('/api/ebook-pkb/tracking/mark-complete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                    body: JSON.stringify({
                        completed_page: this.currentPage,
                        total_time_seconds: totalSeconds,
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        console.log('✅ Reading session marked as complete!');
                        console.log('Reading duration:', data.reading_duration);
                    }
                })
                .catch(err => console.error('Error marking complete:', err));
            },

            endSession() {
                clearInterval(this.updateInterval);
            }
        };

        // Initialize tracker when page loads
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => readingTracker.init(), 500);
            });
        } else {
            setTimeout(() => readingTracker.init(), 500);
        }

        // Override nextPage and previousPage to track page changes
        // Do this AFTER page load to ensure functions exist
        setTimeout(() => {
            if (typeof nextPage !== 'undefined' && typeof previousPage !== 'undefined') {
                const originalNextPage = nextPage;
                const originalPreviousPage = previousPage;

                window.nextPage = function() {
                    originalNextPage();
                    const newPage = Math.min(readingTracker.currentPage + 1, readingTracker.totalPages);
                    readingTracker.onPageChanged(newPage);
                };

                window.previousPage = function() {
                    originalPreviousPage();
                    const newPage = Math.max(readingTracker.currentPage - 1, 1);
                    readingTracker.onPageChanged(newPage);
                };

                console.log('✅ Page navigation overrides installed');
            } else {
                console.warn('⚠️ nextPage/previousPage functions not found yet');
            }
        }, 1000);
    </script>
</body>
</html>


