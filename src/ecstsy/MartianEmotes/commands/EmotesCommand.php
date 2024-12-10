<?php

namespace ecstsy\MartianEmotes\commands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseCommand;
use ecstsy\MartianEmotes\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as C;

class EmotesCommand extends BaseCommand {

    private Config $config;
    private $cfgEmotes;
    private $perPage;
    private $messages;

    public function prepare(): void {
        $this->setPermission($this->getPermission());
        $this->registerArgument(0, new IntegerArgument("page", true));

        $this->config = Utils::getConfiguration("config.yml");

        if ($this->config !== null) {
            $this->cfgEmotes = $this->config->get("emotes", []);
            $this->perPage = $this->config->getNested("settings.per-page", 5);
            $this->messages = $this->config->getNested("settings.emote-list", []);
        }
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!isset($this->config) || $this->config->getAll() === []) {
            $sender->sendMessage(C::colorize("&r&l&4Error: &r&cConfiguration not found or is empty."));
            return;
        }    

        $page = isset($args["page"]) ? (int) $args["page"] : 1;
        $allEmotes = array_keys($this->cfgEmotes);
        $totalPages = ceil(count($allEmotes) / $this->perPage);
    
        if ($page < 1 || $page > $totalPages) {
            $sender->sendMessage(C::colorize("&r&l&4Error: &r&cPage does not exist."));
            return;
        }
    
        $startIndex = ($page - 1) * $this->perPage;
        $emotes = array_slice($allEmotes, $startIndex, $this->perPage);
    
        foreach ($this->messages as $message) {
            $sender->sendMessage(C::colorize(str_replace(["{page}", "{total-pages}"], [$page, $totalPages], $message)));
        }
    
        foreach ($emotes as $emote) {
            $phrase = $this->cfgEmotes[$emote]['phrase'];
            $output = $this->cfgEmotes[$emote]['output'];
            $line = str_replace(["{phrase}", "{output}"], [$phrase, $output], $this->messages[2] ?? "");
            $sender->sendMessage(C::colorize($line));
        }
    }    

    public function getPermission(): string {
        return "martian_emotes.default";
    }
}
