<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Console;

use Composer\Autoload\ClassLoader;
use Dflydev\EmbeddedComposer\Core\EmbeddedComposerBuilder;
use Symfony\Component\Console\Application as ConsoleApplication;
use Yosymfony\Spress\Command\NewPluginCommand;
use Yosymfony\Spress\Command\NewPostCommand;
use Yosymfony\Spress\Command\NewSiteCommand;
use Yosymfony\Spress\Command\SelfUpdateCommand;
use Yosymfony\Spress\Command\SiteBuildCommand;
use Yosymfony\Spress\Command\UpdatePluginCommand;
use Yosymfony\Spress\Command\WelcomeCommand;
use Yosymfony\Spress\Core\Spress;
use Yosymfony\Spress\Plugin\ConsoleCommandBuilder;

/**
 * Spress CLI tool.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Application extends ConsoleApplication
{
    /** @var ClassLoader */
    private $classloader;

   /**
    * Constructor.
    *
    * @param ClassLoader $classloader Composer Class Loader
    */
   public function __construct(ClassLoader $classloader)
   {
       $this->classloader = $classloader;

       parent::__construct('Spress - The static site generator', Spress::VERSION);
   }

   /**
    * Returns the Composer's classloader.
    *
    * @return ClassLoader The classloader
    */
   public function getClassloader()
   {
       return $this->classloader;
   }

   /**
    * Returns an Spress instance with the minimum configuration:
    *  - Classloader.
    *  - Default configuration.
    *
    * @return Spress The Spress instance
    */
   public function getSpress()
   {
       $spress = new Spress();
       $spress['spress.config.default_filename'] = __DIR__.'/../../app/config/config.yml';
       $spress['spress.plugin.classLoader'] = $this->getClassloader();

       return $spress;
   }

   /**
    * Returns an EmbeddedComposer instance.
    *
    * @return  Dflydev\EmbeddedComposer\Core\EmbeddedComposer
    */
   public function getEmbeddedComposer($siteDir, $composerFilename, $vendorDir)
   {
       $classloader = $this->getClassloader();
       $builder = new EmbeddedComposerBuilder($classloader, $siteDir);

       return $builder
           ->setComposerFilename($composerFilename)
           ->setVendorDirectory($vendorDir)
           ->build();
   }

   /**
    * Registers the command plugins present in the current directory.
    */
   public function registerCommandPlugins()
   {
       $spress = $this->getSpress();

       try {
           $pm = $spress['spress.plugin.pluginManager'];

           $commandBuilder = new ConsoleCommandBuilder($pm);
           $consoleCommandFromPlugins = $commandBuilder->buildCommands();

           foreach ($consoleCommandFromPlugins as $consoleCommand) {
               $this->add($consoleCommand);
           }
       } catch (\Exception $e) {
       }
   }

   /**
    * Registers the standard commands of Spress.
    */
   public function registerStandardCommands()
   {
       $welcomeCommand = new WelcomeCommand();

       $this->add($welcomeCommand);
       $this->add(new SiteBuildCommand());
       $this->add(new NewSiteCommand());
       $this->add(new NewPostCommand());
       $this->add(new SelfUpdateCommand());
       $this->add(new NewPluginCommand());
       $this->add(new UpdatePluginCommand());

       $this->setDefaultCommand($welcomeCommand->getName());
   }
}
