<?php

class Database{
    private const SERVERNAME = "localhost";
    private const USERNAME = "root";
    private const PASSWORD = "Bathri1409_";
    private const DBNAME = "deposit";


    public $conn;

    public function __construct()
    {
        $this->conn  = new mysqli(self::SERVERNAME,self::USERNAME,self::PASSWORD,self::DBNAME);
        if ($this->conn->connect_error){
            die("Connection failed: ".$this->conn->connect_error);
        }
        echo "Connected Successfully";
    }
}
