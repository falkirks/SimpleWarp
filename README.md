![SimpleWarp](/resources/simplewarp-2.png)

SimpleWarp is the original warp plugin for PocketMine-MP. It allows players to move from point **A** to **B** with ease. At the core of SimpleWarp is simplicity and extensibility. Although very easy on the end user, it exposes a beast of a backend for developers to hack around with.
 
**SimpleWarp 2.0 is not compatible with older SimpleWarp and PocketMine versions.**

## 2.0 betas
2.0 is still under development and although most features are implemented, it needs to be thoroughly tested before being posted. You can get the latest beta at https://github.com/Falkirks/SimpleWarp/releases 

## Commands
| Command | Usage | Description | 
| ------- | ----- | ----------- |
| `/warp` | `/warp <name> [player]` | Warps you or another player to a specified warp. |
| `/addwarp` | `/addwarp <name> [<ip> <port>|<x> <y> <z> <level>|<player>]` | Creates a new warp at a set location. |
| `/delwarp` | `/delwarp <name>` | Deletes specified warp. |
| `/listwarps` | `/listwarps` | Prints out list of warps. |

## Permissions
```yaml
 simplewarp:
  default: op
  children:
   simplewarp.command:
    default: op
    children:
     simplewarp.command.list:
      default: true
      children:
       simplewarp.command.list.xyz:
        default: op
       simplewarp.command.list.visual:
        default: op
     simplewarp.command.addwarp:
      default: op
     simplewarp.command.delwarp:
      default: op
     simplewarp.command.warp:
      default: true
      children:
        simplewarp.command.warp.other:
         default: op
     simplewarp.command.openwarp:
      default: op
     simplewarp.command.closewarp:
       default: op
   simplewarp.warp:
    default: op
    description: Allows usage of all warps
```

## API
What good is a plugin without an API? SimpleWarp has an API which is used by it's own core components. 

### Getting access
Make sure to add the following to your `plugin.yml`

```yaml
depend: ["SimpleWarp"]
```
**Note:** If you use `softdepend` you will need to check if SimpleWarp is installed.

Now you can a copy of the API in your `onEnable` method

```php
$api = SimpleWarpAPI::getInstance($this); // This only works inside a PluginBase
```

If you want to get the instance outside your main class, you can do

```php
$api = $server->getPluginManager()->getPlugin("SimpleWarp")->getApi(); // $server is an instance of \pocketmine\Server
```