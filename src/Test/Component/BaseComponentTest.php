<?php
/*
 * Copyright (c) 2026. Wepesi Dev Framework
 */

namespace Wepesi\Test\Component;

use Wepesi\Core\Component\Providers\BaseComponent;
use PHPUnit\Framework\TestCase;

class TestComponent extends BaseComponent
{

    protected string $content = '';

    public function content(string $content): self
    {
        $this->content = htmlspecialchars($content);
        return $this;
    }

    public function render(array $data = []): string
    {
        if (!empty($data)) {
            $this->content = htmlspecialchars($data['content'] ?? $this->content);
        }
        $attrs = $this->buildAttributes();
        return "<div{$attrs}>{$this->content}</div>";
    }
}

final class BaseComponentTest extends TestCase
{

    public function testIdSetterAndBuildAttributes(): void
    {
        $comp = new TestComponent();
        $comp->id('my-id');
        $this->assertStringContainsString('id="my-id"', $comp->render());
    }

    public function testClassSetter(): void
    {
        $comp = new TestComponent();
        $comp->class('btn btn-primary');
        $this->assertStringContainsString('class="btn btn-primary"', $comp->render());
    }

    public function testAttributeSetter(): void
    {
        $comp = new TestComponent();
        $comp->attribute('aria-label', 'close');
        $this->assertStringContainsString('aria-label="close"', $comp->render());
    }

    public function testDataSetter(): void
    {
        $comp = new TestComponent();
        $comp->data('controller', 'modal');
        $this->assertStringContainsString('data-controller="modal"', $comp->render());
    }

    public function testMultipleAttributes(): void
    {
        $comp = new TestComponent();
        $comp->id('foo')
            ->class('bar')
            ->attribute('data-test', '123')
            ->data('live', 'true');
        $html = $comp->render();
        $this->assertMatchesRegularExpression('/id="foo".*class="bar".*data-test="123".*data-live="true"/s', $html);
    }

    public function testToStringAutoRenders(): void
    {
        $comp = new TestComponent();
        $comp->content('Auto');
        $this->assertEquals('<div>Auto</div>', $comp->render());
    }

    public function testToStringCatchesExceptions(): void
    {
        $comp = new class extends BaseComponent {
            public function render(array $data = []): string
            {
                throw new \RuntimeException('Render failed');
            }
        };
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Render failed');

        $comp->render();
    }

    public function testHtmlSpecialCharsEscaping(): void
    {
        $comp = new TestComponent();
        $comp->id('"><script>')
            ->class('&')
            ->content('<b>bold</b>');
        $html = $comp->render();
        $this->assertStringContainsString('id="&quot;&gt;&lt;script&gt;"', $html);
        $this->assertStringContainsString('class="&amp;"', $html);
        $this->assertStringContainsString('&lt;b&gt;bold&lt;/b&gt;', $html);
    }
}
