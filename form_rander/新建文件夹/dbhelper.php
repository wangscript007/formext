<?php
namespace form_rander;

use PDO;

class dbhelper
{

    public $sql = null;

    public function connect($driver, $engine = null, $host = null, $username = null, $password = null, $database = null, $port = 3306)
    {
        switch ($driver)
        {
            case 'pdo':
                if ($engine == 'mysql')
                {
                    $sql = new PDO('mysql:host=' . $host . ';port=' . $port . ';dbname=' . $database, $username, $password, [
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING
                    ]);
                }
                break;
        }
        $sql->driver = $driver;
        $sql->engine = $engine;
        $this->sql = $sql;
    }

    public function disconnect()
    {
        switch ($this->sql->driver)
        {

            case 'pdo':
                $this->sql = null;
                break;

        }
    }

    public function fetch_all($query)
    {
        $data = [];
        $params = func_get_args();
        unset($params[0]);
        $params = array_values($params);
        list($query, $params) = $this->preparse_query($query, $params);

        switch ($this->sql->driver)
        {

            case 'pdo':
                $stmt = $this->sql->prepare($query);
                $stmt->execute($params);
                if ($stmt->errorCode() != 0)
                {
                    $errors = $stmt->errorInfo();
                    throw new \Exception($errors[2]);
                }
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;

        }

        return $data;
    }

    public function fetch_row($query)
    {
        $data = [];
        $params = func_get_args();
        unset($params[0]);
        $params = array_values($params);
        list($query, $params) = $this->preparse_query($query, $params);

        switch ($this->sql->driver)
        {

            case 'pdo':
                $stmt = $this->sql->prepare($query);
                $stmt->execute($params);
                if ($stmt->errorCode() != 0)
                {
                    $errors = $stmt->errorInfo();
                    throw new \Exception($errors[2]);
                }
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                break;

        }

        return $data;
    }

    public function fetch_col($query)
    {
        $data = [];
        $params = func_get_args();
        unset($params[0]);
        $params = array_values($params);
        list($query, $params) = $this->preparse_query($query, $params);

        switch ($this->sql->driver)
        {

            case 'pdo':
                $stmt = $this->sql->prepare($query);
                $stmt->execute($params);
                if ($stmt->errorCode() != 0)
                {
                    $errors = $stmt->errorInfo();
                    throw new \Exception($errors[2]);
                }
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($data))
                {
                    $data_tmp = [];
                    foreach ($data as $dat)
                    {
                        $data_tmp[] = $dat[ array_keys($dat)[0] ];
                    }
                    $data = $data_tmp;
                }
                break;

        }

