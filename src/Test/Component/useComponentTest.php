<?php
/*
 * Copyright (c) 2026. Wepesi Dev Framework
 */

namespace Wepesi\Core\Component;

use PHPUnit\Framework\TestCase;
use Wepesi\Test\Component\Example\Card;

class useComponentTest extends TestCase
{
    private string $configFile;

    protected function setUp(): void
    {
        // Create a temporary config file for testing
        $this->configFile = __DIR__ . '/temp_components.php';
        file_put_contents($this->configFile, '<?php return ["card"=>Wepesi\Test\Component\Example\Card::class];');
        useComponent::loadConfig($this->configFile);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->configFile)) {
            unlink($this->configFile);
        }
    }

    public function testCallStaticWithArrayRendersString(): void
    {
        $result = useComponent::card(['title' => 'Test']);
        $this->assertIsString($result);
        $this->assertStringContainsString('Test', $result);
    }

    public function testCallStaticWithoutArgumentsReturnsInstance(): void
    {
        $instance = useComponent::card();
        $this->assertInstanceOf(Card::class, $instance);
    }

    public function testCallStaticWithUnknownComponentThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Component 'unknown' not registered or not implemented.");
        useComponent::unknown();
    }
}
