<?php
// Project root (same as __DIR__ parent since we're in app folder)
define('PROJECTROOT', dirname(__DIR__));

// Load Config
require_once __DIR__ . '/../config/config.php';

// Load Helpers
require_once __DIR__ . '/helpers/Session.php';

// Load Core Classes
require_once __DIR__ . '/core/Core.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Database.php';

// Initialize Session
Session::init();
