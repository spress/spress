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
use Symfony\Component\Console\Question\ChoiceQuestion;
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
    /** @var InputInterface */
    protected $input;

    /** @var SymfonyStyle */
    protected $sStyle;

    /** @var array<int, int> */
    protected $verbosityMap;

    /** @var bool */
    protected $isFirstMessage = true;

    /**
     * Constructor.
     *
     * @param InputInterface  $input  The Symfony Console input
     * @param OutputInterface $output The Symfony Console Ouput
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->sStyle = new SymfonyStyle($input, $output);
        $this->verbosityMap = [
           self::VERBOSITY_QUIET => OutputInterface::VERBOSITY_QUIET,
           self::VERBOSITY_NORMAL => OutputInterface::VERBOSITY_NORMAL,
           self::VERBOSITY_VERBOSE => OutputInterface::VERBOSITY_VERBOSE,
           self::VERBOSITY_VERY_VERBOSE => OutputInterface::VERBOSITY_VERY_VERBOSE,
           self::VERBOSITY_DEBUG => OutputInterface::VERBOSITY_DEBUG,
       ];
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
        return $this->sStyle->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE;
    }

    /**
     * {@inheritdoc}
     */
    public function isVeryVerbose()
    {
        return $this->sStyle->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE;
    }

    /**
     * {@inheritdoc}
     */
    public function isDebug()
    {
        return $this->sStyle->getVerbosity() === OutputInterface::VERBOSITY_DEBUG;
    }

    /**
     * {@inheritdoc}
     */
    public function isDecorated()
    {
        return $this->sStyle->isDecorated();
    }

    /**
     * {@inheritdoc}
     */
    public function write($messages, $newline = true, $verbosity = self::VERBOSITY_NORMAL)
    {
        $sfVerbosity = $this->verbosityMap[$verbosity];

        if ($sfVerbosity > $this->sStyle->getVerbosity()) {
            return;
        }

        $this->sStyle->write($messages, $newline);
        $this->isFirstMessage = false;
    }

    /**
     * {@inheritdoc}
     * Based on ProgressBar helper of Symfony Console component.
     *
     * @see https://github.com/symfony/console/blob/master/Helper/ProgressBar.php#L470
     */
    public function overwrite($messages, $newline = true, $verbosity = self::VERBOSITY_NORMAL)
    {
        if ($this->hasSupportAnsiCommands() === false || $this->isFirstMessage === true) {
            $this->write($messages, $newline, $verbosity);

            return;
        }

        $messages = implode($newline ? PHP_EOL : '', (array) $messages);
        $numNewLines = substr_count($messages, PHP_EOL);

        $this->write("\x0D", false, $verbosity);
        $this->write("\x1B[2K", false, $verbosity);

        if ($numNewLines > 0) {
            $this->write(str_repeat("\x1B[1A\x1B[2K", $this->formatLineCount), false, $verbosity);
        }

        $this->write($messages, false, $verbosity);

        if ($newline) {
            $this->write('', true, $verbosity);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function ask($question, $default = null)
    {
        return $this->sStyle->ask($question, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function askConfirmation($question, $default = true)
    {
        return $this->sStyle->confirm($question, $default);
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

        return $this->sStyle->askQuestion($question);
    }

    /**
     * {@inheritdoc}
     */
    public function askAndHideAnswer($question, $fallback = true)
    {
        return $this->sStyle->askHidden($question);
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

        return $this->sStyle->askQuestion($question);
    }

    /**
     * {@inheritdoc}
     */
    public function askChoice($question, array $choices, $default = null, $attempts = null, $errorMessage = 'Value "%s" is invalid', $multiselect = false)
    {
        $attempts = is_int($attempts) ?: null;

        $question = new Question($question);
        $question = new ChoiceQuestion($question, $choices, $default);
        $question->setErrorMessage($errorMessage);
        $question->setMultiselect($multiselect);
        $question->setMaxAttempts($attempts);

        return $this->sStyle->askQuestion($question);
    }

    /**
     * Formats a success result bar.
     *
     * @param string|array $message The message
     */
    public function success($message)
    {
        $this->sStyle->success($message);
    }

    /**
     * Formats an error result bar.
     *
     * @param string|array $message The message
     */
    public function error($message)
    {
        $this->sStyle->error($message);
    }

    /**
     * Formats an warning result bar.
     *
     * @param string|array $message The message
     */
    public function warning($message)
    {
        $this->sStyle->warning($message);
    }

    /**
     * Formats a list.
     *
     * @param array $elements List of element to display
     */
    public function listing(array $elements)
    {
        $this->sStyle->listing($elements);
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
        $this->sStyle->newLine($count);
    }

    /**
     * Does the terminal has support for ANSI commands?
     *
     * @see http://www.inwap.com/pdp10/ansicode.txt
     *
     * @return bool
     */
    private function hasSupportAnsiCommands()
    {
        return $this->isDecorated();
    }
}
