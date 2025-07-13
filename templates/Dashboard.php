<?php
/**
 * Minimal Dashboard template for the IslamDashboard extension
 *
 * @file
 * @ingroup Extensions
 * @license GPL-3.0-or-later
 */

declare( strict_types = 1 );

use MediaWiki\Extension\IslamDashboard\WidgetManager;
use MediaWiki\Extension\IslamDashboard\Navigation\NavigationManager;
use MediaWiki\Extension\IslamDashboard\Navigation\NavigationRenderer;
use MediaWiki\Html\Html;
use MediaWiki\Output\OutputPage;
use MediaWiki\MediaWikiServices;

// Get template data from global scope
$data = $GLOBALS['islamDashboardData'] ?? [];

// Extract common variables with defaults
$messages = $data['messages'] ?? [];
$pageTitle = $messages['dashboardTitle'] ?? 'Dashboard';
$user = $data['user'] ?? $this->getUser();
$widgets = $data['widgets'] ?? [];
$layout = $data['layout'] ?? [];
$canEdit = $data['canEdit'] ?? false;
$editToken = $data['editToken'] ?? '';
$apiUrl = $data['apiUrl'] ?? '';

// Get services
$out = $this->getOutput();
$skin = $out->getSkin();

// Set page title
$out->setPageTitle( $pageTitle );

// Add configuration variables for JavaScript
$out->addJsConfigVars( [
    'wgIslamDashboardConfig' => [
        'apiUrl' => $apiUrl,
        'editToken' => $editToken,
        'canEdit' => $canEdit,
        'messages' => $messages
    ]
] );

// Let the skin handle the wrapper
if ( method_exists( $skin, 'setupSkinUserCss' ) ) {
    $skin->setupSkinUserCss( $out );
}

// Add dashboard modules
$out->addModules( 'ext.islamDashboard' );
$out->addModuleStyles( [
    'ext.islamDashboard.styles',
    'ext.islamDashboard.navigation.styles',
    'ext.islamDashboard.widgets.welcome',
    'ext.islamDashboard.widgets.recentActivity',
    'ext.islamDashboard.widgets.quickActions'
] );

?>

