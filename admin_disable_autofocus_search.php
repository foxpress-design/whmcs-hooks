<?php

/**
 * Prevent the client search dropdown from auto-focusing on client detail pages.
 *
 * WHMCS auto-focuses the Select2 client search field when loading client pages,
 * which opens a large dropdown that is easy to accidentally click on. This hook
 * blurs the field on page load so the dropdown stays closed until intentionally
 * clicked.
 */

add_hook('AdminAreaFooterOutput', 1, function ($vars) {
    return <<<'HTML'
<script>
(function() {
    // Close any Select2 dropdowns that auto-opened and blur focused search inputs
    function dismissAutoFocus() {
        // Blur any auto-focused Select2 search fields
        var focused = document.querySelector('.select2-container--open .select2-search__field');
        if (focused) {
            focused.blur();
        }
        // Close any open Select2 dropdowns
        var openDropdowns = document.querySelectorAll('.select2-container--open');
        openDropdowns.forEach(function(el) {
            var select = el.previousElementSibling;
            if (select && typeof jQuery !== 'undefined') {
                jQuery(select).select2('close');
            }
        });
        // Remove autofocus from any inputs on the page
        document.querySelectorAll('[autofocus]').forEach(function(el) {
            el.removeAttribute('autofocus');
            el.blur();
        });
    }

    // Run immediately and after a short delay (Select2 may initialize async)
    dismissAutoFocus();
    setTimeout(dismissAutoFocus, 100);
    setTimeout(dismissAutoFocus, 300);
})();
</script>
HTML;
});
