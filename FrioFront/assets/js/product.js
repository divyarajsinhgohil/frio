const defaultImg = "https://lh3.googleusercontent.com/aida-public/AB6AXuB2ZQ8yeQoePJq5Gwlxo2DKly2CS8BFuRajp8W9-EB2GEkGaWnYdWVidnjcUNSQ_GueB6HE6B7tUaTt99qYx1VONkvRjBC1Mfc05PQM-IIb58hYyjtUVPBLPd_iDMPUxpH9-Sg8U-UmXwuIWCwVSZ2g_ge58tw8BXS08Vkh2W65JU5QxcAoHu39ApLe6TI0j9mn4ciHpZMFyvvUI41sSreckbQbqg49L9AvwiE98sRKKNXMTskI-_bTIF_qLllr1U_FicJ8MJj_5UEH";

function selectSize(buttonEl, isInitialLoad = false) {
    // 1. Remove active state from all buttons
    document.querySelectorAll('.size-chip-active').forEach(btn => {
        btn.classList.remove('size-chip-active', 'bg-primary', 'text-white', 'border-primary');
        btn.classList.add('bg-white', 'text-gray-700', 'border-[#e2e8f0]');
        btn.style.borderColor = '#e2e8f0';
    });
    
    // 2. Add active state to clicked button
    if (buttonEl && buttonEl.classList) {
        buttonEl.classList.remove('bg-white', 'text-gray-700', 'border-[#e2e8f0]');
        buttonEl.classList.add('size-chip-active', 'bg-primary', 'text-white', 'border-primary');
        buttonEl.style.borderColor = 'var(--color-primary)';
    }

    // 3. Update SKU / Product Code and Product Name
    const skuCode = buttonEl.getAttribute('data-code');
    const varName = buttonEl.getAttribute('data-name');
    
    const skuEl = document.getElementById('product-sku');
    if (skuEl) {
        skuEl.textContent = skuCode ? 'SKU: ' + skuCode : '';
        skuEl.style.display = skuCode ? 'block' : 'none';
    }
    
    // Only update the H1 title if this was an actual user click, not the initial auto-load
    if (!isInitialLoad) {
        const titleEl = document.getElementById('product-title');
        if (titleEl) {
            const defaultName = titleEl.getAttribute('data-default-name');
            titleEl.textContent = (varName && varName.trim() !== '') ? varName : defaultName;
        }
    }

    // 4. Update Main Product Image
    const varImage = buttonEl.getAttribute('data-image');
    const mainImg = document.getElementById('main-product-image');
    const fallback = document.getElementById('image-fallback');

    if (mainImg) {
        const baseImage = mainImg.getAttribute('data-base-image');
        
        // On initial page load, ALWAYS show the main product image, not the variant image.
        let targetImage = varImage || baseImage;
        if (isInitialLoad && baseImage) {
            targetImage = baseImage;
        }

        if (targetImage) {
            mainImg.src = targetImage;
            mainImg.classList.remove('hidden');
            if (fallback) fallback.classList.add('hidden');
        } else {
            mainImg.classList.add('hidden');
            if (fallback) fallback.classList.remove('hidden');
        }

        // 5. Sync active gallery thumbnail highlight
        if (targetImage) {
            document.querySelectorAll('.gallery-thumb').forEach((t, idx) => {
                const thumbImg = t.querySelector('img');
                if (thumbImg && thumbImg.src === targetImage) {
                    t.classList.remove('border-[#e2e8f0]', 'border-outline-variant/40');
                    t.classList.add('border-primary', 'shadow-sm', 'scale-105');
                    window.currentImageIndex = idx; // Update active index for Fancybox
                } else {
                    t.classList.remove('border-primary', 'shadow-sm', 'scale-105');
                    t.classList.add('border-outline-variant/40');
                }
            });
        }
    }
}

