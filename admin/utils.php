<?php

// takes a timestamp and turns into readable date format, if the timestamp is invalid, it returns the original string
function readable_date($createdAt)
{
    if (!empty($createdAt)) {
        $timestamp = strtotime((string) $createdAt);
        $date = $timestamp ? date('M j, Y', $timestamp) : (string) $createdAt;
    }

    return $date;
}

function hash_password($password){
    $hash = password_hash($password, PASSWORD_DEFAULT);
    return $hash;
}

?>