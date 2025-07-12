<?php
/**
 * Navigation Renderer for IslamDashboard
 *
 * @file
 * @ingroup Extensions
 */

namespace MediaWiki\Extension\IslamDashboard\Navigation;

use Html;
use MediaWiki\MediaWikiServices;
use Title;

class NavigationRenderer {
    /** @var NavigationManager */
    private $navigationManager;

    /** @var array User preferences for navigation */
    private $userPreferences;

    /**
     * Constructor
     *
     * @param NavigationManager $navigationManager
     */
    public function __construct( NavigationManager $navigationManager ) {
        $this->navigationManager = $navigationManager;
        $this->userPreferences = [];
    }

    /**
     * Set user preferences
     *
     * @param array $preferences
     */
    public function setUserPreferences( array $preferences ): void {
        $this->userPreferences = $preferences;
    }

    /**
     * Get the HTML for the navigation menu
     *
     * @param array $options Rendering options
     * @return string HTML
     */
    public function getNavigationHTML( array $options = [] ): string {
        $options = array_merge( [
            'user' => null,
            'currentPath' => '',
            'collapsed' => false,
            'mobile' => false
        ], $options );

        $user = $options['user'] ?? \RequestContext::getMain()->getUser();
        $navigation = $this->navigationManager->getNavigationForUser( $user );
        
        // Start building HTML
        $html = Html::openElement( 'nav', [
            'class' => $this->getNavigationClasses( $options ),
            'role' => 'navigation',
            'aria-label' => wfMessage( 'islamdashboard-navigation-label' )->text()
        ] );

        // Add logo/header
        $html .= $this->getHeaderHTML( $options );

        // Add navigation sections
        $html .= Html::openElement( 'div', [ 'class' => 'dashboard-navigation-sections' ] );
        
        foreach ( $navigation as $sectionId => $section ) {
            $html .= $this->getSectionHTML( $sectionId, $section, $options );
        }
        
        $html .= Html::closeElement( 'div' );
        
        // Add footer/collapse toggle
        $html .= $this->getFooterHTML( $options );
        
        $html .= Html::closeElement( 'nav' );
        
        return $html;
    }

    /**
     * Get CSS classes for the navigation element
     *
     * @param array $options Rendering options
     * @return string CSS classes
     */
    private function getNavigationClasses( array $options ): string {
        $classes = [
            'dashboard-navigation',
            'mw-portlet',
            'mw-portlet-navigation'
        ];

        if ( $options['collapsed'] ) {
            $classes[] = 'collapsed';
        }

        if ( $options['mobile'] ) {
            $classes[] = 'mobile-navigation';
        }

        return implode( ' ', $classes );
    }

    /**
     * Get HTML for the navigation header
     *
     * @param array $options Rendering options
     * @return string HTML
     */
    private function getHeaderHTML( array $options ): string {
        $html = Html::openElement( 'header', [ 'class' => 'dashboard-navigation-header' ] );
        
        // Logo/Title
        $html .= Html::element( 'a', [
            'href' => Title::newMainPage()->getLocalURL(),
            'class' => 'dashboard-logo',
            'title' => wfMessage( 'sitetitle' )->text()
        ], wfMessage( 'islamdashboard-dashboard' )->text() );
        
        // Collapse toggle (desktop)
        if ( !$options['mobile'] ) {
            $html .= Html::element( 'button', [
                'class' => 'dashboard-navigation-toggle',
                'aria-label' => wfMessage( 'islamdashboard-navigation-toggle' )->text(),
                'title' => wfMessage( 'islamdashboard-navigation-toggle' )->text()
            ], '❮' );
        }
        
        $html .= Html::closeElement( 'header' );
        
        return $html;
    }

