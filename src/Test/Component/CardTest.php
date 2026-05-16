<?php
/*
 * Copyright (c) 2026. Wepesi Dev Framework
 */
declare(strict_types=1);

namespace Wepesi\Test\Component\Example;

use PHPUnit\Framework\TestCase;
use Wepesi\Test\Component\Example\Card;

class CardTest extends TestCase
{
    public function testFluentSetters(): void
    {
        $card = new Card();
        $card->title('My Title')
            ->content('Some content')
            ->footer('Footer text')
            ->class('card-class');

        $html = $card->render();
        $this->assertStringContainsString('<div class="card-header">My Title</div>', $html);
        $this->assertStringContainsString('<div class="card-body">Some content</div>', $html);
        $this->assertStringContainsString('<div class="card-footer">Footer text</div>', $html);
        $this->assertStringContainsString('class="card card-class"', $html);
    }

    public function testArrayStyleRender(): void
    {
        $card = new Card();
        $html = $card->render([
            'title' => 'Array Title',
            'content' => 'Array Body',
            'footer' => 'Array Footer',
            'class' => 'array-card'
        ]);
        $this->assertStringContainsString('Array Title', $html);
        $this->assertStringContainsString('Array Body', $html);
        $this->assertStringContainsString('Array Footer', $html);
        $this->assertStringContainsString('array-card', $html);
    }

    public function testNoFooterWhenEmpty(): void
    {
        $card = new Card();
        $card->title('No footer');
        $html = $card->render();
        $this->assertStringNotContainsString('card-footer', $html);
    }

    public function testEscaping(): void
    {
        $card = new Card();
        $card->title('<script>alert(1)</script>');
        $html = $card->render();
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }
}
