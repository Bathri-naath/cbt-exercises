<?php

class Database
{
    private const SERVERNAME = "localhost";
    private const USERNAME = "root";
    private const PASSWORD = "Bathri1409_";
    private const DBNAME = "deposit";
    private mysqli $connection;

    public function __construct()
    {
        $this->connection  = new mysqli(self::SERVERNAME, self::USERNAME, self::PASSWORD, self::DBNAME);
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
        echo "Connected Successfully";
    }

    public function getDatabaseConnection()
    {
        return $this->connection;
    }
}
