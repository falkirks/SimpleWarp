<?php

namespace falkirks\simplewarp\command;

use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\permission\SimpleWarpPermissions;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\utils\Utils;
use pocketmine\plugin\Plugin;

class WarpReportCommand extends SimpleWarpCommand {
    private SimpleWarpAPI $api;
    public function __construct(SimpleWarpAPI $api){
        parent::__construct($api->executeTranslationItem("warpreport-cmd"), $api->executeTranslationItem("warpreport-desc"), $api->executeTranslationItem("warpreport-usage"));
        $this->api = $api;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(parent::execute($sender, $commandLabel, $args)) {
            if ($sender->hasPermission(SimpleWarpPermissions::WARP_REPORT_COMMAND)) {
                $data = $this->getOwningPlugin()->getDebugDumpFactory()->generate();
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
        }
    }

    public function getOwningPlugin(): Plugin{
        return $this->api->getSimpleWarp();
    }
}