<?php
function loginUser($username, $password)
{   // Checks if user exists and password is correct
    $user = getUser($username);

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return false;
}

function registerUser($firstname, $lastname, $username, $password, $email)
{
    // Checks if username already exists, if not creates a new user with hashed password
    $user = getUser($username);
    if ($user) {
        return false;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    return addUser($firstname, $lastname, $username, $hashed_password, $email);
}
?>
