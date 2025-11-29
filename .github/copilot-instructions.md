Berikut adalah perbaikan instruksi tersebut. Saya menambahkan Bagian 6 yang secara spesifik mengatur alur kerja CI/CD (Continuous Integration/Continuous Deployment), validasi kode otomatis, dan mekanisme self-healing (perbaikan otomatis) sebelum kode didorong (push) ke GitHub.

Dokumen ini dirancang agar AI Agent (seperti di Cursor/VSCode) mengerti bahwa ia tidak boleh sembarangan mengubah kode tanpa validasi.

Kresuber Resto Plugin - AI Agent Instructions
This document provides essential guidelines for AI coding agents to effectively understand, contribute to, and maintain the Kresuber Resto WordPress plugin codebase.

1. Big Picture Architecture
The Kresuber Resto plugin extends WordPress to provide a Point-of-Sale (POS) terminal and a customer-facing application.

WordPress Plugin Structure: Standard WordPress plugin, utilizing hooks (add_action, add_filter), custom rewrite rules, and AJAX for frontend-backend communication.

Core Logic (includes/core/class-pos-core.php): Acts as the main controller. It registers custom URL endpoints (/pos-terminal, /app), enqueues assets (CSS, JS modules), and initializes other core components.

API Endpoints (includes/api/class-pos-api.php): Handles all AJAX requests from the frontend. It primarily interacts with WooCommerce for product data retrieval.

Frontend Applications:

templates/app-shell.php: Renders the POS terminal UI.

templates/user-app-shell.php: Renders the customer-facing application UI.

assets/js/pos-app.js: The main JavaScript entry point for both frontend applications, built with ES Modules.

WooCommerce Integration: Product data is fetched directly from WooCommerce using WP_Query and WC_Product objects within Kresuber_POS_Api.

2. Key Files and Directories
kresuber-resto.php: Plugin entry point, defines constants, autoloader, and activation hooks.

includes/core/class-pos-core.php: Core plugin setup, routing, asset loading.

includes/api/class-pos-api.php: Backend AJAX handlers.

assets/js/pos-app.js: Main frontend JavaScript.

assets/js/modules/: Contains frontend JavaScript modules (cart.js, ui.js).

templates/: Contains PHP template files for the frontend UIs.

.github/workflows/: (New) Contains CI/CD configurations for automated testing.

3. Critical Developer Workflows
3.1. Custom Rewrites & Routing
The plugin registers custom rewrite rules for /pos-terminal and /app during activation.

Problem: If these URLs are not working, the rewrite rules might need to be flushed. This can be done by deactivating and reactivating the plugin, or programmatically using flush_rewrite_rules().

Implementation: See register_activation_hook in kresuber-resto.php and rewrites() in class-pos-core.php.

3.2. Frontend-Backend Communication (AJAX)
Frontend JavaScript (pos-app.js) communicates with the WordPress backend via admin-ajax.php.

Global JS Object: Essential data (ajax_url, nonce, site_url) is passed from PHP to JavaScript via the KRESUBER global object, localized in class-pos-core.php::assets().

Nonce Security: AJAX calls use KRESUBER_NONCE for security checks. Be aware that the kresuber_get_products action has a relaxed nonce check for public access.

3.3. JavaScript Modules
Frontend JavaScript (pos-app.js and its modules) uses ES Module syntax (import, export).

Important: The class-pos-core.php::add_type_attribute() method is crucial for ensuring pos-app.js is loaded with type="module" in the browser. Do not remove or alter this without understanding its purpose.

4. Project-Specific Conventions
PHP Class Naming: All main plugin PHP classes follow the Kresuber_POS_ prefix (e.g., Kresuber_POS_Core).

Autoloading: A custom autoloader (in kresuber-resto.php) handles loading Kresuber_POS_ prefixed classes from includes/core/, includes/api/, includes/admin/, and includes/utils/. Files should be named class-pos-<module-name>.php.

Image Fallbacks: When displaying product images, there's a fallback to a placeholder image if get_image_id() is empty. See class-pos-api.php::get_products().

Global Frontend Functions: window.triggerAdd and window.triggerUpdate are exposed globally in pos-app.js to facilitate direct calls from HTML onclick attributes.

5. External Dependencies
WooCommerce: The plugin relies heavily on WooCommerce for product management.

jQuery: Used in pos-app.js.

CDNs: Google Fonts (Plus Jakarta Sans) and Remix Icons are loaded from CDNs.

6. Automated GitHub Updates & Quality Assurance (Crucial)
Protocol for AI Agent: Whenever you modify code or add features, you must strictly follow this "Check-Fix-Push" cycle. Do not commit code that breaks the build.

6.1. The Self-Healing Workflow
Before finalizing any task, the AI Agent must execute the following logic loop:

Generate Code: Implement the requested feature or fix.

Static Analysis (Linting):

Run PHP Syntax Check: php -l <filename>

(Optional) Run PHPCS (WordPress Standards): phpcs --standard=WordPress .

(Optional) Run ESLint for JS files.

Auto-Correction:

IF Errors Found: You must interpret the error message, fix the code immediately, and repeat Step 2.

IF Warning Found: Apply automatic fixers (e.g., phpcbf) if safe, or manually adjust formatting.

Verification: Ensure no fatal errors remain.

6.2. GitHub Integration Commands
Once the code passes the "Self-Healing Workflow", use the following Git commands to update the repository automatically:

Bash

# 1. Stage all changes
git add .

# 2. Commit with Semantic Versioning
# Format: [TYPE] Description (Reference)
# Types: FEAT (New Feature), FIX (Bug fix), REFACTOR (Code cleanup), STYLE (UI changes)
git commit -m "FEAT: Add table management logic to API"

# 3. Push to Repository
git push origin main
6.3. Handling Conflicts & Failures
If the git push fails due to conflicts, the AI Agent must pull the latest changes (git pull --rebase), resolve conflicts intelligently (prioritizing local logic for new features), and retry the push.

If a PHP Fatal Error is detected during the linting phase, DO NOT PUSH. Stop and rewrite the faulty logic.