<?php

// set global db variable
$db = null;

function get_global_db_pdo() {
    global $db;

    if ($db === null) {
        try {
            $db = new PDO(DB_ENGINE.':host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASSWORD);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (Exception $e) {
            message_inline_red('Caught exception connecting to database');
            throw $e;
        }
    }

    return $db;
}

function db_insert ($table, array $fields) {
    $db = get_global_db_pdo();

    try {
        $sql = 'INSERT INTO '.$table.' (';
        $sql .= implode(', ', array_keys($fields));
        $sql .= ') VALUES (';
        $sql .= implode(', ', array_fill(0, count($fields), '?'));
        $sql .= ')';

        $stmt = $db->prepare($sql);

        // fix null values
        array_walk($fields, 'null_to_bool');

        // get the field values
        $values = array_values($fields);

        $stmt->execute($values);

        return $db->lastInsertId();

    } catch (PDOException $e) {
        sql_exception($e);
    }
}

function db_update ($table, array $fields, array $where, $whereGlue = 'AND') {
    $db = get_global_db_pdo();

    try {
        $sql = 'UPDATE '.$table.' SET ';
        $sql .= implode('=?, ', array_keys($fields)).'=? ';
        $sql .= 'WHERE '.implode('=? '.$whereGlue.' ', array_keys($where)).'=?';

        $stmt = $db->prepare($sql);

        // fix null values
        array_walk($fields, 'null_to_bool');

        // get the field values and "WHERE" values. merge them into one array.
        $values = array_merge(array_values($fields), array_values($where));

        // execute the statement
        $stmt->execute($values);

        return $stmt->rowCount();

    } catch (PDOException $e) {
        sql_exception($e);
    }
}

function db_delete ($table, array $where, $whereGlue = 'AND') {
    $db = get_global_db_pdo();

    try {
        $sql = 'DELETE FROM '.$table.' ';
        $sql .= 'WHERE '.implode('=? '.$whereGlue.' ', array_keys($where)).'=?';

        $stmt = $db->prepare($sql);

        // get the field values and "WHERE" values
        $values = array_values($where);

        // execute the statement
        $stmt->execute($values);

        return $stmt->rowCount();

    } catch (PDOException $e) {
        sql_exception($e);
    }
}

function db_count_num ($table, array $where = null, $whereGlue = 'AND') {
    $count = db_select_one ($table, array('COUNT(*) AS num'), $where, null, $whereGlue, false);
    return $count['num'];
}

function db_select_one ($table, array $fields, array $where = null, $orderBy = null, $whereGlue = 'AND') {
    return db_select ($table, $fields, $where, $orderBy, $whereGlue, false);
}

function db_select_all ($table, array $fields, array $where = null, $orderBy = null, $whereGlue = 'AND') {
    return db_select ($table, $fields, $where, $orderBy, $whereGlue, true);
}

function db_select ($table, array $fields, array $where = null, $orderBy = null, $whereGlue = 'AND', $all = true) {
    $db = get_global_db_pdo();

    try {
        $query = 'SELECT '.implode(', ', $fields).' ';
        $query .= 'FROM '.$table.' ';

        if (!empty($where)) {
            $query .= 'WHERE '.implode('=? '.$whereGlue.' ', array_keys($where)).'=?';
        }

        if (!empty($orderBy)) {
            $query .= ' ORDER BY ' . $orderBy;
        }

        if (!empty($where)) {
            $stmt = $db->prepare($query);

            // get the field values and "WHERE" values. merge them into one array.
            $values = array_values($where);

            // execute the statement
            $stmt->execute($values);
        }

        else {
            $stmt = $db->query($query);
        }

        if ($all) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        else {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

    } catch (PDOException $e) {
        sql_exception($e);
    }
}

function db_query_fetch_one ($query, array $values = null) {
    return db_query($query, $values, false);
}

function db_query_fetch_all ($query, array $values = null) {
    return db_query($query, $values, true);
}

function db_query_fetch_none ($query, array $values = null) {
    db_query($query, $values, null);
}

function db_query ($query, array $values = null, $all = true) {
    $db = get_global_db_pdo();

    try {
        if (!empty($values)) {
            $stmt = $db->prepare($query);
            $stmt->execute($values);
        }

        else {
            $stmt = $db->query($query);
        }

        if ($all === true) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        else if ($all === false) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

    } catch (PDOException $e) {
        sql_exception($e);
    }
}

function db_begin_transaction() {
    $db = get_global_db_pdo();
    $db->beginTransaction();
}

function db_end_transaction() {
    $db = get_global_db_pdo();
    $db->commit();
}

function db_rollback_transaction() {
    $db = get_global_db_pdo();
    $db->rollBack();
}

function sql_exception (PDOException $e) {
    log_exception($e);
    message_error('An SQL exception occurred. Please check the exceptions log.');
}

function null_to_bool(&$val) {
    if (!isset($val)) {
        $val = 0;
    }
}