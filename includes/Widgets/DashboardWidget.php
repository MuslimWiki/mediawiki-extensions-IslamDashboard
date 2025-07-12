<?php
/**
 * Base class for all dashboard widgets
 *
 * @file
 * @ingroup Extensions
 * @license GPL-3.0-or-later
 */

namespace MediaWiki\Extension\IslamDashboard\Widgets;

use MediaWiki\MediaWikiServices;
use MediaWiki\Context\RequestContext;
use MediaWiki\Context\IContextSource;
use Html;
use Mustache_Engine;

/**
 * Base class for all dashboard widgets
 */
abstract class DashboardWidget {
    /** @var IContextSource */
    protected $context;
    /** @var string Unique identifier for the widget */
    protected $id;
    
    /** @var string Widget title message key */
    protected $titleMsg;
    
    /** @var string Widget description message key */
    protected $descriptionMsg;
    
    /** @var string Default section where the widget should appear (main or sidebar) */
    protected $defaultSection = 'main';
    
    /** @var bool Whether the widget can be hidden by the user */
    protected $canHide = true;
    
    /**
     * Constructor
     * 
     * @param string $id Unique identifier for the widget
     * @param string $titleMsg Message key for the widget title
     * @param string $descriptionMsg Message key for the widget description
     */
    /**
     * @param string $id Unique identifier for the widget
     * @param string $titleMsg Message key for the widget title
     * @param string $descriptionMsg Message key for the widget description
     * @param IContextSource|null $context Context source (uses RequestContext::getMain() if null)
     */
    public function __construct( $id, $titleMsg, $descriptionMsg, IContextSource $context = null ) {
        $this->id = $id;
        $this->titleMsg = $titleMsg;
        $this->descriptionMsg = $descriptionMsg;
        $this->context = $context ?: RequestContext::getMain();
    }
    
    /**
     * Get the widget's unique identifier
     * 
     * @return string
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Get the current user
     * 
     * @return \MediaWiki\User\User
     */
    protected function getUser() {
        return $this->context->getUser();
    }
    
    /**
     * Get the current context
     * 
     * @return IContextSource
     */
    public function getContext() {
        return $this->context;
    }
    
    /**
     * Get the widget's title
     * 
     * @return string Localized title
     */
    public function getTitle() {
        return wfMessage( $this->titleMsg )->text();
    }
    
    /**
     * Get the widget's description
     * 
     * @return string Localized description
     */
    public function getDescription() {
        return wfMessage( $this->descriptionMsg )->text();
    }
    
    /**
     * Get the default section for this widget
     * 
     * @return string 'main' or 'sidebar'
     */
    public function getDefaultSection() {
        return $this->defaultSection;
    }
    
    /**
     * Check if the widget can be hidden by the user
     * 
     * @return bool
     */
    public function canHide() {
        return $this->canHide;
    }
    
    /**
     * Get the HTML content for the widget
     * 
     * @return string HTML content
     */
    abstract public function getContent();
    
    /**
     * Get any additional CSS classes for the widget container
     * 
     * @return string[] Array of CSS class names
     */
    public function getContainerClasses() {
        return [ 'dashboard-widget', 'dashboard-widget-' . $this->id ];
    }
    
    /**
     * Get any additional attributes for the widget container
     * 
     * @return array Array of HTML attributes
     */
    public function getContainerAttributes() {
        return [
            'data-widget-id' => $this->id,
            'data-widget-type' => $this->getType()
        ];
    }
    
    /**
     * Get the widget type (used for client-side initialization)
     * 
     * @return string Widget type
     */
    protected function getType() {
        return str_replace( 'widget', '', strtolower( ( new \ReflectionClass( $this ) )->getShortName() ) );
    }
    
    /**
     * Get any required ResourceLoader modules for this widget
     * 
     * @return string[] Array of module names
     */
    public function getModules() {
        return [];
    }
    
