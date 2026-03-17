<?php
/**
 * Category Description Show More/Less (Universal Version)
 *
 * Add this code to your WordPress theme's functions.php file
 * OR save as a plugin and activate it
 *
 * Plugin Name: Category Description Show More/Less
 * Description: Adds show more/less button to category descriptions (shows 16 lines by default)
 * Version: 1.1
 */

// Add CSS for show more/less functionality
add_action('wp_head', 'cdesc_show_more_styles', 999);
function cdesc_show_more_styles() {
    ?>
    <style>
    /* Category description show more/less - Universal */
    .wsf-cat-intro-text,
    .cdesc-content-wrapper {
        position: relative;
    }
    .cdesc-content {
        overflow: hidden;
        transition: max-height 0.3s ease;
        position: relative;
    }
    .cdesc-content.collapsed {
        max-height: 27em !important; /* ~16 lines with line-height: 1.7 */
    }
    .cdesc-content.collapsed::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 80px;
        background: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,1));
        pointer-events: none;
        z-index: 1;
    }
    .cdesc-show-more-btn {
        color: #327A1F;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease;
        display: none;
        position: relative;
        z-index: 2;
    }
    .cdesc-show-more-btn.visible {
        display: inline-block;
    }
    </style>
    <?php
}

// Add JavaScript for show more/less functionality
add_action('wp_footer', 'cdesc_show_more_script', 999);
function cdesc_show_more_script() {
    ?>
    <script>
    (function() {
        'use strict';

        function initDescriptionToggle() {
            // Find all possible description containers
            var possibleSelectors = [
                '.wsf-cat-intro-text',
                '.woocommerce-taxonomy-archive-description',
                '.term-description',
                '.archive-description',
                '.category-description',
                '.taxonomy-description',
                'div[class*="cat-desc"]',
                'div[class*="category-desc"]'
            ];

            possibleSelectors.forEach(function(selector) {
                var containers = document.querySelectorAll(selector);

                containers.forEach(function(container) {
                    // Skip if already processed
                    if (container.hasAttribute('data-cdesc-processed')) return;
                    if (container.querySelector('.cdesc-content-wrapper')) return;

                    // Get original content
                    var originalContent = container.innerHTML.trim();
                    if (!originalContent) return;

                    // Mark as processed
                    container.setAttribute('data-cdesc-processed', 'true');

                    // Create wrapper
                    var wrapper = document.createElement('div');
                    wrapper.className = 'cdesc-content-wrapper';

                    var contentDiv = document.createElement('div');
                    contentDiv.className = 'cdesc-content';
                    contentDiv.innerHTML = originalContent;

                    var button = document.createElement('button');
                    button.className = 'cdesc-show-more-btn';
                    button.textContent = 'Show More';
                    button.type = 'button';

                    // Clear and rebuild
                    container.innerHTML = '';
                    wrapper.appendChild(contentDiv);
                    wrapper.appendChild(button);
                    container.appendChild(wrapper);

                    // Check height after render
                    setTimeout(function() {
                        var computedStyle = window.getComputedStyle(contentDiv);
                        var lineHeight = parseFloat(computedStyle.lineHeight);

                        // If lineHeight is not set or is 'normal', estimate it
                        if (isNaN(lineHeight) || lineHeight === 0) {
                            var fontSize = parseFloat(computedStyle.fontSize);
                            lineHeight = fontSize * 1.7; // Estimate line-height
                        }

                        var maxHeight = lineHeight * 16; // 16 lines

                        if (contentDiv.scrollHeight > maxHeight + 10) {
                            contentDiv.classList.add('collapsed');
                            button.classList.add('visible');

                            // Add click handler
                            button.addEventListener('click', function() {
                                if (contentDiv.classList.contains('collapsed')) {
                                    // Expand
                                    contentDiv.classList.remove('collapsed');
                                    contentDiv.style.maxHeight = 'none';
                                    button.textContent = 'Show Less';
                                } else {
                                    // Collapse
                                    contentDiv.classList.add('collapsed');
                                    contentDiv.style.maxHeight = '';
                                    button.textContent = 'Show More';

                                    // Scroll to top smoothly
                                    setTimeout(function() {
                                        container.scrollIntoView({
                                            behavior: 'smooth',
                                            block: 'start',
                                            inline: 'nearest'
                                        });
                                    }, 50);
                                }
                            });
                        }
                    }, 200);
                });
            });
        }

        // Run on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initDescriptionToggle);
        } else {
            initDescriptionToggle();
        }

        // Re-run after AJAX updates
        if (typeof jQuery !== 'undefined') {
            jQuery(document).ajaxComplete(function() {
                setTimeout(initDescriptionToggle, 300);
            });
        }

        // MutationObserver for dynamic content
        var observer = new MutationObserver(function(mutations) {
            var shouldReinit = false;
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    shouldReinit = true;
                }
            });
            if (shouldReinit) {
                setTimeout(initDescriptionToggle, 100);
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

    })();
    </script>
    <?php
}
?>
