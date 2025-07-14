<?php
namespace MediaWiki\Extension\IslamDashboard\Special;

use MediaWiki\SpecialPage\SpecialPage;
use MediaWiki\User\User;
use MediaWiki\MediaWikiServices;
use Html;
use MediaWiki\Title\Title;
use MediaWiki\User\UserAvatarLookup;
use SpecialRecentChanges;
use Wikimedia\Rdbms\ILoadBalancer;

class SpecialIslamDashboard extends SpecialPage {
    /** @var ILoadBalancer */
    private $loadBalancer;

    /**
     * @param ILoadBalancer $loadBalancer
     */
    public function __construct(ILoadBalancer $loadBalancer) {
        parent::__construct('IslamDashboard');
        $this->loadBalancer = $loadBalancer;
        $this->mIncludable = true;
    }

    /**
     * @inheritDoc
     */
    public static function factory(ILoadBalancer $loadBalancer) {
        return new self($loadBalancer);
    }

    /**
     * @inheritDoc
     */
    public function getGroupName() {
        return 'users';
    }
    
    /**
     * @inheritDoc
     */
    public function execute($subPage) {
        $out = $this->getOutput();
        $user = $this->getUser();
        
        // Check if user is logged in
        if (!$user->isRegistered()) {
            $out->addHTML('<div class="error">' . $this->msg('islamdashboard-mustbeloggedin')->parse() . '</div>');
            return;
        }
        
        // Set page title and add CSS/JS
        $out->setPageTitle($this->msg('islamdashboard')->text());
        $out->addModuleStyles(['ext.islamDashboard.styles']);
        $out->addModules(['ext.islamDashboard']);
        
        // Start dashboard grid
        $out->addHTML('<div class="mw-dashboard-grid">');
        
        // Left sidebar (25% width)
        $out->addHTML('<div class="mw-dashboard-sidebar">');
        $this->renderLeftColumn($out);
        $out->addHTML('</div>');
        
        // Main content area (50% width)
        $out->addHTML('<div class="mw-dashboard-main">');
        $this->renderMiddleColumn($out);
        $out->addHTML('</div>');
        
        // Right sidebar (25% width)
        $out->addHTML('<div class="mw-dashboard-right-sidebar">');
        $this->renderRightColumn($out);
        $out->addHTML('</div>');
        
        // Close dashboard grid
        $out->addHTML('</div>');
    }
    
    /**
     * Render the left column content
     */
    protected function renderLeftColumn($out) {
        $user = $this->getUser();
        
        // User profile card
        $out->addHTML('<div class="dashboard-widget user-profile">');
        $out->addHTML('<h2>' . $this->msg('islamdashboard-profile')->escaped() . '</h2>');
        $out->addHTML('<div class="user-avatar">');
        // Simple avatar placeholder - replace with actual avatar implementation
        $out->addHTML(Html::element('div', [
            'class' => 'mw-avatar',
            'style' => 'width: 100px; height: 100px; background: #ccc; border-radius: 50%; display: flex; align-items: center; justify-content: center;'
        ], mb_substr($user->getName(), 0, 1)));
        $out->addHTML('</div>');
        $out->addHTML('<div class="user-details">');
        $out->addHTML('<h3>' . htmlspecialchars($user->getName()) . '</h3>');
        
        // User stats
        $editCount = $user->getEditCount();
        $registration = $user->getRegistration();
        $daysRegistered = $registration ? 
            floor((time() - wfTimestamp(TS_UNIX, $registration)) / 86400) : 0;
            
        $out->addHTML('<div class="user-stats">');
        $out->addHTML('<div class="stat"><span class="stat-value">' . $editCount . '</span> ' . 
            $this->msg('islamdashboard-stats-edits')->escaped() . '</div>');
        $out->addHTML('<div class="stat"><span class="stat-value">' . $daysRegistered . '</span> ' . 
            $this->msg('islamdashboard-stats-days')->escaped() . '</div>');
        $out->addHTML('</div>'); // .user-stats
        
        $out->addHTML('</div>'); // .user-details
        $out->addHTML('</div>'); // .user-profile
        
        // Quick Actions
        $out->addHTML('<div class="dashboard-widget quick-actions">');
        $out->addHTML('<h2>' . $this->msg('islamdashboard-quick-actions')->escaped() . '</h2>');
        $out->addHTML('<ul class="quick-actions-list">');
        $out->addHTML('<li><a href="' . 
            htmlspecialchars(Title::newFromText('Special:CreatePage')->getLocalURL()) . '">' . 
            $this->msg('islamdashboard-createpage')->escaped() . '</a></li>');
        $out->addHTML('<li><a href="' . 
            htmlspecialchars(Title::newFromText('Special:Upload')->getLocalURL()) . '">' . 
            $this->msg('islamdashboard-uploadfile')->escaped() . '</a></li>');
        $out->addHTML('<li><a href="' . 
            htmlspecialchars(Title::newFromText('Special:Watchlist')->getLocalURL()) . '">' . 
            $this->msg('islamdashboard-watchlist')->escaped() . '</a></li>');
        $out->addHTML('</ul>');
        $out->addHTML('</div>'); // .quick-actions
    }
    
