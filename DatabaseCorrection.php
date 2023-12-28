<?php
class DatabaseSynchronizer {
    private $conn1;
    private $conn2;

    public function __construct($servername1, $username1, $password1, $dbname1, $servername2, $username2, $password2, $dbname2) {
        $this->conn1 = new mysqli($servername1, $username1, $password1, $dbname1);
        $this->conn2 = new mysqli($servername2, $username2, $password2, $dbname2);
        if ($this->conn1->connect_error || $this->conn2->connect_error) {
            die("Ошибка подключения к базе данных: " . $this->conn1->connect_error . " " . $this->conn2->connect_error);
        }
    }

    public function synchronizeDatabases($table) {
        $sql_select = "SELECT * FROM $table";
        $result = $this->conn1->query($sql_select);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $check_query = "SELECT * FROM $table WHERE id = " . $row['id']; // замените id на уникальный идентификатор в таблице
                $check_result = $this->conn2->query($check_query);

                if ($check_result->num_rows == 0) {
                    $insert_query = "INSERT INTO $table (column1, column2, ...) VALUES ('" . $row['id'] . "', '" . $row['name'] . "', ...)";
                    $this->conn2->query($insert_query);
                }
            }
            echo "Базы данных синхронизированы.";
        } else {
            echo "Нет данных для синхронизации.";
        }
    }

    public function closeConnections() {
        $this->conn1->close();
        $this->conn2->close();
    }
}

$synchronizer = new DatabaseSynchronizer('localhost:5432', 'user1', '1234', 'db1', 'localhost:5432', 'user1', '1234', 'db2');
$synchronizer->synchronizeDatabases('user');
$synchronizer->closeConnections();
?>