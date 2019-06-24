<?php

declare(strict_types=1); // strict mode

namespace AwdStudio\AwdInstaller;

use Composer\Config;
use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;

class AwdInstaller extends LibraryInstaller
{

    const AWD_ADDITION_TYPE = 'awd-addition';

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
        if (isset($extra['awd-additions'])) {
          $extraPath = $extra['awd-additions'];
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
        $vendorDir = $this->composer->getConfig()->get('vendor-dir', Config::RELATIVE_PATHS) ?? 'vendor';
        if ($path === $vendorDir || $path === '.') {
            throw new \InvalidArgumentException('The path ' . $path . ' is an unsafe installation directory for ' . $package->getPrettyName() . '.');
        }

        return $path;
    }

}
