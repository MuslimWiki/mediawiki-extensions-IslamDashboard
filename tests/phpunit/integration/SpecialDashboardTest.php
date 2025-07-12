<?php
/**
 * @covers \MediaWiki\Extension\IslamDashboard\SpecialDashboard
 */

namespace MediaWiki\Extension\IslamDashboard\Tests\Integration;

use MediaWiki\Extension\IslamDashboard\SpecialDashboard;
use MediaWiki\MediaWikiServices;
use MediaWiki\Tests\Integration\Permissions\MockAuthorityTrait;
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
    use MockAuthorityTrait;
    
    /** @var SpecialDashboard */
    private $specialPage;
    
    /** @var User */
    private $testUser;
    
    /** @var RequestContext */
    private $context;
    
    /** @var array */
    private $originalConfig;

    protected function setUp(): void {
        parent::setUp();
        
        // Store original config
        global $wgIslamDashboardConfig;
        $this->originalConfig = $wgIslamDashboardConfig;
        
        // Set up test configuration
        $this->setMwGlobals( [
            'wgIslamDashboardConfig' => [
                'enabled' => true,
                'defaultLayout' => 'default',
                'allowedWidgets' => [ 'WelcomeWidget', 'RecentActivityWidget', 'QuickActionsWidget' ]
            ]
        ]);
        
        // Create a test user with a name and real name
        $this->testUser = $this->getTestUser()->getUser();
        $this->testUser->setRealName( 'Test User' );
        
        // Create a test context with a mock authority
        $this->context = new RequestContext();
        $this->context->setUser( $this->testUser );
        
        // Create the special page
        $this->specialPage = new SpecialDashboard();
        $this->specialPage->setContext( $this->context );
    }
    
    protected function tearDown(): void {
        // Restore original config
        $this->setMwGlobals( [
            'wgIslamDashboardConfig' => $this->originalConfig
        ]);
        
        parent::tearDown();
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
        // Test with a user that has a real name
        $html = $this->specialPage->getUserProfileHTML();
        
        // Check if user profile contains the username and real name
        $this->assertStringContainsString( htmlspecialchars( $this->testUser->getName() ), $html );
        $this->assertStringContainsString( 'Test User', $html );
        $this->assertStringContainsString( 'user-avatar', $html );
        $this->assertStringContainsString( 'user-info', $html );
        
        // Test with a user that doesn't have a real name
        $userWithoutRealName = $this->getTestUser()->getUser();
        $this->context->setUser( $userWithoutRealName );
        $html = $this->specialPage->getUserProfileHTML();
        
        // Reset the context user
        $this->context->setUser( $this->testUser );
        
        $this->assertStringContainsString( htmlspecialchars( $userWithoutRealName->getName() ), $html );
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
        // Test with default configuration
        $html = $this->specialPage->getWidgetsHTML();
        
        // Check if widget containers are present
        $this->assertStringContainsString( 'dashboard-widget', $html );
        $this->assertStringContainsString( 'welcome-widget', $html );
        $this->assertStringContainsString( 'recent-activity-widget', $html );
        $this->assertStringContainsString( 'quick-actions-widget', $html );
        
        // Test with custom widget configuration
        $this->setMwGlobals( [
            'wgIslamDashboardConfig' => [
                'enabled' => true,
                'defaultLayout' => 'custom',
                'allowedWidgets' => [ 'WelcomeWidget' ]
            ]
        ]);
        
        $html = $this->specialPage->getWidgetsHTML();
        $this->assertStringContainsString( 'welcome-widget', $html );
        $this->assertStringNotContainsString( 'recent-activity-widget', $html );
        $this->assertStringNotContainsString( 'quick-actions-widget', $html );
    }

    /**
     * @covers ::getWelcomeWidgetHTML
     */
    public function testGetWelcomeWidgetHTML() {
        $html = $this->specialPage->getWelcomeWidgetHTML();
        
        // Check if welcome widget contains the username
        $this->assertStringContainsString( htmlspecialchars( $this->testUser->getName() ), $html );
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
