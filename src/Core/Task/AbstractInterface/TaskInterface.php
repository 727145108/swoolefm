<?php
# @Author: crababy
# @Date:   2018-06-26T10:46:38+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-26T10:46:51+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace SwooleFm\Core\Task\AbstractInterface;

abstract class TaskInterface {

  private $task_id;

  private $worker_id;

  private $data = null;

  private $result = null;

  public function setTaskId($task_id) {
    $this->task_id = $task_id;
  }

  public function getTaskId() {
    return $this->task_id;
  }

  public function setWorkerId($worker_id) {
    $this->worker_id = $worker_id;
  }

  public function getWorkerId() {
    return $this->worker_id;
  }

  public function setData($data) {
    $this->data = $data;
  }

  public function getData() {
    return $this->data;
  }

  public function setResult($result) {
    $this->result = $result;
  }

  public function getResult() {
    return $this->result;
  }

  abstract function run();

  abstract function finish();

}
