<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager\SiteAttribute;

use Yosymfony\Spress\Core\DataSource\ItemInterface;

/**
 * Interface for managing site attributes availables at compilation time.
 * e.g: "site.pages.page1".
 *
 * The site atributes is the array structure of attriutes used
 * by your site.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface SiteAttributeInterface
{
    /**
     * Adds an attribute using "dot" notation if doesn't exists.
     * You can to escape a dot in a key surrendering with brackets: "[.]".
     *
     * e.g: $a->addAttribute('site.name', 'Spress site');
     *      $a->addAttribute('site.pages.index[.]html', 'The content');
     *
     * @param string $name  The name of the attribute.
     * @param mixed  $value The value of the attribute.
     */
    public function addAttribute($name, $value);

    /**
     * Get a value using "dot" notation.
     * You can to escape a dot in a key surrendering with brackets: "[.]".
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getAttribute($name);

    /**
     * Gets the site attributes.
     *
     * @return array The attributes.
     */
    public function getAttributes();

    /**
     * Check if an attribute exists in using "dot" notation.
     * You can to escape a dot in a key surrendering with brackets: "[.]".
     *
     * @param string $name The name of the attribute.
     *
     * @return bool
     */
    public function hasAttribute($name);

    /**
     * Set an attribute using "dot" notation.
     *
     * @param string $name  The name of the attribute.
     * @param mixed  $value The value of the attribute.
     */
    public function setAttribute($name, $value);

    /**
     * Called in some phase of Spress's lifecycle:
     *  - invoqued to all items just before first `spress.before_render_block` event.
     *  - before and after of each `spress.*_render_*` event.
     *
     * @param \Yosymfony\Spress\Core\DataSource\ItemInterface $item
     */
    public function setItem(ItemInterface $item);

    /**
     * Initializes the site attributes structure.
     *
     * @param array $attributes Initial attributes.
     */
    public function initialize(array $attributes = []);
}
