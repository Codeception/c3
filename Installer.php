<?php
namespace Codeception\c3;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;

class InstallerPlugin implements PluginInterface
{
    /**
     * @var IOInterface
     */
    private $io;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->io = $io;
    }

    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::POST_INSTALL_CMD => array(
                array('copyC3', 0)
            ),
            ScriptEvents::POST_UPDATE_CMD => array(
                array('askForUpdate', 0)
            )
        );
    }

    public function copyC3()
    {
        if ($this->c3HasChanged()) {
            $this->io->write("<info>[c3]</info> c3.php is already up-to-date");
            return;
        }

        $this->io->write("<info>[c3]</info> Copying c3.php to the root of your project...");
        copy(__DIR__.DIRECTORY_SEPARATOR.'c3.php', getcwd().DIRECTORY_SEPARATOR.'c3.php');
        $this->io->write("<info>[c3]</info> Include c3.php into index.php in order to collect codecoverage from server scripts");
    }

    public function askForUpdate()
    {
        if (!$this->c3HasChanged()) return;
        $replace = $this->io->askConfirmation("Do you want to replace c3.php with latest version?", false);
        if (!$replace) return;
        $this->copyC3();
    }

    private function c3HasChanged()
    {
        return file_exists(getcwd().DIRECTORY_SEPARATOR.'c3.php') &&
            md5_file(__DIR__.DIRECTORY_SEPARATOR.'c3.php') === md5_file(getcwd().DIRECTORY_SEPARATOR.'c3.php');
    }

}

