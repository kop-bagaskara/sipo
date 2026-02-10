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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
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
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header-brand {
            color: white;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .flipbook-container {
            width: 100%;
            flex: 1;
            position: relative;
            overflow: hidden;
            min-height: 0;
            transform-origin: center center;
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
            transform-origin: center center;
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
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 20px 20px 2px 20px;
            box-sizing: border-box;
            margin-bottom: 0;
            position: relative;
            transform-origin: center top;
        }

        .page-content canvas {
            width: 100% !important;
            height: auto !important;
            display: block;
            max-width: 100%;
            margin: 0 auto;
            padding: 0;
            object-fit: contain;
            image-rendering: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
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

        .swipe-hint {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(102, 126, 234, 0.9);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 12px;
            z-index: 100;
            animation: fadeOut 3s forwards;
        }

        @keyframes fadeOut {
            0%, 70% { opacity: 1; }
            100% { opacity: 0; }
        }

        @media (max-width: 768px) {
            .header-bar {
                padding: 10px 15px;
            }

            .header-brand {
                font-size: 16px;
            }

            .controls {
                padding: 8px 10px;
                gap: 6px;
                flex-wrap: nowrap;
            }

            .btn-nav {
                padding: 8px 12px;
                font-size: 11px;
                white-space: nowrap;
                flex-shrink: 0;
            }

            .btn-nav span {
                display: inline;
            }

            .page-info {
                padding: 0 8px;
                font-size: 11px;
                white-space: nowrap;
                flex-shrink: 1;
                min-width: 0;
            }

            .page-info span {
                font-size: 11px;
            }
        }

        @media (max-width: 480px) {
            .header-bar {
                padding: 8px 12px;
            }

            .header-brand {
                font-size: 14px;
            }

            .controls {
                padding: 6px 8px;
                gap: 5px;
            }

            .btn-nav {
                padding: 6px 10px;
                font-size: 10px;
            }

            .btn-nav span {
                font-size: 12px;
            }

            .page-info {
                padding: 0 6px;
                font-size: 10px;
            }

            .page-info span {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="ebook-container">
        <div class="flipbook-wrapper">
            <div class="header-bar">
                <div class="header-brand">SiPO - Krisanthium</div>
            </div>

            <div class="controls">
                <button class="btn-nav" id="btn-prev" onclick="previousPage()">
                    <span>←</span> Sebelumnya
                </button>
                <div class="page-info">
                    <span id="page-info">Halaman 1 / 1</span>
                </div>
                <button class="btn-nav" id="btn-next" onclick="nextPage()">
                    Selanjutnya <span>→</span>
                </button>
            </div>

            <div class="swipe-hint">Geser ke kanan/kiri untuk navigasi halaman</div>

            <div class="loading" id="loading">
                <div class="loading-spinner"></div>
                <div>Memuat e-book...</div>
            </div>

            <div class="flipbook-container" id="flipbook">
                <div class="page active" id="page-container">
                    <div class="page-content"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <script>
        // Set PDF.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let pdfDoc = null;
        let currentPage = 1;
        let totalPages = 0;
        let isNavigating = false;

        // PDF file path
        const pdfPath = "{{ asset('sipo_krisan/public/PKB 2026 REV 1.pdf') }}";

        // Initialize
        async function initPDF() {
            try {
                const loading = document.getElementById('loading');
                loading.style.display = 'block';

                const loadingTask = pdfjsLib.getDocument(pdfPath);
                pdfDoc = await loadingTask.promise;
                totalPages = pdfDoc.numPages;

                loading.style.display = 'none';

                // Load first page
                await renderPage(1);
                updatePageInfo();
                updateButtons();

                // Show swipe hint
                setTimeout(() => {
                    const hint = document.querySelector('.swipe-hint');
                    if (hint) hint.style.display = 'none';
                }, 3000);
            } catch (error) {
                console.error('Error loading PDF:', error);
                document.getElementById('loading').innerHTML =
                    '<div style="color: red;">Error memuat PDF. Pastikan file ada di public/PKB 2026 REV 1.pdf</div>';
            }
        }

        // Calculate scale to fit page with high quality for mobile
        function calculateScale(page, containerWidth, containerHeight) {
            const viewport = page.getViewport({ scale: 1.0 });
            const pageWidth = viewport.width;

            // Get device pixel ratio for retina displays
            const devicePixelRatio = window.devicePixelRatio || 1;

            // Calculate scale to fit width (with padding)
            const scaleX = (containerWidth - 40) / pageWidth;

            // For HD quality, use higher scale - minimum 2.0, up to 4.0 for retina
            const baseScale = Math.max(scaleX, 2.0); // Minimum 2x for quality
            const maxScale = devicePixelRatio > 1 ? 4.0 : 3.5; // Higher for retina

            return Math.min(baseScale, maxScale);
        }

        // Scroll to top
        function scrollToTop() {
            const pageContainer = document.getElementById('page-container');
            pageContainer.scrollTop = 0;
        }

        // Render single page with high quality
        async function renderPage(pageNum, scrollToTopAfter = false) {
            const pageContainer = document.getElementById('page-container');
            const pageContent = pageContainer.querySelector('.page-content');

            if (pageNum < 1 || pageNum > totalPages) return;

            try {
                const page = await pdfDoc.getPage(pageNum);
                const containerWidth = pageContainer.offsetWidth;
                const containerHeight = pageContainer.offsetHeight;

                // Get device pixel ratio for retina displays
                const devicePixelRatio = window.devicePixelRatio || 1;

                const scale = calculateScale(page, containerWidth, containerHeight);

                // Use higher scale for rendering quality - multiply for HD
                const hdMultiplier = 2; // 2x for HD quality
                const renderScale = scale * hdMultiplier;
                const viewport = page.getViewport({ scale: renderScale });

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');

                // Enable high-quality rendering
                context.imageSmoothingEnabled = true;
                context.imageSmoothingQuality = 'high';

                // Set actual canvas size - use devicePixelRatio for retina + HD multiplier
                const qualityMultiplier = devicePixelRatio > 1 ? devicePixelRatio * 1.5 : 2.5; // HD quality
                canvas.width = viewport.width * qualityMultiplier;
                canvas.height = viewport.height * qualityMultiplier;

                // Set display size (CSS pixels) - match the render scale but fit container
                const maxWidth = containerWidth - 40; // Account for padding
                const displayWidth = Math.min(viewport.width / hdMultiplier, maxWidth);

                canvas.style.width = displayWidth + 'px';
                canvas.style.maxWidth = '100%';
                canvas.style.height = 'auto';

                // Scale the rendering context for HD quality
                context.scale(qualityMultiplier, qualityMultiplier);

                // Render with high quality settings
                await page.render({
                    canvasContext: context,
                    viewport: viewport,
                    intent: 'display' // High quality rendering
                }).promise;

                pageContent.innerHTML = '';
                pageContent.appendChild(canvas);

                // Scroll to top if requested
                if (scrollToTopAfter) {
                    setTimeout(() => {
                        scrollToTop();
                    }, 100);
                }
            } catch (error) {
                console.error('Error rendering page:', error);
            }
        }

        // Next page with slide animation
        async function nextPage() {
            if (isNavigating || currentPage >= totalPages) return;

            isNavigating = true;
            const pageContainer = document.getElementById('page-container');

            // Slide out to left
            pageContainer.classList.remove('active');
            pageContainer.classList.add('slide-left');

            setTimeout(async () => {
                currentPage++;
                await renderPage(currentPage, true); // Scroll to top after render

                // Reset and slide in from right
                pageContainer.classList.remove('slide-left');
                pageContainer.classList.add('slide-right');

                setTimeout(() => {
                    pageContainer.classList.remove('slide-right');
                    pageContainer.classList.add('active');
                    updatePageInfo();
                    updateButtons();
                    // Scroll to top immediately after animation
                    scrollToTop();
                    isNavigating = false;
                }, 50);
            }, 400);
        }

        // Previous page with slide animation
        async function previousPage() {
            if (isNavigating || currentPage <= 1) return;

            isNavigating = true;
            const pageContainer = document.getElementById('page-container');

            // Slide out to right
            pageContainer.classList.remove('active');
            pageContainer.classList.add('slide-right');

            setTimeout(async () => {
                currentPage--;
                await renderPage(currentPage, true); // Scroll to top after render

                // Reset and slide in from left
                pageContainer.classList.remove('slide-right');
                pageContainer.classList.add('slide-left');

                setTimeout(() => {
                    pageContainer.classList.remove('slide-left');
                    pageContainer.classList.add('active');
                    updatePageInfo();
                    updateButtons();
                    isNavigating = false;
                    // Ensure scroll to top after animation
                    scrollToTop();
                }, 50);
            }, 400);
        }

        // Update page info
        function updatePageInfo() {
            const pageInfo = document.getElementById('page-info');
            pageInfo.textContent = `Halaman ${currentPage} / ${totalPages}`;
        }

        // Update buttons
        function updateButtons() {
            document.getElementById('btn-prev').disabled = currentPage <= 1;
            document.getElementById('btn-next').disabled = currentPage >= totalPages;
        }

        // Touch swipe handling
        let touchStartX = 0;
        let touchEndX = 0;
        let touchStartY = 0;
        let touchEndY = 0;

        const flipbook = document.getElementById('flipbook');

        flipbook.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
            touchStartY = e.changedTouches[0].screenY;
        }, { passive: true });

        flipbook.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            touchEndY = e.changedTouches[0].screenY;
            handleSwipe();
        }, { passive: true });

        function handleSwipe() {
            const swipeThreshold = 50;
            const diffX = touchStartX - touchEndX;
            const diffY = touchStartY - touchEndY;

            // Only trigger if horizontal swipe is greater than vertical
            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > swipeThreshold) {
                if (diffX > 0) {
                    // Swipe left - next page
                    nextPage();
                } else {
                    // Swipe right - previous page
                    previousPage();
                }
            }
        }

        // Mouse drag handling (for desktop)
        let isDragging = false;
        let dragStartX = 0;
        let dragStartY = 0;

        flipbook.addEventListener('mousedown', (e) => {
            isDragging = true;
            dragStartX = e.clientX;
            dragStartY = e.clientY;
        });

        flipbook.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            e.preventDefault();
        });

        flipbook.addEventListener('mouseup', (e) => {
            if (!isDragging) return;

            const diffX = dragStartX - e.clientX;
            const diffY = dragStartY - e.clientY;
            const swipeThreshold = 100;

            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > swipeThreshold) {
                if (diffX > 0) {
                    nextPage();
                } else {
                    previousPage();
                }
            }

            isDragging = false;
        });

        flipbook.addEventListener('mouseleave', () => {
            isDragging = false;
        });

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

        // Handle window resize and zoom
        let resizeTimeout;
        let lastWindowWidth = window.innerWidth;
        let lastWindowHeight = window.innerHeight;

        function handleResize() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(async () => {
                const currentWidth = window.innerWidth;
                const currentHeight = window.innerHeight;

                // Only re-render if significant size change (more than 50px difference)
                if (Math.abs(currentWidth - lastWindowWidth) > 50 ||
                    Math.abs(currentHeight - lastWindowHeight) > 50 ||
                    window.devicePixelRatio !== (window.devicePixelRatio || 1)) {

                    if (pdfDoc && currentPage > 0) {
                        const scrollPosition = document.getElementById('page-container').scrollTop;
                        await renderPage(currentPage);
                        // Restore scroll position
                        setTimeout(() => {
                            document.getElementById('page-container').scrollTop = scrollPosition;
                        }, 100);
                    }

                    lastWindowWidth = currentWidth;
                    lastWindowHeight = currentHeight;
                }
            }, 250);
        }

        window.addEventListener('resize', handleResize);
        window.addEventListener('orientationchange', handleResize);

        // Listen for zoom changes
        let lastZoomLevel = window.devicePixelRatio || window.outerWidth / window.innerWidth;
        setInterval(() => {
            const currentZoom = window.devicePixelRatio || window.outerWidth / window.innerWidth;
            if (Math.abs(currentZoom - lastZoomLevel) > 0.1) {
                lastZoomLevel = currentZoom;
                handleResize();
            }
        }, 500);

        // Initialize on load
        window.addEventListener('load', initPDF);
    </script>
</body>
</html>
