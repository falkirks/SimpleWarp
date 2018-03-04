<?php

namespace falkirks\simplewarp\command;

use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\event\WarpCloseEvent;
use falkirks\simplewarp\permission\SimpleWarpPermissions;
use falkirks\simplewarp\SimpleWarp;
use falkirks\simplewarp\Version;
use falkirks\simplewarp\Warp;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;
use pocketmine\plugin\Plugin;

class WarpReportCommand extends SimpleWarpCommand {
    private $api;
    public function __construct(SimpleWarpAPI $api){
        parent::__construct($api->executeTranslationItem("warpreport-cmd"), $api->executeTranslationItem("warpreport-desc"), $api->executeTranslationItem("warpreport-usage"));
        $this->api = $api;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     *
     * @return mixed
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(parent::execute($sender, $commandLabel, $args)) {
            if ($sender->hasPermission(SimpleWarpPermissions::WARP_REPORT_COMMAND)) {
                $data = $this->getPlugin()->getDebugDumpFactory()->generate();
                if ($sender instanceof ConsoleCommandSender) {
                    $issueContent = "\n\n(Explain your problem here)\n\n```\n$data\n```";
                    $url = "https://github.com/Falkirks/SimpleWarp/issues/new" . (count($args) > 0 ? "?title=" . urlencode(implode(" ", $args)) . "\&" : "?") . "body=" . urlencode($issueContent);
                    switch (Utils::getOS()) {
                        case 'win':
                            `start $url`;
                            break;
                        case 'mac':
                            `open $url`;
                            break;
                        case 'linux':
                            `xdg-open $url`;
                            break;
                        default:
                            $sender->sendMessage("Copy and paste the following URL into your browser to start a report.");
                            $sender->sendMessage("------------------");
                            $sender->sendMessage($url);
                            $sender->sendMessage("------------------");
                            break;
                    }
                }
                $sender->sendMessage("--- SimpleWarp Data ---");
                $sender->sendMessage($data);
            }
            else {
                $sender->sendMessage($this->api->executeTranslationItem("warpreport-noperm"));
            }
            return true;
        }
    }


    /**
     * @return \pocketmine\plugin\Plugin
     */
    public function getPlugin(): Plugin{
        return $this->api->getSimpleWarp();
    }
}