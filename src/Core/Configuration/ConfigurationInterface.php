<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Configuration;

/**
 * Iterface configuration loader.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface ConfigurationInterface
{
    /**
     * Loads the configuration.
     *
     * @param string $sitePath Path to Spress site (configuration filename not included). e.g: "/var/spress-site".
     * @param string $envName  Environment name. e.g: "dev", "prod"
     *
     * @return array Configuration values.
     */
    public function loadConfiguration($sitePath, $envName);
}
