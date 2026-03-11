<?php

/**
 * Fix Safari autofill on WHMCS admin pages.
 *
 * Safari treats password fields on settings pages as "new password" fields,
 * showing the password generator instead of saved credentials. This hook
 * injects a small script that sets autocomplete="current-password" on those
 * fields so Safari offers the correct autofill.
 */

use WHMCS\Module\AbstractWidget;

add_hook('AdminAreaFooterOutput', 1, function ($vars) {
    return <<<HTML
<script>
(function() {
    document.querySelectorAll('input[type="password"]').forEach(function(el) {
        if (!el.getAttribute('autocomplete') || el.getAttribute('autocomplete') === 'new-password') {
            el.setAttribute('autocomplete', 'current-password');
        }
    });
})();
</script>
HTML;
});
