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
    $result = array(
      'header'  => array(
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Max-Age' => '86400',
        'Access-Control-Allow-Method' => 'POST, GET, PUT, UPDATE, DELETE',
        'Access-Control-Allow-Headers' => 'Origin, X-CSRF-Token, X-Requested-With, Content-Type, Accept',
        'Content-type' => 'application/json;charset=utf-8',
      )
    );
    Event::tigger('beforRequest', $request, $response);
    try {
      $res = Dispatcher::dispatch($request, $response);
      $result['header'] = array_merge($result['header'], isset($res['header']) ? $res['header'] : array());
      $result['status'] = isset($res['status']) ? $res['status'] : 200;
      $result['result'] = $res['result'];
    } catch (\Exception $e) {
      Logger::error("Exception:{$e->getCode()}  {$e->getMessage()} in File: {$e->getFile()} {$e->getLine()}");
      $result['status'] = 502;
      $result['result'] = array('code' => $e->getCode(), 'message' => $e->getMessage());
      //throw $e;
    }
    if(isset($result['header']) && is_array($result['header'])) {
      foreach ($result['header'] as $item => $val) {
        $response->header($item, $val);
      }
    }
    $response->status($result['status']);
    Event::tigger('afterResponse', $request, $response);
    $response->write(json_encode($result['result'], JSON_UNESCAPED_UNICODE));
    echo getmypid() .  " \tEnd Memory:" . memory_get_usage(true) / 1024 . PHP_EOL;
    return false;
  }
}
