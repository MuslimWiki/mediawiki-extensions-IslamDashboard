<?php
/**
 * PHPUnit bootstrap file for the IslamDashboard extension
 *
 * @file
 * @since 1.0.0
 */

// Load the MediaWiki test environment
$mwDir = getenv( 'MW_INSTALL_PATH' ) !== false ? getenv( 'MW_INSTALL_PATH' ) : dirname( dirname( dirname( dirname( __DIR__ ) ) ) );

if ( !is_readable( $mwDir . '/tests/phpunit/TestSetup.php' ) ) {
    die( 'The MediaWiki installation could not be found. Set MW_INSTALL_PATH environment variable.' );
}

// Load the test setup
require_once $mwDir . '/tests/phpunit/TestSetup.php';

// Load the autoloader
require_once $mwDir . '/vendor/autoload.php';

// Load the extension
require_once dirname( __DIR__ ) . '/IslamDashboard.php';

// Load test helpers
require_once __DIR__ . '/TestHelper.php';

// Set up the test environment
TestSetup::snapshotGlobals();
