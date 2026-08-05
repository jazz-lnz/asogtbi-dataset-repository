<?= $this->extend('layouts/portal') ?>
<?= $this->section('content') ?>
<header class="portal-heading portal-heading--wide">
    <p class="eyebrow">Accountability</p>
    <h1>Audit logs</h1>
    <p>Review authentication, upload, moderation, publication, access, and lifecycle events.</p>
</header>

<section class="audit-log-shell">
    <div class="audit-log-toolbar">
        <div>
            <strong>Repository activity trail</strong>
            <small><?= count($logs) ?> visible <?= count($logs) === 1 ? 'event' : 'events' ?></small>
        </div>
        <form class="portal-search audit-log-search" method="get">
            <input type="search" name="q" value="<?= esc($search) ?>" placeholder="Search action, user, or details">
            <button class="button" type="submit">Search</button>
        </form>
    </div>
    <div class="table-wrap audit-log-table"><table class="portal-table"><thead><tr><th>Time</th><th>User</th><th>Action</th><th>Entity</th><th>Details</th><th>IP</th></tr></thead><tbody><?php foreach ($logs as $log): ?><tr><td><?= esc($log['created_at']) ?></td><td><?= esc($log['user_name'] ?? 'System') ?></td><td><?= esc($log['action']) ?></td><td><?= esc(($log['entity_type'] ?? '-') . ($log['entity_id'] ? ' #' . $log['entity_id'] : '')) ?></td><td><?= esc($log['details'] ?? '') ?></td><td><?= esc($log['ip_address'] ?? '') ?></td></tr><?php endforeach; ?></tbody></table></div>
</section>
<?= $this->endSection() ?>
