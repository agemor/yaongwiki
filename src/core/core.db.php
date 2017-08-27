<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */

/*
 * 데이터베이스 접속 정보
 */
class YwDatabaseConnectInfo {
    
    // 서버 정보
    public $host = "localhost";
    public $database;
    public $port = 3306;
    
    // 유저 정보
    public $user = "root";
    public $password;
    
    function __construct($host, $user, $password, $database) {
        $this->host = $host;
        $this->database = $database;
        $this->user = $user;
        $this->password = $password;
    }
}

/*
 * 데이터베이스 접속 모듈
 */
class YwDatabase {
    
    // MySQL 연결
    private $connection;
    public $connect_info;
    
    // 연결 상태
    public $connected = false;
    public $error;
    
    // 결과 데이터
    private $result;
    
     function __construct($connect_info) {
        $this->connect_info = $connect_info;
    }

    // 데이터베이스 접속
    public function connect() {
        $this->connection = new mysqli($this->connect_info->host, 
                                       $this->connect_info->user, 
                                       $this->connect_info->password, 
                                       $this->connect_info->database);
        if ($this->connection->connect_errno) {
            $this->connected = false;
        } else {
            $this->connected = true;
        }
        return $this->connected;
    }
    
    public function log($name, $behavior, $data) {
        $this->query("INSERT INTO ".LOG_TABLE." (`ip`, `user_name`, `behavior`, `data`) VALUES ('"
        .$_SERVER['REMOTE_ADDR']."', '".$this->purify($name)."', '".$this->purify($behavior)."', '".$this->purify($data)."');");
    }
    
    public function error() {
        return $this->connection->error;
    }

    public function purify($text) {
        return $this->connection->real_escape_string($text);
    }
    
    public function query($query) {
        
        if (!$this->connected)
            return false;
        if (empty($query))
            return false;

        $this->result = $this->connection->query($query);
        if (!$this->result)
            return false;
        return true;
    }
    
    public function total_results() {
        return $this->result->num_rows;
    }
    
    public function get_result() {
        return $this->result->fetch_assoc();
    }
    
    public function close() {
        $this->connection->close();
    }
    
}

// 테이블 이름
const USER_TABLE = '`'.DB_NAME.'`.`yw_user`';
const ARTICLE_TABLE = '`'.DB_NAME.'`.`yw_article`';
const FILE_TABLE = '`'.DB_NAME.'`.`yw_file`';
const REVISION_TABLE = '`'.DB_NAME.'`.`yw_revision`';
const LOG_TABLE = '`'.DB_NAME.'`.`yw_log`';

$db_connect_info = new YwDatabaseConnectInfo(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
?>