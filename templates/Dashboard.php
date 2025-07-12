<?php
/**
 * Dashboard template for the IslamDashboard extension
 *
 * @file
 * @ingroup Extensions
 * @license GPL-3.0-or-later
 */

declare( strict_types = 1 );

use MediaWiki\MediaWikiServices;
use MediaWiki\Extension\IslamDashboard\Navigation\NavigationManager;
use MediaWiki\Extension\IslamDashboard\Navigation\NavigationRenderer;

/**
 * @var \MediaWiki\Extension\IslamDashboard\SpecialDashboard $specialDashboard
 * @var array $widgets Array of widget instances
 * @var array $layout User's widget layout
 * @var bool $canEdit Whether the user can edit the dashboard
 */

$out = $specialDashboard->getOutput();
$user = $specialDashboard->getUser();
$widgetManager = MediaWiki\Extension\IslamDashboard\WidgetManager::getInstance();

// Add page title
$out->setPageTitle( $specialDashboard->msg( 'islamdashboard-dashboard' )->text() );

// Add body classes
$out->addBodyClasses( [ 'islam-dashboard-page' ] );

// Add required modules and styles
$out->addModules( [ 'ext.islamDashboard', 'ext.islamDashboard.navigation' ] );
$out->addModuleStyles( [ 'oojs-ui.styles.icons-interactions', 'oojs-ui.styles.icons-content', 'ext.islamDashboard.styles', 'ext.islamDashboard.navigation.styles' ] );

// Add configuration variables for JavaScript
$out->addJsConfigVars( [
    'wgIslamDashboardConfig' => [
        'apiUrl' => wfScript( 'api' ),
        'editToken' => $user->getEditToken(),
        'canEdit' => $canEdit,
        'messages' => [
            'confirmRemoveWidget' => wfMessage( 'islamdashboard-confirm-remove-widget' )->text(),
            'widgetError' => wfMessage( 'islamdashboard-widget-error' )->text(),
            'loading' => wfMessage( 'islamdashboard-loading' )->text()
        ]
    ]
] );

// Initialize dashboard JavaScript
$out->addInlineScript( 'jQuery( document ).ready( function() { mw.loader.using( [ "ext.islamDashboard", "ext.islamDashboard.navigation" ] ).done( function() { mw.islamDashboard.init(); } ); } );' );

// Get navigation components
$navManager = NavigationManager::getInstance();
$navRenderer = new NavigationRenderer( $navManager );
$currentPath = $specialDashboard->getRequest()->getRequestURL();

