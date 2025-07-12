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
    // Keep i18n globals for backward compatibility
    $wgMessagesDirs['IslamDashboard'] = __DIR__ . '/i18n';
    $wgExtensionMessagesFiles['IslamDashboardAlias'] = __DIR__ . '/IslamDashboard.alias.php';
    
    // Register special page
    $wgAutoloadClasses['SpecialDashboard'] = __DIR__ . '/SpecialDashboard.php';
    $wgSpecialPages['Dashboard'] = 'SpecialDashboard';
    
    // Register module
    $wgResourceModules['ext.islamDashboard'] = [
        'scripts' => [
            'resources/scripts/ext.islamDashboard.js',
            'resources/scripts/ext.islamDashboard.init.js'
        ],
        'styles' => [
            'resources/styles/ext.islamDashboard.less',
            'resources/styles/ext.islamDashboard.css'
        ],
        'dependencies' => [
            'mediawiki.api',
            'mediawiki.util',
            'mediawiki.jqueryMsg',
            'mediawiki.cookie',
            'oojs-ui-core',
            'oojs-ui-windows',
            'oojs-ui.styles.icons-interactions',
            'oojs-ui.styles.icons-moderation',
            'oojs-ui.styles.icons-content',
            'codex',
            'codex-search'
        ],
        'localBasePath' => __DIR__,
        'remoteExtPath' => 'IslamDashboard',
        'position' => 'top'
    ];
    
    // Register hooks
    $wgHooks['BeforePageDisplay'][] = 'IslamDashboardHooks::onBeforePageDisplay';
    $wgHooks['SkinTemplateNavigation::Universal'][] = 'IslamDashboardHooks::onSkinTemplateNavigationUniversal';
    
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
