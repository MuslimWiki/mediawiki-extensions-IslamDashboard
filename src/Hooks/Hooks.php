<?php
/**
 * Hooks for the IslamDashboard extension
 *
 * @file
 * @ingroup Extensions
 * @license GPL-3.0-or-later
 */

namespace MediaWiki\Extension\IslamDashboard\Hooks;

use MediaWiki\MediaWikiServices;
use MediaWiki\Extension\IslamDashboard\WidgetManager;
use MediaWiki\SpecialPage\SpecialPage;
// Special page hooks
use OutputPage;
use Skin;
use SkinTemplate;
use Title;
use User;
use DatabaseUpdater;

class IslamDashboardHooks {
    /**
     * Add IslamDashboard link to user menu
     *
     * @param SkinTemplate $sktemplate Skin template object
     * @param array &$links Navigation links
     * @return bool Always true
     */
    public static function onSkinTemplateNavigationUniversal( $sktemplate, &$links ) {
        return self::onSkinTemplateNavigation( $sktemplate, $links );
    }
    
    /**
     * Add IslamDashboard link to user menu (legacy)
     *
     * @param SkinTemplate $sktemplate Skin template object
     * @param array &$links Navigation links
     * @return bool Always true
     */
    public static function onSkinTemplateNavigation( $sktemplate, &$links ) {
        global $wgIslamDashboardShowInUserMenu;
        
        // Check if IslamDashboard should be shown in user menu
        if ( !$wgIslamDashboardShowInUserMenu ) {
            return true;
        }
        
        // Add IslamDashboard link to user menu
        if ( isset( $links['user-menu'] ) ) {
            $links['user-menu']['IslamDashboard'] = [
                'text' => wfMessage( 'islamdashboard-IslamDashboard' )->text(),
                'href' => SpecialPage::getTitleFor( 'IslamDashboard' )->getLocalURL(),
                'class' => 'IslamDashboard-link',
                'active' => ( $sktemplate->getTitle()->isSpecial( 'IslamDashboard' ) )
            ];
        }
        
        return true;
    }
    
    /**
     * Add IslamDashboard modules to the page
     *
     * @param OutputPage $out Output page object
     * @param Skin $skin Skin object
     * @return bool Always true
     */
    public static function onBeforePageDisplay( OutputPage $out, Skin $skin ) {
        // Add IslamDashboard styles and scripts to all pages
        $out->addModules( 'ext.islamDashboard' );
        
        // Only add modules on the IslamDashboard page
        if ( $out->getTitle()->isSpecial( 'IslamDashboard' ) ) {
            $out->addModuleStyles( 'ext.islamDashboard.styles' );
            
            // Add widget-specific modules
            $widgetManager = WidgetManager::getInstance();
            $user = $out->getUser();
            
            // Add modules for all visible widgets
            $modules = $widgetManager->getRequiredModules( $user );
            foreach ( $modules as $module ) {
                $out->addModules( $module );
            }
            
            // Add widget layout data
            $widgetData = [
                'widgets' => $widgetManager->getWidgetDefinitions( $user ),
                'layout' => $widgetManager->getUserWidgetLayout( $user ),
                'hiddenWidgets' => $widgetManager->getHiddenWidgets( $user )
            ];
            
            $out->addJsConfigVars( 'wgIslamDashboard', $widgetData );
        }
        
        return true;
    }
    
    /**
     * Add IslamDashboard link to personal URLs
     *
     * @param array &$personal_urls Array of personal URLs
     * @param Title &$title Title of the current page
     * @param SkinTemplate $sktemplate Skin template object
     * @return bool Always true
     */
    public static function onPersonalUrls( &$personal_urls, &$title, $sktemplate ) {
        global $wgIslamDashboardShowInUserMenu;
        
        // Check if IslamDashboard should be shown in personal URLs
        if ( !$wgIslamDashboardShowInUserMenu ) {
            return true;
        }
        
        // Add IslamDashboard link to personal URLs
        $personal_urls = array_merge(
            [ 'IslamDashboard' => [
                'text' => wfMessage( 'islamdashboard-IslamDashboard' )->text(),
                'href' => SpecialPage::getTitleFor( 'IslamDashboard' )->getLocalURL(),
                'active' => $title->isSpecial( 'IslamDashboard' )
            ] ],
            $personal_urls
        );
        
        return true;
    }
    
