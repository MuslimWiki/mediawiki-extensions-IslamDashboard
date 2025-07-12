<?php
/**
 * @covers \MediaWiki\Extension\IslamDashboard\Widgets\DashboardWidget
 */

namespace MediaWiki\Extension\IslamDashboard\Tests\Unit\Widgets;

use MediaWiki\Extension\IslamDashboard\Widgets\DashboardWidget;
use MediaWiki\MediaWikiServices;
use MediaWikiIntegrationTestCase;
use RequestContext;
use Title;

/**
 * @group IslamDashboard
 * @coversDefaultClass \MediaWiki\Extension\IslamDashboard\Widgets\DashboardWidget
 */
class DashboardWidgetTest extends MediaWikiIntegrationTestCase {

    /**
     * @var DashboardWidget
     */
    private $widget;

    protected function setUp(): void {
        parent::setUp();
        
        // Set up test configuration
        $this->setMwGlobals( [
            'wgIslamDashboardConfig' => [
                'enabled' => true,
                'defaultLayout' => 'default',
                'allowedWidgets' => [ 'TestWidget' ]
            ]
        ]);

        // Create a test context
        $context = new RequestContext();
        $context->setTitle( Title::newMainPage() );
        
        // Create a test widget instance
        $this->widget = $this->getMockForAbstractClass(
            'MediaWiki\Extension\IslamDashboard\Widgets\DashboardWidget',
            [ $context ]
        );
    }

    /**
     * @covers ::__construct
     * @covers ::getContext
     */
    public function testGetContext() {
        $context = $this->widget->getContext();
        $this->assertInstanceOf( 'RequestContext', $context );
    }

    /**
     * @covers ::getUser
     */
    public function testGetUser() {
        $user = $this->widget->getUser();
        $this->assertInstanceOf( 'User', $user );
    }

    /**
     * @covers ::getOutput
     */
    public function testGetOutput() {
        $output = $this->widget->getOutput();
        $this->assertInstanceOf( 'OutputPage', $output );
    }

    /**
     * @covers ::getTitle
     */
    public function testGetTitle() {
        $title = $this->widget->getTitle();
        $this->assertInstanceOf( 'Title', $title );
    }

    /**
     * @covers ::getConfig
     */
    public function testGetConfig() {
        $config = $this->widget->getConfig();
        $this->assertIsArray( $config );
        $this->assertArrayHasKey( 'enabled', $config );
    }

    /**
     * @covers ::getDefinition
     */
    public function testGetDefinition() {
        $definition = $this->widget->getDefinition();
        $this->assertIsArray( $definition );
        $this->assertArrayHasKey( 'id', $definition );
        $this->assertArrayHasKey( 'name', $definition );
        $this->assertArrayHasKey( 'description', $definition );
        $this->assertArrayHasKey( 'icon', $definition );
    }

    /**
     * @covers ::getTemplatePath
     */
    public function testGetTemplatePath() {
        $path = $this->widget->getTemplatePath( 'TestWidget' );
        $this->assertStringContainsString( 'templates/TestWidget.mustache', $path );
    }

    /**
     * @covers ::renderTemplate
     */
    public function testRenderTemplate() {
        // Create a test template file
        $templateDir = dirname( dirname( dirname( __DIR__ ) ) ) . '/templates';
        if ( !is_dir( $templateDir ) ) {
            mkdir( $templateDir, 0777, true );
        }
        
        $templateFile = $templateDir . '/TestWidget.mustache';
        file_put_contents( $templateFile, 'Hello, {{name}}!' );
        
        // Test template rendering
        $result = $this->widget->renderTemplate( 'TestWidget', [ 'name' => 'World' ] );
        $this->assertEquals( 'Hello, World!', $result );
        
        // Clean up
        unlink( $templateFile );
    }
}
