<?php
/**
 * Copyright © Hampus Westman 2021
 * See LICENCE provided with this module for licence details
 *
 * @author     Hampus Westman <hampus.westman@gmail.com>
 * @copyright  Copyright (c) 2021 Hampus Westman
 * @license    MIT License https://opensource.org/licenses/MIT
 * @link       https://github.com/Pr00xxy
 */

declare(strict_types=1);

namespace PrOOxxy\MagentoComposerConstraints\Event;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use PrOOxxy\MagentoComposerConstraints\Magento\ConstraintValidator;
use PrOOxxy\MagentoComposerConstraints\Magento\LoaderInterface;
use PrOOxxy\MagentoComposerConstraints\Magento\PackageLoader;

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

    public function  deactivate(Composer $composer, IOInterface $io)
    {
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => ['checkModules']
        ];
    }

    private function getPackageLoader(): PackageLoader
    {
        return new PackageLoader($this->composer, $this->io);
    }

    private function getVendorPackages(Event $event): array
    {
        return $event->getComposer()->getRepositoryManager()->getLocalRepository()->getPackages();
    }

    public function checkModules(Event $event)
    {
        $installedPackages = $this->getVendorPackages($event);
        $packages = $this->getPackageLoader()->load();

        $validator = new ConstraintValidator($this->composer, $installedPackages, $packages);

        $this->io->write('Scanning Magento 2 modules constraints..');
        foreach ($packages as $package) {
            $violations = $validator->satisfies($package);
            if (!empty($violations)) {
                $this->io->write(sprintf('<comment>[NO] %s</comment>', $package->getName()));
                foreach ($violations as $v) {
                    $this->io->write($v);
                }
            } elseif ($this->io->isVerbose()) {
                $this->io->write(sprintf('<info>[OK] %s</info>', $package->getName()));
            }
        }
    }
}
