<?php $path = trim(service('uri')->getPath(), '/'); ?>
<header class="site-header">
<div class="shell nav-shell">
    <a class="brand" href="/" aria-label="Counterpart POS home"><span class="brand-mark" aria-hidden="true">CP</span><span>Counterpart<small>POS ledger</small></span></a>
    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">Menu</button>
    <nav id="site-nav" aria-label="Primary">
        <a href="/" <?= $path===''?'aria-current="page"':'' ?>>Home</a>
        <a href="/about" <?= $path==='about'?'aria-current="page"':'' ?>>About</a>
        <?php if(session()->get('auth_user_id')): ?>
            <a href="/customers" <?= str_starts_with($path,'customers')?'aria-current="page"':'' ?>>Customers</a>
            <a href="/users" <?= str_starts_with($path,'users')?'aria-current="page"':'' ?>>Staff</a>
            <span class="identity"><strong><?= esc(session()->get('auth_user_name')) ?></strong><small><?= esc(session()->get('auth_user_role')) ?></small></span>
            <form action="/logout" method="post"><?= csrf_field() ?><button class="nav-action" type="submit">Sign out</button></form>
        <?php else: ?><a class="nav-action" href="/login" <?= $path==='login'?'aria-current="page"':'' ?>>Staff sign in</a><?php endif ?>
    </nav>
</div>
</header>
