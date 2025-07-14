<?php
/**
 * Recent activity widget for IslamDashboard
 *
 * @file
 * @ingroup Extensions
 * @license GPL-3.0-or-later
 */

namespace MediaWiki\Extension\IslamDashboard\Widgets;

use MediaWiki\Context\IContextSource;
use MediaWiki\Html\Html;
use MediaWiki\Linker\LinkRenderer;
use MediaWiki\Linker\LinkTarget;
use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\SpecialPage\SpecialPage;
use MediaWiki\Title\Title;
use MediaWiki\Title\TitleFactory;
use RecentChange;
use User;

/**
 * Widget that displays recent user activity
 */
class RecentActivityWidget extends DashboardWidget {
    
    /** @var int Maximum number of activity items to show */
    private const MAX_ITEMS = 10;
    
    /** @var LinkRenderer */
    private $linkRenderer;
    
    /**
     * @param IContextSource|null $context Context source
     */
    public function __construct( IContextSource $context = null ) {
        parent::__construct(
            'recentactivity',
            'islamdashboard-widget-recentactivity-title',
            'islamdashboard-widget-recentactivity-desc',
            $context
        );
        
        $this->defaultSection = 'main';
        $this->canHide = true;
        $this->linkRenderer = MediaWikiServices::getInstance()->getLinkRenderer();
    }
    
    /**
     * @inheritDoc
     */
    public function getIcon() {
        return 'recentChanges';
    }
    
    /**
     * @inheritDoc
     */
    public function isLoading() {
        // This widget could be loading if it fetches data asynchronously
        return false;
    }
    
    /**
     * @inheritDoc
     */
    public function isEditable() {
        return true; // Allow users to configure which activities to show
    }
    
    /**
     * @inheritDoc
     */
    public function getContent(): string {
        $context = $this->getContext();
        $activities = $this->getRecentActivity();
        
        // Prepare template data structure that matches the Mustache template
        $templateData = [
            'header' => [
                'title' => $context->msg( 'islamdashboard-widget-recentactivity-title' )->text(),
                'subtitle' => $context->msg( 'islamdashboard-widget-recentactivity-desc' )->text()
            ],
            'hasActivities' => !empty( $activities ),
            'activities' => $activities,
            'noActivitiesMessage' => $context->msg( 'islamdashboard-no-recent-activity' )->text(),
            'viewAllLink' => [
                'url' => SpecialPage::getTitleFor( 'Contributions', $this->getUser()->getName() )->getLocalURL(),
                'text' => $context->msg( 'islamdashboard-view-all-activity' )->text()
            ]
        ];
        
        // Add suggested actions if there are no activities
        if ( empty( $activities ) ) {
            $templateData['suggestedActions'] = [
                'message' => $context->msg( 'islamdashboard-suggested-actions-message' )->text(),
                'actions' => [
                    [
                        'url' => SpecialPage::getTitleFor( 'CreatePage' )->getLocalURL(),
                        'text' => $context->msg( 'islamdashboard-action-create-page' )->text()
                    ],
                    [
                        'url' => SpecialPage::getTitleFor( 'Upload' )->getLocalURL(),
                        'text' => $context->msg( 'islamdashboard-action-upload-file' )->text()
                    ],
                    [
                        'url' => SpecialPage::getTitleFor( 'RecentChanges' )->getLocalURL(),
                        'text' => $context->msg( 'islamdashboard-action-recent-changes' )->text()
                    ]
                ]
            ];
        }
        
        // Render the template with the widgets/ prefix for the template file
        // The template is automatically suffixed with .mustache
        return $this->renderTemplate( 'widgets/RecentActivityWidget', $templateData );
    }
    
