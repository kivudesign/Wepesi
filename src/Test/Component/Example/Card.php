<?php
/*
 * Copyright (c) 2026. Wepesi Dev Framework
 */

namespace Wepesi\Test\Component\Example;

use Wepesi\Core\Component\Providers\BaseComponent;

class Card extends BaseComponent
{
    protected string $title = '';
    protected string $content = '';
    protected string $footer = '';
    protected string $class = '';

    // Fluent setters
    public function title(string $title): self
    {
        $this->title = htmlspecialchars($title);
        return $this;
    }

    public function content(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function footer(string $footer): self
    {
        $this->footer = htmlspecialchars($footer);
        return $this;
    }

    public function class(string $class): self
    {
        $this->class = htmlspecialchars($class);
        return $this;
    }
    // Also support array data (for backward compatibility)
    public function render(array $data = []): string
    {
        // If array data is provided, use it (overrides setters)
        if (!empty($data)) {
            $this->title = htmlspecialchars($data['title'] ?? $this->title);
            $this->content = $data['content'] ?? $this->content;
            $this->footer = htmlspecialchars($data['footer'] ?? $this->footer);
            $this->class = htmlspecialchars($data['class'] ?? $this->class);
        }

        return <<<HTML
            <div class="card {$this->class}">
                <div class="card-header">{$this->title}</div>
                <div class="card-body">{$this->content}</div>
                {$this->renderFooter()}
            </div>
        HTML;
    }
    protected function renderFooter(): string
    {
        return $this->footer ? "<div class=\"card-footer\">{$this->footer}</div>" : '';
    }
}
