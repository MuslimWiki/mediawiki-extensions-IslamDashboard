<?php

namespace MediaWiki\Extension\IslamDashboard\Tests\Unit\Widgets;

use MediaWiki\Extension\IslamDashboard\Widgets\DashboardWidget;
use MediaWiki\Extension\IslamDashboard\Widgets\WidgetManager;
use MediaWiki\MediaWikiServices;
use MediaWikiIntegrationTestCase;
use OutputPage;
use RequestContext;
use Title;
use User;
use PHPUnit\Framework\MockObject\MockObject;
use MediaWiki\Tests\Unit\MockServiceDependenciesTrait;

// Ensure WidgetManager is loaded
if (!class_exists(WidgetManager::class)) {
    require_once __DIR__ . '/../../../../includes/WidgetManager.php';
}

/**
 * Concrete test class for DashboardWidget
 */
class TestDashboardWidget extends DashboardWidget {
    public function getTitle() {
        return 'Test Widget';
    }
    
    public function getIcon() {
        return 'test-icon';
    }
    
    public function getBody() {
        return '<div class="test-widget">Test Content</div>';
    }
    
    public function getContent() {
        return $this->getBody();
    }
    
    public function getTemplateData() {
        return [
            'test' => 'value',
            'title' => $this->getTitle(),
            'icon' => $this->getIcon(),
            'body' => $this->getBody()
        ];
    }
}

/**
 * @covers \MediaWiki\Extension\IslamDashboard\Widgets\DashboardWidget
 * @group Database
 * @group IslamDashboard
 */
/**
 * @covers \MediaWiki\Extension\IslamDashboard\Widgets\DashboardWidget
 * @group Database
 * @group IslamDashboard
 */
class DashboardWidgetTest extends MediaWikiIntegrationTestCase {
    use MockServiceDependenciesTrait;

    /** @var User */
    protected $user;

    /** @var RequestContext */
    protected $context;

    /** @var Title */
    protected $title;

    /** @var OutputPage */
    protected $output;

    /** @var WidgetManager */
    protected $widgetManager;

    /** @var TestDashboardWidget */
    protected $widget;
    