    /**
     * Get recent activity for the current user
     * 
     * @return array Array of activity items
     */
    protected function getRecentActivity(): array {
        $user = $this->getUser();
        $services = MediaWikiServices::getInstance();
        $dbr = $services->getDBLoadBalancer()->getMaintenanceConnectionRef( DB_REPLICA );
        
        // Get recent edits using ActorMigration for MW 1.36+
        $commentStore = $services->getCommentStore();
        
        // Get actor ID for the current user
        $actorId = $user->getActorId();
        
        // Build the query
        $queryBuilder = $dbr->newSelectQueryBuilder()
            ->select( [
                'rc_timestamp',
                'rc_namespace',
                'rc_title',
                'rc_this_oldid',
                'rc_last_oldid',
                'rc_type',
                'rc_source',
                'rc_deleted',
                'page_latest',
                'page_namespace',
                'page_title',
                'page_is_redirect',
                'page_len',
                'page_content_model',
                'rc_comment_text' => 'comment_rc_comment.comment_text',
                'rc_comment_data' => 'comment_rc_comment.comment_data'
            ] )
            ->from( 'recentchanges' )
            ->join( 'page', null, 'page_id=rc_cur_id' )
            ->leftJoin( 'comment', 'comment_rc_comment', 'comment_rc_comment.comment_id = rc_comment_id' )
            ->where( [
                'rc_actor' => $actorId,
                'rc_type != ' . $dbr->addQuotes( RC_EXTERNAL ) // Exclude external changes
            ] )
            ->orderBy( 'rc_timestamp', 'DESC' )
            ->limit( self::MAX_ITEMS )
            ->caller( __METHOD__ );
        
        $res = $queryBuilder->fetchResultSet();
        
        $activities = [];
        
        foreach ( $res as $row ) {
            $activities[] = $this->formatActivity( $row );
        }
        
        return $activities;
    }
    
    /**
     * Format a database row as an activity item
     * 
     * @param \stdClass $row Database row
     * @return array Formatted activity item
     */
    protected function formatActivity( \stdClass $row ): array {
        $services = MediaWikiServices::getInstance();
        $titleFactory = $services->getTitleFactory();
        $lang = $this->getContext()->getLanguage();
        
        // Safely access row properties with null coalescing
        $namespace = $row->rc_namespace ?? NS_MAIN;
        $titleText = $row->rc_title ?? '';
        $timestamp = $row->rc_timestamp ?? wfTimestampNow();
        
        // Create title object safely
        try {
            $title = $titleFactory->makeTitleSafe($namespace, $titleText);
            if (!$title || !$title->exists()) {
                return [];
            }
        } catch (\Exception $e) {
            wfDebugLog('IslamDashboard', 'Error creating title: ' . $e->getMessage());
            return [];
        }
        
        $formattedTime = $lang->timeanddate($timestamp, true);
        $activityType = $this->getActivityType($row);
        
        // Get appropriate icon HTML based on activity type
        $iconMap = [
            'create' => 'article',
            'edit' => 'edit',
            'delete' => 'trash',
            'move' => 'move',
            'undo' => 'undo',
            'rollback' => 'reload',
            'upload' => 'upload',
            'comment' => 'chat'
        ];
        
        $iconClass = $iconMap[$activityType] ?? 'edit';
        $icon = Html::element('span', [
            'class' => 'mw-ui-icon mw-ui-icon-' . $iconClass
        ]);
        
        $activity = [
            'type' => $activityType,
            'title' => $title->getPrefixedText(),
            'url' => $title->getLocalURL(),
            'formattedTime' => $formattedTime,
            'icon' => $icon
        ];
        
        // Add comment if available
        $comment = $this->formatComment( $row );
        if ( !empty( $comment ) ) {
            $activity['comment'] = $comment;
        }
        
        return $activity;
    }
    
    /**
     * Get the activity type from a database row
     * 
     * @param \stdClass $row Database row
     * @return string Activity type (edit, create, delete, etc.)
     */
    protected function getActivityType( \stdClass $row ): string {
        if ( $row->rc_type == RC_NEW ) {
            return 'create';
        } elseif ( $row->rc_source === 'mw.undo' ) {
            return 'undo';
        } elseif ( $row->rc_source === 'mw.rollback' ) {
            return 'rollback';
        } else {
            return 'edit';
        }
    }
    
    /**
     * Format a comment from a database row
     * 
     * @param \stdClass $row Database row
     * @return string Formatted comment
     */
    protected function formatComment( \stdClass $row ): string {
        if ( isset( $row->rc_deleted ) && ( $row->rc_deleted & RevisionRecord::DELETED_COMMENT ) ) {
            return wfMessage( 'rev-deleted-comment' )->text();
        }
        
        // Safely access comment_text with null coalescing
        $commentText = $row->comment_text ?? $row->rc_comment_text ?? '';
        
        if ( empty( $commentText ) ) {
            return '';
        }
        
        // TODO: Format comment with proper parsing
        return $commentText;
    }
    
    /**
     * Get the icon for an activity type
     * 
     * @param \stdClass $row Database row
     * @return string Icon name
     */
    protected function getActivityIcon( \stdClass $row ): string {
        switch ( $this->getActivityType( $row ) ) {
            case 'create':
                return 'article';
            case 'delete':
                return 'trash';
            case 'move':
                return 'move';
            case 'undo':
                return 'undo';
            case 'rollback':
                return 'reload';
            default:
                return 'edit';
        }
    }
    