    /**
     * Handle SpecialPage_initList hook
     * 
     * @param array &$list List of special pages
     * @return void
     */
    public static function onSpecialPage_initList( array &$list ) {
        global $wgOut;
        
        try {
            // Register the special page with only the required services
            $list['IslamDashboard'] = [
                'class' => 'MediaWiki\\Extension\\IslamDashboard\\Special\\SpecialIslamDashboard',
                'services' => [
                    'DBLoadBalancer',
                    'LinkRenderer',
                    'UserOptionsLookup',
                    'UserOptionsManager',
                    'TitleFactory',
                    'UserFactory',
                    'PermissionManager',
                    'NamespaceInfo',
                    'SpecialPageFactory'
                ]
            ];
            
        } catch ( \Exception $e ) {
            // Log the error but don't break the page
            $errorMsg = 'Error registering IslamDashboard special page: ' . $e->getMessage();
            wfDebugLog( 'IslamDashboard', $errorMsg );
            if ( $wgOut ) {
                $wgOut->addHTML( '<!-- ' . htmlspecialchars( $errorMsg ) . ' -->' );
            }
            unset( $list['IslamDashboard'] );
        }
    }
    
    /**
     * Initialize the extension
     */
    /**
     * Initialize the extension
     * @return void
     */
    public static function onExtensionLoad() {
        // Define all global variables with default values first
        global $wgIslamDashboardEnable, $wgIslamDashboardDefaultLayout,
               $wgIslamDashboardShowInUserMenu, $wgIslamDashboardEnableAnalytics,
               $wgIslamDashboardWidgets, $wgIslamDashboardMaxActivityItems,
               $wgIslamDashboardAllowWidgetCustomization, $wgIslamDashboardViewPermission,
               $wgIslamDashboardCustomizePermission, $wgIslamDashboardViewAllPermission,
               $wgMessagesDirs, $wgExtensionMessagesFiles, $wgAPIModules,
               $wgSpecialPages, $wgSpecialPageGroups, $wgResourceModules, $wgHooks;
               
        // Ensure all global arrays are properly initialized
        $wgMessagesDirs = is_array( $wgMessagesDirs ?? null ) ? $wgMessagesDirs : [];
        $wgExtensionMessagesFiles = is_array( $wgExtensionMessagesFiles ?? null ) ? $wgExtensionMessagesFiles : [];
        $wgAPIModules = is_array( $wgAPIModules ?? null ) ? $wgAPIModules : [];
        $wgSpecialPages = is_array( $wgSpecialPages ?? null ) ? $wgSpecialPages : [];
        $wgSpecialPageGroups = is_array( $wgSpecialPageGroups ?? null ) ? $wgSpecialPageGroups : [];
        $wgResourceModules = is_array( $wgResourceModules ?? null ) ? $wgResourceModules : [];
        $wgHooks = is_array( $wgHooks ?? null ) ? $wgHooks : [];
        
        // Register message files and other configurations
        $dir = dirname( __DIR__ ) . '/i18n';
        
        // Update local arrays
        $wgMessagesDirs['IslamDashboard'] = $dir;
        $wgExtensionMessagesFiles['IslamDashboard'] = $dir . '/IslamDashboard.i18n.php';
        $wgExtensionMessagesFiles['IslamDashboardAlias'] = $dir . '/IslamDashboard.alias.php';
        
        // Register API modules
        $wgAPIModules['islamdashboard'] = 'MediaWiki\\Extension\\IslamDashboard\\ApiIslamDashboard';
        
        // Register special page with correct namespace and group
        $wgSpecialPages['IslamDashboard'] = 'MediaWiki\\Extension\\IslamDashboard\\Special\\SpecialIslamDashboard';
        $wgSpecialPageGroups['IslamDashboard'] = 'users';
        
        // Update global scope
        $GLOBALS['wgMessagesDirs'] = $wgMessagesDirs;
        $GLOBALS['wgExtensionMessagesFiles'] = $wgExtensionMessagesFiles;
        $GLOBALS['wgAPIModules'] = $wgAPIModules;
        $GLOBALS['wgSpecialPages'] = $wgSpecialPages;
        $GLOBALS['wgSpecialPageGroups'] = $wgSpecialPageGroups;
        $GLOBALS['wgResourceModules'] = $wgResourceModules;
        $GLOBALS['wgHooks'] = $wgHooks;
        
        // Set default configuration
        $wgIslamDashboardEnable = $wgIslamDashboardEnable ?? true;
        $wgIslamDashboardDefaultLayout = $wgIslamDashboardDefaultLayout ?? 'default';
        $wgIslamDashboardShowInUserMenu = $wgIslamDashboardShowInUserMenu ?? true;
        $wgIslamDashboardEnableAnalytics = $wgIslamDashboardEnableAnalytics ?? false;
        
        // Widget configuration
        $wgIslamDashboardWidgets = $wgIslamDashboardWidgets ?? [
            'welcome' => true,
            'recent-activity' => true,
            'quick-actions' => true,
            'notifications' => true,
            'quick-links' => true
        ];
        
        $wgIslamDashboardMaxActivityItems = $wgIslamDashboardMaxActivityItems ?? 10;
        $wgIslamDashboardAllowWidgetCustomization = $wgIslamDashboardAllowWidgetCustomization ?? true;
        
        // Permission configuration
        $wgIslamDashboardViewPermission = $wgIslamDashboardViewPermission ?? 'read';
        $wgIslamDashboardCustomizePermission = $wgIslamDashboardCustomizePermission ?? 'editmyoptions';
        $wgIslamDashboardViewAllPermission = $wgIslamDashboardViewAllPermission ?? 'viewdashboard-all';
        

        
        // Resource modules are now defined in extension.json
        // This ensures proper loading and avoids duplication
        $resourceModules = [];
        
        // Initialize resource modules array
        $resourceModules = is_array( $resourceModules ) ? $resourceModules : [];
        
        // Initialize global resource modules if not set
        if ( !isset( $GLOBALS['wgResourceModules'] ) || !is_array( $GLOBALS['wgResourceModules'] ) ) {
            $GLOBALS['wgResourceModules'] = [];
        }
        
        // Merge resource modules
        $GLOBALS['wgResourceModules'] = array_merge( 
            $GLOBALS['wgResourceModules'] ?? [], 
            $resourceModules 
        );
        
        // Initialize wgHooks if not set
        if ( !isset( $GLOBALS['wgHooks'] ) || !is_array( $GLOBALS['wgHooks'] ) ) {
            $GLOBALS['wgHooks'] = [];
        }
        
        // Initialize hook arrays if they don't exist
        $hookNames = [
            'BeforePageDisplay',
            'SkinTemplateNavigation::Universal',
            'PersonalUrls',
            'GetPreferences',
            'UserGetRights',
            'LoadExtensionSchemaUpdates',
            'GetAllRights',
            'GetPermissions',
            'SpecialPage_initList'
        ];
        
        foreach ( $hookNames as $hook ) {
            if ( !isset( $GLOBALS['wgHooks'][$hook] ) || !is_array( $GLOBALS['wgHooks'][$hook] ) ) {
                $GLOBALS['wgHooks'][$hook] = [];
            }
        }
        
        // Register hooks
        $hookClass = 'MediaWiki\\Extension\\IslamDashboard\\IslamDashboardHooks';
        $GLOBALS['wgHooks']['BeforePageDisplay'][] = "$hookClass::onBeforePageDisplay";
        $GLOBALS['wgHooks']['SkinTemplateNavigation::Universal'][] = "$hookClass::onSkinTemplateNavigationUniversal";
        $GLOBALS['wgHooks']['PersonalUrls'][] = "$hookClass::onPersonalUrls";
        $GLOBALS['wgHooks']['GetPreferences'][] = "$hookClass::onGetPreferences";
        $GLOBALS['wgHooks']['UserGetRights'][] = "$hookClass::onUserGetRights";
        $GLOBALS['wgHooks']['LoadExtensionSchemaUpdates'][] = "$hookClass::onLoadExtensionSchemaUpdates";
        $GLOBALS['wgHooks']['GetAllRights'][] = "$hookClass::onGetAllRights";
        $GLOBALS['wgHooks']['GetPermissions'][] = "$hookClass::onGetPermissions";
    }
    
