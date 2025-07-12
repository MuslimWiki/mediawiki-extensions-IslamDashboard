<?php
/**
 * Special page for the IslamDashboard
 *
 * @file
 * @ingroup Extensions
 */

namespace MediaWiki\Extension\IslamDashboard;

use Html;
use MediaWiki\MediaWikiServices;
use SpecialPage;
use Title;

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
        $this->setHeaders();
        $out = $this->getOutput();
        $user = $this->getUser();
        
        // Check if user is logged in
        if ( !$user->isRegistered() ) {
            $out->addHTML( $this->msg( 'islamdashboard-mustbeloggedin' )->parse() );
            return;
        }
        
        // Add module styles and scripts
        $out->addModuleStyles( [ 'ext.islamDashboard', 'ext.islamDashboard.navigation' ] );
        $out->addModules( [ 'ext.islamDashboard', 'ext.islamDashboard.navigation' ] );
        
        // Set page title
        $out->setPageTitle( $this->msg( 'islamdashboard' )->text() );
        
        // Add the main dashboard container
        $out->addHTML( $this->getDashboardHTML() );
    }
    
    /**
     * Generate the dashboard HTML structure
     *
     * @return string HTML for the dashboard
     */
    private function getDashboardHTML() {
        $html = Html::openElement( 'div', [ 'class' => 'islam-dashboard-container' ] );
        
        // Header
        $html .= Html::rawElement( 'header', [ 'class' => 'islam-dashboard-header' ],
            Html::element( 'h1', [], $this->msg( 'islamdashboard-welcome' )->params( $this->getUser()->getName() )->text() )
        );
        
        // Main content area with grid layout
        $html .= Html::openElement( 'div', [ 'class' => 'islam-dashboard-grid' ] );
        
        // Left sidebar
        $html .= $this->getSidebarHTML();
        
        // Main content area
        $html .= Html::openElement( 'main', [ 'class' => 'islam-dashboard-main' ] );
        $html .= $this->getWidgetsHTML();
        $html .= Html::closeElement( 'main' );
        
        // Right sidebar (optional)
        $html .= $this->getRightSidebarHTML();
        
        $html .= Html::closeElement( 'div' ); // Close grid
        $html .= Html::closeElement( 'div' ); // Close container
        
        return $html;
    }
    
    /**
     * Generate the sidebar HTML
     *
     * @return string HTML for the sidebar
     */
    private function getSidebarHTML() {
        $html = Html::openElement( 'aside', [ 'class' => 'islam-dashboard-sidebar' ] );
        
        // User profile section
        $html .= $this->getUserProfileHTML();
        
        // Navigation menu
        $html .= $this->getNavigationMenuHTML();
        
        $html .= Html::closeElement( 'aside' );
        
        return $html;
    }
    
    /**
     * Generate the user profile section HTML
     *
     * @return string HTML for the user profile section
     */
    private function getUserProfileHTML() {
        $user = $this->getUser();
        $userPage = $user->getUserPage();
        
        $html = Html::openElement( 'div', [ 'class' => 'user-profile' ] );
        
        // Avatar
        $html .= Html::element( 'div', [ 'class' => 'user-avatar' ] );
        
        // User info
        $html .= Html::openElement( 'div', [ 'class' => 'user-info' ] );
        $html .= Html::element( 'h3', [], $user->getName() );
        $html .= Html::element( 'a', [ 
            'href' => $userPage->getFullURL(),
            'class' => 'user-page-link'
        ], $this->msg( 'islamdashboard-viewprofile' )->text() );
        $html .= Html::closeElement( 'div' );
        
        $html .= Html::closeElement( 'div' );
        
        return $html;
    }
    
    /**
     * Generate the navigation menu HTML
     *
     * @return string HTML for the navigation menu
     */
    private function getNavigationMenuHTML() {
        // Get the singleton instance of NavigationManager
        $navManager = \MediaWiki\Extension\IslamDashboard\Navigation\NavigationManager::getInstance();
        $navRenderer = new \MediaWiki\Extension\IslamDashboard\Navigation\NavigationRenderer($navManager);
        
        // Render the navigation using NavigationRenderer
        return $navRenderer->getNavigationHTML([
            'user' => $this->getUser(),
            'currentPath' => $this->getPageTitle()->getLocalURL(),
            'collapsed' => false,
            'mobile' => false
        ]);
    }
    
    /**
     * Generate the widgets HTML
     *
     * @return string HTML for the dashboard widgets
     */
    private function getWidgetsHTML() {
        $html = '';
        
        // Welcome widget
        $html .= $this->getWelcomeWidgetHTML();
        
        // Recent activity widget
        $html .= $this->getRecentActivityWidgetHTML();
        
        // Quick actions widget
        $html .= $this->getQuickActionsWidgetHTML();
        
        return $html;
    }
    
    /**
     * Generate the welcome widget HTML
     *
     * @return string HTML for the welcome widget
     */
    private function getWelcomeWidgetHTML() {
        $user = $this->getUser();
        $editCount = $user->getEditCount();
        $registration = $user->getRegistration();
        $memberSince = $this->getLanguage()->userDate( $registration, $user );
        
        $html = Html::openElement( 'div', [ 'class' => 'dashboard-widget welcome-widget' ] );
        $html .= Html::element( 'h2', [ 'class' => 'widget-title' ], $this->msg( 'islamdashboard-welcomeback' )->params( $user->getName() )->text() );
        
        $html .= Html::openElement( 'div', [ 'class' => 'widget-content' ] );
        $html .= Html::element( 'p', [], $this->msg( 'islamdashboard-member-since' )->params( $memberSince )->text() );
        $html .= Html::element( 'p', [], $this->msg( 'islamdashboard-edit-count' )->numParams( $editCount )->text() );
        
        // Quick stats
        $html .= Html::openElement( 'div', [ 'class' => 'quick-stats' ] );
        $html .= $this->getStatBox( 'islamdashboard-total-edits', $editCount );
        // Add more stat boxes as needed
        $html .= Html::closeElement( 'div' );
        
        $html .= Html::closeElement( 'div' ); // Close widget-content
        $html .= Html::closeElement( 'div' ); // Close widget
        
        return $html;
    }
    
    /**
     * Generate a stat box for the welcome widget
     *
     * @param string $message Message key for the stat label
     * @param mixed $value The stat value
     * @param string $icon Optional icon name
     * @return string HTML for the stat box
     */
    private function getStatBox( $message, $value, $icon = '' ) {
        $html = Html::openElement( 'div', [ 'class' => 'stat-box' ] );
        
        if ( $icon ) {
            $html .= Html::element( 'span', [ 'class' => 'stat-icon ' . $icon ] );
        }
        
        $html .= Html::element( 'div', [ 'class' => 'stat-value' ], $value );
        $html .= Html::element( 'div', [ 'class' => 'stat-label' ], $this->msg( $message )->text() );
        
        $html .= Html::closeElement( 'div' );
        return $html;
    }
    
    /**
     * Generate the recent activity widget HTML
     *
     * @return string HTML for the recent activity widget
     */
    private function getRecentActivityWidgetHTML() {
        // This would be populated with actual recent activity data
        $recentEdits = []; // Placeholder for actual data
        
        $html = Html::openElement( 'div', [ 'class' => 'dashboard-widget recent-activity' ] );
        $html .= Html::element( 'h2', [ 'class' => 'widget-title' ], $this->msg( 'islamdashboard-recent-activity' )->text() );
        
        $html .= Html::openElement( 'div', [ 'class' => 'widget-content' ] );
        
        if ( empty( $recentEdits ) ) {
            $html .= Html::element( 'p', [ 'class' => 'no-activity' ], 
                $this->msg( 'islamdashboard-no-recent-activity' )->text() );
        } else {
            $html .= Html::openElement( 'ul', [ 'class' => 'activity-list' ] );
            // Loop through recent edits and create list items
            foreach ( $recentEdits as $edit ) {
                $html .= $this->getActivityItemHTML( $edit );
            }
            $html .= Html::closeElement( 'ul' );
        }
        
        $html .= Html::closeElement( 'div' ); // Close widget-content
        $html .= Html::closeElement( 'div' ); // Close widget
        
        return $html;
    }
    
    /**
     * Generate HTML for a single activity item
     *
     * @param array $edit Edit data
     * @return string HTML for the activity item
     */
    private function getActivityItemHTML( $edit ) {
        // This would be implemented to format an individual activity item
        return '';
    }
    
    /**
     * Generate the quick actions widget HTML
     *
     * @return string HTML for the quick actions widget
     */
    private function getQuickActionsWidgetHTML() {
        $quickActions = [
            [
                'icon' => 'edit',
                'text' => 'islamdashboard-createpage',
                'href' => Title::newFromText( 'Special:CreatePage' )->getLocalURL(),
                'class' => 'create-page'
            ],
            [
                'icon' => 'upload',
                'text' => 'islamdashboard-uploadfile',
                'href' => Title::newFromText( 'Special:Upload' )->getLocalURL(),
                'class' => 'upload-file'
            ],
            [
                'icon' => 'settings',
                'text' => 'islamdashboard-preferences',
                'href' => Title::newFromText( 'Special:Preferences' )->getLocalURL(),
                'class' => 'preferences'
            ]
        ];
        
        $html = Html::openElement( 'div', [ 'class' => 'dashboard-widget quick-actions' ] );
        $html .= Html::element( 'h2', [ 'class' => 'widget-title' ], $this->msg( 'islamdashboard-quick-actions' )->text() );
        
        $html .= Html::openElement( 'div', [ 'class' => 'widget-content' ] );
        $html .= Html::openElement( 'div', [ 'class' => 'quick-actions-grid' ] );
        
        foreach ( $quickActions as $action ) {
            $html .= Html::openElement( 'a', [
                'href' => $action['href'],
                'class' => 'quick-action ' . $action['class'],
                'title' => $this->msg( $action['text'] )->text()
            ] );
            
            $html .= Html::element( 'span', [ 'class' => 'action-icon ' . $action['icon'] ] );
            $html .= Html::element( 'span', [ 'class' => 'action-text' ], $this->msg( $action['text'] )->text() );
            
            $html .= Html::closeElement( 'a' );
        }
        
        $html .= Html::closeElement( 'div' ); // Close grid
        $html .= Html::closeElement( 'div' ); // Close widget-content
        $html .= Html::closeElement( 'div' ); // Close widget
        
        return $html;
    }
    
    /**
     * Generate the right sidebar HTML
     *
     * @return string HTML for the right sidebar
     */
    private function getRightSidebarHTML() {
        $html = Html::openElement( 'aside', [ 'class' => 'islam-dashboard-right-sidebar' ] );
        
        // Add widgets to the right sidebar
        $html .= $this->getNotificationsWidgetHTML();
        $html .= $this->getQuickLinksWidgetHTML();
        
        $html .= Html::closeElement( 'aside' );
        
        return $html;
    }
    
    /**
     * Generate the notifications widget HTML
     *
     * @return string HTML for the notifications widget
     */
    private function getNotificationsWidgetHTML() {
        $notifications = []; // Placeholder for actual notifications
        
        $html = Html::openElement( 'div', [ 'class' => 'dashboard-widget notifications' ] );
        $html .= Html::element( 'h3', [ 'class' => 'widget-title' ], $this->msg( 'islamdashboard-notifications' )->text() );
        
        $html .= Html::openElement( 'div', [ 'class' => 'widget-content' ] );
        
        if ( empty( $notifications ) ) {
            $html .= Html::element( 'p', [ 'class' => 'no-notifications' ], 
                $this->msg( 'islamdashboard-no-notifications' )->text() );
        } else {
            $html .= Html::openElement( 'ul', [ 'class' => 'notifications-list' ] );
            // Loop through notifications and create list items
            foreach ( $notifications as $notification ) {
                $html .= $this->getNotificationItemHTML( $notification );
            }
            $html .= Html::closeElement( 'ul' );
        }
        
        $html .= Html::closeElement( 'div' ); // Close widget-content
        $html .= Html::closeElement( 'div' ); // Close widget
        
        return $html;
    }
    
    /**
     * Generate HTML for a single notification item
     *
     * @param array $notification Notification data
     * @return string HTML for the notification item
     */
    private function getNotificationItemHTML( $notification ) {
        // This would be implemented to format an individual notification
        return '';
    }
    
    /**
     * Generate the quick links widget HTML
     *
     * @return string HTML for the quick links widget
     */
    private function getQuickLinksWidgetHTML() {
        $quickLinks = [
            [
                'text' => 'islamdashboard-help',
                'href' => Title::newFromText( 'Help:Contents' )->getLocalURL(),
                'icon' => 'help'
            ],
            [
                'text' => 'islamdashboard-faq',
                'href' => Title::newFromText( 'Help:FAQ' )->getLocalURL(),
                'icon' => 'helpNotice'
            ],
            [
                'text' => 'islamdashboard-community',
                'href' => Title::newFromText( 'Project:Community_portal' )->getLocalURL(),
                'icon' => 'group'
            ]
        ];
        
        $html = Html::openElement( 'div', [ 'class' => 'dashboard-widget quick-links' ] );
        $html .= Html::element( 'h3', [ 'class' => 'widget-title' ], $this->msg( 'islamdashboard-quick-links' )->text() );
        
        $html .= Html::openElement( 'div', [ 'class' => 'widget-content' ] );
        $html .= Html::openElement( 'ul', [ 'class' => 'quick-links-list' ] );
        
        foreach ( $quickLinks as $link ) {
            $html .= Html::openElement( 'li', [ 'class' => 'quick-link-item' ] );
            $html .= Html::element( 'a', [
                'href' => $link['href'],
                'class' => 'quick-link',
                'data-icon' => $link['icon']
            ], $this->msg( $link['text'] )->text() );
            $html .= Html::closeElement( 'li' );
        }
        
        $html .= Html::closeElement( 'ul' );
        $html .= Html::closeElement( 'div' ); // Close widget-content
        $html .= Html::closeElement( 'div' ); // Close widget
        
        return $html;
    }
    
    /**
     * Check if the user has a specific right
     *
     * @param string $right The right to check
     * @return bool Whether the user has the right
     */
    private function userHasRight( $right ) {
        return $this->getUser()->isAllowed( $right );
    }
    
    /**
     * Get the group name for this special page
     *
     * @return string Group name
     */
    protected function getGroupName() {
        return 'users';
    }
}
