<?php
/**
 * Special page for the IslamDashboard extension
 *
 * @file
 * @ingroup Extensions
 * @license GPL-3.0-or-later
 */

namespace MediaWiki\Extension\IslamDashboard;

use Html;
use MediaWiki\MediaWikiServices;
use MediaWiki\Extension\IslamDashboard\Navigation\NavigationManager;
use MediaWiki\Extension\IslamDashboard\Navigation\NavigationRenderer;
use SpecialPage;
use User;

class SpecialDashboard extends SpecialPage {

    /**
     * Initialize the special page
     */
    public function __construct() {
        parent::__construct( 'Dashboard' );
    }

    /**
     * Show the page to the user
     *
     * @param string|null $subPage The subpage string argument (if any)
     */
    public function execute( $subPage ) {
        $out = $this->getOutput();
        $user = $this->getUser();

        // Set page title and other properties
        $this->setHeaders();
        $out->setPageTitle( $this->msg( 'islamdashboard-dashboard' )->text() );

        // Check if user has permission to view the dashboard
        if ( !$this->userCanExecute( $user ) ) {
            $this->displayRestrictionError();
            return;
        }

        // Add CSS and JS resources
        $out->addModules( [ 'ext.islamDashboard', 'ext.islamDashboard.navigation' ] );
        $out->addModuleStyles( [ 'ext.islamDashboard.styles', 'ext.islamDashboard.navigation.styles' ] );

        // Get widget manager
        $widgetManager = WidgetManager::getInstance();
        
        // Get widgets for the current user
        $widgets = $widgetManager->getWidgets( $user );
        $layout = $widgetManager->getUserWidgetLayout( $user );

        // Get navigation manager and renderer
        $navManager = NavigationManager::getInstance();
        $navRenderer = new NavigationRenderer( $navManager );
        
        // Get current path for active state
        $currentPath = $this->getRequest()->getRequestURL();
        
        // Start building the dashboard HTML
        $html = Html::openElement( 'div', [ 'class' => 'islam-dashboard' ] );
        
        // Add header
        $html .= $this->getDashboardHeader( $user );
        
        // Add main content wrapper
        $html .= Html::openElement( 'div', [ 'class' => 'dashboard-wrapper' ] );
        
        // Add navigation sidebar
        $html .= $this->getNavigationSidebar( $navRenderer, $currentPath );
        
        // Add main content area
        $html .= Html::openElement( 'div', [ 'class' => 'dashboard-container' ] );
        
        // Add main content area
        $html .= Html::openElement( 'div', [ 'class' => 'dashboard-main' ] );
        $html .= $this->renderWidgets( $widgets, $layout, 'main' );
        $html .= Html::closeElement( 'div' );
        
        // Add right sidebar for widgets
        $html .= Html::openElement( 'div', [ 'class' => 'dashboard-sidebar' ] );
        $html .= $this->renderWidgets( $widgets, $layout, 'sidebar' );
        $html .= Html::closeElement( 'div' );
        
        // Close dashboard container and wrapper
        $html .= Html::closeElement( 'div' ); // .dashboard-container
        $html .= Html::closeElement( 'div' ); // .dashboard-wrapper
        
        // Add edit mode toggle
        if ( $user->isAllowed( 'editdashboard' ) ) {
            $html .= $this->getEditModeControls();
        }
        
        // Close dashboard div
        $html .= Html::closeElement( 'div' );
        
        // Add the dashboard to the output
        $out->addHTML( $html );
        
        // Add configuration data for JavaScript
        $this->addJsConfigVars( $widgets, $layout );
    }
    
