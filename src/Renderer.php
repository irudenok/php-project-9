<?php

namespace Hexlet\Code;

class Renderer
{
    public function __construct(
        private string $templatesPath
    // phpcs:ignore error
    ) {
    }

    public function render(string $template, array $data = []): string
    {
        $content = $this->renderTemplate($template, $data);

        $layoutData = $data;
        $layoutData['content'] = $content;
        $layoutData['currentPage'] = $data['currentPage'] ?? '';

        return $this->renderTemplate('layout.phtml', $layoutData);
    }

    private function renderTemplate(string $template, array $data = []): string
    {
        extract($data);
        ob_start();
        include_once $this->templatesPath . '/' . $template; // nosonar php:S4833
        return ob_get_clean();
    }
}
