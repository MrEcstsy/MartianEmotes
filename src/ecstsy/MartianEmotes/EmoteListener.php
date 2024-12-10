<?php

namespace ecstsy\MartianEmotes;

use ecstsy\MartianEmotes\utils\Utils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as C;

class EmoteListener implements Listener {

    private Config $config;
    private $emotes;
    private bool $allDefault;

    public function __construct()
    {
        $this->config = Utils::getConfiguration("config.yml");

        if ($this->config !== null) {
            $this->emotes = $this->config->get("emotes", []);
            $this->allDefault = $this->config->getNested("settings.all-default", true);
        }
    }

    public function onUseEmoji(PlayerChatEvent $event): void {
        $message = $event->getMessage();
        $player = $event->getPlayer();
        
        foreach ($this->emotes as $identifier => $emote) {
            if (isset($emote['phrase'], $emote['output']) && strpos($message, $emote['phrase']) !== false) {
                if (!$this->allDefault && !$player->hasPermission("martian_emotes.$identifier")) {
                    $event->cancel();
                    $player->sendMessage(C::colorize("&r&l&4Error: &r&cYou do not have permission to use this emote."));
                    return;
                }

                $message = str_replace($emote['phrase'], $emote['output'], $message);
            }
        }

        $event->setMessage(C::colorize($message));
    }
}