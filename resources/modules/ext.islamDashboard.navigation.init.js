/**
 * Navigation initialization for IslamDashboard
 *
 * Initializes the navigation component when the page is ready.
 */
( function ( $, mw ) {
    'use strict';

    // Wait for the DOM to be ready
    $( function () {
        // Initialize navigation if the container exists
        var $navContainer = $( '.dashboard-navigation' );
        if ( $navContainer.length ) {
            // Initialize navigation with default options
            // eslint-disable-next-line no-new
            new mw.islamDashboard.Navigation( {
                container: '.dashboard-navigation',
                sectionHeader: '.dashboard-navigation-section-header',
                sectionContent: '.dashboard-navigation-section-content',
                toggleButton: '.dashboard-navigation-toggle',
                userMenuToggle: '.dashboard-user-menu',
                userMenu: '.dashboard-user-dropdown',
                mobileBreakpoint: 768,
                collapsedClass: 'collapsed',
                activeClass: 'active',
                storageKey: 'islamdashboard-navigation-state',
                ajaxLoad: true
            } );
        }
    } );

} )( jQuery, mediaWiki );

// Add this module as a dependency for the main module
mw.loader.using( 'ext.islamDashboard.navigation' ).then( function () {
    // Navigation module is now loaded
    mw.hook( 'ext.islamDashboard.navigation' ).fire();
} );
