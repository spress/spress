<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\DataWriter;

use Yosymfony\Spress\Core\DataSource\ItemInterface;

/**
 * Iterface for a data writer.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface DataWriterInterface
{
    /**
     * Prepare the place to store.
     * e.g: clean up the output folder.
     */
    public function setUp();

    /**
     * Write a item.
     *
     * @param \Yosymfony\Spress\Core\DataSource\ItemInterface $item
     */
    public function write(ItemInterface $item);

    /**
     * Brings down the connections to the data.
     */
    public function tearDown();
}
