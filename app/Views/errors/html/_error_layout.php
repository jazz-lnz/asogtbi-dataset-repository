<?php

helper('url');

$error = array_merge([
    'code' => '',
    'title' => '',
    'message' => '',
    'actions' => [],
    'illustration' => null,
    'illustrationClass' => '',
], $error ?? []);

$actionClassMap = [
    'primary' => 'button gold',
    'secondary' => 'button secondary',
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <meta name="theme-color" content="#03558c">
    <title><?= esc(($error['code'] ? $error['code'] . ' - ' : '') . $error['title']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800;900&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="error-page">
<main class="error-shell">
    <div class="shell">
        <section class="error-card" aria-labelledby="error-title">
            <?php if (! empty($error['illustration'])) : ?>
                <div class="error-visual<?= $error['illustrationClass'] ? ' ' . esc($error['illustrationClass'], 'attr') : '' ?>" aria-hidden="true">
                    <?= $error['illustration'] ?>
                </div>
            <?php endif; ?>

            <?php if (! empty($error['code'])) : ?>
                <p class="error-code">Error <?= esc($error['code']) ?></p>
            <?php endif; ?>

            <h1 id="error-title"><?= esc($error['title']) ?></h1>

            <?php if (! empty($error['message'])) : ?>
                <p class="error-message"><?= esc($error['message']) ?></p>
            <?php endif; ?>

            <?php if (! empty($error['actions']) && is_array($error['actions'])) : ?>
                <div class="error-actions" aria-label="Error page actions">
                    <?php foreach ($error['actions'] as $action) : ?>
                        <?php
                        $variant = $action['variant'] ?? 'secondary';
                        $classes = $actionClassMap[$variant] ?? $actionClassMap['secondary'];
                        $isHistoryBack = ($action['behavior'] ?? null) === 'history-back';
                        $attributes = [];

                        foreach (($action['attributes'] ?? []) as $name => $value) {
                            $attributes[] = esc($name, 'attr') . '="' . esc((string) $value, 'attr') . '"';
                        }
                        ?>
                        <?php if ($isHistoryBack) : ?>
                            <button
                                class="<?= esc($classes) ?>"
                                type="button"
                                onclick="window.history.back(); return false;"
                                <?= $attributes ? implode(' ', $attributes) : '' ?>
                            >
                                <?= esc($action['label'] ?? '') ?>
                            </button>
                        <?php else : ?>
                            <a
                                class="<?= esc($classes) ?>"
                                href="<?= esc($action['href'] ?? '#', 'attr') ?>"
                                <?php if (! empty($action['target'])) : ?>target="<?= esc($action['target'], 'attr') ?>"<?php endif; ?>
                                <?php if (! empty($action['rel'])) : ?>rel="<?= esc($action['rel'], 'attr') ?>"<?php endif; ?>
                                <?= $attributes ? implode(' ', $attributes) : '' ?>
                            >
                                <?= esc($action['label'] ?? '') ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>
</body>
</html>
