<?php
namespace Codeception\c3;
use Composer\Composer;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UninstallOperation;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Composer\Semver\Comparator;

class Installer implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var IOInterface
     */
    private $io;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    public function uninstall(Composer $composer, IOInterface $io) {
        $this->deleteFile();
    }

    protected function isOperationOnC3(PackageEvent $event)
    {
        if (static::composerV2()) {
            return true;
        }
        $name = '';

        if ($event->getOperation() instanceof InstallOperation) {
            list(, $name) = explode('/', $event->getOperation()->getPackage()->getName());
        } elseif ($event->getOperation() instanceof UpdateOperation) {
            list(, $name) = explode('/', $event->getOperation()->getTargetPackage()->getName());
        } elseif ($event->getOperation() instanceof UninstallOperation) {
            list(, $name) = explode('/', $event->getOperation()->getPackage()->getName());
        }

        return $name === 'c3';
    }
    
    public static function getSubscribedEvents()
    {
        if (static::composerV2()) {
            return [
                ScriptEvents::POST_INSTALL_CMD => [
                    ['copyC3V2', 0]
                ],
                ScriptEvents::POST_UPDATE_CMD => [
                    ['askForUpdateV2', 0]
                ],
            ];
        }
        return [
            PackageEvents::POST_PACKAGE_INSTALL => [
                ['copyC3', 0]
            ],
            PackageEvents::POST_PACKAGE_UPDATE => [
                ['askForUpdate', 0]
            ],
            PackageEvents::POST_PACKAGE_UNINSTALL => [
                ['deleteC3', 0]
            ]
        ];
    }

    public function copyC3(PackageEvent $event)
    {
        if (!$this->isOperationOnC3($event)) {
            return;
        }

        $this->copyC3V2(null);
    }

    public function copyC3V2(Event $event)
    {
        if ($this->c3NotChanged()) {
            $this->io->write("<comment>[codeception/c3]</comment> c3.php is already up-to-date");
            return;
        }

        $this->io->write("<comment>[codeception/c3]</comment> Copying c3.php to the root of your project...");
        copy(__DIR__ . DIRECTORY_SEPARATOR . 'c3.php', getcwd() . DIRECTORY_SEPARATOR.'c3.php');
        $this->io->write("<comment>[codeception/c3]</comment> Include c3.php into index.php in order to collect codecoverage from server scripts");
    }

    public function askForUpdate(PackageEvent $event)
    {
        if (!$this->isOperationOnC3($event) || $this->c3NotChanged()) {
            return;
        }
        if (file_exists(getcwd() . DIRECTORY_SEPARATOR . 'c3.php')) {
            $replace = $this->io->askConfirmation("<warning>c3.php has changed</warning> Do you want to replace c3.php with latest version?", false);
            if (!$replace) {
                return;
            }
        }
        $this->copyC3($event);
    }

    private function c3NotChanged()
    {
        return file_exists(getcwd() . DIRECTORY_SEPARATOR . 'c3.php') &&
            md5_file(__DIR__ . DIRECTORY_SEPARATOR . 'c3.php') === md5_file(getcwd() . DIRECTORY_SEPARATOR . 'c3.php');
    }

    public function deleteC3(PackageEvent $event)
    {
        if (!$this->isOperationOnC3($event)) {
            return;
        }
        $this->deleteFile();
    }

    private function deleteFile() {
        if (file_exists(getcwd() . DIRECTORY_SEPARATOR . 'c3.php')) {
            $this->io->write("<comment>[codeception/c3]</comment> Deleting c3.php from the root of your project...");
            unlink(getcwd() . DIRECTORY_SEPARATOR . 'c3.php');
        }
    }

    private static function composerV2()
    {
        return Comparator::greaterThanOrEqualTo(PluginInterface::PLUGIN_API_VERSION, '2.0.0');
    }

}
