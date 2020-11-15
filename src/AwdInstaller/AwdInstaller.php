<?php

declare(strict_types=1); // strict mode

namespace AwdStudio\AwdInstaller;

use Composer\Config;
use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;

class AwdInstaller extends LibraryInstaller
{
    public const AWD_ADDITION_TYPE = 'awd-addition';
    private const AWD_ADDITIONS = 'awd-additions';

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return self::AWD_ADDITION_TYPE === $packageType;
    }

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package): string
    {
        // get the extra configuration of the top-level package
        if ($rootPackage = $this->composer->getPackage()) {
            $extra = $rootPackage->getExtra();
        } else {
            $extra = [];
        }

        // use path from configuration, otherwise fall back to default
        if (isset($extra[self::AWD_ADDITIONS])) {
            $extraPath = $extra[self::AWD_ADDITIONS];
            $name = $package->getPrettyName();
            $path = \str_replace('{$name}', $name, $extraPath);
        } else {
            $path = 'awd';
        }

        // if explicitly set to something invalid (e.g. `false`), install to vendor dir
        if (!is_string($path)) {
            return parent::getInstallPath($package);
        }

        // don't allow unsafe directories
        $config = $this->composer->getConfig();
        $vendorDir = $config->get('vendor-dir', Config::RELATIVE_PATHS) ?? 'vendor';
        if ($path === $vendorDir || $path === '.') {
            throw new \InvalidArgumentException('The path ' . $path . ' is an unsafe installation directory for ' . $package->getPrettyName() . '.');
        }

        return $path;
    }
}
