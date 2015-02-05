<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Plugin\Event;

/**
 * Description about Spress events
 */
final class SpressEvents
{
    /**
     * The spress.start is thrown when start to generate a project. With this
     * event you can modify the configuration repository, add converters or
     * extends Twig.
     *
     * The event listener receiver an
     *  Yosymfony\Spress\Plugin\Event\EnvironmentEvent instance.
     *
     * @var string
     */
    const SPRESS_START = 'spress.start';

    /**
     * The spress.before_convert is thrown before convert the content of each page.
     *
     * The event listener receiver an
     *  Yosymfony\Spress\Plugin\Event\ConvertEvent instance.
     *
     * @var string
     */
    const SPRESS_BEFORE_CONVERT = 'spress.before_convert';

    /**
     * The spress.after_convert is thrown after convert the content of each page.
     * If the content don't have Front-matter this event never be
     * dispatcher.
     *
     * The event listener receiver an
     *  Yosymfony\Spress\Plugin\Event\ConvertEvent instance.
     *
     * @var string
     */
    const SPRESS_AFTER_CONVERT = 'spress.after_convert';

    /**
     * The spress.after_convert_posts is thrown after convert all posts.
     *
     * The event listener receiver an
     *  Yosymfony\Spress\Plugin\Event\AfterConvertPostsEvent instance.
     *
     * @var string
     */
    const SPRESS_AFTER_CONVERT_POSTS = 'spress.after_convert_posts';

    /**
     * The spress.before_render is thrown before render the content of each page.
     * If the content don't have Front-matter this event never be
     * dispatcher.
     *
     * The event listener receiver an
     *  Yosymfony\Spress\Plugin\Event\RenderEvent instance.
     *
     * @var string
     */
    const SPRESS_BEFORE_RENDER = 'spress.before_render';

    /**
     * The spress.after_render is thrown after render the content of each page.
     * If the content don't have Front-matter this event never be
     * dispatcher.
     *
     * The event listener receiver an
     *  Yosymfony\Spress\Plugin\Event\RenderEvent instance.
     *
     * @var string
     */
    const SPRESS_AFTER_RENDER = 'spress.after_render';

    /**
     * The spress.before_render_pagination is thrown before render the content of each item
     * in pagination phase.
     * If the content don't have Front-matter this event never be
     * dispatcher.
     *
     * The event listener receiver an
     *  Yosymfony\Spress\Plugin\Event\RenderEvent instance.
     *
     * @var string
     */
    const SPRESS_BEFORE_RENDER_PAGINATION = 'spress.before_render_pagination';

    /**
     * The spress.after_render_pagination is thrown after render the content of each item
     * in pagination phase.
     * If the content don't have Front-matter this event never be
     * dispatcher.
     *
     * The event listener receiver an
     *  Yosymfony\Spress\Plugin\Event\RenderEvent instance.
     *
     * @var string
     */
    const SPRESS_AFTER_RENDER_PAGINATION = 'spress.after_render_pagination';

    /**
     * The spress.finish is thrown when the site was generated. All files are
     * saved in _site folder.
     *
     * The event listener receiver an
     *  Yosymfony\Spress\Plugin\Event\FinishEvent instance.
     *
     * @var string
     */
    const SPRESS_FINISH = 'spress.finish';
}
