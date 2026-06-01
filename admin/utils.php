<?php

// takes a timestamp and turns into readable date format, if the timestamp is invalid, it returns the original string
function readableDate($createdAt)
{
    if (!empty($createdAt)) {
        $timestamp = strtotime((string) $createdAt);
        $date = $timestamp ? date('M j, Y', $timestamp) : (string) $createdAt;
    }

    return $date;
}
function checkIfEmpty($input){
    if (trim($input) === '') {
        return true;
    }
    return false;
}
?>