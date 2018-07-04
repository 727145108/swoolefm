<?php
# @Author: crababy
# @Date:   2018-06-25T11:20:24+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-25T11:20:28+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace SwooleFm\Core\AbstractInterface;

use SwooleFm\Core\Http\Response;

interface EventInterface {

  public static function _init() : void;

  public static function _onWorkerStart(\swoole_server $server, $worker_id) : void;

  public static function beforRequest(\swoole_http_request $request, \swoole_http_response $response) : void;

  public static function afterResponse(\swoole_http_request $request, \swoole_http_response $response) : void;
}
