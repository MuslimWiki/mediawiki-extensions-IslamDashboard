<?php
/**
 * Widget manager for IslamDashboard
 *
 * @file
 * @ingroup Extensions
 * @license GPL-3.0-or-later
 */

namespace MediaWiki\Extension\IslamDashboard;

use MediaWiki\MediaWikiServices;
use MediaWiki\Extension\IslamDashboard\Widgets\DashboardWidget;
use MediaWiki\Extension\IslamDashboard\Widgets\WelcomeWidget;
use MediaWiki\Extension\IslamDashboard\Widgets\RecentActivityWidget;
use MediaWiki\Extension\IslamDashboard\Widgets\QuickActionsWidget;

/**
 * Manages dashboard widgets
 */
class WidgetManager {
    /** @var array Registered widgets */
    private $widgets = [];
    
    /** @var bool Whether widgets have been initialized */
    private $initialized = false;
    
    /** @var self Singleton instance */
    private static $instance;
    
    /**
     * Get the singleton instance
     * 
     * @return self
     */
    public static function getInstance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        // Private to enforce singleton
    }
    
    /**
     * Initialize the widget manager
     */
    private function initialize() {
        if ( $this->initialized ) {
            return;
        }
        
        // Register core widgets
        $this->registerCoreWidgets();
        
        // Allow extensions to register their own widgets
        $this->initialized = true;
    }
    
    /**
     * Register core widgets
     */
    private function registerCoreWidgets() {
        $this->registerWidget( new Widgets\WelcomeWidget() );
        $this->registerWidget( new Widgets\RecentActivityWidget() );
        $this->registerWidget( new Widgets\QuickActionsWidget() );
    }
    
    /**
     * Register a widget
     * 
     * @param DashboardWidget $widget Widget to register
     * @throws \InvalidArgumentException If widget ID is already registered
     */
    public function registerWidget( DashboardWidget $widget ) {
        $widgetId = $widget->getId();
        
        if ( isset( $this->widgets[$widgetId] ) ) {
            throw new \InvalidArgumentException( "Widget with ID '{$widgetId}' is already registered" );
        }
        
        $this->widgets[$widgetId] = $widget;
    }
    
    /**
     * Get a widget by ID
     * 
     * @param string $widgetId Widget ID
     * @return DashboardWidget|null Widget instance or null if not found
     */
    public function getWidget( $widgetId ) {
        $this->initialize();
        return $this->widgets[$widgetId] ?? null;
    }
    
    /**
     * Get all registered widgets
     * 
     * @param \User|null $user User to filter widgets for (optional)
     * @return DashboardWidget[] Array of widget instances
     */
    public function getWidgets( \User $user = null ) {
        $this->initialize();
        
        // If no user is provided, return all widgets
        if ( $user === null ) {
            return $this->widgets;
        }
        
        // Otherwise, filter by user permissions
        return array_filter( 
            $this->widgets,
            function( $widget ) use ( $user ) {
                return $widget->isVisibleTo( $user );
            }
        );
    }
    
    /**
     * Get widget definitions for the client-side
     * 
     * @param \User $user User to get widgets for
     * @return array Array of widget definitions
     */
    public function getWidgetDefinitions( \User $user ) {
        $widgets = $this->getWidgets( $user );
        $definitions = [];
        
        foreach ( $widgets as $widget ) {
            $definitions[$widget->getId()] = $widget->getDefinition();
        }
        
        return $definitions;
    }
    
    /**
     * Get the widget layout for a user
     * 
     * @param \User $user User to get layout for
     * @return array Layout configuration
     */
    public function getUserWidgetLayout( \User $user ) {
        $userOptionsLookup = MediaWikiServices::getInstance()->getUserOptionsLookup();
        $layoutJson = $userOptionsLookup->getOption( $user, 'islamdashboard-layout', '{}' );
        
        if ( !is_string( $layoutJson ) ) {
            // Handle invalid stored value
            wfDebugLog( 'IslamDashboard', 'Invalid layout data type: ' . gettype( $layoutJson ) );
            return $this->getDefaultLayout( $user );
        }
        
        $layout = json_decode( $layoutJson, true );
        
        // If JSON decode failed or layout is invalid, use default
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            wfDebugLog( 'IslamDashboard', 'JSON decode error: ' . json_last_error_msg() );
            return $this->getDefaultLayout( $user );
        }
        
        // Ensure layout has required sections
        if ( !is_array( $layout ) || !isset( $layout['main'] ) || !isset( $layout['sidebar'] ) ) {
            wfDebugLog( 'IslamDashboard', 'Invalid layout structure' );
            return $this->getDefaultLayout( $user );
        }
        
        return $layout;
    }
    
    /**
     * Save the widget layout for a user
     * 
     * @param \User $user User to save layout for
     * @param array $layout Layout configuration
     * @return bool Whether the save was successful
     */
    /**
     * Save the widget layout for a user
     * 
     * @param \User $user User to save layout for
     * @param array $layout Layout configuration
    /**
     * Save the widget layout for a user
     * 
     * @param \User $user User to save layout for
     * @param array $layout Layout configuration
     * @return bool Whether the save was successful
     */
    public function saveUserWidgetLayout( \User $user, array $layout ): bool {
        // Convert layout to JSON for storage
        $json = json_encode( $layout );
        if ( $json === false ) {
            wfDebugLog( 'IslamDashboard', 'Failed to encode layout to JSON' );
            return false;
        }
        
        $userOptionsManager = MediaWikiServices::getInstance()->getUserOptionsManager();
        
        try {
            // Set the option
            $userOptionsManager->setOption( 
                $user, 
                'islamdashboard-layout', 
                $json
            );
            
            // Save the options
            $userOptionsManager->saveOptions( $user );
            return true;
        } catch ( \Exception $e ) {
            wfDebugLog( 'IslamDashboard', 'Failed to save user widget layout for ' . $user->getName() . ': ' . $e->getMessage() );
            return false;
        }
    }
    
    /**
     * Get the default widget layout
     * 
     * @param \User $user User to get default layout for
     * @return array Default layout configuration
     */
    public function getDefaultLayout( \User $user ) {
        $widgets = $this->getWidgets( $user );
        $layout = [
            'main' => [],
            'sidebar' => []
        ];
        
        // Sort widgets into their default sections
        foreach ( $widgets as $widget ) {
            $section = $widget->getDefaultSection();
            if ( isset( $layout[$section] ) ) {
                $layout[$section][] = $widget->getId();
            }
        }
        
        return $layout;
    }
    
    /**
     * Get widgets for a specific user with layout information
     * 
     * @param \User $user User to get widgets for
     * @return DashboardWidget[] Array of widget instances keyed by widget ID
     */
    public function getWidgetsForUser( \User $user ) {
        $widgets = $this->getWidgets( $user );
        $widgetsArray = [];
        
        foreach ( $widgets as $widget ) {
            $widgetsArray[$widget->getId()] = $widget;
        }
        
        return $widgetsArray;
    }
    
    /**
     * Get the layout for a specific user
     * 
     * @param \User $user User to get layout for
     * @return array Layout configuration
     */
    public function getUserLayout( \User $user ) {
        return $this->getUserWidgetLayout( $user );
    }
    
    /**
     * Get the user's group names
     * 
     * @param \User $user User to get groups for
     * @return string[] Array of group names
     */
    public function getUserGroupNames( \User $user ) {
        $userGroupManager = MediaWikiServices::getInstance()->getUserGroupManager();
        $groups = $userGroupManager->getUserEffectiveGroups( $user );
        $groupNames = [];
        
        foreach ( $groups as $group ) {
            if ( $group === '*' ) {
                continue;
            }
            $groupNames[] = $this->getGroupName( $group );
        }
        
        return $groupNames;
    }
    
    /**
     * Get the display name of a group
     * 
     * @param string $group Internal group name
     * @return string Display name of the group
     */
    private function getGroupName( $group ) {
        $msg = wfMessage( "group-$group" );
        return $msg->isDisabled() ? $group : $msg->text();
    }
    
    /**
     * Get hidden widgets for a user
     * 
     * @param \User $user User to get hidden widgets for
     * @return string[] Array of hidden widget IDs
     */
    public function getHiddenWidgets( \User $user ) {
        $userOptionsLookup = MediaWikiServices::getInstance()->getUserOptionsLookup();
        $hidden = $userOptionsLookup->getOption( $user, 'islamdashboard-hidden-widgets', '' );
        return $hidden ? explode( '|', $hidden ) : [];
    }
    
    /**
     * Set hidden widgets for a user
     * 
     * @param \User $user User to set hidden widgets for
     * @param string[] $hiddenWidgets Array of widget IDs to hide
     * @return bool Whether the save was successful
     */
    public function setHiddenWidgets( \User $user, array $hiddenWidgets ) {
        try {
            $userOptionsManager = MediaWikiServices::getInstance()->getUserOptionsManager();
            $userOptionsManager->setOption( 
                $user, 
                'islamdashboard-hidden-widgets', 
                implode( '|', $hiddenWidgets )
            );
            $userOptionsManager->saveOptions( $user );
            return true;
        } catch ( \Exception $e ) {
            wfDebugLog( 'IslamDashboard', 'Failed to save hidden widgets for ' . $user->getName() . ': ' . $e->getMessage() );
            return false;
        }
    }
    
    /**
     * Get all resource modules required by visible widgets
     * 
     * @param \User $user User to get modules for
     * @return string[] Array of module names
     */
    public function getRequiredModules( \User $user ) {
        $widgets = $this->getWidgets( $user );
        $modules = [];
        
        foreach ( $widgets as $widget ) {
            $modules = array_merge( $modules, $widget->getModules() );
        }
        
        return array_unique( $modules );
    }
}
