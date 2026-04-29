<?php
// public/index.php
// Single entry point — ALL requests go through here

session_start();

// Autoload core files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/App.php';
require_once __DIR__ . '/../app/helpers.php';

// Boot the router
new App();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);