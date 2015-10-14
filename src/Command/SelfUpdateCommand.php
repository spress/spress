<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yosymfony\Spress\IO\ConsoleIO;

/**
 * SelfUpdate command.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class SelfUpdateCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setAliases(['selfupdate'])
            ->setDescription('Update spress.phar to the latest version')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command replace your spress.phar by the
latest version from spress.yosymfony.com.
<info>php spress.phar %command.name%</info>
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleIO($input, $output, $this->getHelperSet());

        if ($this->isInstalledAsPhar() === false) {
            $io->write('<error>Self-update is available only for PHAR version.</error>');

            return 1;
        }

        $manifest = $this->getManifestFile();
        $remoteVersion = $manifest['version'];
        $localVersion = $this->getApplication()->getVersion();

        if ($localVersion === $remoteVersion) {
            $io->write('<info>Spress is already up to date.</info>');

            return;
        }

        $remoteFilename = $manifest['url'];
        $localFilename = $_SERVER['argv'][0];
        $tempFilename = basename($localFilename, '.phar').'-tmp.phar';

        $this->downloadRemoteFilename($remoteFilename);

        try {
            copy($remoteFilename, $tempFilename);
            chmod($tempFilename, 0777 & ~umask());

            $phar = new \Phar($tempFilename);

            unset($phar);
            rename($tempFilename, $localFilename);

            $io->write(sprintf('<success>Spress updated from %s to %s.</success>', $localVersion, $remoteVersion));

            if (isset($manifest['changelog_url']) === true) {
                $io->write(sprintf('<info>Go to %s for more details.</info>', $manifest['changelog_url']));
            }
        } catch (\Exception $e) {
            if ($e instanceof \UnexpectedValueException === false && $e instanceof \PharException === false) {
                throw $e;
            }

            unlink($tempFilename);

            $io->write(sprintf('<error>The download is corrupt (%s).</error>', $e->getMessage()));
            $io->write('<error>Please re-run the self-update command to try again.</error>');

            return 1;
        }
    }

    protected function getManifestFile()
    {
        if (false === $manifest = @file_get_contents('http://spress.yosymfony.com/download/version.json')) {
            throw new \RuntimeException('Unable to download the manifest file from the server.');
        }

        $manifest = json_decode($manifest, true);

        if (is_array($manifest) === false || isset($manifest[0]['url']) === false || isset($manifest[0]['version']) === false) {
            throw new \RuntimeException('The manifest file is corrupt.');
        }

        return $manifest[0];
    }

    protected function downloadRemoteFilename($remoteFilename)
    {
        if (@file_get_contents($remoteFilename) === false) {
            throw new \RuntimeException('Unable to download new versions from the server.');
        }
    }

    /**
     * Is installed Spress as Phar file?
     *
     * @return bool
     */
    protected function isInstalledAsPhar()
    {
        return substr(__DIR__, 0, 7) === 'phar://';
    }
}
