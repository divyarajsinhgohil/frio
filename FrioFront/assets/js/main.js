// Mobile hamburger toggle
const hamburgerBtn = document.getElementById('hamburger-btn');
const mobileMenu   = document.getElementById('mobile-menu');
const hamLine1     = document.getElementById('ham-line1');
const hamLine2     = document.getElementById('ham-line2');
const hamLine3     = document.getElementById('ham-line3');

if (hamburgerBtn && mobileMenu) {
    hamburgerBtn.addEventListener('click', () => {
        const isOpen = mobileMenu.classList.toggle('open');
        hamLine1.style.transform = isOpen ? 'rotate(45deg) translate(3px, 3px)' : '';
        hamLine2.style.opacity   = isOpen ? '0' : '1';
        hamLine3.style.transform = isOpen ? 'rotate(-45deg) translate(3px, -3px)' : '';
        hamLine3.style.width     = isOpen ? '24px' : '';
    });
}

// Scroll shadow effect
window.addEventListener('scroll', () => {
    const mainNav = document.getElementById('main-nav');
    if (mainNav) {
        mainNav.classList.toggle('shadow-xl', window.scrollY > 60);
    }
});

// Init TomSelect for Global Search
function initGlobalSearch(selector) {
    if (!document.querySelector(selector)) return;
    
    // Fallback if global constant is not defined
    const apiBase = window.API_BASE_URL || '../FrioAdmin/';
    
    new TomSelect(selector, {
        valueField: 'url',
        labelField: 'name',
        searchField: 'name',
        optgroups: [
            {value: 'category', label: 'Categories'},
            {value: 'product', label: 'Products'}
        ],
        optgroupField: 'class',
        optgroupLabelField: 'label',
        optgroupValueField: 'value',
        lockOptgroupOrder: true,
        searchConjunction: 'and',
        create: false,
        maxItems: 1,
        placeholder: "Search...",
        load: function(query, callback) {
            if (!query.length) return callback();
            // Fetch from backend api/front_api/search.php
            fetch(apiBase + 'api/front_api/search.php?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(json => {
                    let items = [];
                    // Categories
                    if(json.data && json.data.categories) {
                        json.data.categories.forEach(c => {
                            items.push({
                                class: 'category',
                                url: 'product.php?category_id=' + c.id,
                                name: c.name,
                                image: c.image ? apiBase + c.image : apiBase + 'assets/imag/placeholder.jpg'
                            });
                        });
                    }
                    // Products
                    if(json.data && json.data.products) {
                        json.data.products.forEach(p => {
                            items.push({
                                class: 'product',
                                url: 'product-detail.php?id=' + p.id,
                                name: p.name,
                                code: p.code,
                                image: p.image ? apiBase + p.image : apiBase + 'assets/imag/placeholder.jpg'
                            });
                        });
                    }
                    callback(items);
                }).catch(()=>{
                    callback();
                });
        },
        onChange: function(value) {
            if(value) {
                window.location.href = value;
            }
        },
        render: {
            option: function(item, escape) {
                return `<div class="flex items-center gap-3">
                            <img loading="lazy" src="${escape(item.image)}" class="ts-search-img" onerror="this.src='${apiBase}assets/imag/placeholder.jpg'" />
                            <div class="flex flex-col">
                                <span class="font-bold">${escape(item.name)}</span>
                                ${item.code ? `<span class="text-xs opacity-70">${escape(item.code)}</span>` : ''}
                            </div>
                        </div>`;
            },
            item: function(item, escape) {
                return `<div>${escape(item.name)}</div>`;
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initGlobalSearch('#global-search');
    initGlobalSearch('#mobile-global-search');

    // Hero Banner Slider Logic
    const bannerSlider = document.getElementById('banner-slider');
    if (bannerSlider) {
        const slides = bannerSlider.querySelectorAll('.banner-slide');
        const dots = bannerSlider.querySelectorAll('.banner-dot');
        const total = slides.length;
        let current = 0;
        let timer;

        function goBannerSlide(n) {
            if (total === 0) return;
            // hide current
            slides[current].classList.add('hidden');
            if (dots[current]) {
                dots[current].classList.remove('bg-white');
                dots[current].classList.add('bg-white/30');
            }

            current = (n + total) % total;

            // show next
            slides[current].classList.remove('hidden');
            if (dots[current]) {
                dots[current].classList.remove('bg-white/30');
                dots[current].classList.add('bg-white');
            }
        }

        window.goBannerSlide = goBannerSlide;

        window.moveBanner = function (dir) {
            clearInterval(timer);
            goBannerSlide(current + dir);
            startAuto();
        };

        function startAuto() {
            if (total <= 1) return;
            timer = setInterval(function () { goBannerSlide(current + 1); }, 5000);
        }

        startAuto();
    }

    // Automatic Category Card Slideshow (Cycles through multi-image category sets)
    setInterval(() => {
        const slideshowContainers = [];
        document.querySelectorAll('.cat-slideshow-img').forEach(img => {
            const parent = img.parentElement;
            if (parent && !slideshowContainers.includes(parent)) {
                slideshowContainers.push(parent);
            }
        });

        slideshowContainers.forEach(container => {
            const imgs = container.querySelectorAll('.cat-slideshow-img');
            if (imgs.length > 1) {
                let activeIdx = -1;
                imgs.forEach((img, i) => {
                    if (img.classList.contains('opacity-100')) {
                        activeIdx = i;
                    }
                });
                if (activeIdx !== -1) {
                    imgs[activeIdx].classList.remove('opacity-100');
                    imgs[activeIdx].classList.add('opacity-0');
                    const nextIdx = (activeIdx + 1) % imgs.length;
                    imgs[nextIdx].classList.remove('opacity-0');
                    imgs[nextIdx].classList.add('opacity-100');
                }
            }
        });
    }, 2000);
});
