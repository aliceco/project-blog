
<?php
$head = 'Privacy Policy';
require_once __DIR__ . '/../admin/session.php';
require_once __DIR__ . '/../includes/document-head.php';
require_once __DIR__ . '/../components/navbar.php';

?>

<main class="max-w-3xl mx-auto px-6 py-10">
    <div class="mb-6">
        <button type="button" onclick="history.back()"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm bg-secondary text-secondary-foreground rounded-md hover:opacity-90 transition-opacity cursor-pointer">
            Back
        </button>
    </div>

    <article class="bg-card border border-border rounded-lg shadow-sm p-6 md:p-8 space-y-6">
        <header class="border-b border-border pb-4">
            <h1 class="text-3xl text-foreground" style="font-family: 'Playfair Display', serif; font-weight: 600;">
                Privacy Policy (GDPR)
            </h1>
            <p class="text-sm text-muted-foreground mt-2" style="font-family: 'DM Sans', sans-serif;">
                Information about the collection and processing of personal data.
            </p>
        </header>

        <section class="space-y-2">
            <h2 class="text-lg text-foreground" style="font-family: 'DM Sans', sans-serif; font-weight: 700;">What data is collected?</h2>
            <p class="text-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
                Your username and any other information you choose to upload to the web server.
            </p>
        </section>

        <section class="space-y-2">
            <h2 class="text-lg text-foreground" style="font-family: 'DM Sans', sans-serif; font-weight: 700;">Purpose and data controller</h2>
            <p class="text-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
                This website is part of coursework for students at Lulea University of Technology in the course
                Web Development 2. If you create an account, your chosen username and any uploaded information are
                collected to administer your account. Data is stored on a web server provided by Microsoft Azure.
            </p>
        </section>

        <section class="space-y-2">
            <h2 class="text-lg text-foreground" style="font-family: 'DM Sans', sans-serif; font-weight: 700;">Legal basis</h2>
            <p class="text-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
                Consent is the legal basis. You may withdraw your consent at any time, and your data will then be deleted.
            </p>
        </section>

        <section class="space-y-2">
            <h2 class="text-lg text-foreground" style="font-family: 'DM Sans', sans-serif; font-weight: 700;">Data retention period</h2>
            <p class="text-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
                Data is stored until the course has been graded, unless you request deletion earlier.
            </p>
        </section>

        <section class="space-y-2">
            <h2 class="text-lg text-foreground" style="font-family: 'DM Sans', sans-serif; font-weight: 700;">Sharing and transfers</h2>
            <p class="text-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
                The platform provider may access data for support purposes and stores data in Northern Europe.
                Data is not transferred to countries outside the EU/EEA.
            </p>
        </section>

        <section class="space-y-2">
            <h2 class="text-lg text-foreground" style="font-family: 'DM Sans', sans-serif; font-weight: 700;">Important notice</h2>
            <p class="text-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
                Do not share sensitive information about yourself or others. There is always a risk of data leakage.
            </p>
        </section>

        <section class="space-y-2">
            <h2 class="text-lg text-foreground" style="font-family: 'DM Sans', sans-serif; font-weight: 700;">Contact</h2>
            <p class="text-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
                If you have questions, you can contact course coordinator Susanne Fahlman: susanne.fahlman@ltu.se.
                If you are dissatisfied with how your personal data is processed, you may contact the Swedish Authority
                for Privacy Protection or the university's data protection officer at dataskydd@ltu.se.
            </p>
            <a href="https://www.ltu.se/om-ltu/personuppgifter-gdpr" target="_blank" rel="noopener noreferrer"
                class="inline-block text-accent hover:opacity-80 underline">
                Read more about personal data processing (LTU)
            </a>
        </section>
    </article>
</main>
<?= require_once __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>
