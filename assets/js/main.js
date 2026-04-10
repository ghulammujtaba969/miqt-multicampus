/**
 * Main JavaScript
 * MIQT System
 */

document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle (single handler; footer.php defines toggleSidebar() for class + localStorage)
    const toggleBtn = document.getElementById('sidebarToggle') || document.querySelector('.toggle-btn');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    if (toggleBtn && sidebar && mainContent) {
        // Restore previous state on page load
        const stored = localStorage.getItem('sidebarCollapsed');
        let wasCollapsed = stored === 'true';

        // On first load, collapse by default on small screens
        if (stored === null && window.innerWidth <= 768) {
            wasCollapsed = true;
        }

        if (wasCollapsed) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        } else {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
        }

        function syncSidebarToggleAria() {
            const collapsed = sidebar.classList.contains('collapsed');
            toggleBtn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        }
        syncSidebarToggleAria();

        function handleToggle(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            if (typeof window.toggleSidebar === 'function') {
                window.toggleSidebar();
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                localStorage.setItem('sidebarCollapsed', String(sidebar.classList.contains('collapsed')));
            }
            syncSidebarToggleAria();
        }

        toggleBtn.addEventListener('click', handleToggle);
        toggleBtn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                handleToggle(e);
            }
        });

        // Hover expand/collapse when collapsed
        sidebar.addEventListener('mouseenter', function() {
            if (sidebar.classList.contains('collapsed')) {
                sidebar.classList.add('hovering');
                mainContent.classList.add('hovering');
            }
        });

        sidebar.addEventListener('mouseleave', function() {
            sidebar.classList.remove('hovering');
            mainContent.classList.remove('hovering');
        });

        // Optional: set link titles for tooltips when collapsed
        const links = document.querySelectorAll('.sidebar-menu a');
        links.forEach(link => {
            if (!link.getAttribute('title')) {
                const text = link.textContent.replace(/\s+/g, ' ').trim();
                if (text) link.setAttribute('title', text);
            }
        });
    }

    // Dropdown menu toggle
    const dropdownLinks = document.querySelectorAll('.has-dropdown');
    dropdownLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.toggle('active');
            const submenu = this.nextElementSibling;
            if (submenu) {
                submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
            }
        });
    });

    // Highlight active link based on current URL
    const currentUrl = window.location.href;
    const sidebarLinks = document.querySelectorAll('.sidebar-menu a');

    sidebarLinks.forEach(link => {
        if (link.href === currentUrl) {
            link.classList.add('active');

            // If it's a submenu item, expand its parent dropdown
            const parentSubmenu = link.closest('.submenu');
            if (parentSubmenu) {
                parentSubmenu.style.display = 'block';
                const parentDropdown = parentSubmenu.previousElementSibling;
                if (parentDropdown) {
                    parentDropdown.classList.add('active');
                }
            }
        }
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#dc3545';
                } else {
                    field.style.borderColor = '#dee2e6';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    });

    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('.btn-delete, [data-action="delete"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });

    // Print functionality
    const printButtons = document.querySelectorAll('.btn-print');
    printButtons.forEach(button => {
        button.addEventListener('click', function() {
            window.print();
        });
    });
});
