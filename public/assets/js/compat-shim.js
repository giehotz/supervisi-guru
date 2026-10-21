/**
 * Bootstrap 4 to Bootstrap 5 Attribute Bridge
 * Otomatis memetakan data-toggle, data-target, data-dismiss ke versi BS5 (data-bs-*)
 * Menjamin seluruh Modal, Dropdown, dan Tooltip di 117 view bekerja tanpa edit manual.
 */
(function() {
    function bridgeAttributes(root) {
        var context = root || document;
        // Bridge data-toggle -> data-bs-toggle
        context.querySelectorAll('[data-toggle]').forEach(function(el) {
            if (!el.hasAttribute('data-bs-toggle')) {
                el.setAttribute('data-bs-toggle', el.getAttribute('data-toggle'));
            }
        });
        // Bridge data-target -> data-bs-target
        context.querySelectorAll('[data-target]').forEach(function(el) {
            if (!el.hasAttribute('data-bs-target')) {
                el.setAttribute('data-bs-target', el.getAttribute('data-target'));
            }
        });
        // Bridge data-dismiss -> data-bs-dismiss
        context.querySelectorAll('[data-dismiss]').forEach(function(el) {
            if (!el.hasAttribute('data-bs-dismiss')) {
                el.setAttribute('data-bs-dismiss', el.getAttribute('data-dismiss'));
            }
        });
    }

    function initSidebarToggle() {
        if (document.documentElement.classList.contains('sidebar-collapsed')) {
            document.body.classList.add('sidebar-collapsed');
        }

        var toggleButtons = document.querySelectorAll('#sidebarToggle, #sidebarToggleTop');
        toggleButtons.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var isCollapsed = document.body.classList.toggle('sidebar-collapsed');
                if (isCollapsed) {
                    document.documentElement.classList.add('sidebar-collapsed');
                } else {
                    document.documentElement.classList.remove('sidebar-collapsed');
                }
                try {
                    localStorage.setItem('sidebar_collapsed', isCollapsed ? 'true' : 'false');
                } catch (err) {}
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            bridgeAttributes();
            initSidebarToggle();
        });
    } else {
        bridgeAttributes();
        initSidebarToggle();
    }
})();
