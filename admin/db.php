<?php

/*
|--------------------------------------------------------------------------
| Enkel databasmodul (MySQLi + Prepared Statements)
|--------------------------------------------------------------------------
| Denna fil innehåller funktioner för att:
| - ansluta till databasen
| - lägga till användare
| - uppdatera användare
| - ta bort användare
| - hämta en eller flera användare
| 
| Du ska lägga till alla funktioner som du behöver
| och ändra i de befintliga om det behövs
|
| Viktigt:
| - Alla SQL-frågor använder prepared statements (skydd mot SQL injection)
| - Samma databasanslutning återanvänds under hela scriptets körning
| - Lösenord som skickas in ska redan vara hashade (password_hash)
|
*/

// Sökväg till din lokala db-credentials.php
$localPath = __DIR__ . '/db-credentials.php';

// Sökväg till db-credentials.php på webbservern
// VIKTIGT: Byt USER mot ditt användarnamn
$serverPath = '/var/private/USER/db-credentials.php';

// Om skriptet körs på webbservern används $serverPath
// Annars används $localPath för att läsa in dina lokala databasuppgifter
if (file_exists($serverPath)) {
    require_once($serverPath);
} elseif (file_exists($localPath)) {
    require_once($localPath);
} else {
    die("Kunde inte hitta db-credentials.php.");
}
function connect()
{
    // static gör att variabeln "lever kvar" mellan funktionsanrop
    // Det betyder att vi bara skapar EN databasanslutning per request
    static $connection = null;

    // Om anslutning redan finns – återanvänd den
    if ($connection !== null) {
        return $connection;
    }

    // Skapa ny anslutning till databasen 
    $connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

    // Om anslutningen misslyckas avbryts programmet
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Sätt teckenkodning till utf8mb4 (viktigt för att stödja alla tecken, t.ex. emoji)
    mysqli_set_charset($connection, 'utf8mb4');

    return $connection;
}

// User-functions
function addUser($firstname, $lastname, $username, $hashedPassword, $email, $title = null, $bio = null)
{
    $connection = connect();

    $sql = "INSERT INTO users (firstname, lastname, username, password, email, title, bio) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($connection));
    }

    mysqli_stmt_bind_param($stmt, "sssssss", $firstname, $lastname, $username, $hashedPassword, $email, $title, $bio);

    $success = mysqli_stmt_execute($stmt);

    // Om insert misslyckas (t.ex. duplicate username) returneras false
    if (!$success) {
        mysqli_stmt_close($stmt);
        return false;
    }

    // Hämtar id från AUTO_INCREMENT-kolumnen
    $newId = mysqli_insert_id($connection);

    mysqli_stmt_close($stmt);

    return $newId; // Returnera id för posten
}

function updateUserProfile($id, $newFirstname, $newLastname, $newUsername, $newTitle, $newBio)
{
    $connection = connect();

    $sql = "UPDATE users SET firstname = ?, lastname = ?, username = ?, title = ?, bio = ? WHERE id = ?";
    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($connection));
    }

    mysqli_stmt_bind_param($stmt, "sssssi", $newFirstname, $newLastname, $newUsername, $newTitle, $newBio, $id);
    $success = mysqli_stmt_execute($stmt);

    if (!$success) {
        mysqli_stmt_close($stmt);
        return false;
    }

    $affectedRows = mysqli_stmt_affected_rows($stmt);

    mysqli_stmt_close($stmt);

    // Returnerar antal påverkade rader:
    // 1+ = något uppdaterades
    // 0  = inget ändrades (t.ex. fel id eller samma värde)
    return $affectedRows;
}

function usernameExists($username)
{
   return getUser($username) !== null;
}


function getUser($username)
{
    $connection = connect();

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($connection));
    }

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    // Hämtar EN rad som en associativ array:
    // $row['username'], $row['password'], etc.
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return $row; // Returnerar en associativ array (eller null)
}

function getUserById($id)
{
    $connection = connect();

    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($connection));
    }

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    // Hämtar EN rad som en associativ array:
    // $row['username'], $row['password'], etc.
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return $row; // Returnerar en associativ array (eller null)
}

function getUsers()
{
    $connection = connect();

    // ORDER BY created_at sorterar användarna i den ordning de skapades
    // Kräver att tabellen har en kolumn som heter created_at
    $sql = "SELECT * FROM users ORDER BY created_at";
    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($connection));
    }

    mysqli_stmt_execute($stmt);

    $rows = getResult($stmt);

    mysqli_stmt_close($stmt);

    return $rows;
}

// Post-related functions

function deletePost($post_id, $user_id)
{
    $connection = connect();

    $sql = "DELETE FROM posts WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($connection));
    }

    mysqli_stmt_bind_param($stmt, "ii", $post_id, $user_id);
    mysqli_stmt_execute($stmt);

    $affectedRows = mysqli_stmt_affected_rows($stmt);

    mysqli_stmt_close($stmt);

    // Returnerar antal påverkade rader:
    // 1+ = något uppdaterades
    // 0  = inget ändrades (t.ex. fel id eller samma värde)
    return $affectedRows;
}

function getResult($stmt)
{
    $rows = array();

    // Hämtar resultatobjektet från prepared statement
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        // Loopa igenom alla rader
        // Varje rad läggs in i arrayen $rows
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }

    // Returnerar en array med alla rader
    // Om inga rader finns returneras en tom array []
    return $rows;
}

function getPostsSorted(){
    $connection = connect();

    $sql = "SELECT * FROM posts ORDER BY created_at DESC LIMIT 6";  
    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($connection));
    }

    mysqli_stmt_execute($stmt);

    $posts = getResult($stmt);
    mysqli_stmt_close($stmt);

    return $posts;
}

function getPostsByUser($user_id)
{
    $connection = connect();

    // Hämtar inlägg från specifik användare (user_id) och inkluderar bilder från images table om det finns 
    $sql = "
        SELECT 
            p.id,
            p.title,
            p.content,
            p.created_at,
            i.filename,
            i.description
        FROM posts p
        LEFT JOIN images i ON i.post_id = p.id
        WHERE p.user_id = ?
        ORDER BY p.created_at DESC
    ";

    $stmt = mysqli_prepare($connection, $sql);
    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($connection));
    }

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $userPosts = getResult($stmt);
    mysqli_stmt_close($stmt);

    return $userPosts;
}


function addPost($user_id, $title, $content)
{
    // Add image upload
    $connection = connect();

    $sql = "INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($connection));
    }

    mysqli_stmt_bind_param($stmt, "iss", $user_id, $title, $content);
    $success = mysqli_stmt_execute($stmt);

    if (!$success) {
        mysqli_stmt_close($stmt);
        return false;
    }

    $newId = mysqli_insert_id($connection);
    mysqli_stmt_close($stmt);

    return $newId;
}


function updatePost($id, $user_id, $newTitle, $newContent)
{
    $connection = connect();

    $sql = "UPDATE posts SET title = ?, content = ? WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($connection));
    }

    mysqli_stmt_bind_param($stmt, "ssii", $newTitle, $newContent, $id, $user_id);
    $success = mysqli_stmt_execute($stmt);

    if (!$success) {
        mysqli_stmt_close($stmt);
        return false;
    }

    $affectedRows = mysqli_stmt_affected_rows($stmt);

    mysqli_stmt_close($stmt);

    // Returnerar antal påverkade rader:
    // 1+ = något uppdaterades
    // 0  = inget ändrades (t.ex. fel id eller samma värde)
    return $affectedRows;
}
