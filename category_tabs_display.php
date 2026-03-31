// ==========================================
// Category Page - Tabs Display (Overview, Videos, Reviews, etc.)
// ==========================================

// Display tabs on category pages
if (!function_exists('wf_display_category_tabs')) {
    add_action('woocommerce_archive_description', 'wf_display_category_tabs', 15);

    function wf_display_category_tabs() {
        if (!is_product_category()) {
            return;
        }

        $term = get_queried_object();
        $term_id = $term->term_id;

        // Get data
        $videos = wf_get_category_videos($term_id);
        $review_link = wf_get_category_review_link($term_id);
        $description = term_description($term_id, 'product_cat');

        ?>
        <div class="wf-category-tabs" style="margin: 30px 0;">
            <!-- Tab Navigation -->
            <div class="tab-navigation" style="border-bottom: 2px solid #e0e0e0; margin-bottom: 20px;">
                <button class="tab-btn active" data-tab="overview" style="padding: 12px 24px; border: none; background: #2c5f2d; color: white; cursor: pointer; margin-right: 5px; font-size: 14px; font-weight: 600;">
                    Overview
                </button>
                <?php if (!empty($videos)): ?>
                <button class="tab-btn" data-tab="videos" style="padding: 12px 24px; border: none; background: #f5f5f5; color: #333; cursor: pointer; margin-right: 5px; font-size: 14px; font-weight: 600;">
                    Videos
                </button>
                <?php endif; ?>
                <?php if (!empty($review_link)): ?>
                <button class="tab-btn" data-tab="reviews" style="padding: 12px 24px; border: none; background: #f5f5f5; color: #333; cursor: pointer; margin-right: 5px; font-size: 14px; font-weight: 600;">
                    Reviews
                </button>
                <?php endif; ?>
            </div>

            <!-- Tab Content -->
            <div class="tab-content-wrapper">
                <!-- Overview Tab -->
                <div class="tab-content active" id="tab-overview" style="display: block;">
                    <?php if (!empty($description)): ?>
                        <?php echo $description; ?>
                    <?php else: ?>
                        <p>No overview available.</p>
                    <?php endif; ?>
                </div>

                <!-- Videos Tab -->
                <?php if (!empty($videos)): ?>
                <div class="tab-content" id="tab-videos" style="display: none;">
                    <div class="category-videos" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                        <?php foreach ($videos as $video_id): ?>
                            <div class="video-wrapper" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <iframe
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                                    src="https://www.youtube.com/embed/<?php echo esc_attr($video_id); ?>"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Reviews Tab -->
                <?php if (!empty($review_link)): ?>
                <div class="tab-content" id="tab-reviews" style="display: none;">
                    <div class="category-reviews" style="padding: 30px; background: #f9f9f9; border-left: 4px solid #4285f4; border-radius: 4px;">
                        <h3 style="margin-top: 0; color: #333;">Customer Reviews</h3>
                        <p style="font-size: 15px; line-height: 1.6; color: #666;">See what our customers are saying about our products and services.</p>
                        <a href="<?php echo esc_url($review_link); ?>" target="_blank" rel="noopener" style="display: inline-block; background: #4285f4; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: 600; margin-top: 15px;">
                            View Google Reviews →
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- JavaScript for Tab Switching -->
            <script>
            (function() {
                const tabButtons = document.querySelectorAll('.wf-category-tabs .tab-btn');
                const tabContents = document.querySelectorAll('.wf-category-tabs .tab-content');

                tabButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const targetTab = this.getAttribute('data-tab');

                        // Remove active class from all buttons and contents
                        tabButtons.forEach(btn => {
                            btn.style.background = '#f5f5f5';
                            btn.style.color = '#333';
                            btn.classList.remove('active');
                        });
                        tabContents.forEach(content => {
                            content.style.display = 'none';
                            content.classList.remove('active');
                        });

                        // Add active class to clicked button and corresponding content
                        this.style.background = '#2c5f2d';
                        this.style.color = 'white';
                        this.classList.add('active');

                        const targetContent = document.getElementById('tab-' + targetTab);
                        if (targetContent) {
                            targetContent.style.display = 'block';
                            targetContent.classList.add('active');
                        }
                    });
                });
            })();
            </script>
        </div>
        <?php
    }
}
