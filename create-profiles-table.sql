-- SQL to create vinyl profiles table in staging database
-- Run this in phpMyAdmin SQL tab

CREATE TABLE IF NOT EXISTS `wpg0_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL COMMENT 'WooCommerce category ID (18523, 18524, etc)',
  `body_height` varchar(20) DEFAULT NULL COMMENT 'Body height like 48, 60, 72',
  `picket_size` varchar(20) DEFAULT NULL COMMENT 'Picket size like 6, 11-3',
  `rail_size` varchar(50) DEFAULT NULL COMMENT 'Rail size like 1-5-x-5-5, 1-5-x-8',
  `panel_width` varchar(20) DEFAULT NULL COMMENT 'Panel width like 6, 7, 8',
  `lattice_top_rail_size` varchar(50) DEFAULT NULL COMMENT 'Lattice top rail size',
  `gap` varchar(20) DEFAULT NULL COMMENT 'Gap for horizontal aluminum',
  `pdf_url` varchar(500) NOT NULL COMMENT 'Full URL to PDF file',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `body_height` (`body_height`),
  KEY `panel_width` (`panel_width`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data examples (you can modify these after running the CREATE TABLE)
-- Uncomment and modify these INSERT statements as needed:

/*
-- Vinyl Pool Fence (Category 18527)
INSERT INTO `wpg0_profiles` (`category_id`, `body_height`, `picket_size`, `rail_size`, `panel_width`, `lattice_top_rail_size`, `gap`, `pdf_url`) VALUES
(18527, '48', NULL, NULL, '6', NULL, NULL, 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/pool-48-6.pdf'),
(18527, '48', NULL, NULL, '8', NULL, NULL, 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/pool-48-8.pdf'),
(18527, '60', NULL, NULL, '6', NULL, NULL, 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/pool-60-6.pdf'),
(18527, '60', NULL, NULL, '8', NULL, NULL, 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/pool-60-8.pdf');

-- Vinyl Picket (Category 18526)
INSERT INTO `wpg0_profiles` (`category_id`, `body_height`, `picket_size`, `rail_size`, `panel_width`, `lattice_top_rail_size`, `gap`, `pdf_url`) VALUES
(18526, '3', NULL, NULL, '6', NULL, NULL, 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/picket-3-6.pdf'),
(18526, '3', NULL, NULL, '8', NULL, NULL, 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/picket-3-8.pdf'),
(18526, '42', NULL, NULL, '6', NULL, NULL, 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/picket-42-6.pdf'),
(18526, '42', NULL, NULL, '8', NULL, NULL, 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/picket-42-8.pdf'),
(18526, '48', NULL, NULL, '6', NULL, NULL, 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/picket-48-6.pdf'),
(18526, '48', NULL, NULL, '8', NULL, NULL, 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/picket-48-8.pdf');

-- Vinyl Privacy (Category 18524) - Add more combinations as needed
INSERT INTO `wpg0_profiles` (`category_id`, `body_height`, `picket_size`, `rail_size`, `panel_width`, `lattice_top_rail_size`, `gap`, `pdf_url`) VALUES
(18524, '48', '6', '1-5-x-5-5', '6', NULL, NULL, 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/privacy-48-6-1.5x5.5-6.pdf'),
(18524, '48', '6', '1-5-x-5-5', '8', NULL, NULL, 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/privacy-48-6-1.5x5.5-8.pdf');
-- Add more privacy combinations...
*/
