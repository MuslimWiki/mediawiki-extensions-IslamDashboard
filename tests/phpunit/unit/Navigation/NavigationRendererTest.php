<?php
/**
 * @covers \MediaWiki\Extension\IslamDashboard\Navigation\NavigationRenderer
 */

namespace MediaWiki\Extension\IslamDashboard\Tests\Unit\Navigation;

use MediaWiki\Extension\IslamDashboard\Navigation\NavigationManager;
use MediaWiki\Extension\IslamDashboard\Navigation\NavigationRenderer;
use MediaWiki\MediaWikiServices;
use MediaWikiIntegrationTestCase;
use RequestContext;
use User;

/**
 * @group IslamDashboard
 * @coversDefaultClass \MediaWiki\Extension\IslamDashboard\Navigation\NavigationRenderer
 */
class NavigationRendererTest extends MediaWikiIntegrationTestCase {

    /** @var NavigationManager */
    private $navManager;

    /** @var NavigationRenderer */
    private $navRenderer;

    /** @var User */
    private $testUser;

    protected function setUp(): void {
        parent::setUp();

        // Set up test configuration
        $this->setMwGlobals([
            'wgIslamDashboardConfig' => [
                'enabled' => true,
                'defaultLayout' => 'default',
                'allowedWidgets' => ['TestWidget']
            ]
        ]);
        
        // Create a test user
        $this->testUser = $this->getTestUser()->getUser();
        
        // Set up navigation manager with test data
        $this->navManager = NavigationManager::getInstance();
        
        // Reset the navigation structure before each test
        $reflection = new \ReflectionClass($this->navManager);
        $property = $reflection->getProperty('navigation');
        $property->setAccessible(true);
        $property->setValue($this->navManager, []);
        
        // Initialize the renderer with navigation manager
        $this->navRenderer = new NavigationRenderer($this->navManager);
        
        // Add test navigation items
        $this->navManager->registerNavigationSection('test-section', [
            'label' => 'Test Section',
            'icon' => 'test-icon',
            'permission' => 'read',
            'items' => [
                'item1' => [
                    'label' => 'Item 1',
                    'icon' => 'item1-icon',
                    'href' => '/wiki/Item1',
                    'permission' => 'read',
                    'order' => 10
                ],
                'item2' => [
                    'label' => 'Item 2',
                    'icon' => 'item2-icon',
                    'href' => '/wiki/Item2',
                    'permission' => 'read',
                    'order' => 20
                ]
            ]
        ]);
        
        // Create a test context
        $context = new RequestContext();
        $context->setUser( $this->getTestUser( [ 'read' ] )->getUser() );
        
        // Initialize the renderer
        $this->navRenderer = new NavigationRenderer( $this->navManager );
    }

    /**
     * @covers ::__construct
     */
    public function testConstructor() {
        $this->assertInstanceOf( NavigationRenderer::class, $this->navRenderer );
    }

    /**
     * @covers ::getNavigationHTML
     */
    public function testGetNavigationHTML() {
        $html = $this->navRenderer->getNavigationHTML([
            'user' => $this->getTestUser( [ 'read' ] )->getUser(),
            'currentPath' => '/wiki/Main_Page',
            'collapsed' => false,
            'mobile' => false
        ]);
        
        // Basic HTML structure checks
        $this->assertStringContainsString( '<nav', $html );
        $this->assertStringContainsString( 'dashboard-navigation', $html );
        
        // Check if test section is rendered
        $this->assertStringContainsString( 'Test Section', $html );
        
        // Check if items are rendered
        $this->assertStringContainsString( 'Item 1', $html );
        $this->assertStringContainsString( 'Item 2', $html );
        $this->assertStringContainsString( '/wiki/Item1', $html );
        $this->assertStringContainsString( '/wiki/Item2', $html );
    }

    /**
     * @covers ::getNavigationHTML
     */
    public function testGetNavigationHTMLCollapsed() {
        $html = $this->navRenderer->getNavigationHTML([
            'user' => $this->getTestUser( [ 'read' ] )->getUser(),
            'currentPath' => '/wiki/Main_Page',
            'collapsed' => true,
            'mobile' => false
        ]);
        
        $this->assertStringContainsString( 'collapsed', $html );
    }

    /**
     * @covers ::getNavigationHTML
     */
    public function testGetNavigationHTMLMobile() {
        $html = $this->navRenderer->getNavigationHTML([
            'user' => $this->getTestUser( [ 'read' ] )->getUser(),
            'currentPath' => '/wiki/Main_Page',
            'collapsed' => false,
            'mobile' => true
        ]);
        
        $this->assertStringContainsString( 'mobile', $html );
    }

    /**
     * @covers ::getSectionHTML
     */
    public function testGetSectionHTML() {
        $section = [
            'label' => 'Test Section',
            'icon' => 'test-icon',
            'expanded' => true,
            'items' => [
                'item1' => [
                    'label' => 'Item 1',
                    'icon' => 'item1-icon',
                    'href' => '/wiki/Item1',
                    'active' => false,
                    'order' => 10
                ]
            ]
        ];
        
        $html = $this->navRenderer->getSectionHTML( 'test-section', $section, [
            'currentPath' => '/wiki/Main_Page',
            'mobile' => false
        ]);
        
        $this->assertStringContainsString( 'Test Section', $html );
        $this->assertStringContainsString( 'Item 1', $html );
        $this->assertStringContainsString( '/wiki/Item1', $html );
    }

    /**
     * @covers ::getItemHTML
     */
    public function testGetItemHTML() {
        $item = [
            'label' => 'Test Item',
            'icon' => 'test-icon',
            'href' => '/wiki/Test',
            'active' => false,
            'items' => []
        ];
        
        $html = $this->navRenderer->getItemHTML( 'test-item', $item, [
            'currentPath' => '/wiki/Main_Page',
            'mobile' => false
        ]);
        
        $this->assertStringContainsString( 'Test Item', $html );
        $this->assertStringContainsString( '/wiki/Test', $html );
        $this->assertStringContainsString( 'test-icon', $html );
    }

    /**
     * @covers ::isItemActive
     */
    public function testIsItemActive() {
        // Test exact path match
        $result = $this->invokeMethod( $this->navRenderer, 'isItemActive', [
            [ 'href' => '/wiki/Test' ],
            '/wiki/Test'
        ]);
        $this->assertTrue( $result, 'Should be active when path exactly matches' );
        
        // Test with query parameters
        $result = $this->invokeMethod( $this->navRenderer, 'isItemActive', [
            [ 'href' => '/wiki/Test?foo=bar' ],
            '/wiki/Test?foo=bar'
        ]);
        $this->assertTrue( $result, 'Should be active when current path matches with query parameters' );
        
        // Test with different paths
        $result = $this->invokeMethod( $this->navRenderer, 'isItemActive', [
            [ 'href' => '/wiki/Test' ],
            '/wiki/Other'
        ]);
        $this->assertFalse( $result, 'Should not be active when current path does not match' );
        
        // Test with active pattern
        $result = $this->invokeMethod( $this->navRenderer, 'isItemActive', [
            [ 'activePattern' => '#^/wiki/Test#' ],
            '/wiki/Test/Subpage'
        ]);
        $this->assertTrue( $result, 'Should be active when pattern matches' );
    }
    
    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    private function invokeMethod( &$object, $methodName, array $parameters = [] ) {
        $reflection = new \ReflectionClass( get_class( $object ) );
        $method = $reflection->getMethod( $methodName );
        $method->setAccessible( true );
        
        return $method->invokeArgs( $object, $parameters );
    }
}
