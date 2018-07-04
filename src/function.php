<?php
# @Author: crababy
# @Date:   2018-06-22T15:53:01+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-22T15:53:54+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

use SwooleFm\Core\Application;
use SwooleFm\Config\Config;

/**
 * 运行环境校验
 * @return [type] [description]
 */
function envCheck() {
  if(version_compare(phpversion(), '7.0', '<')) {
    die("PHP version\e[31m Must >= 7.0\e[0m\n");
  }
}

function showTag($name, $value) {
  echo "\e[32m" . str_pad($name, 20, ' ', STR_PAD_RIGHT) . "\e[33m" . $value . "\e[0m\n";
}

function showHelp($options) {
  $command = '';
  $args = array_keys($options);
  if($args) {
    $command = $args[0];
  }
  switch ($command) {
    case 'start':
      echo <<<HELP_START
\e[33m操作:\e[0m
\e[31m  swoolefm start\e[0m
\e[33m简介:\e[0m
\e[36m  执行本命令可以启动框架 可选的操作参数如下\e[0m
\e[33m参数:\e[0m
\e[32m  --d \e[0m                   以守护模式启动框架
\e[32m  --ip\e[33m-address \e[0m          指定服务监听地址
\e[32m  --p\e[33m-port \e[0m              指定服务监听端口
\e[32m  --pid\e[33m-fileName \e[0m        指定服务PID存储文件
\e[32m  --log\e[33m-fileName \e[0m        指定服务LOG存储文件
\e[32m  --workerNum\e[33m-num \e[0m       设置worker进程数
\e[32m  --taskWorkerNum\e[33m-num \e[0m   设置Task进程数
\e[32m  --user\e[33m-userName \e[0m       指定以某个用户身份执行
\e[32m  --group\e[33m-groupName \e[0m     指定以某个用户组身份执行
\e[32m  --cpuAffinity \e[0m         开启CPU亲和\n
HELP_START;
      break;
    default:
      echo <<<DEFAULTHELP
\n欢迎使用\e[32m SwooleFm\e[0m 框架 当前版本: \e[34m1.x\e[0m
\e[33m使用:\e[0m
  SwooleFm [操作] [选项]
\e[33m操作:\e[0m
\e[32m  start \e[0m        启动服务
\e[32m  stop \e[0m         停止服务
\e[32m  reload \e[0m       重载服务
\e[32m  restart \e[0m      重启服务
\e[32m  help \e[0m         查看命令的帮助信息\n
\e[31m有关某个操作的详细信息 请使用\e[0m help \e[31m命令查看 \e[0m
\e[31m如查看\e[0m start \e[31m操作的详细信息 请输入\e[0m SwooleFm help --start\n\n
DEFAULTHELP;
      break;
  }
}

/**
 * [opCacheClear]
 * @return [type] [description]
 */
function opCacheClear() {
  if(function_exists('apc_clear_cache')) {
    apc_clear_cache();
  }
  if(function_exists('opcache_reset')) {
    opcache_reset();
  }
}

function logger($level, $message, $output = false) {
  if(true === $output) {
    echo $level . ':' . $message . PHP_EOL;
  }
}


function commandHandler() {
  list($command, $options) = commandParser();
  switch ($command) {
    case 'start':
      serverStart($options);
      break;
    case 'stop':
      serverStop($options);
      break;
    case 'help':
    default:
      showHelp($options);
      break;
  }
}

/**
 * [commandParser 解析]
 * @return [type] [description]
 */
function commandParser() {
  global $argv;
  $command = '';
  $options = array();
  if(isset($argv[1])) {
    $command = $argv[1];
  }
  foreach ($argv as $item) {
    if(substr($item, 0, 2) === '--') {
      $temp = trim($item, "--");
      $temp = explode("-", $temp);
      $key = array_shift($temp);
      $options[$key] = array_shift($temp) ?: '';
    }
  }
  return array($command, $options);
}

function serverStart($options) {
  $config = Config::getInstance();
  $app = Application::getInstance()->initialize();

  if(isset($options['ip'])) {
    $config->setConf('App.MAIN_SERVER.HOST', $options['ip']);
  }
  if(isset($options['p'])) {
    $config->setConf('App.MAIN_SERVER.PORT', $options['p']);
  }
  showTag('Listen Address', $config->getConf('App.MAIN_SERVER.HOST') . ':' . $config->getConf('App.MAIN_SERVER.PORT'));

  if(isset($options['pid'])) {
    $config->setConf('App.MAIN_SERVER.OPTIONS.pid_file', $options['pid']);
  }
  showTag('Server Pid File', $config->getConf('App.MAIN_SERVER.OPTIONS.pid_file'));

  if(isset($options['log'])) {
    $config->setConf('App.MAIN_SERVER.OPTIONS.log_file', $options['log']);
  }
  showTag('Swoole Log', $config->getConf('App.MAIN_SERVER.OPTIONS.log_file'));


  if(isset($options['workerNum'])) {
    $config->setConf('App.MAIN_SERVER.OPTIONS.worker_num', $options['workerNum']);
  }
  showTag('Worker Num', $config->getConf('App.MAIN_SERVER.OPTIONS.worker_num'));

  if(isset($options['taskWorkerNum'])) {
    $config->setConf('App.MAIN_SERVER.OPTIONS.task_worker_num', $options['taskWorkerNum']);
  }
  showTag('Task Num', $config->getConf('App.MAIN_SERVER.OPTIONS.task_worker_num'));

  $user = get_current_user();
  if(isset($options['user'])) {
    $config->setConf('App.MAIN_SERVER.OPTIONS.user', $options['user']);
    $user = $config->getConf('App.MAIN_SERVER.OPTIONS.user');
  }
  showTag('Run At User', $user);

  $daemonize = 'false';
  if(isset($options['d'])) {
    $config->setConf('App.MAIN_SERVER.OPTIONS.daemonize', true);
    $daemonize = 'true';
  }
  showTag('daemonize', $daemonize);

  if(isset($options['cpuAffinity'])) {
    $config->setConf('App.MAIN_SERVER.OPTIONS.open_cpu_affinity', $options['cpuAffinity']);
  }
  showTag('Debug:', $config->getConf('App.Debug') ? 'true' : 'false');
  showTag('PHP Version:', phpversion());
  showTag('Swoole Version:', phpversion('swoole'));
  $app->run();
}

function serverStop($options) {
  $config = Config::getInstance();
  Application::getInstance()->initialize();
  $pidFile = $config->getConf("App.MAIN_SERVER.OPTIONS.pid_file");
  if (!empty($options['pid'])) {
    $pidFile = $options['pid'];
  }
  if(file_exists($pidFile)) {
    $pid = file_get_contents($pidFile);
    if(!swoole_process::kill($pid, 0)) {
      showTag('Warning:', "Pid:{$pid} not exists;");
      return false;
    }
    if(in_array('-f', $options)) {
      swoole_process::kill($pid, SIGKILL);
    } else {
      swoole_process::kill($pid);
    }
    $nowTime = time();
    $flag = false;
    while (true) {
      usleep(1000);
      if(!swoole_process::kill($pid, 0)) {
        showTag('Server Stop At', date("Y-m-d H:i:s"));
        if(is_file($pidFile)) {
          unlink($pidFile);
        }
        $flag = true;
        break;
      } else {
        if(time() - $nowTime > 5) {
          showTag('Stop Server Fail, Try -f Again', '');
          break;
        }
      }
    }
    return $flag;
  } else {
    showTag('Error:', 'Pid File does not exist;');
    return false;
  }
}