    /**
     * Get any required styles for this widget
     * 
     * @return string[] Array of CSS file paths
     */
    public function getStyles() {
        return [];
    }
    
    /**
     * Get any required scripts for this widget
     * 
     * @return string[] Array of JavaScript file paths
     */
    public function getScripts() {
        return [];
    }
    
    /**
     * Get any configuration data to pass to the client-side widget
     * 
     * @return array Configuration data
     */
    public function getConfig() {
        return [];
    }
    
    /**
     * Check if the current user can see this widget
     * 
     * @param \User $user Current user
     * @return bool
     */
    public function isVisibleTo( \User $user ) {
        return true;
    }
    
    /**
     * Get the widget definition for client-side rendering
     * 
     * @return array Widget definition
     */
    public function getDefinition() {
        return [
            'id' => $this->id,
            'type' => $this->getType(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'icon' => $this->getIcon(),
            'editable' => $this->isEditable(),
            'canHide' => $this->canHide(),
            'defaultSection' => $this->getDefaultSection(),
            'config' => $this->getConfig()
        ];
    }
    
    /**
     * Check if the widget can be added to the dashboard
     * 
     * @return bool
     */
    public function canBeAdded() {
        return true;
    }
    
    /**
     * Get the icon name for this widget
     * 
     * @return string Icon name (without the 'oo-ui-icon-' prefix)
     */
    public function getIcon() {
        // Default to a generic icon if not overridden
        return 'widget';
    }
    
    /**
     * Check if the widget is currently loading
     * 
     * @return bool
     */
    public function isLoading() {
        return false;
    }
    
    /**
     * Check if the widget is editable
     * 
     * @return bool
     */
    public function isEditable() {
        return false;
    }
    
    /**
     * Render a template with the given data using MediaWiki's template engine
     *
     * @param string $templateName Name of the template file (without .mustache)
     * @param array $data Data to pass to the template
     * @return string Rendered HTML
     */
    protected function renderTemplate( string $templateName, array $data = [] ): string {
        $templatePath = 'templates/';
        if ( strpos( $templateName, 'widgets/' ) === 0 ) {
            // If the template is in the widgets subdirectory
            $templatePath .= $templateName . '.mustache';
        } else {
            // For backward compatibility, assume it's in the root templates directory
            $templatePath .= $templateName . '.mustache';
        }

        $templatePath = __DIR__ . '/../../' . $templatePath;
        
        try {
            // Use MediaWiki's template engine to render the template
            $template = file_get_contents( $templatePath );
            if ( $template === false ) {
                throw new \RuntimeException( "Template not found: $templatePath" );
            }
            
            // Simple template variable replacement for now
            // This can be enhanced with a proper template engine if needed
            foreach ( $data as $key => $value ) {
                if ( is_array( $value ) || is_object( $value ) ) {
                    // Skip complex data structures for now
                    continue;
                }
                $template = str_replace( '{{' . $key . '}}', htmlspecialchars( (string)$value ), $template );
            }
            
            return $template;
            
        } catch ( \Exception $e ) {
            wfDebugLog( 'IslamDashboard', 'Error rendering template: ' . $e->getMessage() );
            return '';
        }
    }

    /**
     * Render the widget as HTML
     * 
     * @return string HTML
     */
    public function render() {
        $attributes = array_merge(
            [ 'class' => implode( ' ', $this->getContainerClasses() ) ],
            $this->getContainerAttributes()
        );
        
        $html = Html::openElement( 'div', $attributes );
        
        // Widget header
        $html .= Html::rawElement( 
            'div', 
            [ 'class' => 'widget-header' ],
            Html::element( 'h3', [ 'class' => 'widget-title' ], $this->getTitle() )
        );
        
        // Widget content
        $html .= Html::rawElement(
            'div',
            [ 'class' => 'widget-content' ],
            $this->getContent()
        );
        
        $html .= Html::closeElement( 'div' );
        
        return $html;
    }
    

}
