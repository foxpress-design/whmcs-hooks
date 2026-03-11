<?php

/**
 * Prevent Safari password autofill popup and Select2 auto-open on client pages.
 *
 * Injects into the admin <head> so it runs BEFORE Select2 initializes.
 * Uses a MutationObserver to catch and close Select2 dropdowns the instant
 * they appear during page load, while still allowing normal user interaction.
 */

add_hook('AdminAreaHeaderOutput', 1, function ($vars) {
    return <<<'HTML'
<script>
(function() {
    var pageLoad = true;

    // Mark page as settled after load + buffer for async Select2 init
    window.addEventListener('load', function() {
        setTimeout(function() { pageLoad = false; }, 800);
    });

    // Use MutationObserver to catch Select2 dropdowns the instant they appear
    var observer = new MutationObserver(function(mutations) {
        if (!pageLoad) return;
        mutations.forEach(function(m) {
            m.addedNodes.forEach(function(node) {
                if (node.nodeType !== 1) return;
                // Select2 dropdown container
                if (node.classList && node.classList.contains('select2-container') &&
                    node.classList.contains('select2-container--open')) {
                    node.style.display = 'none';
                }
                // Also check children
                var open = node.querySelectorAll && node.querySelectorAll('.select2-container--open');
                if (open) {
                    open.forEach(function(el) { el.style.display = 'none'; });
                }
            });
        });
    });
    observer.observe(document.documentElement, { childList: true, subtree: true });

    // When DOM is ready, close any Select2 that snuck through and fix attributes
    document.addEventListener('DOMContentLoaded', function() {
        function fixAll() {
            // Fix autocomplete on all inputs
            document.querySelectorAll('input[type="text"], input[type="search"], .select2-search__field').forEach(function(el) {
                el.setAttribute('autocomplete', 'off');
                el.setAttribute('data-lpignore', 'true');
                el.setAttribute('data-1p-ignore', 'true');
            });

            // Remove autofocus
            document.querySelectorAll('[autofocus]').forEach(function(el) {
                el.removeAttribute('autofocus');
            });

            // Blur any focused input
            if (document.activeElement && document.activeElement.tagName === 'INPUT') {
                document.activeElement.blur();
            }

            // Force close Select2 dropdowns during page load
            if (pageLoad && typeof jQuery !== 'undefined' && jQuery.fn.select2) {
                jQuery('select').each(function() {
                    try { jQuery(this).select2('close'); } catch(e) {}
                });
                // Unhide any we hid via the observer
                document.querySelectorAll('.select2-container').forEach(function(el) {
                    el.style.display = '';
                });
            }
        }

        fixAll();
        setTimeout(fixAll, 50);
        setTimeout(fixAll, 200);
        setTimeout(fixAll, 500);

        // After page settles, stop observing and ensure everything is visible
        setTimeout(function() {
            observer.disconnect();
            document.querySelectorAll('.select2-container').forEach(function(el) {
                el.style.display = '';
            });
        }, 2000);
    });

    // Intercept Select2 events once jQuery is available
    var jqInterval = setInterval(function() {
        if (typeof jQuery !== 'undefined') {
            clearInterval(jqInterval);
            jQuery(document).on('select2:opening', function(e) {
                if (pageLoad) {
                    e.preventDefault();
                }
            });
            jQuery(document).on('select2:open', function() {
                if (pageLoad) {
                    jQuery('select').each(function() {
                        try { jQuery(this).select2('close'); } catch(e) {}
                    });
                }
                // Fix autocomplete on search field inside dropdown
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
    }, 10);
})();
</script>
HTML;
});
