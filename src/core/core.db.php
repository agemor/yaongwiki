<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */

include "core.db.account.php";

/* 모듈 생성 */
class Database {

    public $connected = false;
    public $connection;

    public $recent_error;
    public $recent_query;

    public $query_blocks = array();

    /* 데이터베이스에 접속한다 */
    public function connect() {

        // 연결은 하나만 생성한다
        if ($this->connected && isset($this->connection)) {
            return true;
        }

        $this->connection = new mysqli(DB_HOST, DB_USER_NAME, DB_USER_PASSWORD, DB_NAME);
        $this->connection->set_charset("utf8");

        // 연결 상태 테스트
        if ($this->connection->connect_error) {
            $this->recent_error = $this->connection->connect_error;
            $this->connected = false;
        } else {
            $this->connected = true;
        }

        return $this->connected;
    }

    public function rq() {
        return $this->recent_query;
    }

    public function custom($query) {

        // 연결 상태 테스트
        if (!$this->connected) {
            $this->connect();
        }

        $this->recent_query = $query;
        $result = $this->connection->query($query);

        // 실패했을 경우
        if ($result === false) {
            $this->recent_error = $this->connection->error;
            return false;
        }
        
        // 응답 반환
        if ($result->num_rows > 0) {
            $returnList = array();
            while ($row = $result->fetch_assoc()) {
                array_push($returnList, $row);
            }
            return $returnList;
        }
        return array();
    }

    /* 응답 반환이 필요하지 않은 쿼리문을 수행한다 */
    public function go() {

        // 연결 상태 테스트
        if (!$this->connected) {
            $this->connect();
        }

        // 쿼리 수행
        $query = $this->formulate();
        $this->recent_query = $query;
        $result = $this->connection->query($query);

        // 실패했을 경우
        if ($result === false) {
            $this->recent_error = $this->connection->error;
            return false;
        }
        return true;
    }

    /* 다중 응답 반환이 필요한 쿼리문을 수행한다 */
    public function go_and_get_all() {

        // 연결 상태 테스트
        if (!$this->connected) {
            $this->connect();
        }

        // 쿼리 수행
        $query = $this->formulate();
        $this->recent_query = $query;
        $result = $this->connection->query($query);

        // 실패했을 경우
        if ($result === false) {
            $this->recent_error = $this->connection->error;
            return false;
        }

        // 응답 반환
        if ($result->num_rows > 0) {
            $returnList = array();
            while ($row = $result->fetch_assoc()) {
                array_push($returnList, $row);
            }
            return $returnList;
        }
        return array();
    }

    /* 단일 응답 반환이 필요한 쿼리문을 수행한다 */
    public function go_and_get() {

        // 연결 상태 테스트
        if (!$this->connected) {
            $this->connect();
        }

        // 쿼리 수행
        $query = $this->formulate();
        $this->recent_query = $query;
        $result = $this->connection->query($query);

        // 실패했을 경우
        if ($result === false) {
            $this->recent_error = $this->connection->error;
            return false;
        }

        // 응답 반환
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function in($table) {
        $this->query_blocks["table"] = $table;
        return $this;
    }

    public function select($row) {
        $this->query_blocks["type"] = "select";
        $this->add_target($row);
        return $this;
    }

    public function update($row, $value, $raw = false) {
        $this->query_blocks["type"] = "update";
        $this->add_target(array($row, $value, $raw));
        return $this;
    }

    public function insert($row, $value) {
        $this->query_blocks["type"] = "insert";
        $this->add_target(array($row, $value));
        return $this;
    }

    public function delete() {
        $this->query_blocks["type"] = "delete";
        return $this;
    }

    public function where($row, $operator, $value, $conjunction = null, $raw_value = false) {
        if ($conjunction === null) {
            $conjunction = "AND";
        }
        if (!isset($this->query_blocks["conditions"])) {
            $this->query_blocks["conditions"] = array();
        }
        array_push($this->query_blocks["conditions"], array($row, $operator, $value, $conjunction, $raw_value));
        return $this;
    }

    public function order_by($order_function) {
        $this->query_blocks["order"] = $order_function;
        return $this;
    }

    public function limit($value) {
        $this->query_blocks["limit"] = $value;
        return $this;
    }

    private function formulate() {
        $query = "";

        // 쿼리 구성 블록은 비어있으면 안 됨
        if (empty($this->query_blocks)) {
            return $query;
        }

        // SELECT 문일 경우
        if ($this->query_blocks["type"] == "select") {
            $query .= "SELECT ";
            foreach ($this->query_blocks["target"] as $target) {
                if ($target == "*") {
                    $query .= "*,";
                } else {
                    $query .= "`".$this->escape($target)."`,";
                }
            }
            $query = rtrim($query, ",");
            $query .= " FROM `".$this->query_blocks["table"]."`";
        }

        // DELETE 문일 경우
        elseif ($this->query_blocks["type"] == "delete") {
            $query = "DELETE FROM `".$this->query_blocks["table"]."`";
        }

        // UPDATE 문일 경우
        elseif ($this->query_blocks["type"] == "update") {
            $query .= "UPDATE `".$this->query_blocks["table"]."` SET ";
            foreach ($this->query_blocks["target"] as $target) {
                $query .= "`".$this->escape($target[0])."`=";
                $query .= $target[2] ? "".$this->escape($target[1])."," : "'".$this->escape($target[1])."',";
            }
            $query = rtrim($query, ",");
        }

        // INSERT 문일 경우
        elseif ($this->query_blocks["type"] == "insert") {
            $query .= "INSERT INTO `".$this->query_blocks["table"]."` ";
            $_set = "";
            $_value = "";
            foreach ($this->query_blocks["target"] as $target) {
                $_set .= "`".$this->escape($target[0])."`,";
                $_value .= "'".$this->escape($target[1])."',";
            }
            $_set = rtrim($_set, ",");
            $_value = rtrim($_value, ",");
            $query .= " (".$_set.") VALUES (".$_value.")";
        }

        // WHERE 문
        if (isset($this->query_blocks["conditions"])) {
            $query .= " WHERE ";

            $length = count($this->query_blocks["conditions"]);
            for ($i = 0; $i < $length; $i++) {
                $condition = $this->query_blocks["conditions"][$i];
                $surrounding = $condition[4] ? "" : "`";
                $query .= $surrounding.$this->escape($condition[0]).$surrounding.$condition[1]."'".$this->escape($condition[2])."'";
                if ($i + 1 < $length) {
                    $query .= " ".$condition[3]." ";
                }
            }
        }

        // ORDER BY 문
        if (isset($this->query_blocks["order"])) {
            $query .= " ORDER BY ".$this->query_blocks["order"];
        }

        // LIMIT 문
        if (isset($this->query_blocks["limit"])) {
            $query .= " LIMIT ".$this->query_blocks["limit"];
        }

        $query .= ";";
        $this->query_blocks = array();
        return $query;
    }

    private function add_target($target) {
        if (!isset($this->query_blocks["target"])) {
            $this->query_blocks["target"] = array();
        }
        array_push($this->query_blocks["target"], $target);
    }

    private function escape($value) {
        $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
        $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
        return str_replace($search, $replace, $value);
    }
}

$db = new Database();
