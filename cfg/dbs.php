<?php
class Database {
    protected $cn;

    public function __construct() {
        $this->connect();
    }

    protected function connect() {
        $host = "localhost";
        $user = "root";
        $pswd = "";
        $db = "uts_sql";

        $this->cn = new mysqli($host, $user, $pswd, $db);
        if ($this->cn->connect_error) {
            die("Connection failed: " . $this->cn->connect_error);
        }
    }
}
?>
