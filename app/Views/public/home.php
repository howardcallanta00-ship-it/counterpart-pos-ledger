<?= $this->extend('layouts/main') ?><?= $this->section('content') ?>
<section class="hero">
    <div class="hero-copy"><p class="kicker">Counter-side account control</p><h1>Keep every customer and staff record in order.</h1><p>Counterpart gives a small POS team one protected ledger for the people behind every sale.</p><div class="actions"><a class="button" href="/login">Open staff ledger</a><a class="text-link" href="/about">How this demo works</a></div></div>
    <div class="ledger-preview" aria-label="System capability summary"><div class="preview-head"><span>Daily register</span><span><?= date('d M Y') ?></span></div><dl><div><dt>Customer records</dt><dd>Contact details kept ready for service</dd></div><div><dt>Staff access</dt><dd>Hashed credentials and clear roles</dd></div><div><dt>Profile images</dt><dd>Validated, cropped, durable when configured</dd></div></dl><p class="preview-note">Built for deliberate work, not decorative dashboards.</p></div>
</section>
<section class="landing-band"><h2>One operational surface</h2><p>Find the record, make the change, and return to the counter. Listings prioritize names, contact details, roles, and the next available action.</p></section>
<section class="capabilities"><div><h2>Customer ledger</h2><p>Add and correct customer contact records with field-level validation and safe output.</p></div><div><h2>Staff register</h2><p>Create accounts, replace passwords, and prepare square avatars without exposing credentials.</p></div></section>
<?= $this->endSection() ?>
