<?php
# @Author: crababy
# @Date:   2018-06-28T11:27:53+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-28T11:27:57+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace SwooleFm\Core\Http;

class Response {

  private $response;

  public function __construct(\swoole_http_response $response) {
    return $this->response = $response;
  }

  public function response() {
    return $this->response;
  }
}
