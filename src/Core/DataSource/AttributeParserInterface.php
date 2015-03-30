<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\DataSource;

/**
 * Iterface for attributes parser of an item
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface AttributeParserInterface
{
    /**
     * Get the attributes of an item from a string such as YAML.
     * e.g: "layout: default"
     *
     * @return array
     */
    public function getAttributesFromString($value);

    /**
     * Get the attributes from the fronmatter of an item. Front-matter
     * block let you specify certain attributes of the page and define
     * new variables that will be available in the content.
     *
     * e.g using YAML:
     *  ---
     *   name: "Victor"
     *  ---
     *
     * @return array Array with two elements: "attributes" and "content".
     */
    public function getAttributesFromFrontmatter($value);

    /**
     * Get the content without frontmatter block
     *
     * @return string
     */
    public function getContentFromFrontmatter($value);
}