    /**
     * Register IslamDashboard preferences
     *
     * @param User $user User object
     * @param array &$prefs Preferences array
     * @return bool Always true
     */
    public static function onGetPreferences( $user, &$prefs ) {
        // Add IslamDashboard preferences section
        $prefs['islamdashboard-prefs'] = [
            'type' => 'api',
            'label-message' => 'islamdashboard-prefs',
            'section' => 'personal/IslamDashboard',
            'help-message' => 'islamdashboard-prefs-help',
            'preferences' => [
                'islamdashboard-layout' => [
                    'type' => 'api',
                    'default' => '{}',
                    'label-message' => 'islamdashboard-prefs-layout',
                    'help-message' => 'islamdashboard-prefs-layout-help',
                ],
                'islamdashboard-hidden-widgets' => [
                    'type' => 'api',
                    'default' => '',
                    'label-message' => 'islamdashboard-prefs-hidden-widgets',
                    'help-message' => 'islamdashboard-prefs-hidden-widgets-help',
                ]
            ]
        ];
        
        return true;
    }
    
    /**
     * Register IslamDashboard permissions
     *
     * @param array &$permissions Array of permissions
     * @return bool Always true
     */
    /**
     * Add IslamDashboard permissions
     *
     * @param User $user User object
     * @param array &$permissions Array of permissions
     * @return bool Always true
     */
    /**
     * Add IslamDashboard permissions to user
     *
     * @param User $user User object
     * @param array &$permissions Array of permissions (passed by reference)
     * @return bool Always true
     */
    public static function onUserGetRights( $user, &$permissions ) {
        global $wgIslamDashboardViewPermission, $wgIslamDashboardCustomizePermission, $wgIslamDashboardViewAllPermission;
        
        // Ensure permissions is an array
        if ( !is_array( $permissions ) ) {
            $permissions = [];
        }
        
        // Add IslamDashboard-specific permissions if user has the required rights
        if ( $user->isAllowed( 'viewdashboard' ) ) {
            $permissions[] = 'viewdashboard';
        }
        
        if ( $user->isAllowed( 'customizedashboard' ) ) {
            $permissions[] = 'customizedashboard';
        }
        
        if ( $user->isAllowed( 'viewalldashboards' ) ) {
            $permissions[] = 'viewalldashboards';
        }
        
        return true;
    }
    
