<?php
/**
 *
 * Configuration File
 *
 * @warning do not put any data here that changes laster. ( Do not put dynamic variable here )
 * This file meant to hold static variables only.
 *
 */

$DIR_DATA               = './data';

$DATABASE_USER          = 'root';
$DATABASE_PASSWORD      = '';
$DATABASE_NAME          = 'nlms';
$DATABASE_HOST          = 'localhost';
$DATABASE_TYPE          = 'sqlite';         // 'mysqli' | 'sqlite'


/**
 * If true, debug mode enabled.
 *
 * If false, All the debug related code will not run.
 *      - no log will be save.
 *      - no debug data will be printed to user.
 */
$DEBUG                  = true;

/**
 * If 'DEBUG_LOG_FILE_PATH' is not empty, then debug data will not be saved.
 */
$DEBUG_LOG_FILE_PATH    = $DIR_DATA . "/debug.log";

/**
 * Database debug message will be logged
 *
 *  if DEBUG = true & DEBUG_LOG_FILE_PATH has value & DEBUG_LOG_DATABASE = true
 *
 *
 */
$DEBUG_LOG_DATABASE         = true;

if ( file_exists( __DIR__ . "/my-config.php") ) require __DIR__ . "/my-config.php";

