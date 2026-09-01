# FRIO Front-End Website

## Overview

**FrioFront** is a professional, responsive PHP-based website for FRIO Industrial, a leading manufacturer of precision brass fittings and industrial safety products. The website is designed to showcase products, categories, and technical documentation while providing an intuitive user experience.

**Tagline**: Safety By Choice

---

## Project Structure

```
FrioFront/
├── index.php                 # Homepage with banners, categories, and featured products
├── product.php               # Product catalog with filtering and search
├── product-detail.php        # Individual product detail page with gallery and specifications
├── category.php              # Category listing page
├── catalogue.php             # Technical catalogue/PDF downloads
├── about.php                 # Company information and mission/vision
├── contact.php               # Contact form and inquiry submission
├── config.php                # API configuration and helper functions
├── includes/
│   ├── header.php           # Shared navbar and page head
│   ├── footer.php           # Shared footer with social links
│   ├── banner.php           # Banner component
│   └── catalogue_card.php   # Catalogue card component
├── assets/
│   └── imag/banners/        # Banner preset images
└── README.md                # This file
```

---

## Features

### 1. **Responsive Design**
- Mobile-first approach using Tailwind CSS
- Optimized for desktop, tablet, and mobile devices
- Smooth animations and transitions

### 2. **Dynamic Content**
- Fetches data from FrioAdmin API (products, categories, banners, catalogues)
- Automatic fallback rendering when API data is unavailable
- Support for product variations and gallery images

### 3. **Product Management**
- Product catalog with search and filtering
- Category-based organization
- Product detail pages with specifications and related products
- Size/variant selection for products

### 4. **User Engagement**
- Hero banner slider with auto-rotation
- Featured products section
- Technical catalogue downloads
- Contact/inquiry form with validation
- Social media integration

### 5. **Performance**
- Optimized image loading with lazy loading support
- Minimal external dependencies (Tailwind CDN)
- Fast API response handling with fallbacks

---

## Configuration

### API Base URL

Edit `config.php` to set the API endpoint:

```php
define('API_BASE_URL', 'http://localhost/FrioAdmin/');
```

For production, update to your live hosting URL:

```php
define('API_BASE_URL', 'https://admin.frio.com/');
```

---

## API Endpoints

The front-end communicates with FrioAdmin via REST API:

| Endpoint | Purpose |
|----------|---------|
| `api/settings.php` | Site settings (logo, email, phone, address) |
| `api/banners.php` | Homepage banners |
| `api/categories.php` | Product categories |
| `api/products.php` | All products or specific product by ID |
| `api/catalogues.php` | Technical catalogues and PDFs |

---

## Pages & Functionality

### Homepage (`index.php`)
- Auto-rotating hero banner
- Featured categories grid
- Why Choose FRIO section
- Featured products carousel
- Call-to-action for catalogue download

### Product Catalog (`product.php`)
- Full product grid with pagination
- Search functionality (by name, code, category)
- Category filtering sidebar
- Sort options (Name A-Z, Name Z-A, Product Code)
- Product count display

### Product Detail (`product-detail.php`)
- Large product image with gallery
- Product specifications cards
- Available sizes/variants with selection
- Related products from same category
- Inquiry CTA button

### Category Page (`category.php`)
- All product categories displayed
- Category image and description
- Link to filtered product catalog

### Catalogue Page (`catalogue.php`)
- Technical documentation downloads
- PDF preview cards
- Request catalogue option

### About Page (`about.php`)
- Company mission and vision
- Manufacturing excellence features
- Statistics and certifications
- Call-to-action buttons

### Contact Page (`contact.php`)
- Contact information panel
- Business hours
- Social media links
- Inquiry form with validation
- Success/error messaging

---

## Styling & Branding

### Color Palette

| Color | Hex | Usage |
|-------|-----|-------|
| Primary | `#003462` | Main brand color (deep blue) |
| Secondary | `#735c00` | Accent color |
| Secondary-Container | `#fed65b` | Button backgrounds |
| Secondary-Fixed | `#ffe088` | Highlights and hover states |
| Surface | `#f9f9ff` | Background color |
| Error | `#ba1a1a` | Error messages |

### Typography

- **Font Family**: Hanken Grotesk (Google Fonts)
- **Headlines**: Bold, 24-48px
- **Body Text**: Regular, 14-18px
- **Labels**: Bold, 12-14px

### Tailwind Configuration

Custom Tailwind theme is configured in `includes/header.php` with Material Design 3 colors and spacing system.

---

## JavaScript Features

### Banner Slider
- Auto-rotation every 5 seconds
- Manual navigation with arrows
- Dot indicators for slide position
- Keyboard support (arrow keys)

### Product Filtering
- Real-time search across product names and codes
- Category filtering with sidebar buttons
- Sort functionality
- Dynamic product count

### Gallery & Variants
- Click to change product images
- Size/variant selection with visual feedback
- Image swapping based on variant selection

### Mobile Menu
- Hamburger toggle on mobile
- Smooth slide-down animation
- Auto-close on link click

---

## Forms & Validation

### Inquiry Form (`contact.php`)
- Required fields: Name, Email, Subject, Message
- Optional fields: Product name, Company
- Client-side validation
- Mailto submission (can be upgraded to backend processing)
- Success/error message display

---

## Improvements Made

1. **Removed Duplicate Files**: Deleted `contect.php` (misspelled duplicate)
2. **Code Cleanup**: Ensured consistent formatting and structure
3. **Documentation**: Added comprehensive README for future maintenance
4. **API Resilience**: Dual-fallback system (cURL → file_get_contents)
5. **Responsive Design**: Mobile-first approach with Tailwind CSS
6. **Accessibility**: Proper semantic HTML, ARIA labels, and keyboard navigation

---

## Deployment

### Requirements
- PHP 7.4+
- MySQL/MariaDB (for FrioAdmin backend)
- Web server (Apache/Nginx)
- cURL extension (recommended for API calls)

### Steps
1. Upload FrioFront folder to your web server
2. Update `config.php` with correct API_BASE_URL
3. Ensure FrioAdmin is running and accessible
4. Test all pages and forms

### Environment Variables
No environment variables required. All configuration is in `config.php`.

---

## Troubleshooting

### Products not showing
- Check API_BASE_URL in `config.php`
- Verify FrioAdmin is running and accessible
- Check browser console for API errors

### Images not loading
- Verify image paths in FrioAdmin
- Check asset_url() function in config.php
- Ensure correct permissions on image directories

### Form not submitting
- Check email address in settings
- Verify JavaScript is enabled
- Test mailto: link functionality

---

## Future Enhancements

- [ ] Backend form submission (save inquiries to database)
- [ ] User authentication and account management
- [ ] Shopping cart and order management
- [ ] Advanced product filtering (price, specifications)
- [ ] Product reviews and ratings
- [ ] Blog/news section
- [ ] Multi-language support
- [ ] SEO optimization
- [ ] Analytics integration

---

## Support & Maintenance

For issues or feature requests, contact the development team or refer to the FrioAdmin documentation.

---

## License

© 2026 FRIO Industrial. All Rights Reserved.

**Tagline**: Safety By Choice.
