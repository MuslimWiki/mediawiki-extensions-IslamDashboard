<?php
/**
 * @covers \MediaWiki\Extension\IslamDashboard\Navigation\NavigationManager
 */

namespace MediaWiki\Extension\IslamDashboard\Tests\Unit\Navigation;

namespace MediaWiki\Extension\IslamDashboard\Tests\Unit\Navigation;

use MediaWiki\Extension\IslamDashboard\Navigation\NavigationManager;
use MediaWiki\MediaWikiServices;
use MediaWikiIntegrationTestCase;

/**
 * @covers \MediaWiki\Extension\IslamDashboard\Navigation\NavigationManager
 * @group IslamDashboard
 */

/**
 * @group IslamDashboard
 * @coversDefaultClass \NavigationManager
 */
class NavigationManagerTest extends MediaWikiIntegrationTestCase {

    /** @var NavigationManager */
    private $navManager;

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
        
        // Create a test instance
        $this->navManager = NavigationManager::getInstance();
        
        // Reset the navigation structure before each test
        $reflection = new \ReflectionClass($this->navManager);
        $property = $reflection->getProperty('navigation');
        $property->setAccessible(true);
        $property->setValue($this->navManager, []);
    }

    /**
     * @covers ::getInstance
     */
    /**
     * @covers ::getInstance
     */
    public function testGetInstance() {
        $this->assertInstanceOf(
            NavigationManager::class,
            $this->navManager,
            'getInstance should return a NavigationManager instance'
        );

        $instance1 = NavigationManager::getInstance();
        $instance2 = NavigationManager::getInstance();
        $this->assertEquals(
            $instance1, 
            $instance2, 
            'getInstance should return the same instance'
        );
    }

    /**
     * @covers ::registerNavigationItem
     * @covers ::getNavigationForUser
     */
    public function testRegisterAndGetNavigationItem() {
        $testItem = [
            'label' => 'test-label',
            'icon' => 'test-icon',
            'permission' => 'read',
            'order' => 100
        ];
        
        // Register a test item
        $this->navManager->registerNavigationItem( 'test-section', 'test-item', $testItem );
        
        // Get navigation for a user with read permission
        $user = $this->getTestUser( [ 'read' ] )->getUser();
        $navigation = $this->navManager->getNavigationForUser( $user );
        
        // Verify the item was registered and is accessible
        $this->assertArrayHasKey( 'test-section', $navigation );
        $this->assertArrayHasKey( 'items', $navigation['test-section'] );
        $this->assertArrayHasKey( 'test-item', $navigation['test-section']['items'] );
        $this->assertEquals( 'test-label', $navigation['test-section']['items']['test-item']['label'] );
    }

    /**
     * @covers ::getNavigationForUser
     */
    public function testGetNavigationForUserWithRestrictedAccess() {
        $restrictedItem = [
            'label' => 'restricted-item',
            'icon' => 'lock',
            'permission' => 'restricted-permission',
            'order' => 100
        ];
        
        // Register a restricted item
        $this->navManager->registerNavigationItem( 'test-section', 'restricted-item', $restrictedItem );
        
        // Get navigation for a user without the required permission
        $user = $this->getTestUser( [ 'read' ] )->getUser();
        $navigation = $this->navManager->getNavigationForUser( $user );
        
        // The section should be empty if no accessible items
        $this->assertArrayNotHasKey( 'test-section', $navigation );
    }

    /**
     * @covers ::registerNavigationSection
     * @covers ::getNavigationForUser
     */
    public function testRegisterNavigationSection() {
        $testSection = [
            'label' => 'test-section',
            'icon' => 'test-icon',
            'permission' => 'read',
            'items' => [
                'item1' => [
                    'label' => 'Item 1',
                    'icon' => 'item1-icon',
                    'permission' => 'read',
                    'order' => 10
                ],
                'item2' => [
                    'label' => 'Item 2',
                    'icon' => 'item2-icon',
                    'permission' => 'read',
                    'order' => 20
                ]
            ]
        ];
        
        // Register a test section
        $this->navManager->registerNavigationSection( 'test-section', $testSection );
        
        // Get navigation for a user with read permission
        $user = $this->getTestUser( [ 'read' ] )->getUser();
        $navigation = $this->navManager->getNavigationForUser( $user );
        
        // Verify the section was registered with its items
        $this->assertArrayHasKey( 'test-section', $navigation );
        $this->assertEquals( 2, count( $navigation['test-section']['items'] ) );
        $this->assertArrayHasKey( 'item1', $navigation['test-section']['items'] );
        $this->assertArrayHasKey( 'item2', $navigation['test-section']['items'] );
    }

    /**
     * @covers ::getNavigationForUser
     */
    public function testNavigationSorting() {
        // Register items in reverse order
        $this->navManager->registerNavigationItem( 'test-section', 'item3', [
            'label' => 'Item 3',
            'icon' => 'icon3',
            'permission' => 'read',
            'order' => 30
        ]);
        
        $this->navManager->registerNavigationItem( 'test-section', 'item1', [
            'label' => 'Item 1',
            'icon' => 'icon1',
            'permission' => 'read',
            'order' => 10
        ]);
        
        $this->navManager->registerNavigationItem( 'test-section', 'item2', [
            'label' => 'Item 2',
            'icon' => 'icon2',
            'permission' => 'read',
            'order' => 20
        ]);
        
        // Get navigation for a user with read permission
        $user = $this->getTestUser( [ 'read' ] )->getUser();
        $navigation = $this->navManager->getNavigationForUser( $user );
        
        // Verify items are sorted by order
        $items = array_keys( $navigation['test-section']['items'] );
        $this->assertEquals( [ 'item1', 'item2', 'item3' ], $items );
    }
}
