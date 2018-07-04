<?php
# @Author: crababy
# @Date:   2018-06-25T16:48:00+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-25T16:48:09+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace SwooleFm\Core\Http\AbstractInterface;

use SwooleFm\Core\Http\Response;

abstract class Controller {

  private $request;

  private $response;

  public function __construct(\swoole_http_request $request, \swoole_http_response $response) {
    $this->request = $request;
    $this->response = $response;
  }

  public function _beforRequest() : void {
    echo 'this is _beforRequest Method' .PHP_EOL;
  }

  public function _afterResponse() : void {
    echo 'this is _afterResponse Method' .PHP_EOL;
  }

  protected function request() {
    return $this->request;
  }

  protected function response() {
    return $this->response;
  }

  /**
   * [writeJson 返回Json]
   * @param  integer $code       [description]
   * @param  array   $result     [description]
   * @param  [type]  $message    [description]
   * @param  integer $statusCode [description]
   * @return [type]              [description]
   */
  protected function writeJson($args) {
    $data = array(
      'code' => isset($args['code']) ? intval($args['code']) : 0,
      'result' => isset($args['result']) ? $args['result'] : null,
      'message' => isset($args['message']) ? $args['message'] : null
    );
    $this->response()->write(json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK));
  }

}