    /**
     * Add IslamDashboard permissions to the list of available rights
     *
     * @param array &$rights Array of rights
     * @return bool Always true
     */
    /**
     * Add IslamDashboard permissions to the list of all available rights
     *
     * @param array &$rights Array of rights (passed by reference)
     * @return bool Always true
     */
    public static function onGetAllRights( &$rights ) {
        // Ensure rights is an array
        if ( !is_array( $rights ) ) {
            $rights = [];
        }
        
        // Add IslamDashboard-specific rights
        $dashboardRights = [
            'viewdashboard',
            'customizedashboard',
            'viewalldashboards'
        ];
        
        // Add each right if it doesn't already exist
        foreach ( $dashboardRights as $right ) {
            if ( !in_array( $right, $rights, true ) ) {
                $rights[] = $right;
            }
        }
        
        return true;
    }
    
    /**
     * Register IslamDashboard permissions with the Permissions Manager
     *
     * @param array &$permissions Array of permissions
     * @return bool Always true
     */
    /**
     * Register IslamDashboard permissions with the Permissions Manager
     *
     * @param array &$permissions Array of permissions (passed by reference)
     * @return bool Always true
     */
    public static function onGetPermissions( &$permissions ) {
        // Ensure permissions is an array
        if ( !is_array( $permissions ) ) {
            $permissions = [];
        }
        
        // Define IslamDashboard permissions
        $dashboardPermissions = [
            'viewdashboard' => [
                'text' => wfMessage( 'islamdashboard-right-viewdashboard' )->text(),
                'description' => wfMessage( 'islamdashboard-right-viewdashboard-desc' )->text(),
                'help' => 'islamdashboard-right-viewdashboard-help',
                'risk' => 'security',
                'grant' => true
            ],
            'customizedashboard' => [
                'text' => wfMessage( 'islamdashboard-right-customizedashboard' )->text(),
                'description' => wfMessage( 'islamdashboard-right-customizedashboard-desc' )->text(),
                'help' => 'islamdashboard-right-customizedashboard-help',
                'risk' => 'security',
                'grant' => true
            ],
            'viewalldashboards' => [
                'text' => wfMessage( 'islamdashboard-right-viewalldashboards' )->text(),
                'description' => wfMessage( 'islamdashboard-right-viewalldashboards-desc' )->text(),
                'help' => 'islamdashboard-right-viewalldashboards-help',
                'risk' => 'security',
                'grant' => true
            ]
        ];
        
        // Merge with existing permissions
        $permissions = array_merge( $permissions, $dashboardPermissions );
        
        return true;
    }
}
