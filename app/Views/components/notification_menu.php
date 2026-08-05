<?php
    $notificationMenuClass = trim('notification-menu ' . ($notificationMenuClass ?? ''));
    $notificationLabel = $notificationLabel ?? 'Notifications';
    $currentUri = service('uri');
    $notificationReturnTo = '/' . ltrim($currentUri->getPath(), '/');
    if ($currentUri->getQuery() !== '') {
        $notificationReturnTo .= '?' . $currentUri->getQuery();
    }
?>
<details class="<?= esc($notificationMenuClass, 'attr') ?>" data-notification-menu data-latest-id="<?= esc((string) ($latestNotificationId ?? 0), 'attr') ?>">
    <summary class="notification-trigger" aria-label="<?= esc($notificationLabel, 'attr') ?>">
        <span class="material-symbols-rounded" aria-hidden="true">notifications</span>
        <span class="notification-badge" data-notification-count <?= ($unreadNotificationCount ?? 0) < 1 ? 'hidden' : '' ?>><?= esc((string) ($unreadNotificationCount ?? 0)) ?></span>
    </summary>
    <div class="notification-popover">
        <div class="notification-popover__head">
            <div><small>Repository activity</small><strong><?= esc($notificationLabel) ?></strong></div>
            <?php if (($unreadNotificationCount ?? 0) > 0): ?><span><?= esc((string) $unreadNotificationCount) ?> new</span><?php endif; ?>
        </div>
        <div class="notification-popover__list">
            <?php if (($headerNotifications ?? []) === []): ?>
                <div class="notification-empty">
                    <span class="material-symbols-rounded" aria-hidden="true">notifications_off</span>
                    <p>No recent repository activity.</p>
                </div>
            <?php else: ?>
                <?php foreach ($headerNotifications as $notification): ?>
                    <a class="<?= empty($notification['read_at']) ? 'is-unread' : '' ?>" href="<?= ! empty($notification['link']) ? site_url(ltrim((string) $notification['link'], '/')) : '#' ?>">
                        <span class="material-symbols-rounded" aria-hidden="true"><?= empty($notification['read_at']) ? 'mark_email_unread' : 'drafts' ?></span>
                        <span><strong><?= esc($notification['title']) ?></strong><small><?= esc($notification['message']) ?></small></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php if (($headerNotifications ?? []) !== []): ?>
            <form method="post" action="<?= site_url('notifications/read') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="return_to" value="<?= esc($notificationReturnTo, 'attr') ?>">
                <button class="text-button" type="submit">Mark all as read</button>
            </form>
        <?php endif; ?>
    </div>
</details>
