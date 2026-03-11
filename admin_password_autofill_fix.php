<?php

/**
 * Fix Safari autofill on WHMCS admin pages.
 *
 * Safari shows "New Strong Password" when it sees a password field without a
 * username field in the same form. This hook injects a hidden username field
 * inside each form that contains a password field, so Safari treats it as a
 * login form and offers saved credentials instead.
 */

add_hook('AdminAreaHeaderOutput', 1, function ($vars) {
    return <<<'HTML'
<script>
(function() {
    function fixPasswordForms() {
        document.querySelectorAll('input[type="password"]').forEach(function(pwField) {
            var form = pwField.closest('form');
            var container = form || pwField.parentNode;

            // Skip if we already injected a decoy username field
            if (container.querySelector('.safari-pw-fix')) return;

            // Inject a hidden username field before the password field
            var decoy = document.createElement('input');
            decoy.type = 'text';
            decoy.name = '';
            decoy.className = 'safari-pw-fix';
            decoy.setAttribute('autocomplete', 'username');
            decoy.setAttribute('tabindex', '-1');
            decoy.setAttribute('aria-hidden', 'true');
            decoy.style.cssText = 'position:absolute;left:-9999px;width:0;height:0;opacity:0;pointer-events:none;';
            pwField.parentNode.insertBefore(decoy, pwField);

            // Use an unrecognized value so Safari stops trying to classify it
            pwField.setAttribute('autocomplete', 'off');
            pwField.setAttribute('data-com-onepassword-ignore', 'true');
            pwField.setAttribute('data-1p-ignore', 'true');
            pwField.setAttribute('data-lpignore', 'true');
            // Readonly trick: Safari won't offer suggestions on readonly fields
            // Remove readonly on focus so the user can still type
            if (!pwField.dataset.safariFixed) {
                pwField.dataset.safariFixed = '1';
                pwField.setAttribute('readonly', '');
                pwField.addEventListener('click', function() { this.removeAttribute('readonly'); });
                pwField.addEventListener('focus', function() {
                    var self = this;
                    setTimeout(function() { self.removeAttribute('readonly'); }, 50);
                });
            }
        });
    }

    // Catch password fields as they appear
    var observer = new MutationObserver(function() { fixPasswordForms(); });
    observer.observe(document.documentElement, { childList: true, subtree: true });

    // Also run on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function() {
        fixPasswordForms();
        setTimeout(fixPasswordForms, 100);
        setTimeout(fixPasswordForms, 500);
        setTimeout(function() { observer.disconnect(); }, 3000);
    });
})();
</script>
HTML;
});
