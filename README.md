# WHMCS Admin Hooks

A collection of quality-of-life hooks for the WHMCS admin area. Drop them into your `includes/hooks/` directory and they work immediately. No core files modified, survives WHMCS updates.

## Hooks

### admin_extend_session.php

Extends admin session lifetime to prevent frequent logouts.

**The problem:** PHP's default `session.gc_maxlifetime` is often 1440 seconds (24 minutes). Combined with WHMCS's own session handling, this causes frequent logouts and re-authentication prompts, even with "Remember Me" checked.

**The fix:** Extends the PHP session lifetime and cookie expiry to 24 hours (86400 seconds) on every admin page load, keeping your session alive as long as you're actively using the admin panel.

### admin_disable_autofocus_search.php

Prevents the Select2 client search dropdown from auto-focusing and opening when you load a client detail page.

**The problem:** WHMCS auto-focuses the client search field on client pages, which immediately opens a large dropdown covering the page content. Easy to accidentally click the wrong client.

**The fix:** Uses a MutationObserver and Select2 event interception to suppress auto-opening during page load while preserving normal click-to-search behavior.

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
