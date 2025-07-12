/**
 * IslamDashboard - Initialization
 * This file is loaded after the main module and handles initialization
 */
( function ( $, mw ) {
    'use strict';

    // Wait for the document to be ready
    $( function () {
        // Check if we're on the dashboard page
        if ( mw.config.get( 'wgCanonicalSpecialPageName' ) !== 'Dashboard' ) {
            return;
        }

        // Initialize the dashboard
        if ( window.IslamDashboard && typeof window.IslamDashboard.initDashboard === 'function' ) {
            window.IslamDashboard.initDashboard();
        } else {
            mw.log.error( 'IslamDashboard: Main module not loaded correctly' );
        }
    } );

} ( jQuery, mediaWiki ) );

// Add any polyfills or compatibility code here if needed
( function () {
    // Check for required features and polyfill if necessary
    if ( !Array.prototype.forEach ) {
        Array.prototype.forEach = function ( callback ) {
            for ( var i = 0; i < this.length; i++ ) {
                callback( this[ i ], i, this );
            }
        };
    }
} )();
