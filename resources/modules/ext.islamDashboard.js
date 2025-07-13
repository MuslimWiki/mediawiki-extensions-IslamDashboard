/**
 * IslamDashboard - Main JavaScript
 */
( function ( $, mw ) {
    'use strict';

    // Create namespace if it doesn't exist
    window.IslamDashboard = window.IslamDashboard || {};

    /**
     * Main dashboard module
     */
    function initDashboard() {
        try {
            // Initialize any interactive components here
            setupEventListeners();
            loadUserData();
            setupWidgets();
            
            // Mark as initialized
            window.IslamDashboard.initialized = true;
            mw.log('IslamDashboard: Initialized successfully');
        } catch (e) {
            mw.log.error('IslamDashboard: Error during initialization', e);
        }
    }

    /**
     * Set up event listeners for the dashboard
     */
    function setupEventListeners() {
        // Toggle widget edit mode
        $( document ).on( 'click', '.widget-edit-toggle', function ( e ) {
            e.preventDefault();
            const $widget = $( this ).closest( '.dashboard-widget' );
            $widget.toggleClass( 'edit-mode' );
        } );

        // Handle widget removal
        $( document ).on( 'click', '.widget-remove', function ( e ) {
            e.preventDefault();
            if ( mw.confirm( mw.msg( 'islamdashboard-confirm-remove-widget' ) ) ) {
                const $widget = $( this ).closest( '.dashboard-widget' );
                removeWidget( $widget );
            }
        } );

        // Handle quick action clicks
        $( document ).on( 'click', '.quick-action', function ( e ) {
            // Add any quick action specific handling here
            mw.track( 'islamdashboard.quickAction', {
                action: $( this ).data( 'action' ) || 'unknown',
                timestamp: new Date().toISOString()
            } );
        } );

        // Handle notification actions
        $( document ).on( 'click', '.mark-read', function ( e ) {
            e.preventDefault();
            const $notification = $( this ).closest( '.notification-item' );
            markNotificationAsRead( $notification.data( 'id' ), $notification );
        } );
    }

    /**
     * Load user-specific data for the dashboard
     */
    function loadUserData() {
        const api = new mw.Api();
        
        // Load user stats
        api.get( {
            action: 'query',
            meta: 'userinfo',
            uiprop: 'editcount|registrationdate|groups',
            format: 'json',
            formatversion: 2
        } ).done( function ( data ) {
            updateUserStats( data.query.userinfo );
        } ).fail( function ( code, data ) {
            mw.log.error( 'Failed to load user data:', code, data );
        } );

        // Load recent activity
        loadRecentActivity();
        
        // Load notifications if the widget exists
        if ( $( '.notifications-widget' ).length ) {
            loadNotifications();
        }
    }

    /**
     * Update the user stats in the dashboard
     * 
     * @param {Object} userInfo User info from the API
     */
    function updateUserStats( userInfo ) {
        // Update welcome message with user's name
        $( '.welcome-message' ).text(
            mw.msg( 'islamdashboard-welcome-back', userInfo.name )
        );
        
        // Update edit count
        $( '.stat-edit-count' ).text( userInfo.editcount );
        
        // Update registration date if needed
        if ( userInfo.registrationdate ) {
            const regDate = new Date( userInfo.registrationdate );
            $( '.member-since' ).text(
                mw.msg( 'islamdashboard-member-since', regDate.toLocaleDateString() )
            );
        }
    }

    /**
     * Load recent activity for the user
     */
    function loadRecentActivity() {
        const api = new mw.Api();
        
        api.get( {
            action: 'query',
            list: 'usercontribs',
            ucuser: mw.config.get( 'wgUserName' ),
            ucprop: 'title|timestamp|comment|flags',
            uclimit: 5,
            format: 'json',
            formatversion: 2
        } ).done( function ( data ) {
            renderRecentActivity( data.query.usercontribs );
        } ).fail( function ( code, data ) {
            mw.log.error( 'Failed to load recent activity:', code, data );
            showError( 'recent-activity', mw.msg( 'islamdashboard-error-loading-activity' ) );
        } );
    }

    /**
     * Render recent activity in the widget
     * 
     * @param {Array} activities Array of activity items
     */
    function renderRecentActivity( activities ) {
        const $activityList = $( '.activity-list' );
        
        if ( !activities || activities.length === 0 ) {
            $activityList.html( 
                '<li class="no-activity">' + 
                    mw.msg( 'islamdashboard-no-recent-activity' ) + 
                '</li>'
            );
            return;
        }
        
        let html = '';
        
        activities.forEach( function ( activity ) {
            const title = new mw.Title( activity.title );
            const time = formatTimestamp(activity.timestamp);
            
            html +=
                '<li class="activity-item">' +
                '  <div class="activity-time">' + time + '</div>' +
                '  <div class="activity-content">' +
                '    <span class="activity-icon">' + getActivityIcon( activity ) + '</span>' +
                '    <div class="activity-details">' +
                '      <h4 class="activity-title">' +
                '        <a href="' + title.getUrl() + '">' + title.getNameText() + '</a>' +
                '      </h4>' +
                ( activity.comment ? 
                    '<p class="activity-description">' + mw.html.escape( activity.comment ) + '</p>' : 
                    '' ) +
                '    </div>' +
                '  </div>' +
                '</li>';
        } );
        
        $activityList.html( html );
    }

    /**
     * Get the appropriate icon for an activity
     * 
     * @param {Object} activity Activity data
     * @return {string} HTML for the icon
     */
    function getActivityIcon( activity ) {
        // Use OOUI icon classes directly
        let iconClass = 'oo-ui-icon-edit';
        let label = mw.msg('edit');
        
        if ( activity.minor ) {
            iconClass = 'oo-ui-icon-edit-undo';
            label = mw.msg('minoredit');
        } else if ( activity.new ) {
            iconClass = 'oo-ui-icon-new-page';
            label = mw.msg('new-page');
        }
        
        // Create icon element with direct HTML for better compatibility
        return `<span class="oo-ui-icon-element ${iconClass}" title="${label}"></span>`;
    }

    /**
     * Load user notifications
     */
    function loadNotifications() {
        const api = new mw.Api();
        
        api.get( {
            action: 'query',
            meta: 'notifications',
            notfilter: '!read',
            notlimit: 5,
            notprop: 'list|count',
            format: 'json',
            formatversion: 2
        } ).done( function ( data ) {
            renderNotifications( data.query.notifications.list, data.query.notifications.rawcount );
        } ).fail( function ( code, data ) {
            mw.log.error( 'Failed to load notifications:', code, data );
            showError( 'notifications', mw.msg( 'islamdashboard-error-loading-notifications' ) );
        } );
    }

    /**
     * Render notifications in the widget
     * 
     * @param {Array} notifications Array of notification items
     * @param {number} totalCount Total number of unread notifications
     */
    function renderNotifications( notifications, totalCount ) {
        const $notificationList = $( '.notifications-list' );
        
        if ( !notifications || notifications.length === 0 ) {
            $notificationList.html( 
                '<li class="no-notifications">' + 
                    mw.msg( 'islamdashboard-no-notifications' ) + 
                '</li>'
            );
            return;
        }
        
        let html = '';
        
        notifications.forEach( function ( notification ) {
            const time = new mw.widgets.DateInputWidget( {
                value: notification.timestamp.utcunix * 1000,
                displayFormat: { 
                    month: 'short', 
                    day: 'numeric', 
                    hour: '2-digit', 
                    minute: '2-digit' 
                }
            } );
            
            html +=
                '<li class="notification-item" data-id="' + notification.id + '">' +
                '  <div class="notification-time">' + time.getValue() + '</div>' +
                '  <div class="notification-content">' +
                '    <span class="notification-icon">' + 
                        new OO.ui.IconWidget( { icon: 'notification' } ).$element + 
                '    </span>' +
                '    <div class="notification-details">' +
                '      <h4 class="notification-title">' + 
                            mw.html.escape( notification.header || notification.title ) + 
                '      </h4>' +
                ( notification.body ? 
                    '<p class="notification-description">' + 
                        mw.html.escape( notification.body ) + 
                    '</p>' : 
                    '' ) +
                '    </div>' +
                '    <div class="notification-actions">' +
                '      <button class="mark-read" title="' + mw.msg( 'islamdashboard-mark-as-read' ) + '">' +
                '        ' + new OO.ui.IconWidget( { icon: 'check' } ).$element +
                '      </button>' +
                '    </div>' +
                '  </div>' +
                '</li>';
        } );
        
        $notificationList.html( html );
        
        // Update notification count in the header
        $( '.notification-count' )
            .text( totalCount )
            .toggleClass( 'has-notifications', totalCount > 0 );
    }

    /**
     * Mark a notification as read
     * 
     * @param {string} notificationId Notification ID
     * @param {jQuery} $notification Notification element
     */
    function markNotificationAsRead( notificationId, $notification ) {
        const api = new mw.Api();
        
        api.postWithToken( 'csrf', {
            action: 'echomarkread',
            list: notificationId,
            format: 'json'
        } ).done( function () {
            // Fade out and remove the notification
            $notification.fadeOut( 300, function () {
                $( this ).remove();
                
                // If no more notifications, show message
                if ( $( '.notification-item' ).length === 0 ) {
                    $( '.notifications-list' ).html( 
                        '<li class="no-notifications">' + 
                            mw.msg( 'islamdashboard-no-notifications' ) + 
                        '</li>'
                    );
                }
                
                // Update notification count
                const $count = $( '.notification-count' );
                const newCount = Math.max( 0, parseInt( $count.text() || '0', 10 ) - 1 );
                $count.text( newCount )
                    .toggleClass( 'has-notifications', newCount > 0 );
            } );
        } ).fail( function ( code, data ) {
            mw.log.error( 'Failed to mark notification as read:', code, data );
            showError( 'notifications', mw.msg( 'islamdashboard-error-marking-read' ) );
        } );
    }

    /**
     * Set up dashboard widgets
     */
    function setupWidgets() {
        // Make widgets sortable if the user has permission
        if ( mw.config.get( 'wgUserGroups' ).includes( 'sysop' ) ) {
            $( '.dashboard-widget' ).each( function () {
                const $widget = $( this );
                
                // Add edit controls
                $widget.prepend(
                    '<div class="widget-actions">' +
                    '  <a href="#" class="widget-edit-toggle" title="' + mw.msg( 'islamdashboard-edit-widget' ) + '">' +
                    '    ' + new OO.ui.IconWidget( { icon: 'settings' } ).$element.outerHTML() +
                    '  </a>' +
                    '  <a href="#" class="widget-remove" title="' + mw.msg( 'islamdashboard-remove-widget' ) + '">' +
                    '    ' + new OO.ui.IconWidget( { icon: 'trash' } ).$element.outerHTML() +
                    '  </a>' +
                    '</div>'
                );
                
                // Make widget draggable
                $widget.draggable( {
                    handle: '.widget-title',
                    revert: 'invalid',
                    zIndex: 100,
                    cursor: 'move',
                    opacity: 0.8
                } );
            } );
            
            // Make widget containers droppable
            $( '.islam-dashboard-main, .islam-dashboard-right-sidebar' ).droppable( {
                accept: '.dashboard-widget',
                hoverClass: 'drop-hover',
                drop: function( event, ui ) {
                    const $widget = ui.draggable;
                    const $target = $( this );
                    
                    // Don't do anything if dropped in the same container
                    if ( $widget.parent().is( $target ) ) {
                        return;
                    }
                    
                    // Move widget to the new container
                    $widget.appendTo( $target );
                    
                    // Save the new layout
                    saveDashboardLayout();
                }
            } );
            
            // Make the main area sortable
            $( '.islam-dashboard-main' ).sortable( {
                items: '> .dashboard-widget',
                placeholder: 'widget-placeholder',
                forcePlaceholderSize: true,
                update: saveDashboardLayout
            } );
            
            // Make the right sidebar sortable
            $( '.islam-dashboard-right-sidebar' ).sortable( {
                items: '> .dashboard-widget',
                placeholder: 'widget-placeholder',
                forcePlaceholderSize: true,
                update: saveDashboardLayout,
                connectWith: '.islam-dashboard-main'
            } );
        }
    }
    
    /**
     * Save the current dashboard layout
     */
    function saveDashboardLayout() {
        const layout = {
            main: [],
            sidebar: []
        };
        
        // Get widget order from the main area
        $( '.islam-dashboard-main > .dashboard-widget' ).each( function () {
            layout.main.push( $( this ).data( 'widget-id' ) );
        } );
        
        // Get widget order from the sidebar
        $( '.islam-dashboard-right-sidebar > .dashboard-widget' ).each( function () {
            layout.sidebar.push( $( this ).data( 'widget-id' ) );
        } );
        
        // Save the layout via API
        const api = new mw.Api();
        
        api.postWithToken( 'csrf', {
            action: 'islamdashboard',
            subaction: 'savelayout',
            format: 'json',
            layout: JSON.stringify( layout )
        } ).done( function ( data ) {
            if ( data.islamdashboard && data.islamdashboard.result === 'success' ) {
                mw.notify( mw.msg( 'islamdashboard-layout-saved' ), { type: 'success' } );
            } else {
                throw new Error( data.error || 'Unknown error' );
            }
        } ).fail( function ( code, data ) {
            mw.log.error( 'Failed to save dashboard layout:', code, data );
            mw.notify( mw.msg( 'islamdashboard-error-saving-layout' ), { type: 'error' } );
        } );
    }
    
    /**
     * Remove a widget from the dashboard
     * 
     * @param {jQuery} $widget Widget element to remove
     */
    function removeWidget( $widget ) {
        const widgetId = $widget.data( 'widget-id' );
        
        // Remove the widget with animation
        $widget.fadeOut( 300, function () {
            $( this ).remove();
            
            // Save the updated layout
            saveDashboardLayout();
        } );
        
        // Update user preferences to hide this widget
        const api = new mw.Api();
        
        api.postWithToken( 'csrf', {
            action: 'islamdashboard',
            subaction: 'hidewidget',
            format: 'json',
            widget: widgetId
        } ).fail( function ( code, data ) {
            mw.log.error( 'Failed to update widget visibility:', code, data );
            mw.notify( mw.msg( 'islamdashboard-error-updating-widget' ), { type: 'error' } );
        } );
    }
    
    /**
     * Show an error message in a widget
     * 
     * @param {string} widgetId ID of the widget to show the error in
     * @param {string} message Error message to display
     */
    function showError( widgetId, message ) {
        const $widget = $( '#' + widgetId );
        
        if ( $widget.length ) {
            $widget.find( '.widget-content' ).html(
                '<div class="error-message">' +
                '  <span class="error-icon">' +
                        new OO.ui.IconWidget( { icon: 'alert' } ).$element.outerHTML() +
                '  </span>' +
                '  <p>' + mw.html.escape( message ) + '</p>' +
                '</div>'
            );
        } else {
            mw.notify( message, { type: 'error' } );
        }
    }
    
    // Initialize the dashboard
    function init() {
        // Only initialize on the dashboard page
        if ( mw.config.get( 'wgCanonicalSpecialPageName' ) === 'Dashboard' ) {
            initDashboard();
        }
    }

    /**
     * Format a timestamp into a human-readable date string
     * 
     * @param {string} timestamp MediaWiki timestamp (e.g., '20230101000000')
     * @return {string} Formatted date string
     */
    function formatTimestamp(timestamp) {
        if (!timestamp) return '';
        
        // Extract date components from MediaWiki timestamp (YYYYMMDDHHmmss)
        const year = timestamp.substr(0, 4);
        const month = parseInt(timestamp.substr(4, 2)) - 1; // JS months are 0-indexed
        const day = timestamp.substr(6, 2);
        const hour = timestamp.substr(8, 2);
        const minute = timestamp.substr(10, 2);
        
        const date = new Date(year, month, day, hour, minute);
        
        // Format as "MMM D, YYYY [at] h:mm a" (e.g., "Jan 1, 2023 at 12:00 PM")
        return date.toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    }

    // Initialize when the document is ready and dependencies are loaded
    mw.loader.using( ['jquery', 'mediawiki.api', 'mediawiki.jqueryMsg'] ).then( function() {
        // Expose init function to global scope
        window.IslamDashboard.initDashboard = initDashboard;
        
        // Auto-initialize if we're on the dashboard
        if (mw.config.get('wgCanonicalSpecialPageName') === 'Dashboard') {
            initDashboard();
        }
    }, function(error) {
        mw.log.error('IslamDashboard: Failed to load dependencies', error);
    });

    // Make init function available globally
    mw.islamDashboard = {
        init: init,
        Navigation: mw.islamDashboard ? mw.islamDashboard.Navigation : null
    };

    // Make functions available for debugging
    window.IslamDashboard = {
        initDashboard: initDashboard,
        loadUserData: loadUserData,
        loadRecentActivity: loadRecentActivity,
        loadNotifications: loadNotifications,
        saveDashboardLayout: saveDashboardLayout
    };

}( jQuery, mediaWiki ) );
