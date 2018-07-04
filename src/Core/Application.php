<?php
# @Author: crababy
# @Date:   2018-06-22T13:15:05+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-22T13:15:11+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace SwooleFm\Core;

use SwooleFm\Core\AbstractInterface\Singleton;
use SwooleFm\Core\Event\Event;
use SwooleFm\Config\Config;
use SwooleFm\Core\Tool\Logger;
use SwooleFm\SwooleFmEvent;


class Application {

  use Singleton;

  /**
   * [__construct 初始化Application]
   */
  public function __construct() {
    Event::on('_init', function() {
      //初始化日志
      Logger::getInstance();

      $this->errorHandle();
    });
  }

  public function initialize(): Application {
    $runtime = Config::getConf('App.RuntimeDir');
    if(empty($runtime)) {
      $runtime = SWOOLERM_ROOT . DIRECTORY_SEPARATOR . 'Runtime';
      Config::setConf('App.RuntimeDir', $runtime);
    }
    $log_dir = Config::getConf('App.LogDir');
    if(empty($log_dir)) {
      $log_dir = $runtime . DIRECTORY_SEPARATOR . 'Logs';
      Config::setConf('App.LogDir', $log_dir);
    }
    if(!file_exists($runtime)) {
      mkdir($runtime, 0777, true);
    }
    Config::setConf('App.MAIN_SERVER.OPTIONS.pid_file', $runtime . DIRECTORY_SEPARATOR . 'pid.pid');
    Config::setConf('App.MAIN_SERVER.OPTIONS.log_file', $runtime . DIRECTORY_SEPARATOR . 'swoole.log');

    Event::tigger('_init');
    //SwooleFmEvent::_init();

    return $this;
  }

  public function run() : void {
    SwooleManager::getInstance()->start();
  }

  private function errorHandle() : void {
    $func = function() {
      echo 'register shutdown function ' . PHP_EOL;
    };
    register_shutdown_function($func);
  }

}
