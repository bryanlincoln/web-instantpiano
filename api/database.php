<?php

function db_connect() {
    if(LOCAL){
        $connection = new mysqli("localhost", "root", "senha", "piano");
    }
    else {
        $connection = new mysqli("", "", "", "");
    }

    define("SECURE", isset($_SERVER["HTTPS"]));

    if (mysqli_connect_errno()) {
        printf("Conexão falhou: %s\n", mysqli_connect_error());
        exit();
    }
    if (!$connection->set_charset("utf8")) {
        printf("Error loading character set utf8: %s\n", $connection->error);
        exit();
    }

    // seta timezone
    $now = new DateTime();
    $mins = $now->getOffset() / 60;
    $sgn = ($mins < 0 ? -1 : 1);
    $mins = abs($mins);
    $hrs = floor($mins / 60);
    $mins -= $hrs * 60;
    $offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);
    $connection->query("SET SESSION time_zone='$offset'");

    return $connection;
}

function db_insert(string $table, array $fields, array $values) {
    global $db;

    $query_str = "INSERT INTO " . $table . " (";
    $fields_size = count($fields);
    $values_size = count($values);
    if($fields_size != $values_size) {
        echo "The inserting fields are incompatible.";
        return null;
    }
    for($i = 0; $i < $fields_size; $i++) {
        $query_str .= $fields[$i];
        if($i < $fields_size - 1)
            $query_str .= ", ";
    }
    $query_str .= ") VALUES (";
    for($i = 0; $i < $values_size; $i++) {
        if($values[$i] == "null")
            $query_str .= "null";
        else
            $query_str .= "\"" . $values[$i] . "\"";

        if($i < $values_size - 1)
            $query_str .= ", ";
    }
    $query_str .= ")";

    # echo $query_str;

    $res = $db->query($query_str);

    if($res)
        return true;
    return false;
}

function db_select(string $table, array $fields, string $where, $group_by=false, $order_by=false) {
    global $db;

    $query_str = "SELECT ";
    $fields_size = count($fields);
    for($i = 0; $i < $fields_size; $i++) {
        $query_str .= $fields[$i];
        if($i < $fields_size - 1)
            $query_str .= ", ";
    }
    $query_str .= " FROM " . $table . " WHERE " . $where;

    if($group_by) {
        $query_str .= " GROUP BY " . $group_by;
    }
    if($order_by) {
        $query_str .= " ORDER BY " . $order_by;
    }

    // echo $query_str;

    $res = $db->query($query_str);

    if($res)
        return $res;
    return false;
}

function db_update(string $table, array $fields, array $values, string $where) {
    global $db;

    $query_str = "UPDATE " . $table . " SET ";
    $fields_size = count($fields);
    $values_size = count($values);
    if($fields_size != $values_size) {
        echo "The updating fields are incompatible.";
        return null;
    }
    for($i = 0; $i < $fields_size; $i++) {
        $query_str .= $fields[$i] . "=\"" . $values[$i] . "\"";
        if($i < $fields_size - 1)
            $query_str .= ", ";
    }
    $query_str .= " WHERE " . $where;
    $res = $db->query($query_str);

    if($res)
        return true;
    return false;
}

function db_delete(string $table, string $where) {
    global $db;

    $query_str = "DELETE FROM " . $table . " WHERE " . $where;

    $res = $db->query($query_str);

    if($res)
        return true;
    return false;
}