<!-- Main dashboard container -->
<div class="mw-dashboard-grid">
    <div class="mw-dashboard-sidebar">
        <?php
        // Mobile menu toggle
        echo Html::element( 'button', [
            'class' => 'mw-dashboard-menu-toggle',
            'aria-label' => wfMessage( 'islamdashboard-mobile-menu-toggle' )->text(),
            'title' => wfMessage( 'islamdashboard-mobile-menu-toggle' )->text(),
            'id' => 'dashboard-mobile-menu-toggle'
        ], '' );
        
        // Navigation
        echo Html::openElement( 'nav', [
            'class' => 'mw-dashboard-navigation',
            'id' => 'dashboard-navigation',
            'role' => 'navigation',
            'aria-labelledby' => 'dashboard-navigation-label'
        ] );
        
        echo Html::element( 'h2', [ 
            'class' => 'mw-portlet-header',
            'id' => 'dashboard-navigation-label' 
        ], wfMessage( 'islamdashboard-navigation' )->text() );
        
        echo Html::openElement( 'ul', [ 'class' => 'mw-dashboard-nav-list' ] );
        
        // Render navigation items
        try {
            $navManager = NavigationManager::getInstance();
            $navSections = $navManager->getNavigationForUser( $user );
            
            foreach ( $navSections as $sectionId => $section ) {
                // Skip if section has no items
                if ( empty( $section['items'] ) ) {
                    continue;
                }
                
                // Add section header if it has a label
                if ( !empty( $section['label'] ) ) {
                    echo Html::element( 'div', [ 
                        'class' => 'mw-dashboard-nav-section-header' 
                    ], wfMessage( $section['label'] )->text() );
                }
                
                // Start section list
                echo Html::openElement( 'ul', [ 'class' => 'mw-dashboard-nav-section' ] );
                
                // Render section items
                foreach ( $section['items'] as $itemId => $item ) {
                    $linkAttribs = [
                        'href' => $item['url'] ?? '#',
                        'title' => wfMessage( $item['label'] )->text(),
                        'class' => 'mw-dashboard-nav-link'
                    ];
                    
                    if ( isset( $item['class'] ) ) {
                        $linkAttribs['class'] .= ' ' . $item['class'];
                    }
                    
                    if ( isset( $item['id'] ) ) {
                        $linkAttribs['id'] = $item['id'];
                    }
                    
                    echo Html::openElement( 'li', [ 'class' => 'mw-dashboard-nav-item' ] );
                    echo Html::rawElement( 'a', $linkAttribs, 
                        Html::element( 'span', [ 'class' => 'mw-ui-icon ' . ( $item['icon'] ?? '' ) ] ) .
                        Html::element( 'span', [ 'class' => 'mw-dashboard-nav-label' ], 
                            wfMessage( $item['label'] )->text() 
                        )
                    );
                    echo '</li>';
                }
                
                echo '</ul>'; // Close section list
            }
        } catch ( Exception $e ) {
            // Log error but don't break the page
            wfDebugLog( 'IslamDashboard', 'Navigation error: ' . $e->getMessage() );
            echo Html::rawElement( 'li', [ 'class' => 'error' ], 
                wfMessage( 'islamdashboard-navigation-error' )->parse()
            );
        }
        
        echo '</ul>';
        echo '</nav>';
        
        // User profile section at the bottom
        if ( !empty( $data['userProfileHTML'] ) ) {
            echo Html::rawElement( 'div', [ 
                'class' => 'mw-dashboard-user-profile mw-panel',
                'role' => 'complementary',
                'aria-labelledby' => 'user-profile-label'
            ], 
            Html::element( 'h3', [ 'id' => 'user-profile-label' ], 
                wfMessage( 'islamdashboard-user-profile' )->text() 
            ) . $data['userProfileHTML'] );
        }
        ?>
    </div> <!-- Close mw-dashboard-sidebar -->
    
    <!-- Main Content Area -->
    <main class="mw-dashboard-main" role="main">
        <div class="mw-dashboard-widgets-container">
        <?php
        // Render main content widgets
        if ( isset( $layout['main'] ) && is_array( $layout['main'] ) ) {
            $allWidgets = $widgets ?? [];
            $widgetCount = 0;
            
            echo Html::openElement( 'div', [ 'class' => 'mw-dashboard-widgets' ] );
            
            foreach ( $layout['main'] as $widgetId ) {
                if ( isset( $allWidgets[$widgetId] ) && is_object( $allWidgets[$widgetId] ) ) {
                    $widget = $allWidgets[$widgetId];
                    $widgetCount++;
                
                    try {
                        // Get widget HTML content
                        $widgetContent = $widget->getContent();
                        
                        // Only render if the widget has content
                        if ( $widgetContent !== null ) {
                            // Get container attributes including data-widget-type
                            $containerAttribs = $widget->getContainerAttributes();
                            
                            // Ensure we have the required classes
                            $containerAttribs['class'] = isset($containerAttribs['class']) 
                                ? $containerAttribs['class'] . ' mw-dashboard-widget mw-panel'
                                : 'mw-dashboard-widget mw-panel';
                            
                            // Add any additional container classes
                            $containerClasses = $widget->getContainerClasses();
                            if (!empty($containerClasses)) {
                                if (is_array($containerClasses)) {
                                    $containerAttribs['class'] .= ' ' . implode(' ', $containerClasses);
                                } else {
                                    $containerAttribs['class'] .= ' ' . $containerClasses;
                                }
                            }
                            
                            // Render the widget container with all attributes and content
                            echo Html::rawElement('div', $containerAttribs, $widgetContent);
                        }
                    } catch ( Exception $e ) {
                        wfDebugLog( 'IslamDashboard', 'Error rendering widget ' . $widgetId . ': ' . $e->getMessage() );
                        echo Html::rawElement( 'div', [ 'class' => 'errorbox' ],
                            wfMessage( 'islamdashboard-widget-error', $widget->getType() )->parse()
                        );
                    }
                }
            }
            
            echo '</div>'; // Close mw-dashboard-widgets
            
            // If no widgets found, show a welcome message
            if ( $widgetCount === 0 ) {
                echo Html::rawElement( 'div', [ 'class' => 'mw-dashboard-welcome mw-panel' ],
                    Html::element( 'h2', [], wfMessage( 'islamdashboard-welcome-title' )->text() ) .
                    Html::element( 'p', [], wfMessage( 'islamdashboard-no-widgets-configured' )->text() )
                );
            }
        } else {
            // No layout defined, show error
            echo Html::rawElement( 'div', [ 'class' => 'errorbox' ],
                wfMessage( 'islamdashboard-no-layout' )->parse()
            );
        }
        ?>
        </div>
    </main>
    
    <!-- Right Sidebar -->
    <aside class="mw-dashboard-right-sidebar">
        <?php 
        // Render sidebar widgets
        if ( !empty( $layout['sidebar'] ) && is_array( $layout['sidebar'] ) ) {
            $allWidgets = $widgets ?? [];
            
            foreach ( $layout['sidebar'] as $widgetId ) {
                if ( isset( $allWidgets[$widgetId] ) && is_object( $allWidgets[$widgetId] ) ) {
                    $widget = $allWidgets[$widgetId];
                    
                    try {
                        // Get widget HTML content
                        $widgetContent = $widget->getContent();
                        
                        // Only render if the widget has content
                        if ( $widgetContent !== null ) {
                            // Get container attributes including data-widget-type
                            $containerAttribs = $widget->getContainerAttributes();
                            
                            // Ensure we have the required classes
                            $containerAttribs['class'] = isset($containerAttribs['class']) 
                                ? $containerAttribs['class'] . ' mw-dashboard-widget mw-panel'
                                : 'mw-dashboard-widget mw-panel';
                            
                            // Add any additional container classes
                            $containerClasses = $widget->getContainerClasses();
                            if (!empty($containerClasses)) {
                                if (is_array($containerClasses)) {
                                    $containerAttribs['class'] .= ' ' . implode(' ', $containerClasses);
                                } else {
                                    $containerAttribs['class'] .= ' ' . $containerClasses;
                                }
                            }
                            
                            // Render the widget container with all attributes and content
                            echo Html::rawElement('div', $containerAttribs, $widgetContent);
                        }
                    } catch ( Exception $e ) {
                        wfDebugLog( 'IslamDashboard', 'Error rendering sidebar widget ' . $widgetId . ': ' . $e->getMessage() );
                        // Don't show error in sidebar to avoid breaking layout
                    }
                }
            }
        }
        ?>
    </aside>
