
<?php
// Start session
session_start();

// Site configuration
define('SITE_NAME', 'ScholarRecruit');
define('SITE_URL', 'http://localhost/scholar_recruit'); // Change this to your domain

// Define roles
define('ROLE_STUDENT', 'student');
define('ROLE_COMPANY', 'company');
define('ROLE_ADMIN', 'admin');

// Include database connection
require_once 'includes/db_connect.php';

// Include helper functions
require_once 'includes/functions.php';
?>
