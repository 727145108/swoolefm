<?php
# @Author: crababy
# @Date:   2018-06-25T16:17:06+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-25T16:17:11+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace SwooleFm\Core\Http;

use SwooleFm\Core\AbstractInterface\Singleton;
use SwooleFm\Core\Http\Response;
use SwooleFm\Core\Tool\Logger;
use SwooleFm\Config\Config;

class Dispatcher {

  public static function dispatch(\swoole_http_request $request, \swoole_http_response $response) {
    $request_uri = $request->server['request_uri'];
    list(, $class, $method) = explode('/', $request_uri);
    if(!isset($class) || !isset($method)) {
      throw new \Exception('无效的请求地址');
    }
    $classController = Config::getInstance()->getConf('App.ControllerNameSpace') . ucfirst($class);
    if(!class_exists($classController) || !method_exists($classController, $method)) {
      throw new \Exception('无效的请求');
    }
    try {
      $controller = new $classController($request, $response);
      $controller->_beforRequest();
      $controller->$method();
    } catch (\Exception $e) {
      throw $e;
    } finally {
      $controller->_afterResponse();
    }
    return false;
  }
}
