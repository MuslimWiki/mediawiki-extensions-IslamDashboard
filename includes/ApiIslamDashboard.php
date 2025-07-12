<?php
/**
 * API module for the IslamDashboard extension
 *
 * @file
 * @ingroup Extensions
 * @license GPL-3.0-or-later
 */

use MediaWiki\MediaWikiServices;
use MediaWiki\User\UserOptionsLookup;

class ApiIslamDashboard extends ApiBase {
    /**
     * Execute the API request
     */
    public function execute() {
        // Check if the user is logged in
        $user = $this->getUser();
        if ( !$user->isRegistered() ) {
            $this->dieWithError( 'islamdashboard-mustbeloggedin', 'notloggedin' );
        }

        // Get the requested action
        $params = $this->extractRequestParams();
        $subaction = $params['subaction'] ?? '';
        
        // Route to the appropriate handler
        switch ( $subaction ) {
            case 'savelayout':
                $this->saveDashboardLayout( $params );
                break;
                
            case 'hidewidget':
                $this->hideWidget( $params );
                break;
                
            case 'getwidgets':
                $this->getAvailableWidgets();
                break;
                
            default:
                $this->dieWithError( [ 'apierror-invalidparam', 'subaction' ] );
        }
    }
    
    /**
     * Save the user's dashboard layout
     * 
     * @param array $params API parameters
     */
    private function saveDashboardLayout( $params ) {
        $user = $this->getUser();
        
        // Validate the layout data
        $layout = json_decode( $params['layout'] ?? '{}', true );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            $this->dieWithError( 'apierror-badjson', 'invalidlayout' );
        }
        
        // Basic validation of layout structure
        if ( !isset( $layout['main'] ) || !is_array( $layout['main'] ) || 
             !isset( $layout['sidebar'] ) || !is_array( $layout['sidebar'] ) ) {
            $this->dieWithError( 'apierror-badjson', 'invalidlayout' );
        }
        
        // Get user options manager
        $userOptionsManager = MediaWikiServices::getInstance()->getUserOptionsManager();
        
        // Update the layout preference
        $userOptionsManager->setOption( 
            $user, 
            'islamdashboard-layout', 
            FormatJson::encode( $layout )
        );
        $userOptionsManager->saveOptions( $user );
        
