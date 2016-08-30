<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager\Collection;

/**
 * Build a Collection manager.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class CollectionManagerBuilder
{
    /**
     * Build a collection manager with collections
     * loaded from config array.
     *
     * A config array has the following sign:
     * .Array
     * (
     *   [collection_name_1] => Array
     *        (
     *            [attribute_1] => 'value'
     *        )
     *
     *   [collection_name_2] => Array
     *        (
     *        )
     * )
     *
     * @param array $config Configuration array with data about collections
     *
     * @return Yosymfony\Spress\Core\ContentManager\Collection\CollectionManager
     *
     * @throws RuntimeException If unexpected type of data
     */
    public function buildFromConfigArray(array $config)
    {
        $cm = new CollectionManager();
        $collectionItemCollection = $cm->getCollectionItemCollection();

        foreach ($config as $collectionName => $attributes) {
            $path = $collectionName === 'pages' ? '' : $collectionName;

            if (is_array($attributes) === false) {
                throw new \RuntimeException(sprintf('Expected array at the collection: "%s".', $collectionName));
            }

            $collectionItemCollection->set($collectionName, new Collection($collectionName, $path, $attributes));
        }

        return $cm;
    }
}
