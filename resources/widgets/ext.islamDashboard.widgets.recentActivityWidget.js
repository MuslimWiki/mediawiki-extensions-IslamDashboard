/**
 * RecentActivityWidget JavaScript
 *
 * @class mw.ext.islamDashboard.RecentActivityWidget
 * @constructor
 */
( function ( $, mw ) {
    'use strict';

    /**
     * Initialize the recent activity widget
     */
    function initRecentActivityWidget() {
        console.log( 'RecentActivityWidget initialized' );

        // Handle loading more activities
        $( '.recent-activity-widget' ).on( 'click', '.load-more', function ( e ) {
            e.preventDefault();
            loadMoreActivities( $( this ) );
        } );

        // Handle activity item clicks
        $( '.recent-activity-widget' ).on( 'click', '.activity-item', function () {
            const url = $( this ).data( 'url' );
            if ( url ) {
                window.location.href = url;
            }
        } );
    }

    /**
     * Load more activities via AJAX
     * 
     * @param {jQuery} $button The load more button
     */
    function loadMoreActivities( $button ) {
        const $widget = $button.closest( '.recent-activity-widget' );
        const offset = $widget.find( '.activity-item' ).length;
        const limit = 5;

        // Show loading state
        $button.prop( 'disabled', true ).text( mw.msg( 'islamdashboard-loading' ) );

        // Simulate API call (replace with actual API call)
        setTimeout( function () {
            // This would be replaced with an actual API call
            // mw.api.get( {
            //     action: 'islamdashboard',
            //     subaction: 'getactivities',
            //     offset: offset,
            //     limit: limit
            // } ).done( function ( data ) {
            //     if ( data.activities && data.activities.length > 0 ) {
            //         renderActivities( data.activities );
            //     } else {
            //         $button.remove();
            //     }
            // } );

            // For now, just remove the button after a delay
            setTimeout( function () {
                $button.remove();
            }, 1000 );
        }, 500 );
    }

    /**
     * Render activities in the widget
     * 
     * @param {Array} activities Array of activity objects
     */
    function renderActivities( activities ) {
        // This would be implemented to render the activities
        console.log( 'Rendering activities:', activities );
    }

    // Initialize when the document is ready
    $( document ).ready( function () {
        // Only initialize if this widget is present on the page
        if ( $( '.recent-activity-widget' ).length ) {
            initRecentActivityWidget();
        }
    } );

    // Make the initialization function available for other modules
    mw.ext.islamDashboard = mw.ext.islamDashboard || {};
    mw.ext.islamDashboard.initRecentActivityWidget = initRecentActivityWidget;

}( jQuery, mediaWiki ) );
