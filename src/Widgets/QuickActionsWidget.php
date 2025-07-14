<?php
/**
 * Quick actions widget for IslamDashboard
 *
 * @file
 * @ingroup Extensions
 * @license GPL-3.0-or-later
 */

namespace MediaWiki\Extension\IslamDashboard\Widgets;

use MediaWiki\Context\IContextSource;
use MediaWiki\Html\Html;
use MediaWiki\MediaWikiServices;
use MediaWiki\SpecialPage\SpecialPage;
use MediaWiki\Title\Title;
use User;

/**
 * Widget that displays quick action buttons
 */
class QuickActionsWidget extends DashboardWidget {
    
    /**
     * @param IContextSource|null $context Context source
     */
    public function __construct( IContextSource $context = null ) {
        parent::__construct(
            'quick-actions',
            'islamdashboard-widget-quick-actions-title',
            'islamdashboard-widget-quick-actions-desc',
            $context
        );
        
        $this->defaultSection = 'sidebar';
    }
    
    /**
     * @inheritDoc
     */
    public function getIcon() {
        return 'quickActions';
    }
    
    /**
     * @inheritDoc
     */
    public function canBeAdded() {
        // Check if user has any quick actions available
        return !empty($this->getQuickActions());
    }
    
    /**
     * @inheritDoc
     */
    public function isEditable() {
        return false; // Quick actions are not editable
    }
    
    /**
     * @inheritDoc
     */
    public function getContent() {
        $context = $this->getContext();
        $actions = $this->getQuickActions();
        
        if ( empty( $actions ) ) {
            return $this->getNoActionsMessage();
        }
        
        // Prepare template data structure that matches the Mustache template
        $templateData = [
            'header' => [
                'title' => $context->msg( 'islamdashboard-widget-quick-actions-title' )->text(),
                'subtitle' => $context->msg( 'islamdashboard-widget-quick-actions-desc' )->text()
            ],
            'actions' => $this->prepareActionsForTemplate( $actions )
        ];
        
        // Render the template with the widgets/ prefix for the template file
        // The template is automatically suffixed with .mustache
        return $this->renderTemplate( 'widgets/QuickActionsWidget', $templateData );
    }
    
    /**
     * Prepare actions data for the template
     * 
     * @param array $actions Array of action definitions
     * @return array Prepared actions for the template
     */
    protected function prepareActionsForTemplate( array $actions ): array {
        $preparedActions = [];
        $iconMap = [
            'add' => '<span class="mw-ui-icon mw-ui-icon-add"></span>',
            'upload' => '<span class="mw-ui-icon mw-ui-icon-upload"></span>',
            'recentChanges' => '<span class="mw-ui-icon mw-ui-icon-recentChanges"></span>',
            'star' => '<span class="mw-ui-icon mw-ui-icon-star"></span>',
            'userContributions' => '<span class="mw-ui-icon mw-ui-icon-userContributions"></span>'
        ];
        
        foreach ( $actions as $id => $action ) {
            $preparedActions[] = [
                'id' => $id,
                'url' => $action['href'],
                'title' => $action['text'],
                'label' => $action['text'],
                'icon' => $iconMap[$action['icon']] ?? '<span class="mw-ui-icon mw-ui-icon-edit"></span>'
            ];
        }
        
        return $preparedActions;
    }
    
    /**
     * Get quick actions for the current user
     * 
     * @return array Array of action definitions
     */
    protected function getQuickActions() {
        $user = $this->getUser();
        $actions = [];
        
        // Always show these actions
        $actions['create-page'] = [
            'href' => Title::newFromText( 'Special:CreatePage' )->getLocalURL(),
            'text' => wfMessage( 'islamdashboard-action-create-page' )->text(),
            'icon' => 'add',
            'permission' => 'edit'
        ];
        
        $actions['upload-file'] = [
            'href' => SpecialPage::getTitleFor( 'Upload' )->getLocalURL(),
            'text' => wfMessage( 'islamdashboard-action-upload-file' )->text(),
            'icon' => 'upload',
            'permission' => 'upload'
        ];
        
        $actions['recent-changes'] = [
            'href' => SpecialPage::getTitleFor( 'Recentchanges' )->getLocalURL(),
            'text' => wfMessage( 'islamdashboard-action-recent-changes' )->text(),
            'icon' => 'recentChanges',
            'permission' => 'viewrecentchanges'
        ];
        
        // Only show watchlist if user is watching pages
        if ( $user->isRegistered() ) {
            $actions['watchlist'] = [
                'href' => SpecialPage::getTitleFor( 'Watchlist' )->getLocalURL(),
                'text' => wfMessage( 'islamdashboard-action-watchlist' )->text(),
                'icon' => 'star',
                'permission' => 'viewmywatchlist'
            ];
        }
        
        // Only show contributions for registered users
        if ( $user->isRegistered() ) {
            $actions['my-contributions'] = [
                'href' => SpecialPage::getTitleFor( 'Contributions', $user->getName() )->getLocalURL(),
                'text' => wfMessage( 'islamdashboard-action-my-contributions' )->text(),
                'icon' => 'userContributions',
                'permission' => 'read' // Everyone can view their own contributions
            ];
        }
        
        // Filter out actions the user doesn't have permission for
        return array_filter( $actions, function( $action ) use ( $user ) {
            return $user->isAllowed( $action['permission'] ?? 'read' );
        } );
    }
    
    /**
     * Render quick action buttons
     * 
     * @param array $actions Array of action definitions
     * @return string HTML
     */
    protected function renderActionButtons( $actions ) {
        $html = Html::openElement( 'div', [ 'class' => 'quick-actions-grid' ] );
        
        foreach ( $actions as $actionId => $action ) {
            $html .= $this->renderActionButton( $actionId, $action );
        }
        
        $html .= Html::closeElement( 'div' );
        
        return $html;
    }
    
    /**
     * Render a single quick action button
     * 
     * @param string $actionId Action ID
     * @param array $action Action definition
     * @return string HTML
     */
    protected function renderActionButton( $actionId, $action ) {
        $classes = [ 'quick-action-button' ];
        
        $html = Html::openElement( 'a', [
            'href' => $action['href'],
            'class' => implode( ' ', $classes ),
            'title' => $action['text'],
            'data-action' => $actionId
        ] );
        
        // Icon
        $html .= Html::element( 'span', 
            [ 'class' => 'action-icon ooui-icon ooui-icon-' . $action['icon'] ],
            ''
        );
        
        // Label
        $html .= Html::element( 'span', 
            [ 'class' => 'action-label' ],
            $action['text']
        );
        
        $html .= Html::closeElement( 'a' );
        
        return $html;
    }
    
    /**
     * Get the message to display when there are no actions available
     * 
     * @return string HTML
     */
    protected function getNoActionsMessage() {
        return Html::element(
            'div',
            [ 'class' => 'no-actions-message' ],
            wfMessage( 'islamdashboard-no-actions-available' )->text()
        );
    }
    
    /**
     * @inheritDoc
     */
    public function getModules() {
        return [ 'ext.islamDashboard.quickActionsWidget' ];
    }
    
    /**
     * @inheritDoc
     */
    public function getContainerClasses() {
        return array_merge( parent::getContainerClasses(), [ 'quick-actions-widget' ] );
    }
    
    // Inherited from DashboardWidget:
    // - getUser() - Gets the current user
    // - getContext() - Gets the current context
}
