/**
 * Navigation module for IslamDashboard
 *
 * Handles the interactive behavior of the navigation menu, including:
 * - Collapsing/expanding sections
 * - Active state management
 * - Responsive behavior
 * - User preferences
 */
( function ( $, mw ) {
    'use strict';

    /**
     * Navigation module
     */
    function Navigation( config ) {
        this.config = $.extend( {
            // Default configuration
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
        }, config );

        this.$container = $( this.config.container );
        this.$body = $( document.body );
        this.isMobile = false;
        this.isCollapsed = false;
        this.sectionStates = {};
        this.initialized = false;

        // Initialize
        this.init();
    }

    /**
     * Initialize the navigation
     */
    Navigation.prototype.init = function () {
        if ( this.initialized ) {
            return;
        }

        this.checkMobile();
        this.loadState();
        this.setupEventListeners();
        this.setupResizeHandler();
        this.updateAriaAttributes();
        this.initialized = true;

        // Trigger event
        this.trigger( 'init' );
    };

    /**
     * Set up event listeners
     */
    Navigation.prototype.setupEventListeners = function () {
        var self = this;

        // Section headers
        this.$container.on( 'click', this.config.sectionHeader, function ( e ) {
            e.preventDefault();
            e.stopPropagation();
            self.toggleSection( $( this ).closest( '.dashboard-navigation-section' ) );
        } );

        // Navigation toggle (collapse/expand all)
        this.$container.on( 'click', this.config.toggleButton, function ( e ) {
            e.preventDefault();
            e.stopPropagation();
            self.toggleAllSections();
        } );

        // User menu toggle
        this.$container.on( 'click', this.config.userMenuToggle, function ( e ) {
            e.stopPropagation();
            $( this ).toggleClass( 'active' );
        } );

        // Close user menu when clicking outside
        $( document ).on( 'click', function ( e ) {
            if ( !$( e.target ).closest( self.config.userMenuToggle ).length ) {
                $( self.config.userMenuToggle ).removeClass( 'active' );
            }
        } );

        // Handle logout link
        this.$container.on( 'click', '.dashboard-logout-link', function ( e ) {
            e.preventDefault();
            // Use MediaWiki's logout
            window.location.href = mw.util.getUrl( 'Special:UserLogout' );
        } );

        // Handle AJAX navigation
        if ( this.config.ajaxLoad ) {
            this.$container.on( 'click', '.dashboard-navigation-link', this.handleAjaxNavigation.bind( this ) );
        }
    };

    /**
     * Handle AJAX navigation
     */
    Navigation.prototype.handleAjaxNavigation = function ( e ) {
        var $link = $( e.currentTarget ),
            url = $link.attr( 'href' );

        // Don't handle external links or links with special attributes
        if ( !url || url === '#' || $link.attr( 'target' ) === '_blank' || $link.attr( 'data-no-ajax' ) ) {
            return true;
        }

        e.preventDefault();

        // Show loading state
        this.trigger( 'beforeNavigation', { url: url, link: $link } );

        // For now, just follow the link
        // In a real implementation, this would use mw.loader.using() and load content via AJAX
        window.location.href = url;
    };

    /**
     * Toggle a section
     *
     * @param {jQuery} $section Section element
     */
    Navigation.prototype.toggleSection = function ( $section ) {
        var sectionId = $section.data( 'section' );
        
        if ( !sectionId ) {
            return;
        }

        var wasCollapsed = $section.hasClass( this.config.collapsedClass );
        
        // Update UI
        if ( wasCollapsed ) {
            $section.removeClass( this.config.collapsedClass );
            this.sectionStates[ sectionId ] = false;
        } else {
            $section.addClass( this.config.collapsedClass );
            this.sectionStates[ sectionId ] = true;
        }

        // Update ARIA attributes
        this.updateAriaAttributes( $section );

        // Save state
        this.saveState();

        // Trigger event
        this.trigger( 'sectionToggle', {
            section: $section,
            sectionId: sectionId,
            collapsed: !wasCollapsed
        } );
    };

    /**
     * Toggle all sections
     */
    Navigation.prototype.toggleAllSections = function () {
        var self = this,
            $sections = this.$container.find( '.dashboard-navigation-section' ),
            allCollapsed = !this.$container.hasClass( this.config.collapsedClass );

        // Toggle container class
        this.$container.toggleClass( this.config.collapsedClass, allCollapsed );
        this.isCollapsed = allCollapsed;

        // Toggle all sections
        $sections.each( function () {
            var $section = $( this ),
                sectionId = $section.data( 'section' );

            if ( sectionId ) {
                $section.toggleClass( self.config.collapsedClass, allCollapsed );
                self.sectionStates[ sectionId ] = allCollapsed;
                self.updateAriaAttributes( $section );
            }
        } );

        // Save state
        this.saveState();

        // Trigger event
        this.trigger( 'allSectionsToggle', {
            collapsed: allCollapsed
        } );
    };

    /**
     * Update ARIA attributes for a section
     *
     * @param {jQuery} $section Section element (optional, updates all if not provided)
     */
    Navigation.prototype.updateAriaAttributes = function ( $section ) {
        var self = this;

        if ( $section && $section.length ) {
            // Update single section
            var $header = $section.find( this.config.sectionHeader );
            var $content = $section.find( this.config.sectionContent );
            var isExpanded = !$section.hasClass( this.config.collapsedClass );

            $header.attr( 'aria-expanded', isExpanded );
            $content.attr( 'aria-hidden', !isExpanded );
        } else {
            // Update all sections
            $( this.config.sectionHeader ).each( function () {
                var $header = $( this ),
                    $section = $header.closest( '.dashboard-navigation-section' ),
                    $content = $section.find( self.config.sectionContent ),
                    isExpanded = !$section.hasClass( self.config.collapsedClass );

                $header.attr( 'aria-expanded', isExpanded );
                $content.attr( 'aria-hidden', !isExpanded );
            } );
        }
    };

    /**
     * Check if the viewport is mobile-sized
     */
    Navigation.prototype.checkMobile = function () {
        var wasMobile = this.isMobile;
        this.isMobile = window.innerWidth < this.config.mobileBreakpoint;

        if ( wasMobile !== this.isMobile ) {
            this.onViewportChange();
        }

        return this.isMobile;
    };

    /**
     * Handle viewport changes between mobile and desktop
     */
    Navigation.prototype.onViewportChange = function () {
        if ( this.isMobile ) {
            this.$container.addClass( 'is-mobile' );
            // Collapse all sections by default on mobile
            this.$container.find( '.dashboard-navigation-section' ).addClass( this.config.collapsedClass );
        } else {
            this.$container.removeClass( 'is-mobile' );
            // Restore previous state on desktop
            this.loadState();
        }

        this.updateAriaAttributes();
        this.trigger( 'viewportChange', { isMobile: this.isMobile } );
    };

    /**
     * Set up resize handler with debounce
     */
    Navigation.prototype.setupResizeHandler = function () {
        var self = this,
            resizeTimer;

        $( window ).on( 'resize', function () {
            clearTimeout( resizeTimer );
            resizeTimer = setTimeout( function () {
                self.checkMobile();
            }, 250 );
        } );
    };

    /**
     * Save navigation state to localStorage
     */
    Navigation.prototype.saveState = function () {
        if ( !mw.storage ) {
            return;
        }

        var state = {
            collapsed: this.isCollapsed,
            sections: this.sectionStates
        };

        try {
            mw.storage.set( this.config.storageKey, JSON.stringify( state ) );
        } catch ( e ) {
            // Storage may be disabled or full
            mw.log.warn( 'Failed to save navigation state:', e );
        }
    };

    /**
     * Load navigation state from localStorage
     */
    Navigation.prototype.loadState = function () {
        if ( !mw.storage ) {
            return;
        }

        try {
            var savedState = mw.storage.get( this.config.storageKey );
            if ( savedState ) {
                var state = JSON.parse( savedState );
                this.isCollapsed = !!state.collapsed;
                this.sectionStates = state.sections || {};
                this.applyState();
            }
        } catch ( e ) {
            mw.log.warn( 'Failed to load navigation state:', e );
        }
    };

    /**
     * Apply the loaded state to the UI
     */
    Navigation.prototype.applyState = function () {
        var self = this;

        // Apply collapsed state to container
        this.$container.toggleClass( this.config.collapsedClass, this.isCollapsed );

        // Apply section states
        $.each( this.sectionStates, function ( sectionId, isCollapsed ) {
            var $section = self.$container.find( '[data-section="' + sectionId + '"]' );
            if ( $section.length ) {
                $section.toggleClass( self.config.collapsedClass, isCollapsed );
            }
        } );

        // Update ARIA attributes
        this.updateAriaAttributes();
    };

    /**
     * Trigger an event
     *
     * @param {string} eventName Event name
     * @param {Object} data Event data
     */
    Navigation.prototype.trigger = function ( eventName, data ) {
        data = data || {};
        this.$container.trigger( 'navigation:' + eventName, [ this, data ] );
    };

    /**
     * Initialize the navigation when the document is ready
     */
    function init() {
        // Initialize navigation for each container
        $( '.dashboard-navigation' ).each( function () {
            // eslint-disable-next-line no-new
            new Navigation( {
                container: this
            } );
        } );
    }

    // Export to global scope
    mw.islamDashboard = mw.islamDashboard || {};
    mw.islamDashboard.Navigation = Navigation;
    mw.islamDashboard.Navigation.init = init;

    // Auto-initialize on document ready
    $( document ).ready( function() {
        mw.loader.using( ['jquery.ui'] ).done( function() {
            init();
        });
    } );

    // Make Navigation available for module export
    return {
        Navigation: Navigation,
        init: init
    };
}( jQuery, mediaWiki ) );

// Register for dynamic content
mw.hook( 'wikipage.content' ).add( function ( $content ) {
    if ( mw.islamDashboard && mw.islamDashboard.Navigation ) {
        $content.find( '.dashboard-navigation' ).each( function () {
            // eslint-disable-next-line no-new
            new mw.islamDashboard.Navigation( {
                container: this
            } );
        } );
    }
} );

// For module export
if ( typeof module !== 'undefined' ) {
    module.exports = mw.islamDashboard ? mw.islamDashboard.Navigation : {};
}
