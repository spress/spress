<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\PackageManager;

use Composer\IO\BaseIO;
use Yosymfony\Spress\Core\IO\IOInterface;

/**
 * Bridge between Spress IO and Composer IO.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ComposerIOBridge extends BaseIO
{
    /** @var IOInterface */
    protected $io;

    /**
     * Constructor.
     *
     * @param IOInterface $io Spress IO
     */
    public function __construct(IOInterface $io)
    {
        $this->io = $io;
    }

    /**
     * {@inheritdoc}
     */
    public function isInteractive()
    {
        return $this->io->isInteractive();
    }

    /**
     * {@inheritdoc}
     */
    public function isVerbose()
    {
        return $this->io->isVerbose();
    }

    /**
     * {@inheritdoc}
     */
    public function isVeryVerbose()
    {
        return $this->io->isVeryVerbose();
    }

    /**
     * {@inheritdoc}
     */
    public function isDebug()
    {
        return $this->io->isDebug();
    }

    /**
     * {@inheritdoc}
     */
    public function isDecorated()
    {
        return $this->io->isDecorated();
    }

    /**
     * {@inheritdoc}
     */
    public function write($messages, $newline = true, $verbosity = self::NORMAL)
    {
        $this->io->write($messages, $newline, $verbosity);
    }

    /**
     * {@inheritdoc}
     */
    public function writeError($messages, $newline = true, $verbosity = self::NORMAL)
    {
        $this->io->write($messages, $newline, $verbosity);
    }

    /**
     * {@inheritdoc}
     * The Composer's verbosity levels match with the Spress's verbosity levels.
     * In this implementation, the param `size` is unused.
     */
    public function overwrite($messages, $newline = true, $size = null, $verbosity = self::NORMAL)
    {
        $this->io->overwrite($messages, $newline, $verbosity);
    }

    /**
     * {@inheritdoc}
     * The Composer's verbosity levels match with the Spress's verbosity levels.
     * In this implementation, the param `size` is unused.
     */
    public function overwriteError($messages, $newline = true, $size = null, $verbosity = self::NORMAL)
    {
        $this->io->overwrite($messages, $newline, $verbosity);
    }

    /**
     * {@inheritdoc}
     */
    public function ask($question, $default = null)
    {
        return $this->io->ask($question, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function askConfirmation($question, $default = true)
    {
        return $this->io->askConfirmation($question, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function askAndValidate($question, $validator, $attempts = false, $default = null)
    {
        return $this->io->askAndValidate($question, $validator, $attempts, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function askAndHideAnswer($question)
    {
        return $this->io->askAndHideAnswer($question);
    }

    /**
     * {@inheritdoc}
     */
    public function select($question, $choices, $default, $attempts = false, $errorMessage = 'Value "%s" is invalid', $multiselect = false)
    {
        return $default;
    }
}
