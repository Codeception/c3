<?php
namespace Codeception\c3;
use \Composer\Script\Event;

class Installer
{
    public static function copyC3ToRoot(Event $event) {
        $io = $event->getIO();
        $io->write("Copying c3.php to the root of your project...");
        copy(__DIR__.DIRECTORY_SEPARATOR.'c3.php', getcwd());
        $io->write("Include c3.php into index.php in order to collect codecoverage from server scripts");
    }
}