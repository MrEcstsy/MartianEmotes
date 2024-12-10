<?php

namespace ecstsy\MartianEmotes;

use ecstsy\MartianEmotes\commands\EmotesCommand;
use JackMD\ConfigUpdater\ConfigUpdater;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Loader extends PluginBase {

    use SingletonTrait;

    private const CFG_VER = 1;

    public function onLoad(): void {
        self::setInstance($this);
    }

    public function onEnable(): void {
        $this->saveDefaultConfig();

        ConfigUpdater::checkUpdate($this, $this->getConfig(), "version", self::CFG_VER);

        $this->getServer()->getPluginManager()->registerEvents(new EmoteListener(), $this);

        $this->getServer()->getCommandMap()->registerAll("MartianEmotes", [
            new EmotesCommand($this, "emotes", "View the list of emotes on the server.")
        ]);
    }
}