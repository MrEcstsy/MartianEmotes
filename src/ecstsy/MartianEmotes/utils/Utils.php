<?php

namespace ecstsy\MartianEmotes\utils;

use ecstsy\MartianEmotes\Loader;
use pocketmine\utils\Config;

class Utils {

    public static array $configCache = [];

    public static function getConfiguration(string $fileName): ?Config {
        $pluginFolder = Loader::getInstance()->getDataFolder();
        $filePath = $pluginFolder . $fileName;

        if (isset(self::$configCache[$filePath])) {
            return self::$configCache[$filePath];
        }

        if (!file_exists($filePath)) {
            Loader::getInstance()->getLogger()->warning("Configuration file '$filePath' not found.");
            return null;
        }
        
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        switch ($extension) {
            case 'yml':
            case 'yaml':
                $config = new Config($filePath, Config::YAML);
                break;
    
            case 'json':
                $config = new Config($filePath, Config::JSON);
                break;
    
            default:
                Loader::getInstance()->getLogger()->warning("Unsupported configuration file format for '$filePath'.");
                return null;
        }

        self::$configCache[$filePath] = $config;
        return $config;
    }

    public static function getEmoteList(int $page): ?array {
        $config = self::getConfiguration("config.yml");
        $emotes = $config->get("emotes", []);
        $perPage = $config->getNested("settings.per-page", 5);
        $totalPages = ceil(count($emotes) / $perPage);

        if ($page < 1 || $page > $totalPages) {
            return null;
        }

        $startIndex = ($page - 1) * $perPage;
        return array_slice(array_keys($emotes), $startIndex, $perPage);
    }
}