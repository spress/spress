<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Core\IO;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\HelperSet;

/**
 * Console implementation
 * 
 * @author Victor Puertas
 */
class ConsoleIO implements IOInterface
{
    protected $input;
    protected $output;
    protected $helperSet;
    protected $lastMessage;
    
    public function __construct(InputInterface $input, OutputInterface $output, HelperSet $helperSet)
    {
        $this->input = $input;
        $this->output = $output;
        $this->helperSet = $helperSet;
    }
    
    public function isInteractive()
    {
        return $this->input->isInteractive();
    }
    
    /**
     * {@inheritDoc}
     */
    public function isVerbose()
    {
        return OutputInterface::VERBOSITY_VERBOSE <= $this->output->getVerbosity();
    }
    
    /**
     * {@inheritDoc}
     */
    public function isVeryVerbose()
    {
        return OutputInterface::VERBOSITY_VERY_VERBOSE <= $this->output->getVerbosity();
    }
    
    /**
     * {@inheritDoc}
     */
    public function isDebug()
    {
        return OutputInterface::VERBOSITY_DEBUG === $this->output->getVerbosity();
    }
    
    /**
     * {@inheritDoc}
     */
    public function isDecorated()
    {
        return $this->output->isDecorated();
    }

    /**
     * {@inheritDoc}
     */
    public function write($messages, $newline = true)
    {
        $this->output->write($messages, $newline);
    }
    
    /**
     * {@inheritDoc}
     */
    public function ask($question, $default = null)
    {
        return $this->helperSet->get('dialog')->ask($this->output, $question, $default);
    }
    
    /**
     * {@inheritDoc}
     */
    public function askConfirmation($question, $default = true)
    {
        return $this->helperSet->get('dialog')->askConfirmation($this->output, $question, $default);
    }
    
    /**
     * {@inheritDoc}
     */
    public function askAndValidate($question, callable $validator, $attempts = false, $default = null)
    {
        return $this->helperSet->get('dialog')->askAndValidate(
            $this->output,
            $question, 
            $validator,
            $attempts,
            $default);
    }
    
    /**
     * {@inheritDoc}
     */
    public function askAndHideAnswer($question, $fallback = true)
    {
        return $this->helperSet->get('dialog')->askHiddenResponse($this->output, $question, $fallback);
    }
    
    public function askHiddenResponseAndValidate($question, callable $validator, $attempts = false, $fallback)
    {
        return $this->helperSet->get('dialog')->askHiddenResponseAndValidate($this->output, $question, $validator, $attempts, $fallback);
    }
}