<?php
declare(strict_types=1);

// Basic configuration. Adjust for your environment.
// You can override via environment variables.

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', (int)(getenv('DB_PORT') ?: 3306));
define('DB_NAME', getenv('DB_NAME') ?: 'ict_borrow');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

define('APP_NAME', 'ICT Equipment Borrowing');
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8000');

// Email stub settings (for later extension)
define('MAIL_FROM', getenv('MAIL_FROM') ?: 'noreply@example.com');

