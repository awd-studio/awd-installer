<?php

declare(strict_types=1); // strict mode

namespace AwdStudio\AwdInstaller;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\Package;
use Composer\Util\Filesystem;

final class BinariesResolver
{

    /** @var IOInterface */
    private $io;

    /** @var \Composer\Composer */
    private $composer;

    /** @var \Composer\Config */
    private $config;

    /**
     * BinariesResolver constructor.
     *
     * @param \Composer\IO\IOInterface $io
     * @param \Composer\Composer       $composer
     */
    public function __construct(IOInterface $io, Composer $composer)
    {
        $this->io = $io;
        $this->composer = $composer;
        $this->config = $this->composer->getConfig();
    }

    /**
     * Resolves all binaries for a package.
     *
     * @param \Composer\Package\Package $package
     */
    public function resolve(Package $package): void
    {
        if ($binaries = $package->getBinaries()) {
            foreach ($binaries as $binary) {
                $this->handleBinary($binary, $package);
            }
        }
    }

    /**
     * Resolves the internal path for a package.
     *
     * @param string $packageName
     *
     * @return string|null
     */
    private function findPackagePath(string $packageName): ?string
    {
        $repositoryManager = $this->composer->getRepositoryManager();
        $installationManager = $this->composer->getInstallationManager();
        $localRepository = $repositoryManager->getLocalRepository();
        $packages = $localRepository->getPackages();
        foreach ($packages as $package) {
            if ($package->getName() === $packageName) {
                return $installationManager->getInstallPath($package);
            }
        }

        return null;
    }

    /**
     * Finds the full path for a binary file.
     *
     * @param string                    $binary
     * @param \Composer\Package\Package $package
     *
     * @return string
     */
    private function resolveBinFile(string $binary, Package $package): string
    {
        $targetDir = $this->findPackagePath($package->getName());
        $vendorPath = $this->config->get('vendor-dir');

        return \dirname($vendorPath) . '/' . $targetDir . $binary;
    }

    /**
     * Creates symlinks for library binary files.
     *
     * @param string                    $binary
     * @param \Composer\Package\Package $package
     */
    private function handleBinary(string $binary, Package $package): void
    {
        $bin = $this->resolveBinFile($binary, $package);
        if (\is_readable($bin)) {
            $binDir = $this->config->get('bin-dir');
            $target = $binDir . '/' . $binary;
            (new Filesystem())->relativeSymlink($target, $bin);
        } else {
            $this->io->writeError('Target "' . $bin . '" - does not exists!');
        }
    }


}
