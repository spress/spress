<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\IO;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yosymfony\Spress\Core\IO\IOInterface;

/**
 * Symfony Console implementation of Spress IO.
 * This implementation uses the SymfonyStyle class.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ConsoleIO implements IOInterface
{
    protected $input;
    protected $io;

    /**
     * Constructor.
     *
     * @param InputInterface  $input  The Symfony Console input
     * @param OutputInterface $output The Symfony Console Ouput
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * {@inheritdoc}
     */
    public function isInteractive()
    {
        return $this->input->isInteractive();
    }

    /**
     * {@inheritdoc}
     */
    public function isVerbose()
    {
        return $this->io->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE;
    }

    /**
     * {@inheritdoc}
     */
    public function isVeryVerbose()
    {
        return $this->io->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE;
    }

    /**
     * {@inheritdoc}
     */
    public function isDebug()
    {
        return $this->io->getVerbosity() === OutputInterface::VERBOSITY_DEBUG;
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
    public function write($messages, $newline = true)
    {
        $this->io->write($messages, $newline);
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
        return $this->io->confirm($question, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function askAndValidate($question, callable $validator, $attempts = false, $default = null)
    {
        $attempts = is_int($attempts) ? $attempts : null;

        $question = new Question($question, $default);
        $question->setValidator($validator);
        $question->setMaxAttempts($attempts);

        return $this->io->askQuestion($question);
    }

    /**
     * {@inheritdoc}
     */
    public function askAndHideAnswer($question, $fallback = true)
    {
        return $this->io->askHidden($question);
    }

    /**
     * {@inheritdoc}
     */
    public function askHiddenResponseAndValidate($question, callable $validator, $attempts = false, $fallback = true)
    {
        $attempts = is_int($attempts) ?: null;

        $question = new Question($question);
        $question->setHidden(true);
        $question->setValidator($validator);
        $question->setMaxAttempts($attempts);
        $question->setHiddenFallback($fallback);

        return $this->askQuestion($question);
    }

    /**
     * Formats a success result bar.
     *
     * @param string|array $message The message
     */
    public function success($message)
    {
        $this->io->success($message);
    }

    /**
     * Formats an error result bar.
     *
     * @param string|array $message The message
     */
    public function error($message)
    {
        $this->io->error($message);
    }

    /**
     * Formats an warning result bar.
     *
     * @param string|array $message The message
     */
    public function warning($message)
    {
        $this->io->warning($message);
    }

    /**
     * Formats a list.
     *
     * @param array $elements List of element to display
     */
    public function listing(array $elements)
    {
        $this->io->listing($elements);
    }

    /**
     * Formats a pair label-value.
     *
     * @param string $label
     * @param mixed  $value
     */
    public function labelValue($label, $value)
    {
        $this->write(sprintf('<info>%s</info>: %s', $label, $value));
    }

    /**
     * Add newline(s).
     *
     * @param int $count The number of newlines
     */
    public function newLine($count = 1)
    {
        $this->io->newLine($count);
    }
}
