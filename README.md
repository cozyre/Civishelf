# Civishelf
Campus style digital library as web programming 2 class project

## Setup
1. Place folder in htdocs as `/Civishelf`
2. Access via http://localhost/Civishelf/public/
3. Or configure a VirtualHost to point DocumentRoot to the /public folder

> For XAMPP: httpd.conf and make sure LoadModule rewrite_module is uncommented.

## Local Setup (XAMPP)
1. Place root folder (as '/Civishelf') into htdocs
2. Check `C:/xampp/apache/conf/httpd.conf`
    - Uncomment this line (remove the #) LoadModule rewrite_module modules/mod_rewrite.so
    - Find the htdocs Directory block and change AllowOverride
    <Directory "C:/xampp/htdocs">
        Options Indexes FollowSymLinks Includes ExecCGI
        AllowOverride All        ← change None to All
        Require all granted
    </Directory>
3. Create and link database
    Import digital_library into local database of choice and create config/config.php with the contents:
    ```
    <?php
    // config/config.php

    define('DB_HOST', 'localhost');
    define('DB_NAME', 'digital_library');
    define('DB_USER', 'root');        // change to actual user
    define('DB_PASS', '');            // change to actual password
    define('DB_CHARSET', 'utf8mb4');

    // App settings
    define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/Civishelf');
    define('PDF_STORAGE_PATH', __DIR__ . '/../storage/books/');  // outside /public
    define('COVER_IMAGE_PATH', __DIR__ . '/../public/assets/images/covers/');
    ```
4. start the services; Apache and MySQL
5. go to browser and visit 'http://localhost/Civishelf/'