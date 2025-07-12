/**
 * QuickActionsWidget JavaScript
 *
 * @class mw.ext.islamDashboard.QuickActionsWidget
 * @constructor
 */
( function ( $, mw ) {
    'use strict';

    /**
     * Initialize the quick actions widget
     */
    function initQuickActionsWidget() {
        console.log( 'QuickActionsWidget initialized' );

        // Handle quick action clicks
        $( '.quick-actions-widget' ).on( 'click', '.quick-action', function ( e ) {
            e.preventDefault();
            const $action = $( this );
            const actionType = $action.data( 'action' );
            
            // Add click effect
            $action.addClass( 'active' );
            setTimeout( function () {
                $action.removeClass( 'active' );
            }, 150 );

            // Handle different action types
            handleQuickAction( actionType, $action );
        } );
    }

    /**
     * Handle quick action button click
     * 
     * @param {string} actionType The type of action to perform
     * @param {jQuery} $button The clicked button element
     */
    function handleQuickAction( actionType, $button ) {
        console.log( 'Quick action triggered:', actionType );
        
        // Show loading state
        const $icon = $button.find( '.quick-action-icon' );
        const originalIcon = $icon.html();
        $icon.html( '<span class="oo-ui-iconElement-loading"></span>' );
        
        // Simulate API call (replace with actual implementation)
        setTimeout( function () {
            // Restore icon
            $icon.html( originalIcon );
            
            // Show success message
            mw.notify( mw.msg( 'islamdashboard-quickaction-success', $button.find( '.quick-action-label' ).text() ), {
                type: 'success',
                autoHide: true
            } );
            
            // Handle navigation if needed
            if ( $button.attr( 'href' ) && !$button.hasClass( 'no-navigate' ) ) {
                window.location.href = $button.attr( 'href' );
            }
        }, 500 );
    }

    // Initialize when the document is ready
    $( document ).ready( function () {
        // Only initialize if this widget is present on the page
        if ( $( '.quick-actions-widget' ).length ) {
            initQuickActionsWidget();
        }
    } );

    // Make the initialization function available for other modules
    mw.ext.islamDashboard = mw.ext.islamDashboard || {};
    mw.ext.islamDashboard.initQuickActionsWidget = initQuickActionsWidget;

}( jQuery, mediaWiki ) );
