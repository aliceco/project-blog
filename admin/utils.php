<?php

function readableDate($createdAt)
{
    // takes a timestamp and turns into readable date format, if the timestamp is invalid, it returns the original string
    if (!empty($createdAt)) {
        $timestamp = strtotime((string) $createdAt);
        $date = $timestamp ? date('M j, Y', $timestamp) : (string) $createdAt;
    }

    return $date;
}
function checkIfEmpty($input)
{
    if (trim($input) === '') {
        return true;
    }
    return false;
}

function validateFile($file)
{
    // Validates that the uploaded file exists and is a supported image type
    if (empty($file) || !isset($file['tmp_name']) || $file['tmp_name'] === '') {
        return ['ok' => false, 'error' => 'No file uploaded.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    $allowed_types = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
    ];

    if (!array_key_exists($mime, $allowed_types)) {
        return ['ok' => false, 'error' => 'Only JPG, PNG, and GIF images are allowed.'];
    }

    $extension = $allowed_types[$mime];

    return ['ok' => true, 'mime'=> $mime, 'extension' => $extension];

}

function getUploadErrorMessage($errorCode)
{
    // Maps errors given from $_FILE[] to error messages
    $uploadErrors = [
        UPLOAD_ERR_OK => 'No errors.',
        UPLOAD_ERR_INI_SIZE => 'The file is too large (php.ini limit).',
        UPLOAD_ERR_FORM_SIZE => 'The file is too large (form limit).',
        UPLOAD_ERR_PARTIAL => 'The file was only partially uploaded.',
        UPLOAD_ERR_NO_FILE => 'No file was selected.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the upload.',
    ];

    return $uploadErrors[$errorCode] ?? 'Unknown upload error.';
}

function validateOptionalImageUpload($file, $maxBytes = 512000)
{
    // Maps error messages to the correct return object. 

    if (empty($file) || !isset($file['error'])) {
        return ['ok' => true, 'uploaded' => false];
    }

    $errorCode = $file['error']; 

    // checks what errors occurred during upload
    if ($errorCode === UPLOAD_ERR_NO_FILE) { 
        return ['ok' => true, 'uploaded' => false];
    }

    if ($errorCode !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'uploaded' => false, 'error' => getUploadErrorMessage($errorCode)];
    }

    $fileSize = ($file['size'] ?? 0);
    if ($fileSize <= 0) {
        return ['ok' => false, 'uploaded' => false, 'error' => 'Uploaded file is empty.'];
    }

    if ($fileSize > $maxBytes) {
        return ['ok' => false, 'uploaded' => false, 'error' => 'The file is too large.'];
    }

    $fileCheck = validateFile($file);
    if (!$fileCheck['ok']) {
        return ['ok' => false, 'uploaded' => false, 'error' => $fileCheck['error']];
    }

    return [
        'ok' => true, // valid state
        'uploaded' => true, // file was uploaded and is valid
        'mime' => $fileCheck['mime'],
        'extension' => $fileCheck['extension'],
    ];
}

