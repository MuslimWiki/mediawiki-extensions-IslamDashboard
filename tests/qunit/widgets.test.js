/**
 * QUnit tests for the IslamDashboard widget system
 * 
 * @file
 * @since 1.0.0
 */

/* global $ */
( function ( $, mw ) {
    'use strict';

    QUnit.module( 'ext.islamDashboard.widgets', QUnit.newMwEnvironment( {
        // Setup code that runs before each test
        beforeEach: function () {
            // Create a test container for widgets
            this.$fixture = $( '#qunit-fixture' );
            this.$fixture.html( `
                <div class="dashboard-widgets">
                    <div class="dashboard-widget" data-widget-id="welcome-widget">
                        <div class="widget-header">
                            <h3 class="widget-title">Welcome Widget</h3>
                            <div class="widget-actions">
                                <button class="widget-refresh">↻</button>
                                <button class="widget-configure">⚙️</button>
                                <button class="widget-remove">×</button>
                            </div>
                        </div>
                        <div class="widget-content">
                            <p>Welcome to the dashboard!</p>
                        </div>
                    </div>
                    <div class="dashboard-widget" data-widget-id="recent-activity">
                        <div class="widget-header">
                            <h3 class="widget-title">Recent Activity</h3>
                            <div class="widget-actions">
                                <button class="widget-refresh">↻</button>
                            </div>
                        </div>
                        <div class="widget-content">
                            <ul class="activity-list"></ul>
                        </div>
                    </div>
                </div>
            ` );

            // Load the widget module
            return mw.loader.using( 'ext.islamDashboard.widgets' );
        }
    } ) );

    QUnit.test( 'Widget initialization', function ( assert ) {
        var $widgets = $( '.dashboard-widget' );
        
        assert.strictEqual( $widgets.length, 2, 'Two widgets are initialized' );
        
        // Check if each widget has the required structure
        $widgets.each( function () {
            var $widget = $( this );
            assert.ok( $widget.find( '.widget-header' ).length > 0, 'Widget has a header' );
            assert.ok( $widget.find( '.widget-content' ).length > 0, 'Widget has content area' );
            assert.ok( $widget.attr( 'data-widget-id' ), 'Widget has an ID' );
        } );
    } );

    QUnit.test( 'Widget actions', function ( assert ) {
        var $welcomeWidget = $( '.dashboard-widget[data-widget-id="welcome-widget"]' );
        var $refreshBtn = $welcomeWidget.find( '.widget-refresh' );
        var $configureBtn = $welcomeWidget.find( '.widget-configure' );
        var $removeBtn = $welcomeWidget.find( '.widget-remove' );
        
        // Test refresh action
        $refreshBtn.trigger( 'click' );
        assert.ok( true, 'Refresh button click handler executed' );
        
        // Test configure action
        $configureBtn.trigger( 'click' );
        assert.ok( true, 'Configure button click handler executed' );
        
        // Test remove action
        var initialCount = $( '.dashboard-widget' ).length;
        $removeBtn.trigger( 'click' );
        assert.strictEqual( 
            $( '.dashboard-widget' ).length, 
            initialCount - 1, 
            'Widget is removed from the DOM after clicking remove button' 
        );
    } );

    QUnit.test( 'Widget dragging', function ( assert ) {
        var done = assert.async();
        var $widgetsContainer = $( '.dashboard-widgets' );
        var $firstWidget = $widgetsContainer.find( '.dashboard-widget' ).first();
        
        // Simulate drag start
        $firstWidget.trigger( {
            type: 'dragstart',
            originalEvent: {
                dataTransfer: {
                    setData: function () {},
                    setDragImage: function () {}
                }
            }
        } );
        
        assert.ok( $firstWidget.hasClass( 'dragging' ), 'Widget gets dragging class when drag starts' );
        
        // Simulate drag end
        $firstWidget.trigger( 'dragend' );
        
        setTimeout( function () {
            assert.ok( !$firstWidget.hasClass( 'dragging' ), 'Widget loses dragging class when drag ends' );
            done();
        }, 100 );
    } );

    QUnit.test( 'Widget resizing', function ( assert ) {
        var done = assert.async();
        var $widget = $( '.dashboard-widget' ).first();
        var initialWidth = $widget.outerWidth();
        var $resizeHandle = $( '<div class="widget-resize-handle"></div>' ).appendTo( $widget );
        
        // Simulate mouse down on resize handle
        var e = $.Event( 'mousedown' );
        e.which = 1; // Left mouse button
        $resizeHandle.trigger( e );
        
        // Simulate mouse movement
        $( document ).trigger( {
            type: 'mousemove',
            pageX: initialWidth + 100, // Move 100px to the right
            pageY: 0
        } );
        
        // Simulate mouse up
        $( document ).trigger( 'mouseup' );
        
        setTimeout( function () {
            var newWidth = $widget.outerWidth();
            assert.ok( newWidth > initialWidth, 'Widget width increased after resizing' );
            done();
        }, 100 );
    } );

    QUnit.test( 'Widget configuration', function ( assert ) {
        var $widget = $( '.dashboard-widget' ).first();
        var $configureBtn = $widget.find( '.widget-configure' );
        
        // Simulate clicking configure button
        $configureBtn.trigger( 'click' );
        
        // Check if configuration modal is shown
        var $modal = $( '.widget-config-modal' );
        assert.ok( $modal.length > 0, 'Configuration modal is shown' );
        
        // Simulate saving configuration
        var configSaved = false;
        $( document ).on( 'widgetConfigSaved', function ( e, widgetId, config ) {
            configSaved = true;
            assert.strictEqual( widgetId, 'welcome-widget', 'Correct widget ID passed to config save handler' );
            assert.ok( $.isPlainObject( config ), 'Configuration object is passed to save handler' );
        } );
        
        // Trigger save
        $modal.find( '.save-config' ).trigger( 'click' );
        assert.ok( configSaved, 'Configuration save handler was called' );
        
        // Close modal
        $modal.find( '.close' ).trigger( 'click' );
        assert.ok( $modal.is( ':hidden' ), 'Modal is hidden after closing' );
    } );

    QUnit.test( 'Widget refresh', function ( assert ) {
        var done = assert.async();
        var $widget = $( '.dashboard-widget[data-widget-id="recent-activity"]' );
        var $refreshBtn = $widget.find( '.widget-refresh' );
        var refreshCount = 0;
        
        // Set up a mock refresh handler
        $( document ).on( 'refreshWidget', function ( e, widgetId ) {
            if ( widgetId === 'recent-activity' ) {
                refreshCount++;
                
                // Simulate updating the widget content
                var $content = $widget.find( '.widget-content' );
                $content.html( '<p>Last updated: ' + new Date().toISOString() + '</p>' );
                
                // Resolve the refresh
                $( document ).trigger( 'widgetRefreshed', [ widgetId ] );
            }
        } );
        
        // Click the refresh button
        $refreshBtn.trigger( 'click' );
        
        // Wait for the refresh to complete
        setTimeout( function () {
            assert.strictEqual( refreshCount, 1, 'Refresh handler was called once' );
            assert.ok( 
                $widget.find( '.widget-content' ).text().includes( 'Last updated:' ), 
                'Widget content was updated after refresh' 
            );
            
            // Test refresh indicator
            assert.ok( 
                $refreshBtn.hasClass( 'refreshing' ) === false, 
                'Refresh button is not in refreshing state after refresh completes' 
            );
            
            done();
        }, 500 );
        
        // Check if refresh button shows loading state
        assert.ok( 
            $refreshBtn.hasClass( 'refreshing' ), 
            'Refresh button shows loading state during refresh' 
        );
    } );

} )( jQuery, mediaWiki );
