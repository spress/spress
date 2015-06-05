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
     * Add a new layout
     *
     * @param string $name       The name of the layout. e.g: id, path...
     * @param string $content    The content of the layout
     * @param array  $attributes The attributes of the layout
     */
    public function addLayout($name, $content, array $attributes = []);

    /**
     * Add a new include
     *
     * @param string $name       $name The name of the include. e.g: id, path...
     * @param string $content    The content of the include
     * @param array  $attributes The attributes of the include
     */
    public function addInclude($name, $content, array $attributes = []);

    /**
     * Removes a layout.
     *
     * @param string $name The layout name.
     */
    public function removeLayout($name);

    /**
     * Removes a include.
     *
     * @param string $name The include name.
     */
    public function removeInclude($name);

    /**
     * Clears all layout registered.
     */
    public function clearLayout();

    /**
     * Clears all includes registered.
     */
    public function clearInclude();

    /**
     * Render a blocks of content (layout NOT included)
     *
     * @param string $name       The name of the content. e.g: id, path...
     * @param string $content    The content
     * @param array  $attributes The attributes for using inside the content
     *
     * @return string The block rendered
     */
    public function renderBlocks($name, $content, array $attributes);

    /**
     * Render a page completely (layout included)
     *
     * @param string $name       The name of the page. e.g: id, path...
     * @param string $content    The page content
     * @param array  $attributes The attributes for using inside the content
     *
     * @return string The page rendered
     */
    public function renderPage($name, $content, array $attributes);
}
