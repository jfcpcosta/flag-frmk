<?php namespace Flag\Frmk\Database;

use PDO;
use PDOException;
use PDOStatement;

class Database {

    private PDO $connection;

    public function connect(string $host = null, string $name = null, string $user = null, string $pass = null, int $port = 3306): void {
        if (file_exists('../config/database.config.php')) {
            $configs = require '../config/database.config.php';
            extract($configs);
        }

        $dsn = sprintf("mysql:host=%s;dbname=%s;port=%d", $host, $name, $port);

        try {
            $this->connection = new PDO($dsn, $user, $pass, [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
            ]); 
        } catch (PDOException $e) {
            die('Error connecting to database: ' . $e->getMessage());
        }
    }

    public function raw(string $sql): PDOStatement {
        $this->connect();
        
        $stmt = $this->connection->prepare($sql);
        return $stmt;
    }

    public function select(string $table, array $where = null, $fields = ['*']): array {
        $fieldsString = implode(', ', $fields);
        $whereString = !is_null($where) ? ' WHERE ' . $this->prepareArray($where) : '';

        $stmt = $this->raw("SELECT $fieldsString FROM $table $whereString");
        $stmt->execute($where);

        return $stmt->fetchAll();
    }

    public function insert(string $table, array $data): array {
        $fields = implode(', ', array_keys($data));
        $values = implode(', ', array_map(fn($field) => ":$field", array_keys($data)));

        $sql = "INSERT INTO $table ($fields) VALUES ($values)";
        $stmt = $this->raw($sql);
        $stmt->execute($data);

        return [
            'stmt' => $stmt,
            'lastInsertId' => $this->connection->lastInsertId()
        ];
    }

    public function update(string $table, array $data, array $where): PDOStatement {
        $whereKeys = $this->prepareArray($where);
        $fieldPairs = $this->prepareArray($data, ', ');

        $sql = "UPDATE $table SET $fieldPairs WHERE $whereKeys";
        
        $stmt = $this->raw($sql);
        $stmt->execute(array_merge($data, $where));

        return $stmt;
    }

    public function delete(string $table, array $where): PDOStatement {
        $whereKeys = $this->prepareArray($where);
        
        $sql = "DELETE FROM $table WHERE $whereKeys";
        $stmt = $this->raw($sql);
        $stmt->execute($where);
        
        return $stmt;
    }

    private function prepareArray(array $data, string $sep = ' AND '): string {
        $keys = array_keys($data);

        return implode($sep, array_map(fn($key) => "$key = :$key", $keys));
    } 
}