<?php

namespace MediaWiki\Extension\IslamDashboard\Tests\Unit;

use MediaWiki\Extension\IslamDashboard\Hooks as IslamDashboardHooks;
use MediaWiki\HookContainer\HookContainer;
use MediaWiki\MediaWikiServices;
use MediaWikiIntegrationTestCase;
use OutputPage;
use Skin;
use SkinTemplate;
use Title;
use User;

/**
 * @covers \MediaWiki\Extension\IslamDashboard\Hooks
 * @group Database
 * @group IslamDashboard
 */
class HooksTest extends MediaWikiIntegrationTestCase {

    /**
     * Test that hooks are properly registered
     */
    public function testHookRegistration() {
        $hookContainer = MediaWikiServices::getInstance()->getHookContainer();
        
        // Check that our hooks are registered
        $this->assertTrue(
            $hookContainer->isRegistered( 'BeforePageDisplay' ),
            'BeforePageDisplay hook is registered'
        );
        
        $this->assertTrue(
            $hookContainer->isRegistered( 'SkinTemplateNavigation::Universal' ),
            'SkinTemplateNavigation::Universal hook is registered'
        );
        
        $this->assertTrue(
            $hookContainer->isRegistered( 'GetPreferences' ),
            'GetPreferences hook is registered'
        );
        
        $this->assertTrue(
            $hookContainer->isRegistered( 'ResourceLoaderRegisterModules' ),
            'ResourceLoaderRegisterModules hook is registered'
        );
        
        $this->assertTrue(
            $hookContainer->isRegistered( 'PersonalUrls' ),
            'PersonalUrls hook is registered'
        );
        
        $this->assertTrue(
            $hookContainer->isRegistered( 'LoadExtensionSchemaUpdates' ),
            'LoadExtensionSchemaUpdates hook is registered'
        );
    }

    /**
     * Test BeforePageDisplay hook
     */
    public function testOnBeforePageDisplay() {
        // Create a mock OutputPage
        $output = $this->createMock( OutputPage::class );
        $output->expects( $this->once() )
            ->method( 'getTitle' )
            ->willReturn( $this->createMock( Title::class ) );
        
        // Create a mock Skin
        $skin = $this->createMock( Skin::class );
        
        // Call the hook
        $result = IslamDashboardHooks::onBeforePageDisplay( $output, $skin );
        
        // Verify the result
        $this->assertTrue( $result );
    }

    /**
     * Test SkinTemplateNavigation::Universal hook
     */
    public function testOnSkinTemplateNavigationUniversal() {
        // Create a mock SkinTemplate
        $skinTemplate = $this->createMock( SkinTemplate::class );
        
        // Create a links array that will be modified by the hook
        $links = [
            'user-menu' => [
                'userpage' => [
                    'text' => 'User Page',
                    'href' => '/wiki/User:TestUser',
                    'class' => ''
                ]
            ]
        ];
        
        // Call the hook
        $result = IslamDashboardHooks::onSkinTemplateNavigationUniversal( $skinTemplate, $links );
        
        // Verify the result
        $this->assertTrue( $result );
        
        // Check if the dashboard link was added
        $this->assertArrayHasKey( 'dashboard', $links['user-menu'] );
        $this->assertArrayHasKey( 'text', $links['user-menu']['dashboard'] );
        $this->assertArrayHasKey( 'href', $links['user-menu']['dashboard'] );
        $this->assertArrayHasKey( 'class', $links['user-menu']['dashboard'] );
    }

    /**
     * Test GetPreferences hook
     */
    public function testOnGetPreferences() {
        $preferences = [];
        $user = $this->createMock( User::class );
        
        // Call the hook
        $result = IslamDashboardHooks::onGetPreferences( $user, $preferences );
        
        // Verify the result
        $this->assertTrue( $result );
        $this->assertArrayHasKey( 'islamdashboard-prefs-showlink', $preferences );
    }
}