    /**
     * Get HTML for a navigation section
     *
     * @param string $sectionId Section ID
     * @param array $section Section configuration
     * @param array $options Rendering options
     * @return string HTML
     */
    private function getSectionHTML( string $sectionId, array $section, array $options ): string {
        $isCollapsed = $this->isSectionCollapsed( $sectionId, $options );
        
        $sectionClasses = [ 'dashboard-navigation-section' ];
        if ( $isCollapsed ) {
            $sectionClasses[] = 'collapsed';
        }
        
        $html = Html::openElement( 'div', [
            'class' => implode( ' ', $sectionClasses ),
            'data-section' => $sectionId
        ] );
        
        // Section header
        $html .= $this->getSectionHeaderHTML( $sectionId, $section, $isCollapsed, $options );
        
        // Section content
        $html .= Html::openElement( 'div', [ 'class' => 'dashboard-navigation-section-content' ] );
        
        // Section items
        if ( !empty( $section['items'] ) ) {
            $html .= $this->getItemsHTML( $section['items'], $options );
        }
        
        $html .= Html::closeElement( 'div' ); // Close section-content
        $html .= Html::closeElement( 'div' ); // Close section
        
        return $html;
    }
    
    /**
     * Check if a section should be collapsed
     *
     * @param string $sectionId Section ID
     * @param array $options Rendering options
     * @return bool Whether the section is collapsed
     */
    private function isSectionCollapsed( string $sectionId, array $options ): bool {
        // Check if forced in options
        if ( isset( $options['collapsedSections'][$sectionId] ) ) {
            return (bool)$options['collapsedSections'][$sectionId];
        }
        
        // Check user preferences
        if ( isset( $this->userPreferences['collapsed-sections'][$sectionId] ) ) {
            return (bool)$this->userPreferences['collapsed-sections'][$sectionId];
        }
        
        // Default to not collapsed
        return false;
    }
    
    /**
     * Get HTML for a section header
     *
     * @param string $sectionId Section ID
     * @param array $section Section configuration
     * @param bool $isCollapsed Whether the section is collapsed
     * @param array $options Rendering options
     * @return string HTML
     */
    private function getSectionHeaderHTML( string $sectionId, array $section, bool $isCollapsed, array $options ): string {
        $icon = $section['icon'] ?? '';
        $label = wfMessage( $section['label'] )->text();
        
        $html = Html::openElement( 'div', [
            'class' => 'dashboard-navigation-section-header',
            'role' => 'button',
            'tabindex' => '0',
            'aria-expanded' => $isCollapsed ? 'false' : 'true',
            'aria-controls' => 'dashboard-section-' . $sectionId . '-content',
            'data-section' => $sectionId
        ] );
        
        // Icon
        if ( $icon ) {
            $html .= Html::element( 'span', [
                'class' => 'dashboard-navigation-icon oo-ui-icon-' . $icon,
                'aria-hidden' => 'true'
            ] );
        }
        
        // Label
        $html .= Html::element( 'span', [
            'class' => 'dashboard-navigation-label'
        ], $label );
        
        // Toggle icon
        $html .= Html::element( 'span', [
            'class' => 'dashboard-navigation-toggle-icon',
            'aria-hidden' => 'true'
        ], '▼' );
        
        $html .= Html::closeElement( 'div' );
        
        return $html;
    }
    
    /**
     * Get HTML for navigation items
     *
     * @param array $items Items to render
     * @param array $options Rendering options
     * @return string HTML
     */
    private function getItemsHTML( array $items, array $options ): string {
        $html = Html::openElement( 'ul', [ 'class' => 'dashboard-navigation-items' ] );
        
        foreach ( $items as $itemId => $item ) {
            $html .= $this->getItemHTML( $itemId, $item, $options );
        }
        
        $html .= Html::closeElement( 'ul' );
        
        return $html;
    }
    
