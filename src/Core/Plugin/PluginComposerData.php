<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Plugin;

/**
 * Wrapper for Composer data of a plugin
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class PluginComposerData
{
    private $data;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the Spress plugin name
     *
     * @return string
     */
    public function getSpressName()
    {
        $result = '';

        if (isset($this->data['extra']['spress_name'])) {
            $result = $this->data['extra']['spress_name'];
        }

        return $result;
    }

    /**
     * Get the Spress plugin entry-point
     *
     * @return string
     */
    public function getSpressClass()
    {
        $result = '';

        if (isset($this->data['extra']['spress_class'])) {
            $result = $this->data['extra']['spress_class'];
        }

        return $result;
    }
}
