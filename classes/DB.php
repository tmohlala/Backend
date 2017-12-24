<?php


/**
 * Class for accessing databases.
 */
class DB {
    private static $_instance = null;
    private $_pdo,
            $_query,
            $_error = false,
            $_results,
            $_count = 0;
    
    /**
     * Create an instance of the database
     * and store it in $_instance.
     */
    private function __construct() {
        try {
            $this->_pdo = new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname=' .Config::get('mysql/db'), Config::get('mysql/username'), Config::get('mysql/password'));
        } catch(PDOException $e) {
            die($e->getMessage());
        }
    }
    
    /**
     * Function to check if database connection is instantiated
     * and returns a single instance of the database connection.
     * @return $_instance of the database connection.
     */
    public static function getInstance() {
        if(!isset(self::$_instance)) {
            self::$_instance = new DB();
        }
        return self::$_instance;
    }

    /**
    * Function to query the database.
    */
    public function query($sql, $params = []) {
        $this->_error = false;

        if($this->_query = $this->_pdo->prepare($sql)) {
            $x = 1;
            if(count($params)) {
                foreach($params as $param) {
                    $this->_query->bindValue($x, $param);
                    $x++;
                }
            }
            if($this->_query->execute()) {
                $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $this->_query->rowCount();
            }
            else {
                $this->_error = true;
            }
        }
        return $this ;
    }

    /**
    * Function to perform specific database
    * action.
    */

    private function action($action, $table, $where = []) {
        if(count($where) === 3) {
            $operators = ['=', '>', '<', '>=', '<='];
            $field = $where[0];
            $operator = $where[1];
            $value = $where[2];

            if(in_array($operator, $operators)) {
                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
                if(!$this->query($sql, [$value])->error()) {
                    return $this;
                }
            }
        }
        return false; 
    }

    /**
    * Function for getting data from
    * the database
    */

    public function get($table, $where) {
        return $this->action('SELECT *', $table, $where);
    }

    /**
    * Function to insert data into
    * the database
    */
    public function insert($table, $fields = []) {
        $keys = array_keys($fields);
        $values = "";
        $x = 1;

        foreach($fields as $field) {
            $values .= "?";
            if($x < count($fields)) {
                $values .= ", ";
            }
            $x++;
        }
        $sql = "INSERT INTO {$table} (`" .implode('`,`', $keys) . "`) VALUES ({$values})";
        if(!$this->query($sql, $fields)->error()) {
            return true;
        }
        return false;
    }

    /**
    * Function for updating database
    * entries.
    */

    public function update($table, $id,$fields = []) {
        $set = "";
        $x = 1; 

        foreach($fields as $name => $value) {
            $set .= "{$name} = ?";
            if($x < count($fields)) {
                $set .= ", ";
            }
            $x++;
        }
        $sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";
        if(!$this->query($sql, $fields)->error()) {
            return true;
        }
        return false;
    }

    /**
    * Function for deleting data from
    * the database
    */

    public function delete($table, $where) {
        return $this->action('DELETE', $table, $where);
    }

    /**
    * @return the $_results from 
    * database query.
    */

    public function results() {
        return $this->_results;
    }

    /**
    * @return the first result
    * in the object.
    */

    public function first() {
        return $this->results()[0];
    }

    /**
    * @return $_error from the current object;
    */

    public function error() {
        return $this->_error;
    }

    /**
    * @return a count of data retrieved from
    * database.
    */

    public function count() {
        return $this->_count;
    }

}
