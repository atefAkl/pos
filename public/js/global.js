/**
 * Global JavaScript for POS Application
 * This file contains global functions and initializations
 */

// Initialize tooltips globally
document.addEventListener('DOMContentLoaded', function() {
    initializeTooltips();
    
    // Re-initialize tooltips when content is loaded via AJAX
    document.addEventListener('ajaxComplete', function() {
        initializeTooltips();
    });
});

/**
 * Initialize Bootstrap tooltips
 */
function initializeTooltips() {
    // Initialize tooltips using Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        // Check if tooltip is already initialized
        if (!tooltipTriggerEl._tooltip) {
            new bootstrap.Tooltip(tooltipTriggerEl, {
                trigger: 'hover',
                placement: 'top',
                html: true,
                container: 'body',
                boundary: 'window',
                customClass: 'global-tooltip',
                delay: { show: 300, hide: 100 }
            });
        }
    });
}

/**
 * Show a dynamic tooltip
 * @param {HTMLElement} element - The element to show tooltip on
 * @param {string} content - The tooltip content (can include HTML)
 * @param {string} placement - Tooltip placement (top, bottom, left, right)
 */
function showTooltip(element, content, placement = 'top') {
    // Remove any existing tooltip
    if (element._tooltip) {
        element._tooltip.dispose();
    }
    
    // Create and show new tooltip
    element.setAttribute('data-bs-original-title', content);
    element._tooltip = new bootstrap.Tooltip(element, {
        title: content,
        placement: placement,
        html: true,
        trigger: 'manual'
    });
    
    element._tooltip.show();
    
    // Hide tooltip when clicking outside
    const hideTooltip = function(e) {
        if (!element.contains(e.target)) {
            element._tooltip.hide();
            document.removeEventListener('click', hideTooltip);
        }
    };
    
    setTimeout(() => {
        document.addEventListener('click', hideTooltip);
    }, 0);
    
    return element._tooltip;
}

// Make functions available globally
window.App = window.App || {};
window.App.Tooltips = {
    init: initializeTooltips,
    show: showTooltip
};
