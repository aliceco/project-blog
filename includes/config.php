
<?php
$host = $_SERVER['HTTP_HOST'] ?? '';

// Configures BASE_URL depending on if we're in localhost or server, makes paths easier. 
if (!defined('BASE_URL')) {
    if (str_starts_with($host, 'localhost') || str_starts_with($host, '127.0.0.1')) {
        define('BASE_URL', '/project-blog/');
    } else {
        define('BASE_URL', '/d0019e/alicoh5/project-blog/');
    }
}
