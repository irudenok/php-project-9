<?php

namespace Hexlet\Code;

class Renderer
{
    public function __construct(
        private string $templatesPath
    ) {
    }

    public function render(string $template, array $data = []): string
    {
        $content = $this->renderTemplate($template, $data);

        $layoutData = $data;
        $layoutData['content'] = $content;

        return $this->renderTemplate('layout.phtml', $layoutData);
    }

    private function renderTemplate(string $template, array $data = []): string
    {
        extract($data);
        ob_start();
        include_once $this->templatesPath . '/' . $template;
        return ob_get_clean();
    }
}
