<?php

declare(strict_types=1); // strict mode

namespace AwdStudio\AwdInstaller;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class AwdInstallerPlugin implements PluginInterface, EventSubscriberInterface
{

    /**
     * {@inheritDoc}
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $installer = $composer->getInstallationManager();
        $installer->addInstaller(new AwdInstaller($io, $composer));
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'post-package-install' => ['onPackageWasInstalled'],
        ];
    }

    /**
     * Act on a package post-installation event.
     *
     * @param \Composer\Installer\PackageEvent $event
     */
    public function onPackageWasInstalled(PackageEvent $event)
    {
        $binariesResolver = new BinariesResolver($event->getIO(), $event->getComposer());
        $binariesResolver->resolve($event->getOperation()->getPackage());
    }

}
