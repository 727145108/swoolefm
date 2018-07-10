<?php
# @Author: crababy
# @Date:   2018-06-25T09:46:41+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-25T09:46:44+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace SwooleFm\Core\Event;

use SwooleFm\Core\Tool\Logger;
use SwooleFm\Config\Config;
use SwooleFm\Core\Task\AbstractInterface\TaskInterface;

class EventRegister {

  const onStart = 'start';      //Server启动在主进程的主线程回调此函数
  const onShutdown = 'shutdown';  //Server正常结束时发生
  const onWorkerStart = 'workerStart';  //此事件在Worker进程/Task进程启动时发生
  const onWorkerStop = 'workerStop';    //此事件在worker进程终止时发生 可以回收worker进程申请的各类资源
  const onWorkerError = 'workerError';  //当worker/task_worker进程发生异常后会在Manager进程内回调此函数
  const onWorkerExit = 'workerExit';    //仅在开启reload_async特性后有效。异步重启特性
  const onConnect = 'connect';          //有新的连接进入时，在worker进程中回调
  const onReceive = 'receive';          //接收到数据时回调此函数
  const onPacket = 'packet';            //接收到UDP数据包时回调此函数
  const onClose = 'close';              //TCP客户端连接关闭后，在worker进程中回调此函数
  const onBufferFull = 'bufferFull';    //当缓存区达到最高水位时触发此事件
  const onBufferEmpty = 'bufferEmpty';  //当缓存区低于最低水位线时触发此事件
  const onTask = 'task';                //在task_worker进程内被调用
  const onFinish = 'finish';            //当worker进程投递的任务在task_worker中完成时
  const onPipeMessage = 'pipeMessage';  //工作进程收到由 sendMessage 发送的管道消息时会触发
  const onManagerStart = 'managerStart'; //管理进程启动时调用
  const onManagerStop = 'managerStop';  //管理进程结束时调用
  const onRequest = 'request';          //Http 接收到请求
  const onHandShake = 'handShake';      //WebSocket建立连接后进行握手
  const onMessage = 'message';          //WebSocket 接收到请求
  const onOpen = 'open';


  public static function onStart(\swoole_server $server) {

  }

  /**
   * [onWorkerStart 此事件在Worker进程/Task进程启动时发生]
   * @param  swoole_server $server   [description]
   * @param  int           $worker_id [description]
   * @return [type]                  [description]
   */
  public static function onWorkerStart(\swoole_server $server,int $worker_id) {
    $worker_num = Config::getInstance()->getConf('App.MAIN_SERVER.OPTIONS.worker_num');
    $server_name = Config::getInstance()->getConf('App.SERVER_NAME');
    if(PHP_OS != 'Darwin'){
      if($worker_id <= ($worker_num -1)){
        $name = "{$server_name}_Worker_{$worker_id}";
      } else {
        $name = "{$server_name}_Task_Worker_{$worker_id}";
      }
      cli_set_process_title($name);
    }
    echo '_onWorkerStart' . PHP_EOL;
    try {
      Event::tigger('_onWorkerStart', $server, $worker_id);
    } catch (\Exception $e) {
      echo "Tigger::_onWorkerStart Exception" . $e->getMessage();
    }
  }

  /**
   * [onWorkerStop 此事件在worker进程终止时发生 可以回收worker进程申请的各类资源]
   * @param  swoole_server $server    [description]
   * @param  int           $worker_id [description]
   * @return [type]                   [description]
   */
  public static function onWorkerStop(\swoole_server $server, int $worker_id) {
    echo 'onWorkerStop:' . $worker_id . PHP_EOL;
  }

  /**
   * [onWorkerError 当worker/task_worker进程发生异常后会在Manager进程内回调此函数]
   * @param  swoole_server $serv       [description]
   * @param  int           $worker_id  [description]
   * @param  int           $worker_pid [description]
   * @param  int           $exit_code  [description]
   * @param  int           $signal     [description]
   * @return [type]                    [description]
   */
  public static function onWorkerError(\swoole_server $serv, int $worker_id, int $worker_pid, int $exit_code, int $signal) {
    echo 'onWorkerError:' . $worker_id . '-' . $worker_pid . '-' . $exit_code . '-' . $signal . PHP_EOL;
  }

  /**
   * [onWorkerExit 仅在开启reload_async特性后有效。异步重启特性]
   * @param  swoole_server $server    [description]
   * @param  int           $worker_id [description]
   * @return [type]                   [description]
   */
  public static function onWorkerExit(\swoole_server $server, int $worker_id) {
    echo 'onWorkerExit:' . $worker_id . PHP_EOL;
  }

  /**
   * [onTask onTask]
   * @param  swoole_server $server    [description]
   * @param  [type]        $task_id   [description]
   * @param  [type]        $worker_id [description]
   * @param  [type]        $task      [description]
   * @return [type]                   [description]
   */
  public static function onTask(\swoole_server $server, $task_id, $worker_id, $task) {
    if(is_string($task) && class_exists($task)) {
      $task = new $task;
    }
    if($task instanceof TaskInterface) {
      try {
        $task->setTaskId($task_id);
        $task->setWorkerId($worker_id);
        $result = $task->run();
        $task->setResult($result);
        return $task;
      } catch (\Exception $e) {
        Logger::error($e);
      }
    }
    return null;
  }

  public static function onFinish(\swoole_server $server, $task_id, $task) {
    if($task instanceof TaskInterface) {
      try {
        $task->finish();
      } catch (\Exception $e) {
        Logger::error($e);
      }
    }
  }
}
