<?php
/**
 * Test helper for the IslamDashboard extension
 *
 * @file
 * @since 1.0.0
 */

use MediaWiki\MediaWikiServices;

/**
 * Test helper class for the IslamDashboard extension
 */
class IslamDashboardTestHelper {
    /**
     * Create a test user with specified rights
     *
     * @param array $rights User rights
     * @return TestUser
     */
    public static function getTestUser( array $rights = [] ) {
        $testUser = new TestUser(
            'TestUser' . mt_rand(),
            'Test User',
            'test@example.com',
            [ 'testuser' ]
        );

        if ( !empty( $rights ) ) {
            $user = $testUser->getUser();
            $userGroupManager = MediaWikiServices::getInstance()->getUserGroupManager();
            
            // Add user to groups that have the specified rights
            foreach ( $rights as $right ) {
                $group = 'testgroup_' . $right;
                $userGroupManager->addUserToGroup( $user, $group );
                
                // Set up group rights if they don't exist
                global $wgGroupPermissions;
                if ( !isset( $wgGroupPermissions[$group][$right] ) ) {
                    $wgGroupPermissions[$group][$right] = true;
                }
            }
        }

        return $testUser;
    }

    /**
     * Get a test context with a specified user
     *
     * @param User $user User to set in the context
     * @return RequestContext
     */
    public static function getTestContext( User $user ) {
        $context = new RequestContext();
        $context->setUser( $user );
        return $context;
    }

    /**
     * Set up test configuration
     */
    public static function setupTestConfig() {
        global $wgIslamDashboardConfig;
        
        // Set default test configuration
        $wgIslamDashboardConfig = [
            'enabled' => true,
            'defaultLayout' => 'default',
            'maxWidgetsPerRow' => 3,
            'enableCustomWidgets' => true,
            'enableAnalytics' => false,
            'allowedWidgets' => [
                'WelcomeWidget',
                'RecentActivityWidget',
                'QuickActionsWidget'
            ]
        ];
    }
}
