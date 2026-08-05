# Google SMTP Password Reset Email Setup

The simplest setup for this repository is Gmail SMTP with a dedicated Google sender account and an App Password. **Do not create a Google Cloud project or service-account JSON file for this path.** The application uses SMTP username/password settings, not the Gmail API.

## Fastest Path: Gmail SMTP

Use a dedicated sender such as `repository@your-domain.edu` or `repository@gmail.com`. Do not use a personal staff mailbox if the repository will send production messages.

### 1. Open the sender account

Sign in to the Google account that will send the reset emails. Open [Google Account Security](https://myaccount.google.com/security).

### 2. Turn on 2-Step Verification

1. Under **How you sign in to Google**, select **2-Step Verification**.
2. Select **Get started** and complete Google's prompts.
3. Return to the Security page after 2-Step Verification is enabled.

Google requires 2-Step Verification before an App Password can be created. See [Google's App Password guidance](https://support.google.com/accounts/answer/185833).

### 3. Create the SMTP App Password

1. Open [App passwords](https://myaccount.google.com/apppasswords).
2. Sign in again if Google asks.
3. Enter `ASOG Dataset Repository` as the app name.
4. Select **Create**.
5. Copy the displayed 16-character password immediately. Google shows it only once.

If **App passwords** is missing, the account may be managed by an organization that disables them, may use Advanced Protection, or may have 2-Step Verification configured only with security keys. Use the Workspace SMTP relay fallback below instead.

### 4. Add the SMTP settings to `.env`

Open the repository's local `.env` file and add the following. Replace the two placeholders, but do not add spaces inside the App Password.

```ini
CI_ENVIRONMENT = production
app.baseURL = https://your-public-domain.example/

email.protocol = smtp
email.fromEmail = repository@gmail.com
email.fromName = ASOG TBI Dataset Repository
email.SMTPHost = smtp.gmail.com
email.SMTPUser = repository@gmail.com
email.SMTPPass = xxxxxxxxxxxxxxxx
email.SMTPPort = 587
email.SMTPCrypto = tls
email.SMTPAuthMethod = login
email.SMTPTimeout = 10
```

Use the complete sender email address for both `email.fromEmail` and `email.SMTPUser`. Use the 16-character App Password for `email.SMTPPass`, not the normal Google account password. Google's documented Gmail SMTP settings use `smtp.gmail.com`; port `587` is the TLS option. [Gmail SMTP documentation](https://developers.google.com/workspace/gmail/imap/imap-smtp)

### 5. Test the reset flow

1. Make sure the password-reset migration has been applied:

```powershell
php spark migrate
```

2. Open `/forgot-password`.
3. Submit an existing local/password account email.
4. Confirm the email arrives and that its button opens the configured HTTPS site.
5. Set a new password.
6. Try the same link again. It should no longer work because reset links are single-use.
7. If delivery fails, inspect `writable/logs/`. The application logs delivery failure without logging the reset token.

## Fallback: Workspace SMTP Relay

Use this only when the Workspace organization does not allow App Passwords. This requires a Google Workspace administrator.

1. Open the [Google Admin console](https://admin.google.com/), not Google Cloud Console.
2. Go to **Apps** > **Google Workspace** > **Gmail** > **Routing**.
3. Open **SMTP relay service** and select **Configure** or **Add another rule**.
4. Give the rule a name such as `ASOG Dataset Repository`.
5. Allow mail from the approved sender/account. Use the narrowest available sender or IP restriction.
6. Require SMTP authentication for this repository because the current CodeIgniter mailer supplies a username and password.
7. Save the rule and allow time for Google to apply it.
8. Use the relay host and port supplied by the policy. The common configuration is:

```ini
email.protocol = smtp
email.SMTPHost = smtp-relay.gmail.com
email.SMTPPort = 587
email.SMTPCrypto = tls
email.SMTPAuthMethod = login
```

Google documents the relay setup and its authentication/allowlist options in [Route outgoing SMTP relay messages through Google](https://support.google.com/a/answer/2956491). Add the resulting `SMTPUser` and `SMTPPass` to `.env`; do not commit them.

## Important Distinction

If the credentials you receive are a Google Cloud service account, OAuth client, or Gmail API credential rather than SMTP credentials, they will not work with the current `email.SMTP*` settings. That requires a separate Gmail API/OAuth implementation and different environment variables. Ask for SMTP relay credentials or an approved sender account for this repository's current integration.

## Security Checklist

- Use a dedicated sender account.
- Store SMTP credentials only in the deployment secret store or `.env` outside Git.
- Use TLS on port `587` unless the approved provider requires another secure mode.
- Keep `CI_ENVIRONMENT=production` on the deployed server.
- Use an HTTPS `app.baseURL`.
- Revoke and replace App Passwords when the sender account changes or is no longer used. Google also revokes App Passwords when the account's normal password changes.
