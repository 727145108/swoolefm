<?php
# @Author: crababy
# @Date:   2018-06-25T16:17:06+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-25T16:17:11+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace SwooleFm\Core\Socket;

use SwooleFm\Core\AbstractInterface\Singleton;
use SwooleFm\Core\Tool\Logger;
use SwooleFm\Config\Config;

class Dispatcher {

  public static function dispatch(\swoole_server $server, $frame) {
    $class = isset($_params['class']) ? $_params['class'] : 'Socket';
    $action = isset($_params['action']) ? $_params['action'] : 'Index';

    $classController = Config::getInstance()->getConf('App.ControllerNameSpace') . ucfirst($class);
    if(!class_exists($classController) || !method_exists($classController, $action)) {
      throw new \Exception('无效的请求');
    }
    $controller = new $classController($server, $frame);
    $controller->_beforRequest();
    $res = $controller->$action();
    return $res;
  }
}
