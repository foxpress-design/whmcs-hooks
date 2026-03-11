<?php

/**
 * Prevent Safari password autofill popup and auto-focus on client search fields.
 *
 * Safari sees the client search input on client detail pages and offers password
 * autofill (Keychain credentials), covering the page with an unwanted popup.
 * This hook marks search fields so Safari ignores them, removes autofocus, and
 * blurs any auto-focused fields on page load.
 */

add_hook('AdminAreaFooterOutput', 1, function ($vars) {
    return <<<'HTML'
<script>
(function() {
    function fixFields() {
        // Target all text/search inputs that Safari might mistake for login fields
        var inputs = document.querySelectorAll(
            'input[type="text"], input[type="search"], .select2-search__field'
        );
        inputs.forEach(function(el) {
            // Tell Safari this is not a login/password field
            el.setAttribute('autocomplete', 'off');
            el.setAttribute('data-lpignore', 'true');
            el.setAttribute('data-1p-ignore', 'true');
        });

        // Remove autofocus from any elements
        document.querySelectorAll('[autofocus]').forEach(function(el) {
            el.removeAttribute('autofocus');
            el.blur();
        });

        // Blur any currently focused input
        if (document.activeElement && document.activeElement.tagName === 'INPUT') {
            document.activeElement.blur();
        }

        // Close any open Select2 dropdowns
        if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
            jQuery('.select2-container--open').prev('select').select2('close');
        }
    }

    // Run immediately
    fixFields();

    // Run after DOM ready and after Select2 async init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fixFields);
    }
    setTimeout(fixFields, 150);
    setTimeout(fixFields, 500);

    // Intercept Select2 open events to fix the search field inside dropdowns
    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('select2:open', function() {
            setTimeout(function() {
                var field = document.querySelector('.select2-container--open .select2-search__field');
                if (field) {
                    field.setAttribute('autocomplete', 'off');
                    field.setAttribute('data-lpignore', 'true');
                    field.setAttribute('data-1p-ignore', 'true');
                }
            }, 10);
        });
    }
})();
</script>
HTML;
});