?>
<div class="islam-dashboard">
    <?php 
    // Main content wrapper
    ?>
    <div class="dashboard-wrapper">
        <?php
        // Get the navigation HTML
        $navManager = NavigationManager::getInstance();
        $navRenderer = new NavigationRenderer( $navManager );
        $currentPath = $specialDashboard->getRequest()->getRequestURL();
        
        // Navigation container with proper structure for JavaScript
        echo Html::openElement( 'div', [ 'class' => 'dashboard-navigation-container' ] );
        
        // Mobile menu toggle
        echo Html::element( 'button', [
            'class' => 'dashboard-mobile-menu-toggle',
            'aria-label' => wfMessage( 'islamdashboard-mobile-menu-toggle' )->text(),
            'title' => wfMessage( 'islamdashboard-mobile-menu-toggle' )->text(),
            'id' => 'dashboard-mobile-menu-toggle'
        ], '☰' );
        
        // Main navigation container with proper class for JavaScript
        echo Html::openElement( 'nav', [ 'class' => 'dashboard-navigation', 'id' => 'dashboard-navigation' ] );
        
        // Navigation content
        echo $navRenderer->getNavigationHTML( [
            'currentPath' => $currentPath,
            'user' => $user
        ] );
        
        echo Html::closeElement( 'nav' );
        echo Html::closeElement( 'div' );
        
        // Initialize navigation JavaScript
        $out->addInlineScript(
            'jQuery( document ).ready( function() { mw.loader.using( "ext.islamDashboard.navigation" ).done( function() {' .
            '   mw.islamDashboard.Navigation.init();' .
            '} ) } );'
        );
        ?>
        
        <div class="dashboard-container">
            <!-- Main content area -->
            <div class="dashboard-main">
                <?php 
                // Render main content widgets
                if ( isset( $layout['main'] ) ) {
                    foreach ( $layout['main'] as $widgetId ) {
                        if ( isset( $widgets[$widgetId] ) ) {
                            echo $widgets[$widgetId]->render();
                        }
                    }
                }
                ?>
            </div>
            
            <!-- Sidebar -->
            <div class="dashboard-sidebar">
                <?php 
                // Render sidebar widgets
                if ( isset( $layout['sidebar'] ) ) {
                    foreach ( $layout['sidebar'] as $widgetId ) {
                        if ( isset( $widgets[$widgetId] ) ) {
                            echo $widgets[$widgetId]->render();
                        }
                    }
                }
                ?>
            </div>
        </div><!-- /.dashboard-container -->
    </div><!-- /.dashboard-wrapper -->
    
    <?php if ( $canEdit ) : ?>
    <!-- Edit mode controls -->
    <div class="dashboard-edit-controls">
        <button id="toggleEditMode" class="cdx-button cdx-button--action-progressive">
            <?php echo $specialDashboard->msg( 'islamdashboard-edit-layout' )->escaped(); ?>
        </button>
        <button id="resetLayout" class="cdx-button cdx-button--action-destructive" style="display: none;">
            <?php echo $specialDashboard->msg( 'islamdashboard-reset-layout' )->escaped(); ?>
        </button>
        <button id="saveLayout" class="cdx-button cdx-button--weight-primary" style="display: none;">
            <?php echo $specialDashboard->msg( 'islamdashboard-save-layout' )->escaped(); ?>
        </button>
    </div>
    
    <!-- Widget selector (shown in edit mode) -->
    <div id="widgetSelector" class="widget-selector" style="display: none;">
        <h3 class="widget-selector-title">
            <?php echo $specialDashboard->msg( 'islamdashboard-add-widgets' )->escaped(); ?>
        </h3>
        <div class="widget-selector-grid">
            <?php 
            // Show all available widgets that aren't already added
            $availableWidgets = array_diff_key( 
                $widgetManager->getWidgets( $user ),
                $widgets 
            );
            
            foreach ( $availableWidgets as $widget ) : 
                if ( $widget->canBeAdded() ) :
            ?>
                <div class="widget-selector-item" data-widget-id="<?php echo htmlspecialchars( $widget->getId() ); ?>">
                    <div class="widget-selector-icon">
                        <span class="oo-ui-iconElement-icon oo-ui-icon-<?php echo htmlspecialchars( $widget->getIcon() ); ?>"></span>
                    </div>
                    <div class="widget-selector-info">
                        <h4 class="widget-selector-title">
                            <?php echo htmlspecialchars( $widget->getTitle() ); ?>
                        </h4>
                        <p class="widget-selector-description">
                            <?php echo htmlspecialchars( $widget->getDescription() ); ?>
                        </p>
                    </div>
                </div>
            <?php 
                endif;
            endforeach; 
            ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Empty state (shown when no widgets are added) -->
    <div id="emptyDashboard" class="dashboard-empty-state" <?php echo !empty( $widgets ) ? 'style="display: none;"' : ''; ?>>
        <div class="dashboard-empty-state-icon">
            <span class="oo-ui-iconElement-icon oo-ui-icon-dashboard"></span>
        </div>
        <h3 class="dashboard-empty-state-title">
            <?php echo $specialDashboard->msg( 'islamdashboard-empty-title' )->escaped(); ?>
        </h3>
        <p class="dashboard-empty-state-description">
            <?php echo $specialDashboard->msg( 'islamdashboard-empty-description' )->escaped(); ?>
        </p>
        <?php if ( $canEdit ) : ?>
        <button id="addFirstWidget" class="cdx-button cdx-button--action-progressive cdx-button--weight-primary">
            <?php echo $specialDashboard->msg( 'islamdashboard-add-first-widget' )->escaped(); ?>
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- Widget template (used by JavaScript) -->
<template id="widgetTemplate">
    <div class="dashboard-widget" data-widget-id="" data-widget-type="">
        <div class="widget-header">
            <h3 class="widget-title"></h3>
            <div class="widget-actions">
                <button class="widget-edit" title="<?php echo $specialDashboard->msg( 'islamdashboard-edit-widget' )->escaped(); ?>">
                    <span class="oo-ui-iconElement-icon oo-ui-icon-edit"></span>
                </button>
                <button class="widget-remove" title="<?php echo $specialDashboard->msg( 'islamdashboard-remove-widget' )->escaped(); ?>">
                    <span class="oo-ui-iconElement-icon oo-ui-icon-close"></span>
                </button>
            </div>
        </div>
        <div class="widget-content"></div>
        <div class="widget-loading">
            <div class="widget-loading-spinner"></div>
        </div>
    </div>
</template>
