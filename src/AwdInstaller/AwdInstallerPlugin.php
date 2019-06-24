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
        $this->resolveAdditionTypes($event);
    }

    /**
     * Processes packages with a type "awd-additions".
     *
     * @param \Composer\Installer\PackageEvent $event
     */
    private function resolveAdditionTypes(PackageEvent $event): void
    {
        /** @var \Composer\Package\Package $package */
        $package = $event->getOperation()->getPackage();
        if (AwdInstaller::AWD_ADDITION_TYPE === $package->getType()) {
            $binariesResolver = new BinariesResolver($event->getIO(), $event->getComposer());
            $binariesResolver->resolve($package);
        }
    }

}
