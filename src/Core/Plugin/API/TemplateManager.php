<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Plugin\API;

use Yosymfony\Spress\Core\ContentManager\Renderizer;

/**
 * API to manage templates
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class TemplateManager
{
    private $render;

    public function __construct(Renderizer $renderizer)
    {
        $this->render = $renderizer;
    }

    /**
     * Renders a Twig template.
     *
     * @param string $content The template
     * @param array  $payload Data available in the template
     */
    public function render($content, array $payload = [])
    {
        return $this->render->renderString($content, $payload);
    }
}