</div> <!-- Close mw-dashboard-grid -->

<!-- Initialize dashboard JavaScript -->
<?php
// Add main dashboard initialization
$js = <<<'JS'
    jQuery( document ).ready( function() {
        mw.loader.using( ["ext.islamDashboard", "ext.islamDashboard.navigation"] ).done( function() {
            // Initialize dashboard components
            mw.hook( "ext.islamDashboard.loaded" ).fire();
            mw.hook( "ext.islamDashboard.navigation" ).fire();
            
            // Handle mobile menu toggle
            jQuery( "#dashboard-mobile-menu-toggle" ).on( "click", function() {
                jQuery( ".mw-dashboard-sidebar" ).toggleClass( "mobile-menu-visible" );
            });
        });
    });
JS;
$out->addInlineScript( $js );

// Add any additional JavaScript from widgets
if ( !empty( $data['scripts'] ) && is_array( $data['scripts'] ) ) {
    foreach ( $data['scripts'] as $script ) {
        $out->addInlineScript( $script );
    }
}
?>

<!-- Hidden template for dynamic widget creation -->
<template id="widgetTemplate">
    <div class="mw-dashboard-widget mw-panel" data-widget-id="" data-widget-type="">
        <div class="mw-dashboard-widget-header">
            <h3 class="mw-dashboard-widget-title"></h3>
            <div class="mw-dashboard-widget-actions">
                <button type="button" class="mw-dashboard-widget-edit" title="Edit widget">✏️</button>
                <button type="button" class="mw-dashboard-widget-remove" title="Remove widget">🗑️</button>
            </div>
        </div>
        <div class="mw-dashboard-widget-content"></div>
    </div>
</template>

<?php if ( $canEdit ) : ?>
<!-- Dashboard controls and widgets container -->
<div class="mw-dashboard-controls-container">
    <div class="mw-dashboard-controls">
        <button id="dashboard-edit-layout" class="mw-ui-button mw-ui-progressive">
            <?php echo wfMessage( 'islamdashboard-edit-layout' )->text(); ?>
        </button>
        <button id="dashboard-add-widget" class="mw-ui-button mw-ui-progressive">
            <?php echo wfMessage( 'islamdashboard-add-widget' )->text(); ?>
        </button>
    </div>
    
    <div id="dashboard-widgets-container" class="mw-dashboard-widgets-container">
        <!-- Widgets will be loaded here -->
    </div>
    
    <!-- Empty state for dashboard -->
    <div id="emptyDashboard" class="mw-dashboard-empty" <?php echo !empty( $widgets ) ? 'style="display: none;"' : ''; ?>>
        <div class="mw-dashboard-empty-icon">
            <span class="oo-ui-iconElement-icon oo-ui-icon-dashboard"></span>
        </div>
        <h3 class="mw-dashboard-empty-title">
            <?php echo wfMessage( 'islamdashboard-empty-dashboard-title' )->text(); ?>
        </h3>
        <p class="mw-dashboard-empty-description">
            <?php echo wfMessage( 'islamdashboard-empty-dashboard-description' )->text(); ?>
        </p>
        <button id="addFirstWidget" class="mw-ui-button mw-ui-progressive">
            <?php echo wfMessage( 'islamdashboard-add-first-widget' )->text(); ?>
        </button>
    </div>
</div>

<!-- Widget selector dialog -->
<div id="widgetSelectorDialog" class="mw-dashboard-modal" style="display: none;">
    <div class="mw-dashboard-modal-dialog">
        <div class="mw-dashboard-modal-header">
            <h3><?php echo wfMessage( 'islamdashboard-add-widget' )->text(); ?></h3>
            <button type="button" id="widgetSelectorClose" class="mw-dashboard-modal-close">×</button>
        </div>
        <div class="mw-dashboard-modal-body">
            <div class="mw-dashboard-widget-grid">
                <!-- Widgets will be loaded here -->
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
