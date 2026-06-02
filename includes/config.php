
<?php
$host = $_SERVER['HTTP_HOST'] ?? '';

if (!defined('BASE_URL')) {
    if (str_starts_with($host, 'localhost') || str_starts_with($host, '127.0.0.1')) {
        define('BASE_URL', '/project-blog/');
    } else {
        define('BASE_URL', '/d0019e/alicoh5/project-blog/');
    }
}