    protected function setUp(): void {
        parent::setUp();

        // Create a test user
        $this->user = $this->createMock(User::class);
        
        // Create a test context
        $this->context = $this->getMockBuilder(RequestContext::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUser'])
            ->getMock();
            
        $this->context->method('getUser')
            ->willReturn($this->user);
        
        // Create a test title
        $this->title = $this->createMock(Title::class);
        
        // Create output page
        $this->output = $this->getMockBuilder(OutputPage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getContext', 'getTitle'])
            ->getMock();
            
        $this->output->method('getContext')
            ->willReturn($this->context);
            
        $this->output->method('getTitle')
            ->willReturn($this->title);
        
        // Create a mock WidgetManager
        $this->widgetManager = $this->createMock(WidgetManager::class);
        
        // Create test widget
        $this->widget = new class('test-widget', [
            'title' => 'Test Widget',
            'description' => 'A test widget',
            'icon' => 'test-icon',
            'permissions' => ['read'],
            'requiredRights' => [],
            'requiredSkins' => [],
            'maxInRow' => 1,
            'minWidth' => 1,
            'minHeight' => 1,
            'defaultWidth' => 1,
            'defaultHeight' => 1,
            'reloadable' => true,
            'collapsible' => true,
            'removable' => true
        ], $this->widgetManager) extends DashboardWidget {
            public function getType(): string {
                return 'test-widget';
            }
            
            public function getContent(array $params = []): string {
                return 'Test widget content';
            }
            
            public function getDefinition(): array {
                return [
                    'name' => $this->getName(),
                    'type' => $this->getType(),
                    'title' => $this->getTitle(),
                    'description' => $this->getDescription(),
                    'icon' => $this->getIcon(),
                    'permissions' => $this->getPermissions(),
                    'requiredRights' => $this->getRequiredRights(),
                    'requiredSkins' => $this->getRequiredSkins(),
                    'maxInRow' => $this->getMaxInRow(),
                    'minWidth' => $this->getMinWidth(),
                    'minHeight' => $this->getMinHeight(),
                    'defaultWidth' => $this->getDefaultWidth(),
                    'defaultHeight' => $this->getDefaultHeight(),
                    'reloadable' => $this->isReloadable(),
                    'collapsible' => $this->isCollapsible(),
                    'removable' => $this->isRemovable()
                ];
            }
        };
        
        // Set up service container
        $services = MediaWikiServices::getInstance();
        $this->setService('WidgetManager', $this->widgetManager);
    }

    /**
     * Helper method to create a widget with specific options
     */
    protected function createWidgetWithOptions(array $options = []) {
        // Create a mock WidgetManager
        $widgetManager = $this->createMock(\MediaWiki\Extension\IslamDashboard\WidgetManager::class);
        
        $defaults = [
            'id' => 'test-widget',
            'title' => 'Test Widget',
            'description' => 'A test widget',
            'icon' => 'test-icon',
            'permissions' => ['read'],
            'requiredRights' => [],
            'requiredSkins' => [],
            'maxInRow' => 1,
            'minWidth' => 1,
            'minHeight' => 1,
            'defaultWidth' => 1,
            'defaultHeight' => 1,
            'reloadable' => true,
            'collapsible' => true,
            'removable' => true
        ];
        
        $mergedOptions = array_merge($defaults, $options);
        
        return new TestDashboardWidget(
            $widgetManager,
            $mergedOptions['id'],
            $mergedOptions
        );
    }

    public function testWidgetInitialization() {
        // Test basic properties
        $this->assertInstanceOf( DashboardWidget::class, $this->widget );
        $this->assertSame( 'test-widget', $this->widget->getId() );
        $this->assertSame( 'Test Widget', $this->widget->getTitle() );
        $this->assertSame( 'A test widget', $this->widget->getDescription() );
        $this->assertSame( 'test-icon', $this->widget->getIcon() );
        $this->assertSame( ['read'], $this->widget->getPermissions() );
        $this->assertSame( [], $this->widget->getRequiredRights() );
        $this->assertSame( [], $this->widget->getRequiredSkins() );
        $this->assertSame( 1, $this->widget->getMaxInRow() );
        $this->assertSame( 1, $this->widget->getMinWidth() );
        $this->assertSame( 1, $this->widget->getMinHeight() );
        $this->assertSame( 1, $this->widget->getDefaultWidth() );
        $this->assertSame( 1, $this->widget->getDefaultHeight() );
        $this->assertTrue( $this->widget->isReloadable() );
        $this->assertTrue( $this->widget->isCollapsible() );
        $this->assertTrue( $this->widget->isRemovable() );
    }

    public function testTemplatePath() {
        $widget = $this->createWidgetWithOptions();
        
        // Test template path generation
        $templatePath = $widget->getTemplatePath();
        $this->assertIsString( $templatePath );
        $this->assertStringEndsWith( 'templates/widgets/test-widget.mustache', $templatePath );
    }

    public function testRender() {
        $widget = $this->createWidgetWithOptions();
        
        // Test rendering
        $output = $widget->render();
        $this->assertIsString( $output, 'Render output should be a string' );
        $this->assertStringContainsString( 'widget-test-widget', $output );
    }

    public function testPermissions() {
        $widget = $this->createWidgetWithOptions();
        
        // Test permissions
        $permissions = $widget->getPermissions();
        $this->assertIsArray( $permissions, 'Permissions should be an array' );
        $this->assertContains( 'read', $permissions, 'Default permissions should include "read"' );
    }

    public function testGetRequiredRights() {
        $widget = $this->createWidgetWithOptions([
            'requiredRights' => ['edit']
        ]);
        $requiredRights = $widget->getRequiredRights();
        $this->assertIsArray( $requiredRights );
        $this->assertEquals( ['edit'], $requiredRights );
    }

    /**
     * @covers ::getOutputPage
     */
    public function testGetOutputPage() {
        $widget = $this->createWidgetWithOptions();
        $this->assertSame($this->output, $widget->getOutputPage());
    }

    /**
     * @covers ::getOutput
     */
    public function testGetBody() {
        $body = $this->widget->getBody();
        $this->assertIsString( $body );
        $this->assertStringContainsString( 'test-widget', $body );
    }

    /**
     * @covers ::getHookContainer
     */
    public function testGetHookContainer() {
        $widget = $this->createWidgetWithOptions();
        $hookContainer = $widget->getHookContainer();
        $this->assertInstanceOf(
            \MediaWiki\HookContainer\HookContainer::class,
            $hookContainer,
            'getHookContainer() should return a HookContainer instance'
        );
    }

    /**
     * Test that all required methods exist
     * @coversNothing
     */
    public function testRequiredMethodsExist() {
        $widget = $this->createWidgetWithOptions();
        
        // Test that required methods exist
        $this->assertTrue(
            method_exists($widget, 'getDefinition'),
            'getDefinition method should exist'
        );
        $this->assertTrue(
            method_exists($widget, 'getOutput'),
            'getOutput method should exist'
        );
        $this->assertTrue(
            method_exists($widget, 'getTemplatePath'),
            'getTemplatePath method should exist'
        );
    }

    /**
     * @covers ::renderTemplate
     */
    public function testRenderTemplate() {
        $widget = $this->createWidgetWithOptions();
        
        // Create a test template file
        $templateDir = dirname(__DIR__, 3) . '/templates';
        if (!is_dir($templateDir)) {
            mkdir($templateDir, 0777, true);
        }
        
        $templateFile = $templateDir . '/TestWidget.mustache';
        file_put_contents($templateFile, 'Hello, {{name}}!');
        
        try {
            // Test template rendering
            $result = $widget->renderTemplate('TestWidget', ['name' => 'World']);
            $this->assertSame(
                'Hello, World!', 
                $result, 
                'Template should render with variables'
            );
            
            // Test with non-existent template
            $this->expectException(\RuntimeException::class);
            $widget->renderTemplate('NonExistentWidget', []);
        } finally {
            // Clean up
            if (file_exists($templateFile)) {
                unlink($templateFile);
            }
            if (is_dir($templateDir) && count(glob("$templateDir/*")) === 0) {
                rmdir($templateDir);
            }
        }
    }
    
    /**
     * @covers ::getIcon
     */
    public function testGetIcon() {
        $widget = $this->createWidgetWithOptions();
        $icon = $widget->getIcon();
        $this->assertStringContainsString( 'icon-', $icon, 'Icon should contain "icon-" prefix' );
    }
    
    /**
     * @covers ::getTitle
     */
    public function testGetTitle() {
        $widget = $this->createWidgetWithOptions();
        $title = $widget->getTitle();
        $this->assertIsString( $title, 'Title should be a string' );
        $this->assertGreaterThan( 0, strlen( $title ), 'Title should not be empty' );
    }
    
    /**
     * @covers ::getDescription
     */
    public function testGetDescription() {
        $widget = $this->createWidgetWithOptions();
        $description = $widget->getDescription();
        $this->assertIsString($description, 'Description should be a string');
        $this->assertNotEmpty($description, 'Description should not be empty');
        $this->assertEquals('A test widget', $description, 'Description should match the expected value');
    }
}
