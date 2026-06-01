<?php

require_once __DIR__ . '/admin/db.php';

$users = [
    [
        'firstname' => 'Sofia',
        'lastname'  => 'Reyes',
        'username'   => 'sofiareyes',
        'email'      => 'sofia.reyes@example.com',
        'password'   => 'sofia_plain_password',
        'title'      => 'Head of Product',
        'bio'        => 'I build products for a living and think about interfaces way too much in my free time. Currently obsessed with how people actually use software vs. how we think they do. I lecture occasionally at UCL and make a zine about speculative UI — very niche, very fun. Always up for a chat about research, product strategy, or where to find good coffee in East London.',
    ],
    [
        'firstname' => 'Marcus',
        'lastname'  => 'Tran',
        'username'   => 'marcust',
        'email'      => 'marcus.tran@example.com',
        'password'   => 'marcus_plain_password',
        'title'      => 'Senior Engineer',
        'bio'        => "I work on distributed systems and care a lot about reliability. Most of my open-source stuff is database tooling — check the repos if that's your thing. When I'm not staring at traces, I'm at the bouldering wall. I don't post often but when I do it's either something technical I couldn't find documented anywhere, or a trip report. No hot takes, I promise.",
    ],
    [
        'firstname' => 'Amara',
        'lastname'  => 'Kone',
        'username'   => 'amarak',
        'email'      => 'amara.kone@example.com',
        'password'   => 'amara_plain_password',
        'title'      => 'Brand Designer',
        'bio'        => "Designer, sometime illustrator, typography nerd. I've spent the last ten years building visual systems for brands — lately split between West Africa and Europe. I run workshops on type when I get the chance, and I collect vintage Ghanaian and Senegalese film posters (slowly, carefully). This blog is mostly a place to think out loud about design, culture, and what images are actually doing to us.",
    ],
    [
        'firstname' => 'Jonas',
        'lastname'  => 'Petersen',
        'username'   => 'jonaspetersen',
        'email'      => 'jonas.petersen@example.com',
        'password'   => 'jonas_plain_password',
        'title'      => 'Data Scientist',
        'bio'        => "I have a PhD in applied statistics and I use it to make bar charts. I write here mostly about the gap between what data can tell you and what people want it to tell them. I code in R by default, Python under duress. I don't own a smartphone, which people find either inspiring or insufferable. Either reaction is fine with me.",
    ],
    [
        'firstname' => 'Leila',
        'lastname'  => 'Nazari',
        'username'   => 'lnazari',
        'email'      => 'leila.nazari@example.com',
        'password'   => 'leila_plain_password',
        'title'      => 'Legal Counsel',
        'bio'        => "Lawyer. I spent six years in private practice before going in-house, which was the right call. I write here because most legal content online is either too vague to be useful or too dense to finish. I'm particularly interested in data privacy and the ways tech regulation differs across jurisdictions. Also: comparative constitutional law, which is a hobby I inflict on very few people.",
    ],
    [
        'firstname' => 'David',
        'lastname'  => 'Osei',
        'username'   => 'davidosei',
        'email'      => 'david.osei@example.com',
        'password'   => 'david_plain_password',
        'title'      => 'Customer Success Lead',
        'bio'        => 'I work with enterprise customers and spend a lot of time thinking about what actually makes teams stick with software long-term. I also host a podcast on remote work culture — small but loyal audience. Currently learning Portuguese, slowly. I write here about customer relationships, distributed teams, and occasionally about living between Accra and Amsterdam, which keeps things interesting.',
    ],
];

foreach ($users as $user) {
    $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
    $newId = addUser(
        $user['firstname'],
        $user['lastname'],
        $user['username'],
        $hashedPassword,
        $user['email'],
        $user['title'],
        $user['bio']
    );

    if ($newId === false) {
        echo "Skipped/failed: {$user['username']}\n";
    } else {
        echo "Inserted user #{$newId}: {$user['username']}\n";
    }
}
