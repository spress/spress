<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Support;

/**
 * A simply facade for getting support classes.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class SupportFacade
{
    /**
     * Gets an ArrayWrapper.
     *
     * @return \Yosymfony\Spress\Core\Support\ArrayWrapper
     */
    public function getArrayWrapper(array $array = [])
    {
        return new ArrayWrapper($array);
    }

    /**
     * Gets an StringWrapper.
     *
     * @param string $str The string.
     *
     * @return \Yosymfony\Spress\Core\Support\StringWrapper
     */
    public function getStringWrapper($str = '')
    {
        return new StringWrapper($str);
    }

    /**
     * Gets an AttributeResolver.
     *
     * @return \Yosymfony\Spress\Core\Support\AttributesResolver
     */
    public function getAttributesResolver()
    {
        return new AttributesResolver();
    }
}