    /**
     * Get HTML for a navigation item
     *
     * @param string $itemId Item ID
     * @param array $item Item configuration
     * @param array $options Rendering options
     * @return string HTML
     */
    private function getItemHTML( string $itemId, array $item, array $options ): string {
        $isActive = $this->isItemActive( $item, $options );
        
        $itemClasses = [ 'dashboard-navigation-item' ];
        if ( $isActive ) {
            $itemClasses[] = 'active';
        }
        
        $icon = $item['icon'] ?? '';
        $label = wfMessage( $item['label'] )->text();
        $url = $item['url'] ?? '#';
        
        $html = Html::openElement( 'li', [ 'class' => implode( ' ', $itemClasses ) ] );
        
        $html .= Html::openElement( 'a', [
            'href' => $url,
            'class' => 'dashboard-navigation-link',
            'data-item' => $itemId,
            'title' => $label
        ] );
        
        // Icon
        if ( $icon ) {
            $html .= Html::element( 'span', [
                'class' => 'dashboard-navigation-icon oo-ui-icon-' . $icon,
                'aria-hidden' => 'true'
            ] );
        }
        
        // Label
        $html .= Html::element( 'span', [
            'class' => 'dashboard-navigation-label'
        ], $label );
        
        // Badge/count (if any)
        if ( isset( $item['badge'] ) ) {
            $html .= Html::element( 'span', [
                'class' => 'dashboard-navigation-badge',
                'data-badge' => $item['badge']
            ], $item['badge'] );
        }
        
        $html .= Html::closeElement( 'a' );
        $html .= Html::closeElement( 'li' );
        
        return $html;
    }
    
    /**
     * Check if an item is active based on current path
     *
     * @param array $item Item configuration
     * @param array $options Rendering options
     * @return bool Whether the item is active
     */
    private function isItemActive( array $item, array $options ): bool {
        if ( empty( $options['currentPath'] ) ) {
            return false;
        }
        
        $itemUrl = $item['url'] ?? '';
        if ( !$itemUrl ) {
            return false;
        }
        
        // Simple string matching for now
        // Could be enhanced with regex patterns if needed
        return strpos( $options['currentPath'], $itemUrl ) !== false;
    }
    
    /**
     * Get HTML for the navigation footer
     *
     * @param array $options Rendering options
     * @return string HTML
     */
    private function getFooterHTML( array $options ): string {
        $html = Html::openElement( 'footer', [ 'class' => 'dashboard-navigation-footer' ] );
        
        // User menu
        $user = $options['user'] ?? \RequestContext::getMain()->getUser();
        $userName = $user->getName();
        $userPage = $user->getUserPage();
        
        $html .= Html::openElement( 'div', [ 'class' => 'dashboard-user-menu' ] );
        
        // User avatar and name
        $html .= Html::element( 'span', [
            'class' => 'dashboard-user-avatar oo-ui-icon-userAvatar',
            'aria-hidden' => 'true'
        ] );
        
        $html .= Html::element( 'span', [
            'class' => 'dashboard-user-name'
        ], $userName );
        
        // User dropdown toggle
        $html .= Html::element( 'span', [
            'class' => 'dashboard-user-dropdown-toggle oo-ui-icon-expand',
            'aria-hidden' => 'true'
        ] );
        
        // User dropdown menu
        $html .= Html::openElement( 'ul', [ 'class' => 'dashboard-user-dropdown' ] );
        
        // User profile link
        $html .= Html::openElement( 'li' );
        $html .= Html::element( 'a', [
            'href' => $userPage->getLocalURL(),
            'class' => 'dashboard-user-link',
            'title' => wfMessage( 'islamdashboard-viewprofile' )->text()
        ], wfMessage( 'islamdashboard-profile' )->text() );
        $html .= Html::closeElement( 'li' );
        
        // Preferences link
        $html .= Html::openElement( 'li' );
        $html .= Html::element( 'a', [
            'href' => Title::newFromText( 'Special:Preferences' )->getLocalURL(),
            'class' => 'dashboard-user-link',
            'title' => wfMessage( 'preferences' )->text()
        ], wfMessage( 'preferences' )->text() );
        $html .= Html::closeElement( 'li' );
        
        // Logout link
        $html .= Html::openElement( 'li' );
        $html .= Html::element( 'a', [
            'href' => '#' . urlencode( $userName ), // Will be handled by JavaScript
            'class' => 'dashboard-user-link dashboard-logout-link',
            'title' => wfMessage( 'logout' )->text()
        ], wfMessage( 'logout' )->text() );
        $html .= Html::closeElement( 'li' );
        
        $html .= Html::closeElement( 'ul' ); // Close user dropdown
        $html .= Html::closeElement( 'div' ); // Close user menu
        $html .= Html::closeElement( 'footer' );
        
        return $html;
    }
}
