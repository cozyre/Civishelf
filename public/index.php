<?php
// public/index.php
// Single entry point — ALL requests go through here

session_start();

// Autoload core files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/App.php';

// Boot the router
new App();

