<?php
# @Author: crababy
# @Date:   2018-06-25T16:48:00+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-25T16:48:09+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace SwooleFm\Core\Socket\AbstractInterface;

use SwooleFm\Core\Http\Response;

abstract class Controller {

  private $request;

  private $server;

  public function __construct(\swoole_server $server, $frame) {
    $this->request = $frame->data;
    $this->server = $server;
  }

  public function _beforRequest() : void {
  }

  public function _afterResponse() : void {
  }

  protected function request() {
    return $this->request;
  }

  protected function server() {
    return $this->server;
  }

}
