<?php

require_once __DIR__ . '/admin/db.php';

function randomCreatedAtBetween($startDateTime, $endDateTime)
{
    $startTs = strtotime($startDateTime);
    $endTs = strtotime($endDateTime);

    if ($startTs === false || $endTs === false || $endTs <= $startTs) {
        return date('Y-m-d H:i:s');
    }

    $randomTs = random_int($startTs, $endTs);
    return date('Y-m-d H:i:s', $randomTs);
}

function getAllUsersBasic()
{
    $connection = connect();
    $sql = "SELECT id, username FROM users ORDER BY id ASC";
    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($connection));
    }

    mysqli_stmt_execute($stmt);
    $users = getResult($stmt);
    mysqli_stmt_close($stmt);

    return $users;
}

function getPostCountForUser($userId)
{
    $connection = connect();
    $sql = "SELECT COUNT(*) AS total FROM posts WHERE user_id = ?";
    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($connection));
    }

    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return (int) ($row['total'] ?? 0);
}

function addPostWithCreatedAt($userId, $title, $content, $createdAt)
{
    $connection = connect();
    $sql = "INSERT INTO posts (user_id, title, content, created_at) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($connection));
    }

    mysqli_stmt_bind_param($stmt, "isss", $userId, $title, $content, $createdAt);
    $success = mysqli_stmt_execute($stmt);

    if (!$success) {
        mysqli_stmt_close($stmt);
        return false;
    }

    $newId = mysqli_insert_id($connection);
    mysqli_stmt_close($stmt);

    return $newId;
}

$users = getAllUsersBasic();

if (empty($users)) {
    exit("No users found. Run insert-users.php first.\n");
}

$postTemplates = [
    [
        'title' => 'A Small Lesson From This Week',
        'content' => "This week I noticed a pattern in my work: when I slow down at the beginning and define the goal clearly, everything after gets easier. I used to rush into execution, but that usually created rework later. One practical change I made was writing a short 3-point plan before starting any task. It sounds simple, but it helped me stay focused and reduced context switching. Next week I want to keep this habit and track how much time it saves.",
    ],
    [
        'title' => 'Behind The Scenes: Current Project',
        'content' => "I have been working on a project that looks straightforward on the surface, but the details are where things get interesting. The main challenge has been balancing speed with long-term maintainability. I made a few decisions that optimize for clarity first, even if they take slightly longer today. My current focus is cleaning up the rough edges and validating that the flow feels intuitive for real users. I will share a follow-up once I have real usage feedback.",
    ],
    [
        'title' => 'Three Things That Help Me Stay Consistent',
        'content' => "1) I break large tasks into smaller milestones I can finish in one session.\n2) I document decisions while I make them, not after.\n3) I review what worked and what did not at the end of each week.\nThese are not revolutionary ideas, but they reduce friction and make progress visible. Consistency has been more valuable than intensity for me, and these habits help me keep momentum without burning out.",
    ],
    [
        'title' => 'A Tool I Keep Recommending',
        'content' => "I do not recommend tools often, but this one has earned a permanent place in my workflow. It saves me time in tiny ways that add up over a full week. The biggest benefit is not speed, though, it is clarity: fewer moving parts, fewer hidden surprises, and easier collaboration.",
    ],
    [
        'title' => 'A Decision I Changed My Mind About',
        'content' => "I made a decision early in the project that felt correct at the time, but the data told a different story. Changing direction was uncomfortable, but it improved the outcome. This is your reminder that changing your mind is not failure when you have better information.",
    ],
    [
        'title' => 'Common Mistakes I Still See',
        'content' => "Even experienced teams repeat the same three mistakes: skipping the problem definition, overcomplicating the first version, and delaying feedback too long. None of these are dramatic errors, but together they slow everything down. A simpler start usually wins.",
    ],
    [
        'title' => 'What I Am Testing Next',
        'content' => "The next step for me is a small experiment: tighten one part of the workflow and measure whether users complete tasks faster. I am keeping the scope intentionally narrow so I can learn quickly. If the signal is strong, I will scale it. If not, I will document and move on.",
    ],
    [
        'title' => 'A Short Reading List',
        'content' => "Three things worth reading this month: one practical guide, one thoughtful long-form essay, and one piece that challenges your assumptions. I like mixing formats because it helps me connect strategy with execution.",
    ],
    [
        'title' => 'How I Plan My Week',
        'content' => "My weekly planning is simple: define outcomes, block focused time, and protect one buffer slot for unexpected work. I keep the system intentionally lightweight so I actually use it. Fancy systems fail when they become their own job.",
    ],
];

foreach ($users as $user) {
    $userId = (int) ($user['id'] ?? 0);
    $username = (string) ($user['username'] ?? 'unknown');

    if ($userId <= 0) {
        echo "Skipped invalid user row.\n";
        continue;
    }

    $existingCount = getPostCountForUser($userId);

    if ($existingCount >= 3) {
        echo "Skipped {$username}: already has {$existingCount} posts.\n";
        continue;
    }

    $added = 0;
    for ($i = $existingCount; $i < 3; $i++) {
        $templateIndex = ($userId + $i) % count($postTemplates);
        $template = $postTemplates[$templateIndex];
        $title = $template['title'];
        $content = $template['content'];
        $createdAt = randomCreatedAtBetween('2026-03-01 00:00:00', '2026-05-31 23:59:59');

        $newPostId = addPostWithCreatedAt($userId, $title, $content, $createdAt);
        if ($newPostId === false) {
            echo "Failed creating post {$i} for {$username}.\n";
            continue;
        }

        $added++;
    }

    echo "Added {$added} post(s) for {$username}.\n";
}

echo "Done seeding posts.\n";