        return $data;
    }

    public function fetch_cols($query)
    {
        $data = [];
        $params = func_get_args();
        unset($params[0]);
        $params = array_values($params);
        list($query, $params) = $this->preparse_query($query, $params);

        switch ($this->sql->driver)
        {

            case 'pdo':
                $stmt = $this->sql->prepare($query);
                $stmt->execute($params);
                if ($stmt->errorCode() != 0)
                {
                    $errors = $stmt->errorInfo();
                    throw new \Exception($errors[2]);
                }
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($data))
                {
                    $data_tmp = [];
                    foreach ($data as $dat)
                    {
                        $data_tmp[$dat[0]] = $dat[1];
                    }
                    $data = $data_tmp;
                }
                break;

            }

        return $data;
    }

    public function fetch_var($query)
    {
        $data = [];
        $params = func_get_args();
        unset($params[0]);
        $params = array_values($params);
        list($query, $params) = $this->preparse_query($query, $params);

        switch ($this->sql->driver)
        {

            case 'pdo':
                $stmt = $this->sql->prepare($query);
                $stmt->execute($params);
                if ($stmt->errorCode() != 0)
                {
                    $errors = $stmt->errorInfo();
                    throw new \Exception($errors[2]);
                }
                $data = $stmt->fetchObject();
                if (empty($data))
                {
                    return null;
                }
                $data = (Array)$data;
                $data = current($data);
                break;

        }

        return $data;
    }

    public function query($query)
    {
        $data = [];
        $params = func_get_args();
        unset($params[0]);
        $params = array_values($params);
        list($query, $params) = $this->preparse_query($query, $params);

        switch ($this->sql->driver)
        {

            case 'pdo':
                $stmt = $this->sql->prepare($query);
                $stmt->execute($params);
                if ($stmt->errorCode() != 0)
                {
                    $errors = $stmt->errorInfo();
                    throw new \Exception($errors[2]);
                }
                return $stmt->rowCount();
                break;

        }

    }

    public function exec($query){
        return $this->sql->exec($query);
    }

    public function insert($table, $data)
    {
        if( !isset($data[0]) && !is_array(array_values($data)[0]) )
        {
            $data = [$data];
        }
        $query = '';
        $query .= 'INSERT INTO ';
        $query .= '`' . $table . '`';
        $query .= '(';
        foreach($data[0] as $data__key=>$data__value)
        {
            if ($this->sql->engine == 'mysql')
            {
                $query .= '`' . $data__key . '`';
            }
            if ($this->sql->engine == 'postgres')
            {
                $query .= '"' . $data__key . '"';
            }
            if( array_keys($data[0])[count(array_keys($data[0]))-1] !== $data__key )
            {
                $query .= ',';
            }
        }
        $query .= ') ';
        $query .= 'VALUES ';
        foreach($data as $data__key=>$data__value)
        {
            $query .= '(';
                $query .= str_repeat('?,',count($data__value)-1).'?';
            $query .= ')';
            if( array_keys($data)[count(array_keys($data))-1] !== $data__key )
            {
                $query .= ',';
            }
        }
        $args = [];
        $args[] = $query;
        foreach($data as $data__key=>$data__value)
        {
            foreach($data__value as $data__value__key=>$data__value__value)
            {
                // because pdo can't insert true/false values so easily, convert them to 1/0
                if($data__value__value === true)
                {
                    $data__value__value = 1;
                }
                if($data__value__value === false)
                {
                    $data__value__value = 0;
                }
                $args[] = $data__value__value;
            }
        }
        call_user_func_array([$this, 'query'], $args);
        return $this->last_insert_id();
    }

    public function last_insert_id()
    {
        $last_insert_id = null;
        switch ($this->sql->driver)
        {

            case 'pdo':
                if ($this->sql->engine == 'mysql')
                {
                    $last_insert_id = $this->fetch_var("SELECT LAST_INSERT_ID();");
                }
                if ($this->sql->engine == 'postgres')
                {
                    $last_insert_id = $this->fetch_var("SELECT LASTVAL();");
                }
                break;

        }

        return $last_insert_id;
    }

    public function total_count()
    {
        $total_count = 0;
        switch ($this->sql->driver)
        {

            case 'pdo':
                $total_count = $this->fetch_var("SELECT FOUND_ROWS();");
                break;

        }

        return $total_count;
    }

    public function debug($query)
    {

        $params = func_get_args();
        unset($params[0]);
        $params = array_values($params);

        $keys = [];
        $values = $params;
        foreach ($params as $key => $value)
        {
            // check if named parameters (':param') or anonymous parameters ('?') are used
            if (is_string($key))
            {
                $keys[] = '/:' . $key . '/';
            }
            else
            {
                $keys[] = '/[?]/';
            }
            // bring parameter into human-readable format
            if (is_string($value))
            {
                $values[ $key ] = "'" . $value . "'";
            }
            elseif (is_array($value))
            {
                $values[ $key ] = implode(',', $value);
            }
            elseif (is_null($value))
            {
                $values[ $key ] = 'NULL';
            }
        }
        $query = preg_replace($keys, $values, $query, 1, $count);
        echo '<pre>';
        print_r($query);
        echo '</pre>';
        die();

    }

    public function preparse_query($query, $params)
    {

        $return = $query;

        /*
        expand IN-syntax
        fetch('SELECT * FROM table WHERE col1 IN (?) AND col2 IN (?) AND col3 IN (?,?) col4 IN (?)', [1], 2, [7,8], [3,4,5])
        gets to
        fetch('SELECT * FROM table WHERE col1 IN (?) AND col2 IN (?) AND col4 IN (?,?) AND col4 IN (?,?,?)', 1, 2, 3, 7, 8, 3, 4, 5)
        */
        if( strpos($query, 'IN (') !== false || strpos($query, 'IN(') !== false )
        {
            // find all ?s
            $in_positions = $this->find_occurences($query,'?');
            $in_index = 0;
            if( !empty($params) )
            {
                foreach($params as $params__key=>$params__value)
                {
                    if( is_array($params__value) && count($params__value) > 0 )
                    {
                        $in_occurence = $this->find_nth_occurence($return,'?',$in_index);
                        if( substr($return, $in_occurence-1, 3) == '(?)' )
                        {
                            $return = substr($return, 0, $in_occurence-1).
                                    '('.(str_repeat('?,',count($params__value)-1).'?').')'.
                                    substr($return, $in_occurence+2);
                        }
                        foreach($params__value as $params__value__value)
                        {
                            $in_index++;
                        }
                    }
                    else
                    {
                        $in_index++;
                    }
                }
            }
        }
        
        /*
        finally flatten all arguments
        example:
        fetch('SELECT * FROM table WHERE ID = ?', [1], 2, [3], [4,5,6])
        =>
        fetch('SELECT * FROM table WHERE ID = ?', 1, 2, 3, 4, 5, 6)
        */
        $useNamedPara = false; //true：表示类似 :paraName， false：表示 ? 
        if( !empty($params) )
        {
            $params_flattened = [];
            if( is_array($params) && count($params) > 0 )
            {
                foreach($params as $params__value)
                {
                    if( is_array($params__value) && count($params__value) > 0 )
                    {
                        foreach ($params__value as $key => $params__value__value) {
                            // by zxs
                            if($key[0] == ":"){
                                $params_flattened[$key] = $params__value__value;
                                $useNamedPara = true;
                            }else{
                                $params_flattened[] = $params__value__value;
                            }
                        }
                    }
                    elseif( !is_array($params__value) )
                    {
                        $params_flattened[] = $params__value;
                    }
                }
            }
            $params = $params_flattened;
        }

        // try to sort out bad queries
        foreach($params as $params__key=>$params__value)
        {
            if( is_object($params__value) )
            {
                throw new \Exception('object in query');
            }
        }

        // NULL values are treated specially: modify the query
        {
            $pos = 0;
            $delete_keys = [];
            foreach($params as $params__key=>$params__value)
            {
                // no more ?s are left
                if(($pos = strpos($return, '?', $pos + 1)) === false)
                {
                    break;
                }

                // if param is not null, nothing must be done
                if (!is_null($params__value))
                {
                    continue;
                }

                // case 1: if query contains WHERE before ?, then convert != ? to IS NOT NULL and = ? to IS NULL
                if( strpos( substr($return, 0, $pos), 'WHERE' ) !== false )
                {
                    if( strpos(substr($return, $pos-5, 6), '<> ?') !== false )
                    { 
                        $return = substr($return, 0, ($pos - 5)) . preg_replace('/<> \?/', 'IS NOT NULL', substr($return, ($pos - 5)), 1);
                    }
                    elseif( strpos(substr($return, $pos-5, 6), '!= ?') !== false )
                    {
                        $return = substr($return, 0, ($pos - 5)) . preg_replace('/\!= \?/', 'IS NOT NULL', substr($return, ($pos - 5)), 1);
                    }
                    elseif( strpos(substr($return, $pos-5, 6), '= ?') !== false )
                    {
                        $return = substr($return, 0, ($pos - 5)) . preg_replace('/= \?/', 'IS NULL', substr($return, ($pos - 5)), 1);
                    }
                }
                // case 2: in all other cases, convert ? to NULL
                else
                {
                    $return = substr($return, 0, $pos) . 'NULL' . substr($return, $pos+1);
                }

                // delete param
                $delete_keys[] = $params__key;

            }
            if( !empty($delete_keys) )
            {
                foreach($delete_keys as $delete_keys__value)
                {
                    unset($params[$delete_keys__value]);
                }
            }
            if(!$useNamedPara){
                $params = array_values($params);
            }
        }

        return [$return, $params];

    }

    public function update($table, $data, $condition = null)
    {
        if( isset($data[0]) && is_array($data[0]) )
        {
            return $this->update_batch($table, $data);
        }
        $query = "";
        $query .= "UPDATE ";
        $query .= "`" . $table . "`";
        $query .= " SET ";
        foreach ($data as $key => $value)
        {
            $query .= "`" . $key . "`";
            $query .= " = ";
            $query .= "?";
            end($data);
            if ($key !== key($data))
            {
                $query .= ', ';
            }
        }
        $query .= " WHERE ";
        foreach ($condition as $key => $value)
        {
            $query .= "`" . $key . "`";
            $query .= " = ";
            $query .= "? ";
            end($condition);
            if ($key !== key($condition))
            {
                $query .= ' AND ';
            }
        }
        $args = [];
        $args[] = $query;
        foreach ($data as $d)
        {
            if ($d === true)
            {
                $d = 1;
            }
            if ($d === false)
            {
                $d = 0;
            }
            $args[] = $d;
        }
        foreach ($condition as $c)
        {
            if ($c === true)
            {
                $c = 1;
            }
            if ($c === false)
            {
                $c = 0;
            }
            $args[] = $c;
        }
        return call_user_func_array([$this, 'query'], $args); // returns the affected row counts
    }

    public function delete($table, $conditions)
    {
        if( !isset($conditions[0]) && !is_array(array_values($conditions)[0]) )
        {
            $conditions = [$conditions];
        }
        $query = '';
        $query .= 'DELETE FROM ';
        $query .= '`' . $table . '`';
        $query .= ' WHERE ';
        $query .= '(';
        foreach($conditions as $conditions__key=>$conditions__value)
        {
            $query .= '(';
            foreach($conditions__value as $conditions__value__key=>$conditions__value__value)
            {
                $query .= '`'.$conditions__value__key.'`';
                $query .= ' = ';
                $query .= '?';
                if( array_keys($conditions__value)[count(array_keys($conditions__value))-1] !== $conditions__value__key )
                {
                    $query .= ' AND ';
                }
            }
            $query .= ')';
            if( array_keys($conditions)[count(array_keys($conditions))-1] !== $conditions__key )
            {
                $query .= ' OR ';
            }
        }
        $query .= ')';
        $args = [];
        $args[] = $query;
        foreach($conditions as $conditions__key=>$conditions__value)
        {
            foreach($conditions__value as $conditions__value__key=>$conditions__value__value)
            {
                if($conditions__value__value === true)
                {
                    $conditions__value__value = 1;
                }
                if($conditions__value__value === false)
                {
                    $conditions__value__value = 0;
                }
                $args[] = $conditions__value__value;
            }
        }
        return call_user_func_array([$this, 'query'], $args); // returns the affected row counts
    }

    public function update_batch($table, $input)
    {
        $query = '';
        $args = [];
        $query = 'UPDATE '.$table.' SET'.PHP_EOL;
        foreach($input[0][0] as $col__key=>$col__value)
        {
            $query .= $col__key.' = CASE'.PHP_EOL;
            foreach($input as $input__key=>$input__value)
            {
                $query .= 'WHEN (';
                $where = [];
                foreach($input__value[1] as $where__key=>$where__value)
                {
                    $where[] = $where__key.' = ?';
                    $args[] = $where__value;
                }
                $query .= implode(' AND ',$where).')'.' THEN ?'.PHP_EOL;
                $args[] = $input__value[0][$col__key];
            }
            $query .= 'END';
            if( array_keys($input[0][0])[count(array_keys($input[0][0]))-1] !== $col__key ) $query .= ',';
            $query .= PHP_EOL;
        }
        $query .= 'WHERE ';
        $where = [];
        foreach($input[0][1] as $where__key=>$where__value)
        {
            $where_values = [];
            foreach($input as $input__key=>$input__value)
            {
                $where_values[] = $input__value[1][$where__key];
            }
            $where_values = array_unique($where_values);
            $where[] = $where__key.' IN ('.str_repeat('?,',count($where_values)-1).'?)';
            $args = array_merge($args, $where_values);
        }
        $query .= implode(' AND ', $where).';';
        array_unshift($args, $query);
        return call_user_func_array([$this, 'query'], $args);
    }

    public function clear($database)
    {
        $this->query('SET FOREIGN_KEY_CHECKS = 0');        
        $tables = $this->fetch_col('SELECT table_name FROM information_schema.tables WHERE table_schema = ?', $database);
        if( !empty($tables) )
        {
            foreach($tables as $tables__value)
            {
                $this->query('DROP TABLE '.$tables__value);
            }
        }
        $this->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function find_occurences($haystack, $needle)
    {
        $positions = [];
        $pos_last = 0;
        while( ($pos_last = strpos($haystack, $needle, $pos_last)) !== false )
        {
            $positions[] = $pos_last;
            $pos_last = $pos_last + strlen($needle);
        }
        return $positions;
    }

    public function find_nth_occurence($haystack, $needle, $index)
    {
        $positions = $this->find_occurences($haystack, $needle);
        if(empty($positions) || $index > (count($positions)-1)) { return null; }
        return $positions[$index];
    }

}