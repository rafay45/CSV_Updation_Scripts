-- Test data for wpg0_profiles table
-- Insert sample profile combinations for testing

-- Category 18524 = Vinyl Privacy
-- Insert a few test combinations
INSERT INTO `wpg0_profiles` (`category_id`, `body_height`, `picket_size`, `rail_size`, `panel_width`, `pdf_url`) VALUES
(18524, '70', '11-3', '1-5-x-8', '8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/vinyl-privacy-70-11.3-1.5x8-8.pdf'),
(18524, '70', '6', '1-5-x-8', '8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/vinyl-privacy-70-6-1.5x8-8.pdf'),
(18524, '60', '11-3', '1-5-x-8', '8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/vinyl-privacy-60-11.3-1.5x8-8.pdf'),
(18524, '72', '11-3', '1-5-x-5-5', '8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/vinyl-privacy-72-11.3-1.5x5.5-8.pdf'),
(18524, '70', '11-3', '2-x-7', '7', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/vinyl-privacy-70-11.3-2x7-7.pdf');

-- Test: If you don't have actual PDFs, use same PDF for all combinations
-- Uncomment the lines below and comment out the above INSERT

-- INSERT INTO `wpg0_profiles` (`category_id`, `body_height`, `picket_size`, `rail_size`, `panel_width`, `pdf_url`) VALUES
-- (18524, '70', '11-3', '1-5-x-8', '8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/test.pdf'),
-- (18524, '70', '6', '1-5-x-8', '8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/test.pdf'),
-- (18524, '60', '11-3', '1-5-x-8', '8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/test.pdf'),
-- (18524, '72', '11-3', '1-5-x-5-5', '8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/test.pdf'),
-- (18524, '70', '11-3', '2-x-7', '7', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/test.pdf');
