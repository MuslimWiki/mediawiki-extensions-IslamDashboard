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

/**
 * @covers \MediaWiki\Extension\IslamDashboard\Widgets\DashboardWidget
 * @group Database
 * @group IslamDashboard
 */
class DashboardWidgetTest extends MediaWikiIntegrationTestCase {
    use MockServiceDependenciesTrait;

    /** @var WidgetManager|MockObject */
    private $widgetManager;

    /** @var OutputPage|MockObject */
    private $outputPage;

    /** @var RequestContext|MockObject */
    private $context;

    /** @var User|MockObject */
    private $user;

    /** @var DashboardWidget */
    private $widget;
    
    /** @var array Default widget options */
    private const DEFAULT_OPTIONS = [
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

    protected function setUp(): void {
        parent::setUp();

        // Create mock User
        $this->user = $this->createMock(User::class);

        // Create mock RequestContext
        $this->context = $this->createMock(RequestContext::class);
        $this->context->method('getUser')
            ->willReturn($this->user);

        // Create mock OutputPage
        $this->outputPage = $this->createMock(OutputPage::class);
        $this->outputPage->method('getContext')
            ->willReturn($this->context);
        $this->outputPage->method('getTitle')
            ->willReturn($this->createMock(Title::class));

        // Create mock WidgetManager
        $this->widgetManager = $this->createMock(WidgetManager::class);

        // Set up service container
        $services = MediaWikiServices::getInstance();
        $this->setService('WidgetManager', $this->widgetManager);
    }

    /**
     * Create a test widget instance with default options
     */
    private function createTestWidget(array $options = []): DashboardWidget {
        $mergedOptions = array_merge(self::DEFAULT_OPTIONS, $options);
        
        return new class('test-widget', $mergedOptions, $this->widgetManager) extends DashboardWidget {
            public function getType(): string {
                return 'test-widget';
            }
            
            public function getContent(array $params = []): string {
                return 'Test widget content';
            }
            
            public function getOutputPage() {
                global $wgOut;
                return $wgOut;
            }
        };
    }
    
    /**
     * Test widget creation with default options
     */
    public function testCreateWidgetWithDefaults() {
        $widget = $this->createTestWidget();
        $this->assertInstanceOf(DashboardWidget::class, $widget);
        $this->assertInstanceOf('MediaWiki\Extension\IslamDashboard\Widgets\WidgetManager', $this->widgetManager);
    }

    /**
     * Test basic widget properties
     */
    public function testWidgetProperties() {
        $widget = $this->createTestWidget();
        
        // Test basic properties
        $this->assertInstanceOf(DashboardWidget::class, $widget);
        $this->assertSame('test-widget', $widget->getId());
        $this->assertSame('Test Widget', $widget->getTitle());
        $this->assertSame('A test widget', $widget->getDescription());
        $this->assertSame('test-icon', $widget->getIcon());
        $this->assertTrue($widget->isReloadable());
        $this->assertTrue($widget->isCollapsible());
        $this->assertTrue($widget->isRemovable());
    }

    /**
     * Test template path generation
     */
    public function testTemplatePath() {
        $widget = $this->createTestWidget();
        $templatePath = $widget->getTemplatePath();
        $this->assertInternalType('string', $templatePath);
        $this->assertStringContainsString('templates/widgets/test-widget.mustache', $templatePath);
    }

    /**
     * Test widget content rendering
     */
    public function testRender() {
        $widget = $this->createTestWidget();
        $output = $widget->render();
        $this->assertInternalType('string', $output);
        $this->assertContains('Test widget content', $output);
    }

    /**
     * Test widget with custom options
     */
    public function testWidgetWithCustomOptions() {
        $customWidget = $this->createTestWidget([
            'title' => 'Custom Widget',
            'description' => 'A custom test widget',
            'icon' => 'custom-icon',
            'permissions' => ['edit'],
            'reloadable' => false,
            'collapsible' => false,
            'removable' => false
        ]);

        $this->assertSame('Custom Widget', $customWidget->getTitle());
        $this->assertSame('A custom test widget', $customWidget->getDescription());
        $this->assertSame('custom-icon', $customWidget->getIcon());
        $this->assertFalse($customWidget->isReloadable());
        $this->assertFalse($customWidget->isCollapsible());
        $this->assertFalse($customWidget->isRemovable());
    }

    /**
     * Test widget permissions
     */
    public function testPermissions() {
        $widget = $this->createTestWidget();
        $this->assertIsArray($widget->options['permissions']);
        $this->assertContains('read', $widget->options['permissions']);
    }

    /**
     * Test required rights for the widget
     */
    public function testGetRequiredRights() {
        $widget = $this->createTestWidget([
            'requiredRights' => ['edit']
        ]);
        $this->assertIsArray($widget->options['requiredRights']);
        $this->assertEquals(['edit'], $widget->options['requiredRights']);
    }

    /**
     * Test getting the output page
     */
    public function testGetOutputPage() {
        $widget = $this->createTestWidget();
        $output = $widget->getOutputPage();
        $this->assertInstanceOf('OutputPage', $output);
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
