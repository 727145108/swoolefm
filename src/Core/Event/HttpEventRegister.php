<?php
# @Author: crababy
# @Date:   2018-06-25T09:44:20+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-25T09:44:28+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace SwooleFm\Core\Event;

use SwooleFm\Core\AbstractInterface\Singleton;
use SwooleFm\Core\Http\Response;
use SwooleFm\Core\Tool\Logger;
use SwooleFm\Core\Http\Dispatcher;
use SwooleFm\Config\Config;

class HttpEventRegister extends EventRegister {

  public static function onRequest(\swoole_http_request $request, \swoole_http_response $response) {
    echo "Begin Memory:" . memory_get_usage(true) / 1024 . PHP_EOL;
    if($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
      $response->end();
      return false;
    }
    $response->header('Access-Control-Allow-Origin', '*');
    $response->header('Access-Control-Max-Age', '86400');
    $response->header('Access-Control-Allow-Method', 'POST, GET, PUT, UPDATE, DELETE');
    $response->header('Access-Control-Allow-Headers', 'Origin, X-CSRF-Token, X-Requested-With, Content-Type, Accept');
    $response->header('Content-type', 'application/json;charset=utf-8');
    $response->status(200);
    try {
      Event::tigger('beforRequest', $request, $response);
      Dispatcher::dispatch($request, $response);
    } catch (\Exception $e) {
      $response->status(502);
      Logger::error("Exception:{$e->getMessage()} in File: {$e->getFile()} {$e->getLine()}");
      $response->write(json_encode(array('code' => $e->getCode(), 'message' =>  $e->getMessage())));
      //throw $e;
    } finally {
      Event::tigger('afterResponse', $request, $response);
    }
    $response->end();
    echo getmypid() .  " \tEnd Memory:" . memory_get_usage(true) / 1024 . PHP_EOL;
    return false;
  }
}