    /**
     * Render the activity list HTML
     * 
     * @param array $activities Array of activity items
     * @return string HTML
     */
    protected function renderActivityList( array $activities ): string {
        $html = Html::openElement( 'ul', [ 'class' => 'activity-list' ] );
        
        foreach ( $activities as $activity ) {
            $html .= $this->renderActivityItem( $activity );
        }
        
        $html .= Html::closeElement( 'ul' );
        
        // Add a link to the user's contributions
        $html .= $this->renderViewAllLink();
        
        return $html;
    }
    
    /**
     * Render a single activity item
     * 
     * @param array $activity Activity data
     * @return string HTML
     */
    protected function renderActivityItem( array $activity ): string {
        $html = Html::openElement( 'li', [ 'class' => 'activity-item activity-' . $activity['type'] ] );
        
        // Icon
        $html .= Html::element( 'span', 
            [ 'class' => 'activity-icon ooui-icon ooui-icon-' . $activity['icon'] ],
            ''
        );
        
        // Content
        $html .= Html::openElement( 'div', [ 'class' => 'activity-content' ] );
        
        // Title and time
        $html .= Html::rawElement( 'div', [ 'class' => 'activity-header' ],
            Html::element( 'a', 
                [ 
                    'href' => $activity['url'],
                    'class' => 'activity-title',
                    'title' => $activity['title']
                ],
                $activity['title']
            ) .
            Html::element( 'span', 
                [ 
                    'class' => 'activity-time',
                    'title' => $activity['formattedTime']
                ],
                $this->getRelativeTime( $activity['timestamp'] )
            )
        );
        
        // Comment
        if ( !empty( $activity['comment'] ) ) {
            $html .= Html::element( 'div', 
                [ 'class' => 'activity-comment' ],
                $activity['comment']
            );
        }
        
        $html .= Html::closeElement( 'div' );
        $html .= Html::closeElement( 'li' );
        
        return $html;
    }
    
    /**
     * Get a relative time string (e.g. "2 hours ago")
     * 
     * @param string $timestamp Timestamp in MW format
     * @return string Relative time string
     */
    protected function getRelativeTime( $timestamp ) {
        global $wgLang;
        
        $now = wfTimestampNow();
        $diff = wfTimestamp( TS_UNIX, $now ) - wfTimestamp( TS_UNIX, $timestamp );
        
        if ( $diff < 60 ) {
            return wfMessage( 'islamdashboard-just-now' )->text();
        } elseif ( $diff < 3600 ) {
            $minutes = floor( $diff / 60 );
            return wfMessage( 'islamdashboard-minutes-ago', $minutes )->text();
        } elseif ( $diff < 86400 ) {
            $hours = floor( $diff / 3600 );
            return wfMessage( 'islamdashboard-hours-ago', $hours )->text();
        } else {
            $days = floor( $diff / 86400 );
            return wfMessage( 'islamdashboard-days-ago', $days )->text();
        }
    }
    
    /**
     * Render the "View all" link
     * 
     * @return string HTML
     */
    protected function renderViewAllLink() {
        $user = $this->getUser();
        $contribsTitle = SpecialPage::getTitleFor( 'Contributions', $user->getName() );
        
        return Html::rawElement(
            'div',
            [ 'class' => 'activity-view-all' ],
            $this->linkRenderer->makeLink(
                $contribsTitle,
                wfMessage( 'islamdashboard-view-all-activity' )->text(),
                [ 'class' => 'mw-ui-button mw-ui-progressive' ]
            )
        );
    }
    
    /**
     * Get the message to display when there's no activity
     * 
     * @return string HTML
     */
    protected function getNoActivityMessage(): string {
        return Html::element(
            'div',
            [ 'class' => 'no-activity' ],
            wfMessage( 'islamdashboard-no-recent-activity' )->text()
        );
    }
    
    /**
     * @inheritDoc
     */
    public function getModules(): array {
        return [ 
            'ext.islamDashboard.recentActivity',
            'ext.islamDashboard.styles' // Ensure IslamSkin compatibility
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function getContainerClasses(): array {
        return array_merge( 
            parent::getContainerClasses(), 
            [ 
                'recent-activity-widget',
                'islam-widget-recent-activity',
                'islam-widget' // For IslamSkin compatibility
            ] 
        );
    }
    
    // Inherited from DashboardWidget:
    // - getUser() - Gets the current user
    // - getContext() - Gets the current context
}
