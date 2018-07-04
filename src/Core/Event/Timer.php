<?php
# @Author: crababy
# @Date:   2018-06-27T15:36:39+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-27T15:36:45+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace SwooleFm\Core\Event;

use SwooleFm\Core\SwooleManager;

/**
 * 定时器
 */
class Timer {

  /**
   * [loop 循环定时器]
   * @param  [type] $microSeconds [毫秒]
   * @param  [type] $callback     [description]
   * @param  [type] $args         [description]
   * @return [type]               [description]
   */
  public static function loop($microSeconds, $callback, $args = null) {
    return SwooleManager::getInstance()->getServer()->tick($microSeconds, $callback_function, $args);
  }

  /**
   * [delay 延迟定时器]
   * @param  [type] $microSeconds [毫秒]
   * @param  [type] $callback     [description]
   * @param  [type] $args         [description]
   * @return [type]               [description]
   */
  public static function delay($microSeconds, $callback, $args = null) {
    return SwooleManager::getInstance()->getServer()->after($microSeconds, $callback, $args);
  }

  /**
   * [clear 清除定时器]
   * @param  [type] $timer_id [description]
   * @return [type]           [description]
   */
  public static function clear($timer_id) {
    return SwooleManager::getInstance()->getServer()->clearTimer($timer_id);
  }
}