    /**
     * Get the navigation sidebar HTML
     *
     * @param NavigationRenderer $navRenderer Navigation renderer instance
     * @param string $currentPath Current request path
     * @return string HTML for the navigation sidebar
     */
    public function getNavigationSidebar( NavigationRenderer $navRenderer, string $currentPath ): string {
        $html = Html::openElement( 'div', [ 'class' => 'dashboard-navigation-container' ] );
        
        // Add mobile menu toggle button
        $html .= Html::element( 'button', [
            'class' => 'dashboard-mobile-menu-toggle',
            'aria-label' => $this->msg( 'islamdashboard-mobile-menu-toggle' )->text(),
            'title' => $this->msg( 'islamdashboard-mobile-menu-toggle' )->text()
        ], '☰' );
        
        // Add navigation
        $html .= $navRenderer->getNavigationHTML( [
            'currentPath' => $currentPath,
            'user' => $this->getUser()
        ] );
        
        $html .= Html::closeElement( 'div' );
        
        return $html;
    }
    
    /**
     * Get the dashboard header HTML
     *
     * @param User $user Current user
     * @return string HTML for the dashboard header
     */
    public function getDashboardHeader( User $user ) {
        $widgetManager = WidgetManager::getInstance();
        $userName = $user->getName();
        $userPage = $user->getUserPage();
        $userPageLink = $this->getLinkRenderer()->makeLink( $userPage, $userName );
        
        // Get user groups with proper localization
        $userGroupNames = $widgetManager->getUserGroupNames( $user );
        
        // Format user groups as tags
        $groupTags = '';
        foreach ( $userGroupNames as $groupName ) {
            $groupTags .= Html::element( 'span', [ 'class' => 'group-tag' ], $groupName );
        }
        
        $welcomeMessage = $this->msg( 'islamdashboard-welcome', [ $userPageLink ] )->parse();
        
        // Get last login time using UserOptionsLookup service
        $userOptionsLookup = MediaWikiServices::getInstance()->getUserOptionsLookup();
        $lastSeen = $userOptionsLookup->getOption( $user, 'last_login' );
        
        // If last_login is not set, use the user's registration date as fallback
        if ( !$lastSeen ) {
            $lastSeen = $user->getRegistration();
        }
        
        $lastSeenText = $lastSeen ? 
            $this->msg( 'islamdashboard-lastseen' )->params( 
                $this->getLanguage()->userTimeAndDate( $lastSeen, $user ),
                $this->getLanguage()->userDate( $lastSeen, $user ),
                $this->getLanguage()->userTime( $lastSeen, $user )
            )->text() :
            $this->msg( 'islamdashboard-neverseen' )->text();
        
        // Build the header HTML
        $html = Html::openElement( 'div', [ 'class' => 'dashboard-header' ] );
        $html .= Html::element( 'h1', [], $this->msg( 'islamdashboard-dashboard' )->text() );
        
        // User info section
        $html .= Html::openElement( 'div', [ 'class' => 'user-info' ] );
        
        // Welcome message
        $html .= Html::rawElement( 
            'div', 
            [ 'class' => 'welcome-message' ], 
            $this->msg( 'islamdashboard-welcome-back' )->rawParams( $userPageLink )->parse()
        );
        
        // User meta (last seen)
        $html .= Html::element( 'div', [ 'class' => 'user-meta' ], $lastSeenText );
        
        // User groups
        if ( !empty( $groupTags ) ) {
            $html .= Html::openElement( 'div', [ 'class' => 'user-groups' ] );
            $html .= $groupTags;
            $html .= Html::closeElement( 'div' );
        }
        
        $html .= Html::closeElement( 'div' ); // Close user-info
        $html .= Html::closeElement( 'div' ); // Close dashboard-header
        
        return $html;
    }
    
    /**
     * Render widgets for a specific section
     *
     * @param array $widgets Array of widget instances
     * @param array $layout User's widget layout
     * @param string $section Section to render (main or sidebar)
     * @return string HTML for the widgets in the section
     */
    private function renderWidgets( $widgets, $layout, $section ) {
        $html = '';
        $sectionWidgets = $layout[$section] ?? [];
        
        foreach ( $sectionWidgets as $widgetId ) {
            if ( isset( $widgets[$widgetId] ) ) {
                $widget = $widgets[$widgetId];
                $html .= $this->renderWidget( $widget );
            }
        }
        
        return $html;
    }
    
