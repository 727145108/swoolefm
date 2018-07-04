<?php
# @Author: crababy
# @Date:   2018-06-22T13:15:05+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-22T13:15:11+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace SwooleFm\Core;

use SwooleFm\Core\AbstractInterface\Singleton;
use SwooleFm\Core\Event\Event;
use SwooleFm\Core\Event\EventRegister;
use SwooleFm\Core\Tool\Logger;
use SwooleFm\Config\Config;

class SwooleManager {

  use Singleton;

  private $mainServer = null;

  private $serverList = [];

  /**
   * [__construct]
   */
  private function __construct() {
  }

  /**
   * 启动
   */
  public function start() : void {
    $this->createMainServer();
    $this->getServer()->start();
  }

  /**
   * 创建主进程
   * @var [type]
   */
  private function createMainServer() : void {
    $config = Config::getConf('App.MAIN_SERVER');
    switch ($config['SERVER_TYPE']) {
      case 'WEB_SERVER':
        $this->mainServer = new \swoole_http_server($config['HOST'], $config['PORT'], $config['RUN_MODEL'], $config['SOCK_TYPE']);
        $this->mainServer->on(EventRegister::onRequest, array('SwooleFm\Core\Event\HttpEventRegister', 'onRequest'));
        break;
      case 'WEB_SOCKET':
        break;
      default:
        break;
    }
    $this->mainServer->on(EventRegister::onStart, array('SwooleFm\Core\Event\EventRegister', 'onStart'));
    $this->mainServer->on(EventRegister::onWorkerStart, array('SwooleFm\Core\Event\EventRegister', 'onWorkerStart'));
    $this->mainServer->on(EventRegister::onTask, array('SwooleFm\Core\Event\EventRegister', 'onTask'));
    $this->mainServer->on(EventRegister::onFinish, array('SwooleFm\Core\Event\EventRegister', 'onFinish'));
    $this->mainServer->on(EventRegister::onWorkerStop, array('SwooleFm\Core\Event\EventRegister', 'onWorkerStop'));
    $this->mainServer->on(EventRegister::onWorkerError, array('SwooleFm\Core\Event\EventRegister', 'onWorkerError'));
    $this->mainServer->on(EventRegister::onWorkerExit, array('SwooleFm\Core\Event\EventRegister', 'onWorkerExit'));
    $this->mainServer->set($config['OPTIONS']);
  }

  /**
   * [getServer 获取server]
   * @param  [type] $serverName [description]
   * @return [type]             [description]
   */
  public function getServer($serverName = null) : ?\swoole_server {
    if($this->mainServer) {
      if($serverName === null) {
        return $this->mainServer;
      } else {
        if(isset($this->serverList[$serverName])) {
          return $this->serverList[$serverName];
        }
        return null;
      }
    }
    return null;
  }

}
