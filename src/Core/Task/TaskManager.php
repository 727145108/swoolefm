<?php
# @Author: crababy
# @Date:   2018-06-26T10:36:49+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-26T10:36:55+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License


namespace SwooleFm\Core\Task;

use SwooleFm\Core\SwooleManager;

/**
 * TaskManager
 */
class TaskManager {

  public static function async($task, $callback = null, $worker_id = -1) {
    return SwooleManager::getInstance()->getServer()->task($task, $worker_id, $callback);
  }

  public static function sync($task, $time_out = 0.5, $worker_id = -1) {
    return SwooleManager::getInstance()->getServer()->taskwait($task, $time_out, $worker_id);
  }
}