        $this->getResult()->addValue( null, 'islamdashboard', [
            'result' => 'success',
            'layout' => $layout
        ] );
    }
    
    /**
     * Hide a widget for the current user
     * 
     * @param array $params API parameters
     */
    private function hideWidget( $params ) {
        $widgetId = $params['widget'] ?? '';
        if ( !$widgetId ) {
            $this->dieWithError( [ 'apierror-missingparam', 'widget' ] );
        }
        
        $user = $this->getUser();
        
        // Get user options manager
        $userOptionsManager = MediaWikiServices::getInstance()->getUserOptionsManager();
        
        // Get existing hidden widgets
        $hiddenWidgets = $this->getHiddenWidgets( $user );
        
        // Add the new widget ID if not already hidden
        if ( !in_array( $widgetId, $hiddenWidgets ) ) {
            $hiddenWidgets[] = $widgetId;
            
            // Save the updated list
            $userOptionsManager->setOption( 
                $user, 
                'islamdashboard-hidden-widgets', 
                implode( '|', $hiddenWidgets )
            );
            $userOptionsManager->saveOptions( $user );
        }
        
        $this->getResult()->addValue( null, 'islamdashboard', [
            'result' => 'success',
            'hiddenWidgets' => $hiddenWidgets
        ] );
    }
    
    /**
     * Get available widgets for the dashboard
     */
    private function getAvailableWidgets() {
        $widgets = [
            [
                'id' => 'welcome',
                'title' => wfMessage( 'islamdashboard-welcome-widget' )->text(),
                'description' => wfMessage( 'islamdashboard-welcome-widget-desc' )->text(),
                'defaultSection' => 'main',
                'canHide' => false
            ],
            [
                'id' => 'recent-activity',
                'title' => wfMessage( 'islamdashboard-recent-activity' )->text(),
                'description' => wfMessage( 'islamdashboard-recent-activity-desc' )->text(),
                'defaultSection' => 'main',
                'canHide' => true
            ],
            [
                'id' => 'quick-actions',
                'title' => wfMessage( 'islamdashboard-quick-actions' )->text(),
                'description' => wfMessage( 'islamdashboard-quick-actions-desc' )->text(),
                'defaultSection' => 'sidebar',
                'canHide' => true
            ],
            [
                'id' => 'notifications',
                'title' => wfMessage( 'islamdashboard-notifications' )->text(),
                'description' => wfMessage( 'islamdashboard-notifications-desc' )->text(),
                'defaultSection' => 'sidebar',
                'canHide' => true
            ]
        ];
        
        // Allow other extensions to add or modify widgets
        $hookContainer = MediaWikiServices::getInstance()->getHookContainer();
        $hookContainer->run( 'IslamDashboardGetWidgets', [ &$widgets ] );
        
        $this->getResult()->addValue( null, 'islamdashboard', [
            'result' => 'success',
            'widgets' => $widgets
        ] );
    }
    
    /**
     * Get the list of hidden widgets for a user
     * 
     * @param User $user
     * @return array Array of hidden widget IDs
     */
    private function getHiddenWidgets( User $user ) {
        $userOptionsLookup = MediaWikiServices::getInstance()->getUserOptionsLookup();
        $hidden = $userOptionsLookup->getOption( $user, 'islamdashboard-hidden-widgets', '' );
        return $hidden ? explode( '|', $hidden ) : [];
    }
    
    /**
     * Get the parameter descriptions for this API module
     * 
     * @return array Parameter descriptions
     */
    public function getAllowedParams() {
        return [
            'subaction' => [
                ApiBase::PARAM_TYPE => 'string',
                ApiBase::PARAM_REQUIRED => true,
                ApiBase::PARAM_HELP_MSG => 'apihelp-islamdashboard-param-subaction',
            ],
            'layout' => [
                ApiBase::PARAM_TYPE => 'string',
                ApiBase::PARAM_REQUIRED => false,
                ApiBase::PARAM_HELP_MSG => 'apihelp-islamdashboard-param-layout',
            ],
            'widget' => [
                ApiBase::PARAM_TYPE => 'string',
                ApiBase::PARAM_REQUIRED => false,
                ApiBase::PARAM_HELP_MSG => 'apihelp-islamdashboard-param-widget',
            ],
        ];
    }
    
    /**
     * Get examples for this API module
     * 
     * @return array Examples
     */
    protected function getExamplesMessages() {
        return [
            'action=islamdashboard&subaction=savelayout&layout={"main":["welcome"],"sidebar":["quick-actions"]}'
                => 'apihelp-islamdashboard-example-savelayout',
            'action=islamdashboard&subaction=hidewidget&widget=recent-activity'
                => 'apihelp-islamdashboard-example-hidewidget',
            'action=islamdashboard&subaction=getwidgets'
                => 'apihelp-islamdashboard-example-getwidgets',
        ];
    }
    
    /**
     * Indicate that this API module requires a POST request
     * 
     * @return bool True if POST is required
     */
    public function mustBePosted() {
        return true;
    }
    
    /**
     * Indicate that this API module requires a CSRF token
     * 
     * @return string|bool Token type or false if not needed
     */
    public function needsToken() {
        return 'csrf';
    }
    
    /**
     * Indicate that this API module can only be used by registered users
     * 
     * @return bool True if write mode is required
     */
    public function isWriteMode() {
        return true;
    }
    
    /**
     * Get the module's summary
     * 
     * @return string Module summary
     */
    protected function getSummaryMessage() {
        return 'apihelp-islamdashboard-summary';
    }
    
    /**
     * Get the module's description
     * 
     * @return string Module description
     */
    protected function getExtendedDescription() {
        return 'apihelp-islamdashboard-extended-description';
    }
    
    /**
     * Get the module's help URLs
     * 
     * @return array Array of help URLs
     */
    public function getHelpUrls() {
        return [
            'https://www.mediawiki.org/wiki/Extension:IslamDashboard/API',
        ];
    }
}
