<?php
/**
 * Aliases for special pages of the IslamDashboard extension
 *
 * @file
 * @ingroup Extensions
 */

$specialPageAliases = [];

/** English (English) */
$specialPageAliases['en'] = [
    'IslamDashboard' => [ 'IslamDashboard', 'Dashboard', 'UserDashboard' ],
];

/** Arabic (العربية) */
$specialPageAliases['ar'] = [
    'Dashboard' => [ 'لوحة_التحكم' ],
];

$magicWords = [];

/** English (English) */
$magicWords['en'] = [
    'dashboard' => [ 0, 'dashboard' ],
];

// For MediaWiki 1.17 and above
if ( defined( 'MW_VERSION' ) ) {
    // Register extension messages
    $wgExtensionMessagesFiles['IslamDashboardAlias'] = __DIR__ . '/IslamDashboard.alias.php';
}

// Add more language aliases as needed
