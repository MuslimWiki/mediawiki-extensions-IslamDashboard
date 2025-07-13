<?php
/**
 * Welcome widget for the IslamDashboard
 *
 * @file
 * @ingroup Extensions
 * @license GPL-3.0-or-later
 */

namespace MediaWiki\Extension\IslamDashboard\Widgets;

use MediaWiki\MediaWikiServices;
use MediaWiki\Html\Html;
use MediaWiki\SpecialPage\SpecialPage;
use MediaWiki\Context\RequestContext;
use MediaWiki\Context\IContextSource;
use MediaWiki\User\User;

/**
 * Welcome widget that greets the user and shows quick stats
 */
class WelcomeWidget extends DashboardWidget {
    
    /**
     * @param IContextSource|null $context Context source
     */
    public function __construct( IContextSource $context = null ) {
        parent::__construct(
            'welcome',
            'islamdashboard-widget-welcome-title',
            'islamdashboard-widget-welcome-desc',
            $context
        );
        
        $this->defaultSection = 'main';
        $this->canHide = false; // Cannot be hidden
    }
    
    /**
     * @inheritDoc
     */
    public function getIcon() {
        return 'userAvatar';
    }
    
    /**
     * @inheritDoc
     */
    public function getContent(): string {
        $context = $this->getContext();
        $user = $context->getUser();
        
        // Prepare template data structure that matches the Mustache template
        $templateData = [
            'header' => [
                'title' => $context->msg('islamdashboard-welcome-back', $user->getName())->parse(),
                'subtitle' => $context->msg('islamdashboard-welcome-subtitle')->text()
            ],
            'welcomeText' => $context->msg('islamdashboard-welcome-message')->parse(),
            'showStats' => true,
            'stats' => [
                [
                    'value' => $this->getUserEditCount(),
                    'label' => $context->msg('islamdashboard-stats-edits')->text()
                ],
                [
                    'value' => $this->getDaysSinceRegistration(),
                    'label' => $context->msg('islamdashboard-stats-days')->text()
                ],
                [
                    'value' => $this->getRecentActivityCount(),
                    'label' => $context->msg('islamdashboard-stats-recent')->text()
                ]
            ],
            'quickLinks' => [
                'quickLinksTitle' => $context->msg('islamdashboard-quick-links')->text(),
                'links' => [
                    [
                        'url' => SpecialPage::getTitleFor('Contributions', $user->getName())->getLocalURL(),
                        'text' => $context->msg('mycontris')->text(),
                        'icon' => '<span class="mw-ui-icon mw-ui-icon-userContributions"></span>'
                    ],
                    [
                        'url' => SpecialPage::getTitleFor('Watchlist')->getLocalURL(),
                        'text' => $context->msg('watchlist')->text(),
                        'icon' => '<span class="mw-ui-icon mw-ui-icon-watchlist"></span>'
                    ],
                    [
                        'url' => SpecialPage::getTitleFor('Preferences')->getLocalURL(),
                        'text' => $context->msg('preferences')->text(),
                        'icon' => '<span class="mw-ui-icon mw-ui-icon-settings"></span>'
                    ]
                ]
            ]
        ];
        
        // Add user avatar if available
        if (class_exists('MediaWiki\\Extension\\Avatar\\Avatar')) {
            $avatar = MediaWikiServices::getInstance()->getService('Avatar')
                ->getAvatarUrl($user, 'l');
            if ($avatar) {
                $templateData['userAvatar'] = $avatar;
                $templateData['userName'] = $user->getName();
            }
        }
        
        // Render the template with the widgets/ prefix for the template file
        // The template is automatically suffixed with .mustache
        return $this->renderTemplate('widgets/WelcomeWidget', $templateData);
    }
    
    /**
     * Get the user's edit count
     *
     * @return int
     */
    private function getUserEditCount(): int {
        return $this->getContext()->getUser()->getEditCount() ?? 0;
    }
    
    /**
     * Get days since user registration
     *
     * @return int
     */
    private function getDaysSinceRegistration(): int {
        $registration = $this->getContext()->getUser()->getRegistration();
        if (!$registration) {
            return 0;
        }
        $now = wfTimestamp(TS_UNIX);
        $diff = $now - wfTimestamp(TS_UNIX, $registration);
        return (int) floor($diff / (60 * 60 * 24));
    }
    
    /**
     * Get recent activity count (last 7 days)
     *
     * @return int
     */
    private function getRecentActivityCount(): int {
        $user = $this->getContext()->getUser();
        if (!$user->isRegistered()) {
            return 0;
        }
        
        $services = MediaWikiServices::getInstance();
        $dbr = $services->getDBLoadBalancer()->getConnection(DB_REPLICA);
        $timestamp = $dbr->timestamp(strtotime('-7 days'));
        
        $queryBuilder = $dbr->newSelectQueryBuilder()
            ->select('COUNT(*)')
            ->from('revision')
            ->where([
                'rev_actor' => $user->getActorId(),
                $dbr->expr('rev_timestamp', '>', $timestamp)
            ])
            ->caller(__METHOD__);
            
        return (int)$queryBuilder->fetchField();
    }
    
    /**
     * @inheritDoc
     */
    public function getModules(): array {
        return [ 
            'ext.islamDashboard.welcomeWidget',
            'ext.islamDashboard.styles', // Ensure IslamSkin compatibility
            'skin.islam.styles' // Load IslamSkin styles if available
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function getContainerClasses(): array {
        return array_merge(
            parent::getContainerClasses(), 
            [
                'welcome-widget',
                'islam-widget', // For IslamSkin compatibility
                'islam-widget-welcome'
            ]
        );
    }
    
    // Inherited from DashboardWidget:
    // - getUser() - Gets the current user
    // - getContext() - Gets the current context
}
