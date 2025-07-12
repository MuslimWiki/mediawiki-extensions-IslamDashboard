/**
 * QUnit tests for the IslamDashboard navigation module
 *
 * @file
 * @since 1.0.0
 */

/* global $ */
( function ( $, mw ) {
    'use strict';

    QUnit.module( 'ext.islamDashboard.navigation', QUnit.newMwEnvironment( {
        // Setup code that runs before each test
        beforeEach: function () {
            // Create a test navigation structure
            this.$fixture = $( '#qunit-fixture' );
            this.$fixture.html( `
                <nav class="dashboard-navigation">
                    <div class="dashboard-navigation-header">
                        <button class="dashboard-navigation-toggle">Toggle</button>
                    </div>
                    <div class="dashboard-navigation-sections">
                        <div class="dashboard-navigation-section" data-section-id="test-section">
                            <div class="dashboard-navigation-section-header">
                                <span class="dashboard-navigation-section-icon">📊</span>
                                <span class="dashboard-navigation-section-label">Test Section</span>
                                <span class="dashboard-navigation-section-toggle">▼</span>
                            </div>
                            <div class="dashboard-navigation-section-items">
                                <a href="/wiki/Test1" class="dashboard-navigation-item" data-item-id="test1">
                                    <span class="dashboard-navigation-item-icon">📝</span>
                                    <span class="dashboard-navigation-item-label">Test Item 1</span>
                                </a>
                                <a href="/wiki/Test2" class="dashboard-navigation-item" data-item-id="test2">
                                    <span class="dashboard-navigation-item-icon">📊</span>
                                    <span class="dashboard-navigation-item-label">Test Item 2</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </nav>
            ` );

            // Load the navigation module
            return mw.loader.using( 'ext.islamDashboard.navigation' );
        }
    } ) );

    QUnit.test( 'Navigation initialization', function ( assert ) {
        var $nav = $( '.dashboard-navigation' );
        var $section = $( '.dashboard-navigation-section' );
        
        assert.ok( $nav.length > 0, 'Navigation element exists' );
        assert.ok( $section.length > 0, 'Navigation section exists' );
        
        // Check if the section is expanded by default
        assert.strictEqual( 
            $section.hasClass( 'expanded' ), 
            true, 
            'Navigation section is expanded by default' 
        );
    } );

    QUnit.test( 'Section toggling', function ( assert ) {
        var $section = $( '.dashboard-navigation-section' );
        var $sectionHeader = $section.find( '.dashboard-navigation-section-header' );
        var done = assert.async();
        
        // Click the section header to collapse it
        $sectionHeader.trigger( 'click' );
        
        // Wait for the animation to complete
        setTimeout( function () {
            assert.strictEqual( 
                $section.hasClass( 'collapsed' ), 
                true, 
                'Section is collapsed after clicking the header' 
            );
            
            // Click again to expand
            $sectionHeader.trigger( 'click' );
            
            setTimeout( function () {
                assert.strictEqual( 
                    $section.hasClass( 'expanded' ), 
                    true, 
                    'Section is expanded after clicking the header again' 
                );
                done();
            }, 300 );
        }, 300 );
    } );

    QUnit.test( 'Mobile menu toggle', function ( assert ) {
        var $nav = $( '.dashboard-navigation' );
        var $toggle = $( '.dashboard-navigation-toggle' );
        
        // Simulate mobile view
        $nav.addClass( 'mobile' );
        
        // Check if the menu is hidden by default on mobile
        assert.strictEqual( 
            $nav.hasClass( 'menu-visible' ), 
            false, 
            'Mobile menu is hidden by default' 
        );
        
        // Click the toggle button
        $toggle.trigger( 'click' );
        
        // Check if the menu is visible after clicking the toggle
        assert.strictEqual( 
            $nav.hasClass( 'menu-visible' ), 
            true, 
            'Mobile menu is visible after clicking the toggle' 
        );
        
        // Click the toggle button again
        $toggle.trigger( 'click' );
        
        // Check if the menu is hidden again
        assert.strictEqual( 
            $nav.hasClass( 'menu-visible' ), 
            false, 
            'Mobile menu is hidden after clicking the toggle again' 
        );
    } );

    QUnit.test( 'Active item highlighting', function ( assert ) {
        var $items = $( '.dashboard-navigation-item' );
        var $firstItem = $items.eq( 0 );
        
        // Simulate clicking on the first item
        $firstItem.trigger( 'click' );
        
        // Check if the clicked item has the active class
        assert.strictEqual( 
            $firstItem.hasClass( 'active' ), 
            true, 
            'Clicked item has the active class' 
        );
        
        // Check if other items don't have the active class
        assert.strictEqual( 
            $items.not( $firstItem ).filter( '.active' ).length, 
            0, 
            'Other items do not have the active class' 
        );
    } );

    QUnit.test( 'Keyboard navigation', function ( assert ) {
        var $items = $( '.dashboard-navigation-item' );
        var $firstItem = $items.eq( 0 );
        var $secondItem = $items.eq( 1 );
        
        // Focus the first item
        $firstItem.trigger( 'focus' );
        
        // Simulate pressing the down arrow key
        var e = $.Event( 'keydown' );
        e.which = 40; // Down arrow key
        $firstItem.trigger( e );
        
        // Check if the second item is now focused
        assert.strictEqual( 
            $secondItem.is( ':focus' ), 
            true, 
            'Second item is focused after pressing the down arrow key' 
        );
        
        // Simulate pressing the up arrow key
        e = $.Event( 'keydown' );
        e.which = 38; // Up arrow key
        $secondItem.trigger( e );
        
        // Check if the first item is focused again
        assert.strictEqual( 
            $firstItem.is( ':focus' ), 
            true, 
            'First item is focused after pressing the up arrow key' 
        );
    } );

} )( jQuery, mediaWiki );
