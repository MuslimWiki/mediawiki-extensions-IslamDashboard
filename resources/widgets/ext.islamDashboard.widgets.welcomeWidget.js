/**
 * WelcomeWidget JavaScript
 *
 * @class mw.ext.islamDashboard.WelcomeWidget
 * @constructor
 */
( function ( $, mw ) {
    'use strict';

    /**
     * Initialize the welcome widget
     */
    function initWelcomeWidget() {
        // Widget initialization code will go here
        console.log( 'WelcomeWidget initialized' );

        // Example: Add click handler for welcome message
        $( '.welcome-widget' ).on( 'click', function () {
            mw.notify( mw.msg( 'islamdashboard-welcome-message' ) );
        } );
    }

    // Initialize when the document is ready
    $( document ).ready( function () {
        // Only initialize if this widget is present on the page
        if ( $( '.welcome-widget' ).length ) {
            initWelcomeWidget();
        }
    } );

    // Make the initialization function available for other modules
    mw.ext.islamDashboard = mw.ext.islamDashboard || {};
    mw.ext.islamDashboard.initWelcomeWidget = initWelcomeWidget;

}( jQuery, mediaWiki ) );
