<?php
# @Author: crababy
# @Date:   2018-06-22T16:18:17+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-22T16:18:23+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace SwooleFm\Core\Tool;

use SwooleFm\Config\Config;
use SwooleFm\Core\AbstractInterface\Singleton;

class Logger {

  use Singleton;

  const INFO = 'INFO';

  const ERROR = 'ERROR';

  protected static $log_dir;

  private function __construct() {
    self::$log_dir = Config::getInstance()->getConf('App.LogDir');
    if(!file_exists(self::$log_dir)) {
      mkdir(self::$log_dir, 0777, true);
    }
  }

  public static function log($message, $level = 'INFO') {
    $startLine = $level . "|" . getmypid() . "|" . date("m-d H:i:s ") . strtok(microtime(), " "). "|";
    if(is_array($message)) {
      $startLine .= json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		} else {
      $message = false === $message ? "false" : $message;
      $startLine .= $message . PHP_EOL;
    }
    error_log($startLine, 3,  self::$log_dir . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log');
  }

  public static function info($message) {
    static::log($message, self::INFO);
  }

  public static function error($message) {
    static::log($message, self::ERROR);
  }

}
