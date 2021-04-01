<?php

declare(strict_types=1);

namespace PrOOxxy\MagentoComposerConstraints\Event;

use Composer\Composer;
use Composer\Installer\PackageEvent;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class PostInstallCompatability implements PluginInterface, \Composer\EventDispatcher\EventSubscriberInterface
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
            'post-package-install' => ['checkModules']
        ];
    }

    public function checkModules(PackageEvent $event)
    {
    }
}
