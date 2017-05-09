<?php
namespace falkirks\simplewarp\permission;


use falkirks\simplewarp\Warp;
use pocketmine\permission\Permission;
use pocketmine\Server;

class SimpleWarpPermissions {
    const ADD_WARP_COMMAND = "simplewarp.command.addwarp";
    const DEL_WARP_COMMAND = "simplewarp.command.delwarp";
    const WARP_COMMAND = "simplewarp.command.warp";
    const WARP_OTHER_COMMAND = "simplewarp.command.warp.other";
    const LIST_WARPS_COMMAND = "simplewarp.command.list";
    const LIST_WARPS_COMMAND_XYZ = "simplewarp.command.list.xys";
    const LIST_WARPS_COMMAND_VISUAL = "simplewarp.command.list.visual";
    const OPEN_WARP_COMMAND = "simplewarp.command.openwarp";
    const CLOSE_WARP_COMMAND = "simplewarp.command.closewarp";
    const WARP_REPORT_COMMAND = "simplewarp.commamd.report";

    const BASE_WARP_PERMISSION = "simplewarp.warp";

    static private $baseWarpPerm = null;

    static public function setupPermission(Warp $warp){
        if(self::$baseWarpPerm == null) self::$baseWarpPerm = Server::getInstance()->getPluginManager()->getPermission("simplewarp.warp");
        $permission = new Permission(self::BASE_WARP_PERMISSION . "." . $warp->getName(), "Allow use of " . $warp->getName()); //TODO correct default value
        Server::getInstance()->getPluginManager()->addPermission($permission);
        self::$baseWarpPerm->getChildren()[$permission->getName()] = true;
    }
}