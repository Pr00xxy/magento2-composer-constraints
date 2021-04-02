<?php

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

    protected function getPackageLoader(): LoaderInterface
    {
        return new PackageLoader($this->composer, $this->io);
    }

    protected function getVendorPackages(Event $event): array
    {
        return $event->getComposer()->getRepositoryManager()->getLocalRepository()->getPackages();
    }

    public function checkModules(Event $event)
    {
        $installedPackages = $this->getVendorPackages($event);
        $packages = $this->getPackageLoader()->load();

        $validator = new ConstraintValidator($this->composer, $installedPackages, $packages);

        foreach ($packages as $package) {
            $this->io->debug($package->getName());
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
