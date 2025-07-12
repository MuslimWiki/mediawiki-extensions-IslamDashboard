<?php
/**
 * Navigation Manager for IslamDashboard
 *
 * @file
 * @ingroup Extensions
 */

namespace MediaWiki\Extension\IslamDashboard\Navigation;

use MediaWiki\MediaWikiServices;
use MediaWiki\Context\RequestContext;
use User;

class NavigationManager {
    /** @var NavigationManager|null Singleton instance */
    private static $instance = null;

    /** @var array Navigation structure */
    private $navigation = [];

    /** @var array Default navigation structure */
    private $defaultNavigation = [
        'dashboard' => [
            'label' => 'islamdashboard-dashboard',
            'icon' => 'dashboard',
            'items' => [
                'overview' => [
                    'label' => 'islamdashboard-overview',
                    'icon' => 'home',
                    'permission' => 'view-dashboard',
                    'order' => 10
                ],
                'activity' => [
                    'label' => 'islamdashboard-activity',
                    'icon' => 'recentChanges',
                    'permission' => 'view-dashboard',
                    'order' => 20
                ]
            ]
        ],
        'content' => [
            'label' => 'islamdashboard-content',
            'icon' => 'article',
            'items' => [
                'create-page' => [
                    'label' => 'islamdashboard-createpage',
                    'icon' => 'add',
                    'permission' => 'createpage',
                    'order' => 10
                ],
                'create-blog' => [
                    'label' => 'islamdashboard-createblog',
                    'icon' => 'article',
                    'permission' => 'createpage',
                    'order' => 20
                ],
                'upload' => [
                    'label' => 'islamdashboard-upload',
                    'icon' => 'upload',
                    'permission' => 'upload',
                    'order' => 30
                ],
                'categories' => [
                    'label' => 'islamdashboard-categories',
                    'icon' => 'folder',
                    'permission' => 'read',
                    'order' => 40
                ]
            ]
        ],
        'users' => [
            'label' => 'islamdashboard-users',
            'icon' => 'userGroup',
            'permission' => 'userrights',
            'items' => [
                'user-list' => [
                    'label' => 'islamdashboard-userlist',
                    'icon' => 'userGroup',
                    'permission' => 'userrights',
                    'order' => 10
                ],
                'user-rights' => [
                    'label' => 'islamdashboard-userrights',
                    'icon' => 'userRights',
                    'permission' => 'userrights',
                    'order' => 20
                ]
            ]
        ],
        'site' => [
            'label' => 'islamdashboard-site',
            'icon' => 'settings',
            'permission' => 'siteadmin',
            'items' => [
                'site-notice' => [
                    'label' => 'islamdashboard-sitenotice',
                    'icon' => 'notice',
                    'permission' => 'siteadmin',
                    'order' => 10
                ],
                'announcements' => [
                    'label' => 'islamdashboard-announcements',
                    'icon' => 'announce',
                    'permission' => 'siteadmin',
                    'order' => 20
                ]
            ]
        ]
    ];

    /**
     * Get the singleton instance
     *
     * @return NavigationManager
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor
     */
    private function __construct() {
        $this->initializeNavigation();
    }

    /**
     * Initialize the navigation structure
     */
    private function initializeNavigation(): void {
        global $wgHooks;
        
        // Start with default navigation
        $this->navigation = $this->defaultNavigation;
        
        // Allow other extensions to modify the navigation
        $wgHooks['IslamDashboardNavigation'][] = [ $this, 'onNavigationInitialized' ];
        
        // Sort items within each section
        $this->sortNavigationItems();
    }

    /**
     * Hook handler for modifying the navigation
     *
     * @param array &$navigation Reference to the navigation array
     * @return bool Always true
     */
    public function onNavigationInitialized( &$navigation ): bool {
        // Allow other extensions to modify the navigation
        // Example:
        // $navigation['section']['items']['new-item'] = [
        //     'label' => 'New Item',
        //     'icon' => 'new',
        //     'url' => '/path/to/item',
        //     'permission' => 'some-permission',
        //     'order' => 100
        // ];
        
        return true;
    }

    /**
     * Sort items within each navigation section
     */
    private function sortNavigationItems(): void {
        foreach ($this->navigation as &$section) {
            if (isset($section['items']) && is_array($section['items'])) {
                uasort($section['items'], function($a, $b) {
                    $orderA = $a['order'] ?? 999;
                    $orderB = $b['order'] ?? 999;
                    return $orderA <=> $orderB;
                });
            }
        }
    }

    /**
     * Get the navigation structure for a specific user
     *
     * @param User|null $user User to check permissions for
     * @return array Filtered navigation structure
     */
    public function getNavigationForUser( ?User $user = null ): array {
        if ($user === null) {
            $user = RequestContext::getMain()->getUser();
        }

        $filteredNavigation = [];
        
        foreach ($this->navigation as $sectionId => $section) {
            // Check if user has permission to see this section
            if (isset($section['permission']) && !$user->isAllowed($section['permission'])) {
                continue;
            }

            $filteredSection = $section;
            $filteredSection['items'] = [];

            // Filter items in the section
            if (isset($section['items']) && is_array($section['items'])) {
                foreach ($section['items'] as $itemId => $item) {
                    if (!isset($item['permission']) || $user->isAllowed($item['permission'])) {
                        $filteredSection['items'][$itemId] = $item;
                    }
                }
            }

            // Only add section if it has visible items or is explicitly allowed
            if (!empty($filteredSection['items']) || !isset($section['items'])) {
                $filteredNavigation[$sectionId] = $filteredSection;
            }
        }

        return $filteredNavigation;
    }

    /**
     * Add a new navigation section
     *
     * @param string $sectionId Unique section ID
     * @param array $section Section configuration
     * @return bool Success
     */
    public function addSection( string $sectionId, array $section ): bool {
        if (isset($this->navigation[$sectionId])) {
            return false; // Section already exists
        }

        $this->navigation[$sectionId] = $section;
        $this->sortNavigationItems();
        return true;
    }

    /**
     * Remove a navigation section
     *
     * @param string $sectionId Section ID to remove
     * @return bool Success
     */
    public function removeSection( string $sectionId ): bool {
        if (!isset($this->navigation[$sectionId])) {
            return false; // Section doesn't exist
        }

        unset($this->navigation[$sectionId]);
        return true;
    }

    /**
     * Add an item to a navigation section
     *
     * @param string $sectionId Section ID
     * @param string $itemId Item ID
     * @param array $item Item configuration
     * @return bool Success
     */
    public function addItem( string $sectionId, string $itemId, array $item ): bool {
        if (!isset($this->navigation[$sectionId])) {
            return false; // Section doesn't exist
        }

        if (isset($this->navigation[$sectionId]['items'][$itemId])) {
            return false; // Item already exists
        }

        $this->navigation[$sectionId]['items'][$itemId] = $item;
        $this->sortNavigationItems();
        return true;
    }

    /**
     * Remove an item from a navigation section
     *
     * @param string $sectionId Section ID
     * @param string $itemId Item ID to remove
     * @return bool Success
     */
    public function removeItem( string $sectionId, string $itemId ): bool {
        if (!isset($this->navigation[$sectionId]['items'][$itemId])) {
            return false; // Item doesn't exist
        }

        unset($this->navigation[$sectionId]['items'][$itemId]);
        return true;
    }
}
