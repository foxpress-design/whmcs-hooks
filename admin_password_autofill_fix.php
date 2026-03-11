<?php

/**
 * Fix Safari autofill on WHMCS admin pages.
 *
 * Safari treats password fields on settings pages as "new password" fields,
 * showing the password generator instead of saved credentials. This hook
 * injects early in the <head> to mark password fields before Safari classifies
 * them, and adds decoy fields to prevent Safari's heuristic detection.
 */

add_hook('AdminAreaHeaderOutput', 1, function ($vars) {
    return <<<'HTML'
<!-- Decoy fields to absorb Safari's autofill heuristics -->
<div style="position:absolute;left:-9999px;opacity:0;height:0;overflow:hidden;" aria-hidden="true">
    <input type="text" name="safari_decoy_user" tabindex="-1" autocomplete="username">
    <input type="password" name="safari_decoy_pass" tabindex="-1" autocomplete="new-password">
</div>
<script>
(function() {
    // Fix password fields as soon as they appear in the DOM
    var pwObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(m) {
            m.addedNodes.forEach(function(node) {
                if (node.nodeType !== 1) return;
                if (node.tagName === 'INPUT' && node.type === 'password') {
                    node.setAttribute('autocomplete', 'current-password');
                }
                var pwFields = node.querySelectorAll && node.querySelectorAll('input[type="password"]');
                if (pwFields) {
                    pwFields.forEach(function(el) {
                        el.setAttribute('autocomplete', 'current-password');
                    });
                }
            });
        });
    });
    pwObserver.observe(document.documentElement, { childList: true, subtree: true });

    // Also fix on DOMContentLoaded as a safety net
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[type="password"]').forEach(function(el) {
            el.setAttribute('autocomplete', 'current-password');
        });
        // Stop observing after page is loaded
        setTimeout(function() { pwObserver.disconnect(); }, 3000);
    });
})();
</script>
HTML;
});
