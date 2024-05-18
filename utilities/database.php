<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "mitra_inventory_database";
function connect_to_mysql()
{
    global $host;
    global $username;
    global $password;
    global $database;
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn) {
        return $conn;
    } else {
        return false;
    }
}

function get_data(mysqli $conn, string $query, string $information = "", string $link = "")
{
    $return_values = array();
    $number = 1;
    if ($result = $conn->query($query)) {
        while ($row = $result->fetch_assoc()) {
            $return_values[] = array();
            $return_values[count($return_values) - 1]["No"] = $number;
            foreach ($row as $key => $value) {
                $return_values[count($return_values) - 1][$key] = $value;
            }
            $number++;
        }
    }
    insert_timeline($conn, $information, $link);
    return $return_values;
}

function insert_data(mysqli $conn, string $table, string $key, string $values, string $information = "", string $link = "")
{
    $result = $conn->query("INSERT INTO $table$key VALUES $values") === TRUE;
    $result_timelines = insert_timeline($conn, $information, $link);
    if ($result && $result_timelines) {
        return true;
    } else {
        return false;
    }
}

function update_data(mysqli $conn, string $table, string $params, string $new_data, string $information = "", string $link = "")
{
    $result = $conn->query("UPDATE $table SET $new_data WHERE $params") === TRUE;
    $result_timelines = insert_timeline($conn, $information, $link);
    if ($result && $result_timelines) {
        return true;
    } else {
        return false;
    }
}

function delete_data(mysqli $conn, string $table, string $params, string $information = "", string $link = "")
{
    $result = $conn->query("DELETE FROM $table WHERE $params") === TRUE;
    $result_timelines = insert_timeline($conn, $information, $link);
    if ($result && $result_timelines) {
        return true;
    } else {
        return false;
    }
}

function insert_timeline(mysqli $conn, string $information, string $link)
{
    $result_timelines = $information ? ($link == "" ? $conn->query("INSERT INTO timelines(information) VALUES ('$information')") === TRUE : $conn->query("INSERT INTO timelines(information, link) VALUES ('$information', '$link')") === TRUE) : true;
    if ($result_timelines) {
        return true;
    } else {
        return false;
    }
}