function changeGalleryImage(thumb, imgUrl, index) {
    // 1. Update the main image src
    const mainImg = document.getElementById('main-product-image');
    const fallback = document.getElementById('image-fallback');
    if (mainImg) {
        mainImg.src = imgUrl;
        mainImg.classList.remove('hidden');
        if (fallback) fallback.classList.add('hidden');
    }

    // 2. Reset border highlights on all thumbnails
    document.querySelectorAll('.gallery-thumb').forEach(t => {
        t.classList.remove('border-primary', 'shadow-sm', 'scale-105');
        t.classList.add('border-outline-variant/40');
    });

    // 3. Highlight the active clicked thumbnail
    thumb.classList.remove('border-outline-variant/40');
    thumb.classList.add('border-primary', 'shadow-sm', 'scale-105');
    
    // 4. Update the active gallery index
    window.currentImageIndex = index;
}

document.addEventListener('DOMContentLoaded', () => {
    // Auto-select the initially active size chip on page load to sync SKU & Image
    const activeChip = document.querySelector('.size-chip-active');
    if (activeChip) {
        selectSize(activeChip, true);
    }

    // Modern Amazon-style smooth hover zoom effect (inner zoom scale)
    const imgContainer = document.querySelector('.product-image-container');
    const mainImgEl = document.getElementById('main-product-image');
    if (imgContainer && mainImgEl) {
        imgContainer.addEventListener('mousemove', (e) => {
            // Disable on mobile/tablet screens for better touch usability
            if (window.innerWidth < 768) return;
            
            const rect = imgContainer.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const xPercent = (x / rect.width) * 100;
            const yPercent = (y / rect.height) * 100;
            
            mainImgEl.style.transformOrigin = `${xPercent}% ${yPercent}%`;
            mainImgEl.style.transform = 'scale(1.8)';
        });
        
        imgContainer.addEventListener('mouseleave', () => {
            mainImgEl.style.transform = 'scale(1)';
            mainImgEl.style.transformOrigin = 'center center';
        });

        // Click main image to open Amazon-Style Fullscreen Lightbox Modal
        imgContainer.addEventListener('click', () => {
            if (window.galleryImages && window.galleryImages.length > 0) {
                openAmazonModal();
            }
        });

        // ===== AMAZON-STYLE PREMIUM LIGHTBOX MODAL LOGIC =====
        const amazonModal = document.getElementById('amazon-media-modal');
        const amazonCard = document.getElementById('amazon-modal-card');
        const amazonCloseBtn = document.getElementById('amazon-modal-close');
        const amazonPrevBtn = document.getElementById('amazon-modal-prev');
        const amazonNextBtn = document.getElementById('amazon-modal-next');
        const amazonLargeImg = document.getElementById('amazon-modal-large-img');
        const amazonThumbsContainer = document.getElementById('amazon-modal-thumbs');
        const amazonThumbsMobileContainer = document.getElementById('amazon-modal-thumbs-mobile');
        const amazonImgContainer = document.getElementById('amazon-modal-img-container');

        function openAmazonModal() {
            if (!amazonModal) return;
            
            // Sync active index
            const startIndex = window.currentImageIndex || 0;
            
            // Display modal
            amazonModal.classList.remove('hidden');
            amazonModal.classList.add('flex');
            
            // Force browser reflow to enable transition
            amazonModal.offsetWidth;
            
            // Animate in
            amazonModal.classList.remove('opacity-0');
            if (amazonCard) {
                amazonCard.classList.remove('scale-95');
                amazonCard.classList.add('scale-100');
            }
            
            // Disable background scrolling
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
            
            // Render thumbs list dynamically
            renderModalThumbs();
            
            // Update active image
            updateModalImage(startIndex);
        }

        function closeAmazonModal() {
            if (!amazonModal) return;
            
            // Animate out
            amazonModal.classList.add('opacity-0');
            if (amazonCard) {
                amazonCard.classList.remove('scale-100');
                amazonCard.classList.add('scale-95');
            }
            
            // Wait for transition to complete, then hide
            setTimeout(() => {
                amazonModal.classList.add('hidden');
                amazonModal.classList.remove('flex');
                
                // Restore background scrolling
                document.documentElement.style.overflow = '';
                document.body.style.overflow = '';
            }, 300);
        }

        function renderModalThumbs() {
            if (!amazonThumbsContainer || !amazonThumbsMobileContainer || !window.galleryImages) return;
            
            amazonThumbsContainer.innerHTML = '';
            amazonThumbsMobileContainer.innerHTML = '';
            
            window.galleryImages.forEach((imgObj, idx) => {
                // Desktop vertical thumbnail
                const dtThumb = document.createElement('button');
                dtThumb.type = 'button';
                dtThumb.className = `amazon-m-thumb-dt w-[70px] h-[70px] rounded-xl border p-1 bg-white hover:border-primary transition-all overflow-hidden flex items-center justify-center border-outline-variant/40 hover:scale-105`;
                dtThumb.innerHTML = `<img src="${imgObj.src}" class="w-full h-full object-contain" alt="Gallery preview ${idx + 1}" />`;
                dtThumb.addEventListener('click', () => updateModalImage(idx));
                amazonThumbsContainer.appendChild(dtThumb);
                
                // Mobile horizontal thumbnail
                const mbThumb = document.createElement('button');
                mbThumb.type = 'button';
                mbThumb.className = `amazon-m-thumb-mb w-[55px] h-[55px] rounded-lg border p-0.5 bg-white hover:border-primary transition-all overflow-hidden flex items-center justify-center border-outline-variant/40 shrink-0`;
                mbThumb.innerHTML = `<img src="${imgObj.src}" class="w-full h-full object-contain" alt="Gallery preview ${idx + 1}" />`;
                mbThumb.addEventListener('click', () => updateModalImage(idx));
                amazonThumbsMobileContainer.appendChild(mbThumb);
            });
        }

        function updateModalImage(index) {
            if (!window.galleryImages || window.galleryImages.length === 0) return;
            
            // Clamp and wrap index circularly
            index = (index + window.galleryImages.length) % window.galleryImages.length;
            window.currentImageIndex = index;
            
            // Update main large image
            if (amazonLargeImg) {
                amazonLargeImg.src = window.galleryImages[index].src;
            }
            
            // Highlight active thumbnails
            // Desktop list
            const dtThumbs = document.querySelectorAll('.amazon-m-thumb-dt');
            dtThumbs.forEach((thumb, idx) => {
                if (idx === index) {
                    thumb.classList.remove('border-outline-variant/40');
                    thumb.classList.add('border-primary', 'shadow-sm', 'scale-105');
                } else {
                    thumb.classList.remove('border-primary', 'shadow-sm', 'scale-105');
                    thumb.classList.add('border-outline-variant/40');
                }
            });
            
            // Mobile list
            const mbThumbs = document.querySelectorAll('.amazon-m-thumb-mb');
            mbThumbs.forEach((thumb, idx) => {
                if (idx === index) {
                    thumb.classList.remove('border-outline-variant/40');
                    thumb.classList.add('border-primary', 'shadow-sm', 'scale-105');
                    // Scroll into view on mobile
                    thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                } else {
                    thumb.classList.remove('border-primary', 'shadow-sm', 'scale-105');
                    thumb.classList.add('border-outline-variant/40');
                }
            });
        }

        // Close handlers
        if (amazonCloseBtn) {
            amazonCloseBtn.addEventListener('click', closeAmazonModal);
        }
        
        if (amazonModal) {
            // Close on click outside the card
            amazonModal.addEventListener('click', (e) => {
                if (e.target === amazonModal) {
                    closeAmazonModal();
                }
            });
        }

        // Next/Prev buttons
        if (amazonPrevBtn) {
            amazonPrevBtn.addEventListener('click', () => {
                updateModalImage(window.currentImageIndex - 1);
            });
        }
        
        if (amazonNextBtn) {
            amazonNextBtn.addEventListener('click', () => {
                updateModalImage(window.currentImageIndex + 1);
            });
        }

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (amazonModal && !amazonModal.classList.contains('hidden')) {
                if (e.key === 'Escape') {
                    closeAmazonModal();
                } else if (e.key === 'ArrowLeft') {
                    updateModalImage(window.currentImageIndex - 1);
                } else if (e.key === 'ArrowRight') {
                    updateModalImage(window.currentImageIndex + 1);
                }
            }
        });

        // Amazon-style interactive hover zoom for the modal image
        if (amazonImgContainer && amazonLargeImg) {
            amazonImgContainer.addEventListener('mousemove', (e) => {
                if (window.innerWidth < 768) return;
                
                const rect = amazonImgContainer.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const xPercent = (x / rect.width) * 100;
                const yPercent = (y / rect.height) * 100;
                
                amazonLargeImg.style.transformOrigin = `${xPercent}% ${yPercent}%`;
                amazonLargeImg.style.transform = 'scale(2.2)';
            });
            
            amazonImgContainer.addEventListener('mouseleave', () => {
                amazonLargeImg.style.transform = 'scale(1)';
                amazonLargeImg.style.transformOrigin = 'center center';
            });
        }

        // Mobile touch swiping gesture for the modal image
        let touchStartX = 0;
        let touchEndX = 0;
        if (amazonImgContainer) {
            amazonImgContainer.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });
            
            amazonImgContainer.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                const diff = touchEndX - touchStartX;
                if (Math.abs(diff) > 50) {
                    if (diff < 0) {
                        // Swiped left -> next
                        updateModalImage(window.currentImageIndex + 1);
                    } else {
                        // Swiped right -> prev
                        updateModalImage(window.currentImageIndex - 1);
                    }
                }
            }, { passive: true });
        }
    }

    // Accordion tab triggers (Independent Toggling Style)
    document.querySelectorAll('.accordion-trigger').forEach(trigger => {
        trigger.addEventListener('click', () => {
            const targetId = trigger.getAttribute('data-target');
            const targetContent = document.getElementById(targetId);
            const icon = trigger.querySelector('.material-symbols-outlined:last-child');
            
            if (targetContent) {
                const isCollapsed = targetContent.classList.contains('max-h-0') || targetContent.style.maxHeight === '0px' || !targetContent.style.maxHeight;
                
                if (isCollapsed) {
                    // Expand this content
                    targetContent.classList.remove('max-h-0');
                    targetContent.style.maxHeight = targetContent.scrollHeight + 'px';
                    if (icon) icon.style.transform = 'rotate(180deg)';
                } else {
                    // Collapse this content
                    targetContent.classList.add('max-h-0');
                    targetContent.style.maxHeight = '0px';
                    if (icon) icon.style.transform = '';
                }
            }
        });
    });


    // Sticky B2B Inquiry Bar Scroll Listener
    window.addEventListener('scroll', () => {
        const bar = document.getElementById('sticky-inquiry-bar');
        if (!bar) return;
        if (window.scrollY > 400) {
            bar.classList.remove('translate-y-full');
        } else {
            bar.classList.add('translate-y-full');
        }
    });

    // Variations toggle chips logic
    const moreChipsBtn = document.getElementById('more-chips-btn');
    if (moreChipsBtn) {
        moreChipsBtn.addEventListener('click', () => {
            const extraChips = document.querySelectorAll('.extra-chip');
            if (extraChips.length === 0) return;
            const isHidden = extraChips[0].classList.contains('hidden');
            if (isHidden) {
                extraChips.forEach(c => c.classList.remove('hidden'));
                moreChipsBtn.textContent = 'Show Less';
            } else {
                extraChips.forEach(c => c.classList.add('hidden'));
                moreChipsBtn.textContent = '+' + extraChips.length + ' More';
            }
        });
    }
});
