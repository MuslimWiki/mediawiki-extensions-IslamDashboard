<?php
/**
 * IslamDashboard - A comprehensive user dashboard for MediaWiki
 *
 * @file
 * @ingroup Extensions
 * @license GPL-3.0-or-later
 */

if ( function_exists( 'wfLoadExtension' ) ) {
    wfLoadExtension( 'IslamDashboard' );
    
    // Default configuration
    global $wgIslamDashboardConfig;
    $wgIslamDashboardConfig = [
        'EnableQuickActions' => true,
        'ShowDashboardLinkInUserMenu' => true,
        'DefaultDashboardLayout' => 'default',
        'EnableAnalytics' => false,
    ];
    
    return true;
} else {
    die( 'This version of the IslamDashboard extension requires MediaWiki 1.43+' );
}
