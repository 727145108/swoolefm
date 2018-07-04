<?php
# @Author: crababy
# @Date:   2018-06-26T13:07:28+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-26T13:07:46+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace SwooleFm\Core\Tool;

class Invoker {

  public static function call_user_func_array(callable $callable, array $params) {
    return call_user_func_array($callable, $params);
  }

  public static function call_user_func(callable $callable, ...$params) {
    return call_user_func($callable, ...$params);
  }
}
