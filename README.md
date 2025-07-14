# MiniCommerce Requirements

Below is the complete, consolidated requirements list for **MiniCommerce** – the lightweight, build-tool-free e-commerce platform.

---

## ✅ Functional Requirements

1. **Product catalog with dynamic filters** (category, price, stock, shipping, etc.)
2. **Product variants** (size, color, etc.)
3. **Shopping cart** with AJAX or LocalStorage persistence
4. **Multi-step checkout** process (address → payment → confirmation)
5. **Coupon / promotional codes**
6. **Shipping cost calculation** by region or user address
7. **Multiple payment methods** (simulated & external API hooks)
8. **PDF invoices** auto-generated after checkout
9. **Order tracking** with real-time status updates
10. **Return / refund** request system
11. **User registration & login** (bcrypt hashing)
12. **Session management** with CSRF protection & secure tokens
13. **User profile editing**
14. **Order history** per user
15. **Wishlist / favorites**
16. **In-site support** (ticketing or embedded chat)
17. **Email / web notifications** for users
18. **Admin product management** (create, update, delete)
19. **Bulk product import/export** via CSV
20. **Admin order dashboard**
21. **User management** with roles & permissions
22. **SEO configuration** interface (meta tags, URLs, sitemap, etc.)
23. **Analytics dashboard** (sales data, trends, CSV export)
24. **Admin activity log / audit trail**
25. **Editable landing page content** (WYSIWYG or block-based)

---

## 🚀 Innovative / Pro Functionalities

1. **Behavioral recommendations** (lightweight logic, non-ML)
2. **Membership & subscriptions** (recurring payments, premium tiers)
3. **Gamification** (points for purchases, reviews, referrals)
4. **Product comparison** with interactive UI
5. **Seller-side product editor** (simplified CMS)
6. **Catalog-only mode** (showcase without checkout)
7. **Accessibility toggle** (high contrast, large font, keyboard nav)
8. **PWA installable experience**
9. **Multi-language** support (i18n)
10. **Offline fallback** (core browsing via PWA)
11. **Wishlist sharing / public boards**
12. **GDPR data download / deletion requests**

---

## 🧠 Non-Functional Requirements

• Fully responsive, mobile-first design  
• SEO optimized: clean URLs, meta tags, `sitemap.xml`, schema.org  
• Security: XSS & SQLi prevention, CSRF tokens, input validation  
• High performance: lazy-loading, image compression, cache-friendly  
• Fast load times on mobile networks  
• Minimal external JS dependencies (no React, Vue, etc.)  
• Runs on standard LAMP/shared hosting (no Docker/Node)  
• Clean modular MVC codebase (PHP OOP, PSR-4)  
• Upgradable DB layer (SQLite → MySQL/PostgreSQL)  
• Maintainable & well-commented  
• Internationalization-ready (UI text externalized)  
• UX-focused: smooth flows, clear CTAs, minimal clutter  
• Accessibility compliant (WCAG AA)  
• Minimal but effective JS (optional Alpine.js)

---

## 🧱 Foundational Requirements

| Layer | Choice |
|-------|--------|
| Mark-up | **HTML5** |
| Styling | **TailwindCSS** |
| Scripts | **Vanilla JS** (optional Alpine.js) |
| Backend | **PHP 8+** |
| Database | **SQLite** (dev) – easily swappable |
| Build | **None** (no npm, Webpack, etc.) |
| Hosting | Standard shared LAMP (no CLI) |
| Architecture | Custom lightweight Router + MVC |
| Dependencies | Minimal `vendor/` (only essential libs) |
| PWA | Manifest + service worker |

---

### Usage
Use this README as a planning baseline, project brief, or import it into tools like **Cursor AI** / project management boards. Feel free to extend or reprioritize items based on stakeholder needs.