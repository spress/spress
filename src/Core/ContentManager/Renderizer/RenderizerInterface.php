<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager\Renderizer;

/**
 * Interface for rederizers.
 *
 * Renderizer are responsible for formatting items.
 * This can be considered as a template engine.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface RenderizerInterface
{
    /**
     * Add a new layout.
     *
     * @param string $id         The identifier of the layout. e.g: path
     * @param string $content    The content of the layout
     * @param array  $attributes The attributes of the layout
     * 
     * @return void
     */
    public function addLayout(string $id, string $content, array $attributes = []): void;

    /**
     * Add a new include.
     *
     * @param string $id         The identifier of the include. e.g: path
     * @param string $content    The content of the include
     * @param array  $attributes The attributes of the include
     */
    public function addInclude(string $id, string $content, array $attributes = []): void;

    /**
     * Clears all templates registered.
     */
    public function clear(): void;

    /**
     * Render a blocks of content (layout NOT included).
     *
     * @param string $id         The identifier of the content. e.g: path
     * @param string $content    The content
     * @param array  $attributes The attributes for using inside the content
     *
     * @return string The block rendered
     *
     * @throws Yosymfony\Spress\Core\ContentManager\Renderizer\Exception\RenderException If an error occurred during
     *                                                                                   rendering the content
     */
    public function renderBlocks(string $id, string $content, array $attributes): string;

    /**
     * Render a page completely (layout included).
     *
     * @param string $id             The identifier of the page. e.g: path
     * @param string $content        The page content
     * @param string $layoutName     The name of the layout
     * @param array  $siteAttributes The attributes for using inside the content
     *
     * @return string The page rendered
     *
     * @throws Yosymfony\Spress\Core\ContentManager\Renderizer\Exception\RenderException If an error occurred during
     *                                                                                   rendering the content
     */
    public function renderPage(string $id, string $content, ?string $layoutName, array $siteAttributes): string;
}
