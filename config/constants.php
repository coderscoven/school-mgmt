<?php
/*
 | --------------------------------------------------------------------
 | Constant globals definitions
 | --------------------------------------------------------------------
 |
 | Description: Define your constants here
 |
 */

// database configurations
define('DB_HOST', 'localhost');
define('DB_NAME', 'school_mgmt_db');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// password hashing factor encryption
define("HASH_COST_FACTOR", "10");

// for storing log files
define('LOGS_DIR', 'writables/logs/');

// access levels
define("LV_1", "Admin");
define("LV_2", "Bursar");
define("LV_3", "Teacher");

// student photo upload directory
define("STUD_PHOTO_DIR", "assets/uploads/students/");

// teachers photo upload directory
define("TEACHERS_PHOTO_DIR", "assets/uploads/teachers/");

//
define("yes", "yes");
define("no", "no");
