<?php
/**
 * @covers \MediaWiki\Extension\IslamDashboard\SpecialDashboard
 */

namespace MediaWiki\Extension\IslamDashboard\Tests\Integration;

use MediaWiki\Extension\IslamDashboard\SpecialDashboard;
use MediaWiki\MediaWikiServices;
use MediaWikiIntegrationTestCase;
use RequestContext;
use Title;
use User;

/**
 * @group IslamDashboard
 * @coversDefaultClass \MediaWiki\Extension\IslamDashboard\SpecialDashboard
 * @group Database
 */
class SpecialDashboardTest extends MediaWikiIntegrationTestCase {

    /**
     * @var SpecialDashboard
     */
    private $specialPage;

    protected function setUp(): void {
        parent::setUp();
        
        // Set up test configuration
        $this->setMwGlobals( [
            'wgIslamDashboardConfig' => [
                'enabled' => true,
                'defaultLayout' => 'default',
                'allowedWidgets' => [ 'WelcomeWidget', 'RecentActivityWidget', 'QuickActionsWidget' ]
            ]
        ]);
        
        // Create a test user
        $this->testUser = $this->getTestUser()->getUser();
        
        // Create a test context
        $this->context = new RequestContext();
        $this->context->setUser( $this->testUser );
        $this->context->setTitle( Title::newFromText( 'Special:Dashboard' ) );
        
        // Create the special page
        $this->specialPage = new SpecialDashboard();
        $this->specialPage->setContext( $this->context );
    }

    /**
     * @covers ::__construct
     */
    public function testConstructor() {
        $this->assertInstanceOf( SpecialDashboard::class, $this->specialPage );
    }

    /**
     * @covers ::execute
     */
    public function testExecute() {
        // Execute the special page
        $this->specialPage->execute( null );
        
        // Get the output
        $output = $this->context->getOutput();
        $html = $output->getHTML();
        
        // Check if the main container is present
        $this->assertStringContainsString( 'islam-dashboard-container', $html );
        
        // Check if the header is present
        $this->assertStringContainsString( 'islam-dashboard-header', $html );
        
        // Check if the grid layout is present
        $this->assertStringContainsString( 'islam-dashboard-grid', $html );
        
        // Check if the sidebar is present
        $this->assertStringContainsString( 'islam-dashboard-sidebar', $html );
        
        // Check if the main content area is present
        $this->assertStringContainsString( 'islam-dashboard-main', $html );
    }

    /**
     * @covers ::getDashboardHTML
     */
    public function testGetDashboardHTML() {
        $html = $this->specialPage->getDashboardHTML();
        
        // Check the basic structure
        $this->assertStringContainsString( 'islam-dashboard-container', $html );
        $this->assertStringContainsString( 'islam-dashboard-header', $html );
        $this->assertStringContainsString( 'islam-dashboard-grid', $html );
        $this->assertStringContainsString( 'islam-dashboard-sidebar', $html );
        $this->assertStringContainsString( 'islam-dashboard-main', $html );
    }

    /**
     * @covers ::getSidebarHTML
     */
    public function testGetSidebarHTML() {
        $html = $this->specialPage->getSidebarHTML();
        
        // Check if sidebar contains user profile and navigation
        $this->assertStringContainsString( 'user-profile', $html );
        $this->assertStringContainsString( 'dashboard-navigation', $html );
    }

    /**
     * @covers ::getUserProfileHTML
     */
    public function testGetUserProfileHTML() {
        $html = $this->specialPage->getUserProfileHTML();
        
        // Check if user profile contains the username
        $this->assertStringContainsString( $this->testUser->getName(), $html );
        $this->assertStringContainsString( 'user-avatar', $html );
        $this->assertStringContainsString( 'user-info', $html );
    }

    /**
     * @covers ::getNavigationMenuHTML
     */
    public function testGetNavigationMenuHTML() {
        $html = $this->specialPage->getNavigationMenuHTML();
        
        // Check if navigation menu is rendered
        $this->assertStringContainsString( 'dashboard-navigation', $html );
    }

    /**
     * @covers ::getWidgetsHTML
     */
    public function testGetWidgetsHTML() {
        $html = $this->specialPage->getWidgetsHTML();
        
        // Check if widget containers are present
        $this->assertStringContainsString( 'dashboard-widget', $html );
        $this->assertStringContainsString( 'welcome-widget', $html );
        $this->assertStringContainsString( 'recent-activity-widget', $html );
        $this->assertStringContainsString( 'quick-actions-widget', $html );
    }

    /**
     * @covers ::getWelcomeWidgetHTML
     */
    public function testGetWelcomeWidgetHTML() {
        $html = $this->specialPage->getWelcomeWidgetHTML();
        
        // Check if welcome widget contains the username
        $this->assertStringContainsString( $this->testUser->getName(), $html );
        $this->assertStringContainsString( 'welcome-widget', $html );
    }

    /**
     * @covers ::getRecentActivityWidgetHTML
     */
    public function testGetRecentActivityWidgetHTML() {
        $html = $this->specialPage->getRecentActivityWidgetHTML();
        
        // Check if recent activity widget is present
        $this->assertStringContainsString( 'recent-activity-widget', $html );
    }

    /**
     * @covers ::getQuickActionsWidgetHTML
     */
    public function testGetQuickActionsWidgetHTML() {
        $html = $this->specialPage->getQuickActionsWidgetHTML();
        
        // Check if quick actions widget is present
        $this->assertStringContainsString( 'quick-actions-widget', $html );
    }

    /**
     * @covers ::getRightSidebarHTML
     */
    public function testGetRightSidebarHTML() {
        $html = $this->specialPage->getRightSidebarHTML();
        
        // Check if right sidebar is present
        $this->assertStringContainsString( 'islam-dashboard-right-sidebar', $html );
    }

    /**
     * @covers ::getNotificationsWidgetHTML
     */
    public function testGetNotificationsWidgetHTML() {
        $html = $this->specialPage->getNotificationsWidgetHTML();
        
        // Check if notifications widget is present
        $this->assertStringContainsString( 'notifications-widget', $html );
    }

    /**
     * @covers ::getQuickLinksWidgetHTML
     */
    public function testGetQuickLinksWidgetHTML() {
        $html = $this->specialPage->getQuickLinksWidgetHTML();
        
        // Check if quick links widget is present
        $this->assertStringContainsString( 'quick-links-widget', $html );
    }
}
