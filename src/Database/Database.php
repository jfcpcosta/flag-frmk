<?php namespace Flag\Frmk\Database;

use Flag\Frmk\Http\Exception\InternalServerErrorException;
use PDO;
use PDOException;
use PDOStatement;
use stdClass;

class Database {

    private string $defaultConfigPath = '../configs/database.config.php';

    private string $host;
    private string $user;
    private string $pass;
    private string $name;
    private int $port;

    private PDO $connection;

    public function __construct(string $host = null, string $user = null, string $pass = null, string $name = null, int $port = 3306)
    {
        if (is_null($host) && file_exists($this->defaultConfigPath)) {
            $configs = require $this->defaultConfigPath;
            extract($configs);
        }

        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->name = $name;
        $this->port = $port;

        $this->connect();
    }

    private function connect(): void {
        $dsn = sprintf("mysql:host=%s;dbname=%s;port=%d", $this->host, $this->name, $this->port);

        try {
            $this->connection = new PDO($dsn, $this->user, $this->pass, [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
            ]);
        } catch (PDOException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }

    public function query(string $sql, array $data = [], int $limit = null, int $initialRow = null): PDOStatement {
        
        if (!is_null($limit) && !is_null($initialRow)) {
            $sql .= " LIMIT $initialRow, $limit";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($data);

        return $stmt;
    }

    public function loadQuery(string $filePath, array $data = [], int $limit = null, int $initialRow = null): PDOStatement {
        if (!file_exists($filePath)) {
            throw new InternalServerErrorException('File not found');
        }

        $sql = file_get_contents($filePath);
        
        return $this->query($sql, $data, $limit, $initialRow);
    }

    public function all(string $table, array $fields = ['*']): array {
        $fields = implode(', ', $fields);
        $stmt = $this->query("SELECT $fields FROM $table");
        return $stmt->fetchAll();
    }
    
    public function where(string $table, array $where, array $fields = ['*']): array {
        $fields = implode(', ', $fields);
        $whereString = $this->getWhere($where);

        $stmt = $this->query("SELECT $fields FROM $table WHERE $whereString", $where);
        return $stmt->fetchAll();
    }

    public function byId(string $table, mixed $id, array $fields = ['*'], string $idField = 'id'): ?stdClass {
        $result = $this->where($table, [$idField => $id, $fields]);
        return isset($result[0]) ? $result[0] : null;
    }

    public function insert(string $table, array $data): InsertResult {
        $fields = array_keys($data);

        $fieldsAsString = implode(', ', $fields);
        $valuesAsString = ':' . implode(', :', $fields);

        $sql = "INSERT INTO $table ($fieldsAsString) VALUES ($valuesAsString)";
        $stmt = $this->query($sql, $data);
        
        return new InsertResult($this->connection->lastInsertId(), $stmt);
    }

    public function update(string $table, array $data, array $where): PDOStatement {
        $pairsAsString = $this->prepareArray($data);
        $whereString = $this->getWhere($where);

        $sql = "UPDATE $table SET $pairsAsString WHERE $whereString";
        return $this->query($sql, array_merge($data, $where));
    }

    public function delete(string $table, array $where): PDOStatement {
        $whereString = $this->getWhere($where);
        $sql = "DELETE FROM $table WHERE $whereString";
        return $this->query($sql, $where);
    }

    public function exists(string $table, string $field, string $value): bool {
        $result = $this->where($table, [$field => $value], ['COUNT(*) AS counter']);
        return isset($result[0]) ? $result[0]->counter > 0 : false;
    }

    private function getWhere(array $where): string {
        if (!is_array($where)) {
            return $where;
        }
        return $this->prepareArray($where, ' AND ');
    }

    private function prepareArray(array $data, string $implodeValue = ', '): string {
        $pairs = array_map(fn($key) => "$key = :$key", array_keys($data));
        return implode($implodeValue, $pairs);
    }
}