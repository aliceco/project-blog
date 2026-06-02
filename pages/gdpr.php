
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
                Information om insamling och behandling av personuppgifter.
            </p>
        </header>

        <section class="space-y-2">
            <h2 class="text-lg text-foreground" style="font-family: 'DM Sans', sans-serif; font-weight: 700;">Vilka uppgifter samlas in?</h2>
            <p class="text-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
                Ditt användarnamn plus annan information som du själv väljer att ladda upp till webbservern.
            </p>
        </section>

        <section class="space-y-2">
            <h2 class="text-lg text-foreground" style="font-family: 'DM Sans', sans-serif; font-weight: 700;">Ändamål och ansvarig</h2>
            <p class="text-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
                Skapandet av webbplatsen ingår i kursuppgifter för studenter vid Luleå tekniska universitet i kursen
                Webbutveckling 2. Om du skapar en användare samlas ditt valda användarnamn och annan uppladdad information
                in för att administrera ditt konto. Uppgifterna lagras på en webbserver som tillhandahålls av Microsoft Azure.
            </p>
        </section>

        <section class="space-y-2">
            <h2 class="text-lg text-foreground" style="font-family: 'DM Sans', sans-serif; font-weight: 700;">Rättslig grund</h2>
            <p class="text-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
                Samtycke är den rättsliga grunden. Du kan när som helst återkalla ditt samtycke och dina uppgifter kommer då att tas bort.
            </p>
        </section>

        <section class="space-y-2">
            <h2 class="text-lg text-foreground" style="font-family: 'DM Sans', sans-serif; font-weight: 700;">Lagringstid</h2>
            <p class="text-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
                Uppgifterna lagras tills kursen är betygssatt, om du inte begär radering innan dess.
            </p>
        </section>

        <section class="space-y-2">
            <h2 class="text-lg text-foreground" style="font-family: 'DM Sans', sans-serif; font-weight: 700;">Delning och överföring</h2>
            <p class="text-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
                Plattformens tillhandahållare kan ta del av uppgifterna vid support och lagrar uppgifter i Norra Europa.
                Uppgifter överförs inte till land utanför EU/EES.
            </p>
        </section>

        <section class="space-y-2">
            <h2 class="text-lg text-foreground" style="font-family: 'DM Sans', sans-serif; font-weight: 700;">Viktigt att tänka på</h2>
            <p class="text-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
                Dela inte känsliga uppgifter om dig själv eller andra. Det finns alltid en risk för dataläckage.
            </p>
        </section>

        <section class="space-y-2">
            <h2 class="text-lg text-foreground" style="font-family: 'DM Sans', sans-serif; font-weight: 700;">Kontakt</h2>
            <p class="text-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
                Vid frågor kan du kontakta kursansvarig Susanne Fahlman: susanne.fahlman@ltu.se.
                Om du är missnöjd med behandlingen av dina personuppgifter kan du kontakta Integritetsskyddsmyndigheten
                eller universitetets dataskyddsombud via dataskydd@ltu.se.
            </p>
            <a href="https://www.ltu.se/om-ltu/personuppgifter-gdpr" target="_blank" rel="noopener noreferrer"
                class="inline-block text-accent hover:opacity-80 underline">
                Läs mer om behandling av personuppgifter (LTU)
            </a>
        </section>
    </article>
</main>

</body>

</html>