    /**
     * Render a single widget
     *
     * @param Widgets\DashboardWidget $widget Widget instance
     * @return string HTML for the widget
     */
    private function renderWidget( $widget ) {
        $widgetId = $widget->getId();
        $title = $widget->getTitle();
        $content = $widget->render();
        $classes = [ 'dashboard-widget', 'widget-' . $widgetId ];
        $canEdit = $this->getUser()->isAllowed( 'editdashboard' );
        
        if ( $canEdit ) {
            $classes[] = 'editable';
        }
        
        $html = Html::openElement( 'div', [
            'class' => implode( ' ', $classes ),
            'data-widget-id' => $widgetId
        ] );
        
        // Widget header
        $html .= Html::openElement( 'div', [ 'class' => 'widget-header' ] );
        $html .= Html::element( 'h3', [ 'class' => 'widget-title' ], $title );
        
        if ( $canEdit ) {
            $html .= Html::openElement( 'div', [ 'class' => 'widget-actions' ] );
            $html .= Html::element( 'button', [
                'class' => 'widget-edit',
                'title' => $this->msg( 'islamdashboard-edit-widget' )->text(),
                'aria-label' => $this->msg( 'islamdashboard-edit-widget' )->text()
            ], '✎' );
            $html .= Html::element( 'button', [
                'class' => 'widget-remove',
                'title' => $this->msg( 'islamdashboard-remove-widget' )->text(),
                'aria-label' => $this->msg( 'islamdashboard-remove-widget' )->text()
            ], '×' );
            $html .= Html::closeElement( 'div' );
        }
        
        $html .= Html::closeElement( 'div' );
        
        // Widget content
        $html .= Html::rawElement( 
            'div', 
            [ 'class' => 'widget-content' ],
            $content
        );
        
        $html .= Html::closeElement( 'div' );
        
        return $html;
    }
    
    /**
     * Get edit mode controls HTML
     *
     * @return string HTML for the edit mode controls
     */
    private function getEditModeControls() {
        $html = Html::openElement( 'div', [ 'class' => 'dashboard-edit-controls' ] );
        $html .= Html::element( 
            'button', 
            [ 
                'class' => 'edit-mode-toggle',
                'id' => 'toggleEditMode'
            ],
            $this->msg( 'islamdashboard-edit-layout' )->text()
        );
        
        $html .= Html::element( 
            'button', 
            [ 
                'class' => 'reset-layout',
                'id' => 'resetLayout',
                'style' => 'display: none;'
            ],
            $this->msg( 'islamdashboard-reset-layout' )->text()
        );
        
        $html .= Html::element( 
            'button', 
            [ 
                'class' => 'save-layout',
                'id' => 'saveLayout',
                'style' => 'display: none;'
            ],
            $this->msg( 'islamdashboard-save-layout' )->text()
        );
        
        $html .= Html::closeElement( 'div' );
        
        return $html;
    }
    
    /**
     * Add JavaScript configuration variables
     *
     * @param array $widgets Array of widget instances
     * @param array $layout User's widget layout
     */
    private function addJsConfigVars( $widgets, $layout ) {
        $configVars = [
            'wgIslamDashboardWidgets' => [],
            'wgIslamDashboardLayout' => $layout,
            'wgIslamDashboardEditMode' => $this->getRequest()->getBool( 'edit' )
        ];
        
        foreach ( $widgets as $widgetId => $widget ) {
            $configVars['wgIslamDashboardWidgets'][$widgetId] = [
                'id' => $widget->getId(),
                'title' => $widget->getTitle(),
                'description' => $widget->getDescription(),
                'section' => $widget->getSection(),
                'config' => $widget->getClientConfig()
            ];
        }
        
        $this->getOutput()->addJsConfigVars( $configVars );
    }
    
    /**
     * Check if the user can execute this special page
     *
     * @param User $user The user to check
     * @return bool
     */
    public function userCanExecute( User $user ) {
        return $user->isRegistered() && parent::userCanExecute( $user );
    }
    
    /**
     * Get the group name for this special page
     *
     * @return string
     */
    protected function getGroupName() {
        return 'users';
    }
}
