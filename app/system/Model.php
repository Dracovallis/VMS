<?php

namespace app\system;

use PDO;

class Model
{
    protected $_tableName;
    protected $_primary_key;

    public function __construct($db)
    {
        $this->_db = $db;
    }

    public function find($args = [], $usePrimaryKeyAsIndex = false)
    {
        $query = "SELECT * FROM " . $this->_tableName;

        if (!empty($args['conditions'])) {
            $query .= ' WHERE ' . $args['conditions'];
        }
        if (!empty($args['limit'])) {
            $query .= ' LIMIT ' . (int)$args['limit'];
        }
        if (!empty($args['offset'])) {
            $query .= ' OFFSET ' . (int)$args['offset'];
        }

        $stmt = $this->_db->prepare($query);

        if (isset($args['bind'])) {
            preg_match_all("/\:(?'parsedMatches'[A-z0-9]{1,})/i", $args['conditions'], $matches);

            foreach ($matches['parsedMatches'] as $key) {
                $stmt->bindParam(':' . $key, $args['bind'][$key]);
            }
        }

        $stmt->execute();

        $entities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($usePrimaryKeyAsIndex) {
            $mappedEntities = [];

            foreach ($entities as $entity) {
                $key = $entity[$this->_primary_key];

                $mappedEntities[$key] = $entity;
            }

            $entities = $mappedEntities;
         
        }

        return $entities;
    }

    public function findFirst($id) {
        $query = "SELECT * FROM " . $this->_tableName . " WHERE " . $this->_primary_key . " = :id";
        $stmt = $this->_db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $entity = $stmt->fetch(PDO::FETCH_ASSOC);

        return $entity;
    }

    public function save($data, $id = NULL)
    {
        $keys = array_keys($data);
        foreach ($data as $key => $value) {
            $data[":$key"] = $value;
            unset($data["$key"]);
        }

        // Insert
        if ($id === NULL) {
            $sql = "INSERT INTO $this->_table_name (" . implode(", ", $keys) . ") ";
            $sql .= "VALUES ( :" . implode(", :", $keys) . ")";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute($data);
            $id = $this->_db->lastInsertId();
        } else { // Update
            $data[":$this->_primary_key"] = $id;
            $sql = "UPDATE $this->_table_name SET";
            foreach ($keys as $key => $value) {
                $sql .= " $value = :$value";
                if ($key != (count($keys) - 1))
                    $sql .= ', ';
            }
            $sql .= " WHERE $this->_primary_key = :id";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute($data);
        }

        return $id;
    }

    protected function setRelation($modelKey,  $targetModel, $targetModelKey, $alias, $type = 'oneToOne')
    {
        $this->{"get" . $alias} = function () {
            var_dump('test');
            exit;
        };
    }
}
