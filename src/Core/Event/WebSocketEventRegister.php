<?php
# @Author: crababy
# @Date:   2018-06-25T09:44:20+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-25T09:44:28+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace SwooleFm\Core\Event;

use SwooleFm\Core\AbstractInterface\Singleton;
use SwooleFm\Core\Tool\Logger;
use SwooleFm\Core\Socket\Dispatcher;
use SwooleFm\Config\Config;

class WebSocketEventRegister extends EventRegister {

  public static function onOpen(\swoole_websocket_server $server, $request) {

  }

  public static function onMessage(\swoole_websocket_server $server, $frame) {
    $result = array();
    try {
      $result = Dispatcher::dispatch($server, $frame);
    } catch (\Exception $e) {
      Logger::error("Exception:{$e->getMessage()} in File: {$e->getFile()} {$e->getLine()}");
      $result = array('code' => $e->getCode(), 'message' =>  $e->getMessage());
    }
    $server->push($frame->fd, json_encode($result, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
  }

  public static function onRequest(\swoole_http_request $request, \swoole_http_response $response) {
    $mainServer = SwooleManager::getServer();

  }

  public static function onClose(\swoole_websocket_server $server, $fd) {

  }
}
