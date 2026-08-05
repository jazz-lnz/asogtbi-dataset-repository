<!doctype html>
<html lang="en">
<body style="margin:0;background:#f3efe6;color:#06314f;font-family:Arial,sans-serif;">
    <div style="max-width:620px;margin:0 auto;padding:32px 18px;">
        <div style="border-top:5px solid #03558c;border-radius:14px;background:#fffefa;padding:30px;box-shadow:0 18px 38px rgba(3,49,79,.12);">
            <p style="margin:0 0 10px;color:#e8a900;font-size:12px;font-weight:700;letter-spacing:2px;text-transform:uppercase;">ASOG TBI Dataset Repository</p>
            <h1 style="margin:0;color:#03558c;font-family:Georgia,serif;font-size:30px;font-weight:400;">Reset your password</h1>
            <p style="margin:18px 0 0;color:#526271;font-size:15px;line-height:1.6;">A password reset was requested for this repository account. Use the button below to create a new password.</p>
            <p style="margin:24px 0;text-align:center;">
                <a href="<?= esc($resetUrl, 'attr') ?>" style="display:inline-block;border-radius:8px;background:#03558c;color:#fff;padding:12px 20px;font-size:14px;font-weight:700;text-decoration:none;">Create a new password</a>
            </p>
            <p style="margin:0;color:#687786;font-size:13px;line-height:1.5;">This link expires in <?= esc((string) $expiresInMinutes) ?> minutes and can only be used once. If you did not request this reset, you can ignore this email.</p>
            <p style="margin:22px 0 0;border-top:1px solid #e4dccf;padding-top:14px;color:#8a969f;font-size:11px;line-height:1.5;">If the button does not work, open this link directly:<br><a href="<?= esc($resetUrl, 'attr') ?>" style="color:#03558c;overflow-wrap:anywhere;"><?= esc($resetUrl) ?></a></p>
        </div>
    </div>
</body>
</html>
