# WHMCS Admin Hooks

A collection of quality-of-life hooks for the WHMCS admin area. Drop them into your `includes/hooks/` directory and they work immediately. No core files modified, survives WHMCS updates.

## Hooks

### admin_password_autofill_fix.php

Fixes Safari (and some other browsers) treating password fields on admin settings pages as "new password" fields, showing the password generator instead of your saved credentials.

**The problem:** Safari sees a password input on a settings page and assumes you're changing your password. It offers to generate a new one instead of autofilling your saved login.

**The fix:** Sets `autocomplete="current-password"` on all admin password fields so browsers offer the correct autofill behavior.

### admin_disable_autofocus_search.php

Prevents the Select2 client search dropdown from auto-focusing and opening when you load a client detail page.

**The problem:** WHMCS auto-focuses the client search field on client pages, which immediately opens a large dropdown covering the page content. Easy to accidentally click the wrong client.

**The fix:** Removes autofocus attributes, blurs any auto-focused Select2 fields, and closes any auto-opened dropdowns on page load.

## Installation

1. Download or clone this repository
2. Copy the `.php` files into your WHMCS `includes/hooks/` directory
3. That's it. No configuration needed.

```bash
# Example
cp *.php /path/to/whmcs/includes/hooks/
```

## Compatibility

Tested with WHMCS v9. Should work with v8+ as well.

## License

MIT
