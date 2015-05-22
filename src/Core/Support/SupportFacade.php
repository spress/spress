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
  * A simply facade for getting support classes
  *
  * @author Victor Puertas <vpgugr@gmail.com>
  */
class SupportFacade
{
    /**
     * Get an ArrayWrapper
     *
     * @return \Yosymfony\Spress\Core\Support\ArrayWrapper
     */
    public function getArrayWrapper(array $array = [])
    {
        return new ArrayWrapper($array);
    }

    /**
     * Get an AttributeResolver
     *
     * @return \Yosymfony\Spress\Core\Support\AttributesResolver
     */
    public function getAttributesResolver()
    {
        return new AttributesResolver();
    }
}