    /**
     * Render the middle column content
     */
    protected function renderMiddleColumn($out) {
        // Recent Activity
        $out->addHTML('<div class="dashboard-widget recent-activity">');
        $out->addHTML('<h2>' . $this->msg('islamdashboard-recent-activity')->escaped() . '</h2>');
        
        // Add recent changes feed
        // Recent changes list using a database query
        $dbr = $this->loadBalancer->getConnection(DB_REPLICA);
        $res = $dbr->newSelectQueryBuilder()
            ->select([
                'rc_title',
                'rc_timestamp',
                'actor_name as user_name',
                'rc_namespace',
                'rc_this_oldid',
                'rc_cur_id',
                'rc_title as page_title',
                'rc_namespace as page_namespace',
                'rc_cur_id as page_id'
            ])
            ->from('recentchanges')
            ->join('actor', null, 'actor_id=rc_actor')
            ->orderBy('rc_timestamp', 'DESC')
            ->limit(5)
            ->caller(__METHOD__)
            ->fetchResultSet();
        
        $out->addHTML('<ul class="recent-changes-list">');
        foreach ($res as $row) {
            // Create title using namespace and title directly
            $title = Title::makeTitleSafe(
                (int)$row->rc_namespace,
                $row->rc_title,
                '',
                $row->rc_cur_id > 0 ? (int)$row->rc_cur_id : null
            );
            
            if ($title && $title->canExist()) {
                $out->addHTML(Html::rawElement('li', [
                    'class' => 'mw-changeslist-line'
                ],
                    Html::rawElement('span', ['class' => 'mw-title'],
                        Html::element('a', [
                            'href' => $title->getLocalURL(),
                            'class' => 'mw-changeslist-title'
                        ], $title->getPrefixedText())
                    ) . ' ' .
                    Html::element('span', [
                        'class' => 'mw-changeslist-time',
                        'title' => $this->getLanguage()->time($row->rc_timestamp, true)
                    ], $this->getLanguage()->getHumanTimestamp(
                        \MediaWiki\Utils\MWTimestamp::getInstance($row->rc_timestamp),
                        null,
                        $this->getUser()
                    ))
                ));
            }
        }
        $out->addHTML('</ul>');
        
        $out->addHTML('</div>'); // .recent-activity
        
        // Notifications
        $out->addHTML('<div class="dashboard-widget notifications">');
        $out->addHTML('<h2>' . $this->msg('islamdashboard-notifications')->escaped() . '</h2>');
        $out->addHTML('<p class="no-notifications">' . 
            $this->msg('islamdashboard-no-notifications')->escaped() . '</p>');
        $out->addHTML('</div>'); // .notifications
    }
    
    /**
     * Render the right column content
     */
    protected function renderRightColumn($out) {
        // Quick Links
        $out->addHTML('<div class="dashboard-widget quick-links">');
        $out->addHTML('<h2>' . $this->msg('islamdashboard-quick-links')->escaped() . '</h2>');
        $out->addHTML('<ul class="quick-links-list">');
        $out->addHTML('<li><a href="' . 
            htmlspecialchars(Title::newFromText('Help:Contents')->getLocalURL()) . '">' . 
            $this->msg('islamdashboard-help')->escaped() . '</a></li>');
        $out->addHTML('<li><a href="' . 
            htmlspecialchars(Title::newFromText('Project:About')->getLocalURL()) . '">' . 
            $this->msg('islamdashboard-about')->escaped() . '</a></li>');
        $out->addHTML('<li><a href="' . 
            htmlspecialchars(Title::newFromText('Project:Community_portal')->getLocalURL()) . '">' . 
            $this->msg('islamdashboard-community')->escaped() . '</a></li>');
        $out->addHTML('</ul>');
        $out->addHTML('</div>'); // .quick-links
        
        // Stats
        $out->addHTML('<div class="dashboard-widget stats">');
        $out->addHTML('<h2>' . $this->msg('islamdashboard-stats')->escaped() . '</h2>');
        
        // Add some sample stats (replace with actual stats)
        $out->addHTML('<div class="stats-container">');
        $out->addHTML('<div class="stat"><span class="stat-value">' . 
            number_format(12345) . '</span> ' . 
            $this->msg('islamdashboard-stats-pages')->escaped() . '</div>');
        $out->addHTML('<div class="stat"><span class="stat-value">' . 
            number_format(6789) . '</span> ' . 
            $this->msg('islamdashboard-stats-files')->escaped() . '</div>');
        $out->addHTML('</div>'); // .stats-container
        
        $out->addHTML('</div>'); // .stats
    }
    
    /**
     * Get the required user rights to access this special page
     */
    protected function getRequiredRights() {
        return ['viewdashboard'];
    }
    
    public function userCanExecute(User $user) {
        return true; // Temporarily allow everyone to see if it works
    }
}
