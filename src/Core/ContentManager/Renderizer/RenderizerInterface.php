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
 * Interface for rederizers
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface RenderizerInterface
{
    /**
     * Add a new layout
     *
     * @param string $name
     * @param string $content
     * @param array  $attributes
     */
    public function addLayout($name, $content, array $attributes = []);

    /**
     * Add a new include
     *
     * @param string $name
     * @param string $content
     * @param array  $attributes
     */
    public function addInclude($name, $content, array $attributes = []);

    /**
     * Render a blocks of content (layout NOT included)
     *
     * @param string $name       The name of the content
     * @param string $content    The block content
     * @param array  $attributes The attributes for using inside the content
     *
     * @return string The block rendered
     */
    public function renderBlocks($name, $content, array $attributes);

    /**
     * Render a page completely (layout included)
     *
     * @param string $name       The name of the page
     * @param string $content    The page content
     * @param array  $attributes The attributes for using inside the content
     *
     * @return string The page rendered
     */
    public function renderPage($name, $content, array $attributes);
}
