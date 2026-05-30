<?php

function login_user($username, $password)
{
    $user = get_user($username);

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return false;
}

function register_user($username, $password, $email)
{
    $user = get_user($username);
    if ($user) {
        return false;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    return add_user($username, $hashed_password, $email);
}
?>
