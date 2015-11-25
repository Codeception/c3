<?php
namespace Codeception\c3;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Installer\LibraryInstaller;

class InstallerPlugin implements PluginInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
        $installer = new Installer($io, $composer);
        $composer->getInstallationManager()->addInstaller($installer);
    }
}

class Installer extends LibraryInstaller
{
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);
        $this->filesystem->copyThenRemove($this->getInstallPath($package) . DIRECTORY_SEPARATOR . 'c3.php', 'c3.php');
    }

    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        parent::update($repo, $initial, $target);
        $this->filesystem->copyThenRemove($this->getInstallPath($target) . DIRECTORY_SEPARATOR . 'c3.php', 'c3.php');
    }

    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::uninstall($repo, $package);
        $this->filesystem->remove('c3.php');
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return 'codeception-c3' === $packageType;
    }
}