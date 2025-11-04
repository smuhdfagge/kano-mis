<?php
// Project root (one level above app folder)
define('PROJECTROOT', dirname(__DIR__));

// Load Config
require_once PROJECTROOT . '/config/config.php';

// Load Helpers (use explicit app path to avoid relying on APPROOT from config)
require_once PROJECTROOT . '/app/helpers/Session.php';

// Load Core Classes
require_once PROJECTROOT . '/app/core/Core.php';
require_once PROJECTROOT . '/app/core/Controller.php';
require_once PROJECTROOT . '/app/core/Database.php';

// Initialize Session
Session::init();