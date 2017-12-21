<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\SiteMetadata\Exception;

/**
 * Exception class thrown when the there is a problem loading the site metadata.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class LoadSiteMetadataException extends SiteMetadataException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null, $resource = null)
    {
        if ($message === null) {
            if ($resource === null) {
                $message = 'There is an error loading the site metadata.';
            } else {
                $message = sprintf('There is an error loading the site metadata from the resource "%s".', $resource);
            }
        }
        parent::__construct($message, $code, $previous, $resource);
    }
}
