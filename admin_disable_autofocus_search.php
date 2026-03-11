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
    var pageLoad = true;

    function fixFields() {
        // Tell Safari search/text inputs are not login fields
        var inputs = document.querySelectorAll(
            'input[type="text"], input[type="search"], .select2-search__field'
        );
        inputs.forEach(function(el) {
            el.setAttribute('autocomplete', 'off');
            el.setAttribute('data-lpignore', 'true');
            el.setAttribute('data-1p-ignore', 'true');
        });

        // Remove autofocus from any elements
        document.querySelectorAll('[autofocus]').forEach(function(el) {
            el.removeAttribute('autofocus');
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

    // Block Select2 from auto-opening on page load by intercepting the open event
    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('select2:opening', function(e) {
            if (pageLoad) {
                e.preventDefault();
            }
        });

        jQuery(document).on('select2:open', function() {
            if (pageLoad) {
                jQuery('.select2-container--open').prev('select').select2('close');
            }
            // Always fix autocomplete on opened dropdowns
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

    // Run fixes immediately and on delays to catch async init
    fixFields();
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fixFields);
    }
    setTimeout(fixFields, 50);
    setTimeout(fixFields, 150);
    setTimeout(fixFields, 500);

    // Allow normal Select2 interaction after page settles
    setTimeout(function() { pageLoad = false; }, 1500);
})();
</script>
HTML;
});
