# FrioFront API Documentation

This document describes the REST API endpoints used by FrioFront to fetch dynamic content from the FrioAdmin backend.

---

## Base URL

```
http://localhost/FrioAdmin/api/
```

For production, update the base URL in `config.php`:

```php
define('API_BASE_URL', 'https://admin.frio.com/');
```

---

## Authentication

Currently, the API does not require authentication. All endpoints are publicly accessible.

**Future Enhancement**: OAuth2 authentication will be implemented in v3.0.0.

---

## Response Format

All API responses follow a standard JSON structure:

```json
{
  "status": "success",
  "data": {
    // Response data here
  }
}
```

On error:

```json
{
  "status": "error",
  "message": "Error description"
}
```

---

## Endpoints

### 1. Settings

**Endpoint**: `GET /api/settings.php`

**Description**: Retrieves site-wide settings including logo, contact information, and social media links.

**Response**:

```json
{
  "status": "success",
  "data": {
    "logo": "https://example.com/logo.png",
    "email": "info@frioindia.com",
    "phone": "+91-7760-889505",
    "address": "Suite 100, 1234 Main Street, City, State 12345",
    "facebook": "https://facebook.com/frioindia",
    "instagram": "https://instagram.com/frioindia",
    "linkedin": "https://linkedin.com/company/frioindia",
    "twitter": "https://twitter.com/frioindia",
    "youtube": "https://youtube.com/frioindia"
  }
}
```

**Usage in FrioFront**:

```php
$settings = api_fetch('settings.php');
echo $settings['email']; // info@frioindia.com
```

---

### 2. Banners

**Endpoint**: `GET /api/banners.php`

**Description**: Retrieves homepage banners for the hero slider.

**Query Parameters**: None

**Response**:

```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "Precision Brass Fittings",
      "image": "uploads/banners/banner_1.jpg",
      "link": "product.php",
      "active": 1,
      "display_order": 1
    },
    {
      "id": 2,
      "title": "Industrial Safety Solutions",
      "image": "uploads/banners/banner_2.jpg",
      "link": "about.php",
      "active": 1,
      "display_order": 2
    }
  ]
}
```

**Usage in FrioFront**:

```php
$banners = api_fetch('banners.php');
foreach ($banners as $banner) {
    echo $banner['title'];
}
```

---

### 3. Categories

**Endpoint**: `GET /api/categories.php`

**Description**: Retrieves all product categories.

**Query Parameters**: None

**Response**:

```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "code": "#CAT-001",
      "name": "Flare Nuts",
      "description": "Premium flare nuts for hydraulic systems",
      "image": "uploads/categories/flare_nuts.jpg",
      "active": 1,
      "display_order": 1
    },
    {
      "id": 2,
      "code": "#CAT-002",
      "name": "Unions",
      "description": "Brass unions for pipe connections",
      "image": "uploads/categories/unions.jpg",
      "active": 1,
      "display_order": 2
    }
  ]
}
```

**Usage in FrioFront**:

```php
$categories = api_fetch('categories.php');
foreach ($categories as $cat) {
    echo $cat['name'];
}
```

---

### 4. Products

**Endpoint**: `GET /api/products.php`

**Description**: Retrieves all products or a specific product by ID.

**Query Parameters**:

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | Integer | Optional. Fetch specific product by ID |
| `category_id` | Integer | Optional. Filter by category |

**Response** (All Products):

```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "category_id": 1,
      "category_name": "Flare Nuts",
      "code": "#PR-001",
      "name": "Heavy Hex Flare Nuts",
      "description": "Premium high-pressure hex flare nuts",
      "image": "uploads/products/flare_nuts_1.jpg",
      "gallery": [
        { "image": "uploads/products/flare_nuts_1.jpg" },
        { "image": "uploads/products/flare_nuts_2.jpg" }
      ],
      "specifications": {
        "Material": "Brass",
        "Finish": "Polished",
        "Application": "Hydraulic Systems"
      },
      "variations": [
        {
          "id": 1,
          "product_id": 1,
          "code": "#PR-001-01",
          "size": "1/4 inch",
          "image": "",
          "active": 1
        },
        {
          "id": 2,
          "product_id": 1,
          "code": "#PR-001-02",
          "size": "3/8 inch",
          "image": "",
          "active": 1
        }
      ],
      "active": 1,
      "display_order": 1
    }
  ]
}
```

**Response** (Single Product):

```json
{
  "status": "success",
  "data": {
    "id": 1,
    "category_id": 1,
    "category_name": "Flare Nuts",
    "code": "#PR-001",
    "name": "Heavy Hex Flare Nuts",
    "description": "Premium high-pressure hex flare nuts",
    "image": "uploads/products/flare_nuts_1.jpg",
    "gallery": [
      { "image": "uploads/products/flare_nuts_1.jpg" },
      { "image": "uploads/products/flare_nuts_2.jpg" }
    ],
    "specifications": {
      "Material": "Brass",
      "Finish": "Polished",
      "Application": "Hydraulic Systems"
    },
    "variations": [
      {
        "id": 1,
        "product_id": 1,
        "code": "#PR-001-01",
        "size": "1/4 inch",
        "image": "",
        "active": 1
      }
    ],
    "active": 1,
    "display_order": 1
  }
}
```

