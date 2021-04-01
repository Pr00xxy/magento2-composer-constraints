<?php

declare(strict_types=1);

namespace PrOOxxy\MagentoComposerConstraints\Event;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Package\Loader\ArrayLoader;
use Composer\Package\Loader\JsonLoader;
use Composer\Package\PackageInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Composer\Semver\Semver;
use UnexpectedValueException;

define('DS', DIRECTORY_SEPARATOR);

class PostInstallCompatability implements PluginInterface, EventSubscriberInterface
{

    /**
     * @var Composer
     */
    private $composer;

    /**
     * @var IOInterface
     */
    private $io;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            'post-package-install' => ['checkModules'],
            ScriptEvents::POST_INSTALL_CMD => ['checkModules']
        ];
    }

    public function checkModules(Event $event)
    {
        $packages = $event->getComposer()->getRepositoryManager()->getLocalRepository()->getPackages();

        $installedPackages = [];
        foreach ($packages as $package) {
            $installedPackages[$package->getName()] = $package;
        }

        $vendorPath = $this->composer->getConfig()->get('vendor-dir');

        $appCodeDir = implode(DS, [$vendorPath, '..', 'app', 'code', '*', '*','composer.json']);

        $files = [];

        foreach (glob($appCodeDir) as $file) {
            $files[] = $file;
        }

        $l = new ArrayLoader();
        $jsonLoader = new JsonLoader($l);

        $packages = [];
        foreach ($files as $rawFile) {
            $jsonFile = new JsonFile($rawFile);
            try {
                $packages[] = $jsonLoader->load($jsonFile);
            } catch (UnexpectedValueException $e) {
                #$this->io->debug($e->getMessage());
            }

        }

        foreach ($packages as $package) {
            $this->doPackageContraintCheck($package, $installedPackages);
        }

    }

    private function doPackageContraintCheck(PackageInterface $package, array $installedDeps)
    {
        $requires = $package->getRequires();

        $this->io->write($package->getName());
        foreach ($requires as $link) {
            if (!array_key_exists($link->getTarget(), $installedDeps)) {
                continue;
            }

            $dependency = $installedDeps[$link->getTarget()];


            $match = Semver::satisfies($dependency->getVersion(), $link->getConstraint()->getPrettyString());
            if (!$match) {
                $this->io->writeError('<error> - '.$dependency->getName().': '.$dependency->getVersion().'</error>');
                continue;
            }
            $this->io->write('<info> - '.$dependency->getName().': '.$dependency->getVersion().'</info>');
        }

    }
}
