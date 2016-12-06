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

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Buffer implementation of Spress IO based on ConsoleIO.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class BufferIO extends ConsoleIO
{
    /** @var OutputInterface */
    protected $ouput;

   /**
    * Constructor.
    *
    * @param string                        $input
    * @param int                           $verbosity
    */
   public function __construct($input = '', $verbosity = StreamOutput::VERBOSITY_NORMAL)
   {
       $input = new StringInput($input);
       $input->setInteractive(false);
       $this->output = new StreamOutput(fopen('php://memory', 'rw'), $verbosity);

       parent::__construct($input, $this->output);
   }

   /**
    * Returns the output.
    *
    * @return string
    */
   public function getOutput()
   {
       fseek($this->output->getStream(), 0);

       $output = stream_get_contents($this->output->getStream());

       $output = preg_replace_callback("{(?<=^|\n|\x08)(.+?)(\x08+)}", function ($matches) {
           $pre = strip_tags($matches[1]);

           if (strlen($pre) === strlen($matches[2])) {
               return '';
           }

           return rtrim($matches[1])."\n";
       }, $output);

       return $output;
   }
}