**Usage in FrioFront**:

```php
// Get all products
$products = api_fetch('products.php');

// Get specific product
$product = api_fetch('products.php?id=1');

// Get products by category
$cat_products = api_fetch('products.php?category_id=1');
```

---

### 5. Catalogues

**Endpoint**: `GET /api/catalogues.php`

**Description**: Retrieves technical catalogues and PDF downloads.

**Query Parameters**: None

**Response**:

```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "2026 Technical Catalogue",
      "description": "Complete product specifications and installation guide",
      "pdf_file": "uploads/catalogues/frio_catalogue_2026.pdf",
      "preview_image": "uploads/catalogues/catalogue_preview.jpg",
      "active": 1,
      "display_order": 1
    },
    {
      "id": 2,
      "name": "Installation Manual",
      "description": "Step-by-step installation instructions",
      "pdf_file": "uploads/catalogues/installation_manual.pdf",
      "preview_image": "uploads/catalogues/manual_preview.jpg",
      "active": 1,
      "display_order": 2
    }
  ]
}
```

**Usage in FrioFront**:

```php
$catalogues = api_fetch('catalogues.php');
foreach ($catalogues as $cat) {
    echo $cat['name'];
    echo $cat['pdf_file'];
}
```

---

## Helper Functions

### `api_fetch($endpoint)`

Fetches data from the API with dual fallback (cURL → file_get_contents).

**Parameters**:
- `$endpoint` (string): API endpoint path (e.g., 'settings.php', 'products.php?id=1')

**Returns**:
- Array on success
- NULL on failure

**Example**:

```php
$settings = api_fetch('settings.php');
if ($settings) {
    echo $settings['email'];
} else {
    echo "API connection failed";
}
```

### `asset_url($path)`

Builds absolute URL for assets (images, PDFs).

**Parameters**:
- `$path` (string): Relative asset path

**Returns**:
- String: Absolute URL

**Example**:

```php
$image_url = asset_url('uploads/products/product_1.jpg');
// Output: http://localhost/FrioAdmin/uploads/products/product_1.jpg
```

---

## Error Handling

### API Connection Failure

When the API is unavailable, FrioFront displays fallback content:

```php
$products = api_fetch('products.php') ?? [];
if (empty($products)) {
    echo "No products available";
}
```

### Invalid Response

If the API returns invalid JSON:

```php
$data = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    // Handle JSON error
}
```

---

## Rate Limiting

Currently, there are no rate limits on the API. This may be implemented in future versions.

---

## CORS (Cross-Origin Resource Sharing)

The API does not enforce CORS restrictions. Requests from any origin are accepted.

---

## Caching

FrioFront does not implement client-side caching. For better performance, consider:

1. **Server-side Caching**: Implement Redis or Memcached on FrioAdmin
2. **Browser Caching**: Set appropriate cache headers
3. **CDN Caching**: Use a CDN for static assets

---

## Pagination

The API does not support pagination. All results are returned at once.

**Future Enhancement**: Pagination will be implemented in v3.0.0.

---

## Filtering & Sorting

### Products by Category

```php
$products = api_fetch('products.php?category_id=1');
```

### Sorting

Currently, sorting is handled client-side in JavaScript. Server-side sorting will be added in future versions.

---

## Data Types

| Type | Description | Example |
|------|-------------|---------|
| Integer | Whole number | `1`, `123`, `0` |
| String | Text | `"Flare Nuts"`, `"#PR-001"` |
| Boolean | True/False | `1` (true), `0` (false) |
| Array | List of items | `[{...}, {...}]` |
| Object | Key-value pairs | `{"id": 1, "name": "..."}` |
| Null | No value | `null` |

---

## Example Requests

### Using cURL

```bash
curl -X GET "http://localhost/FrioAdmin/api/products.php"
```

### Using PHP

```php
$url = 'http://localhost/FrioAdmin/api/products.php';
$response = file_get_contents($url);
$data = json_decode($response, true);
```

### Using JavaScript (Fetch API)

```javascript
fetch('http://localhost/FrioAdmin/api/products.php')
  .then(response => response.json())
  .then(data => console.log(data));
```

---

## Versioning

The API is currently at **v1.0**. Future versions may introduce breaking changes.

---

## Support

For API issues or questions, contact:
- **Email**: support@frio.com
- **Documentation**: See README.md

---

## License

© 2026 FRIO Industrial. All Rights Reserved.
