-- SQL to insert Horizontal Aluminum profiles into wpg0_profiles table
-- Category ID: 3606 (Horizontal Aluminum)
--
-- Structure:
-- - Semi-Privacy: GAP (1/2, 1, 1 3/8) + WIDTH (6', 8') + BODY (varies by GAP)
-- - Privacy: WIDTH (6', 8') + BODY (21 values)
-- - Mixed: RAIL (3 Rail, 4 Rail) + WIDTH (6', 8') + BODY (21 values for 3-rail, 22 for 4-rail)
--
-- Note: Replace PDF URLs with actual file paths after uploading

-- ==============================================================================
-- SEMI-PRIVACY SECTION
-- ==============================================================================

-- GAP = 1/2" with WIDTH = 6' (22 BODY values)
INSERT INTO `wpg0_profiles` (`category_id`, `gap`, `panel_width`, `body_height`, `pdf_url`) VALUES
(3606, '1-2', '6', '35-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-35.5.pdf'),
(3606, '1-2', '6', '39-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-39.5.pdf'),
(3606, '1-2', '6', '43-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-43.5.pdf'),
(3606, '1-2', '6', '47-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-47.5.pdf'),
(3606, '1-2', '6', '51-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-51.5.pdf'),
(3606, '1-2', '6', '55-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-55.5.pdf'),
(3606, '1-2', '6', '59-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-59.5.pdf'),
(3606, '1-2', '6', '63-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-63.5.pdf'),
(3606, '1-2', '6', '67-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-67.5.pdf'),
(3606, '1-2', '6', '71-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-71.5.pdf'),
(3606, '1-2', '6', '75-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-75.5.pdf'),
(3606, '1-2', '6', '79-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-79.5.pdf'),
(3606, '1-2', '6', '83-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-83.5.pdf'),
(3606, '1-2', '6', '87-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-87.5.pdf'),
(3606, '1-2', '6', '91-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-91.5.pdf'),
(3606, '1-2', '6', '95-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-95.5.pdf'),
(3606, '1-2', '6', '99-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-99.5.pdf'),
(3606, '1-2', '6', '103-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-103.5.pdf'),
(3606, '1-2', '6', '107-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-107.5.pdf'),
(3606, '1-2', '6', '111-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-111.5.pdf'),
(3606, '1-2', '6', '115-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-115.5.pdf'),
(3606, '1-2', '6', '119-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-6-body-119.5.pdf');

-- GAP = 1/2" with WIDTH = 8' (22 BODY values)
INSERT INTO `wpg0_profiles` (`category_id`, `gap`, `panel_width`, `body_height`, `pdf_url`) VALUES
(3606, '1-2', '8', '35-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-35.5.pdf'),
(3606, '1-2', '8', '39-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-39.5.pdf'),
(3606, '1-2', '8', '43-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-43.5.pdf'),
(3606, '1-2', '8', '47-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-47.5.pdf'),
(3606, '1-2', '8', '51-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-51.5.pdf'),
(3606, '1-2', '8', '55-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-55.5.pdf'),
(3606, '1-2', '8', '59-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-59.5.pdf'),
(3606, '1-2', '8', '63-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-63.5.pdf'),
(3606, '1-2', '8', '67-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-67.5.pdf'),
(3606, '1-2', '8', '71-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-71.5.pdf'),
(3606, '1-2', '8', '75-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-75.5.pdf'),
(3606, '1-2', '8', '79-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-79.5.pdf'),
(3606, '1-2', '8', '83-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-83.5.pdf'),
(3606, '1-2', '8', '87-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-87.5.pdf'),
(3606, '1-2', '8', '91-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-91.5.pdf'),
(3606, '1-2', '8', '95-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-95.5.pdf'),
(3606, '1-2', '8', '99-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-99.5.pdf'),
(3606, '1-2', '8', '103-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-103.5.pdf'),
(3606, '1-2', '8', '107-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-107.5.pdf'),
(3606, '1-2', '8', '111-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-111.5.pdf'),
(3606, '1-2', '8', '115-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-115.5.pdf'),
(3606, '1-2', '8', '119-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-0.5-width-8-body-119.5.pdf');

-- GAP = 1" with WIDTH = 6' (20 BODY values)
INSERT INTO `wpg0_profiles` (`category_id`, `gap`, `panel_width`, `body_height`, `pdf_url`) VALUES
(3606, '1', '6', '35', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-35.pdf'),
(3606, '1', '6', '39-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-39.5.pdf'),
(3606, '1', '6', '44', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-44.pdf'),
(3606, '1', '6', '48-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-48.5.pdf'),
(3606, '1', '6', '53', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-53.pdf'),
(3606, '1', '6', '57-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-57.5.pdf'),
(3606, '1', '6', '62', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-62.pdf'),
(3606, '1', '6', '66-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-66.5.pdf'),
(3606, '1', '6', '71', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-71.pdf'),
(3606, '1', '6', '75-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-75.5.pdf'),
(3606, '1', '6', '80', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-80.pdf'),
(3606, '1', '6', '80-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-80.5.pdf'),
(3606, '1', '6', '84-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-84.5.pdf'),
(3606, '1', '6', '89', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-89.pdf'),
(3606, '1', '6', '93-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-93.5.pdf'),
(3606, '1', '6', '98', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-98.pdf'),
(3606, '1', '6', '102-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-102.5.pdf'),
(3606, '1', '6', '107', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-107.pdf'),
(3606, '1', '6', '111-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-111.5.pdf'),
(3606, '1', '6', '116', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-6-body-116.pdf');

-- GAP = 1" with WIDTH = 8' (20 BODY values)
INSERT INTO `wpg0_profiles` (`category_id`, `gap`, `panel_width`, `body_height`, `pdf_url`) VALUES
(3606, '1', '8', '35', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-35.pdf'),
(3606, '1', '8', '39-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-39.5.pdf'),
(3606, '1', '8', '44', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-44.pdf'),
(3606, '1', '8', '48-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-48.5.pdf'),
(3606, '1', '8', '53', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-53.pdf'),
(3606, '1', '8', '57-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-57.5.pdf'),
(3606, '1', '8', '62', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-62.pdf'),
(3606, '1', '8', '66-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-66.5.pdf'),
(3606, '1', '8', '71', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-71.pdf'),
(3606, '1', '8', '75-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-75.5.pdf'),
(3606, '1', '8', '80', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-80.pdf'),
(3606, '1', '8', '80-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-80.5.pdf'),
(3606, '1', '8', '84-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-84.5.pdf'),
(3606, '1', '8', '89', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-89.pdf'),
(3606, '1', '8', '93-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-93.5.pdf'),
(3606, '1', '8', '98', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-98.pdf'),
(3606, '1', '8', '102-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-102.5.pdf'),
(3606, '1', '8', '107', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-107.pdf'),
(3606, '1', '8', '111-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-111.5.pdf'),
(3606, '1', '8', '116', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1-width-8-body-116.pdf');

-- GAP = 1 3/8" with WIDTH = 6' (17 BODY values)
INSERT INTO `wpg0_profiles` (`category_id`, `gap`, `panel_width`, `body_height`, `pdf_url`) VALUES
(3606, '1-3-8', '6', '37-5-8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-6-body-37.625.pdf'),
(3606, '1-3-8', '6', '42-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-6-body-42.5.pdf'),
(3606, '1-3-8', '6', '47-3-8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-6-body-47.375.pdf'),
(3606, '1-3-8', '6', '52-1-4', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-6-body-52.25.pdf'),
(3606, '1-3-8', '6', '57-1-8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-6-body-57.125.pdf'),
(3606, '1-3-8', '6', '62', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-6-body-62.pdf'),
(3606, '1-3-8', '6', '66-7-8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-6-body-66.875.pdf'),
(3606, '1-3-8', '6', '71-3-4', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-6-body-71.75.pdf'),
(3606, '1-3-8', '6', '76-5-8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-6-body-76.625.pdf'),
(3606, '1-3-8', '6', '81-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-6-body-81.5.pdf'),
(3606, '1-3-8', '6', '86-3-8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-6-body-86.375.pdf'),
(3606, '1-3-8', '6', '91-1-4', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-6-body-91.25.pdf'),
(3606, '1-3-8', '6', '96-1-8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-6-body-96.125.pdf'),
(3606, '1-3-8', '6', '101', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-6-body-101.pdf'),
(3606, '1-3-8', '6', '105-7-8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-6-body-105.875.pdf'),
(3606, '1-3-8', '6', '110-3-4', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-6-body-110.75.pdf'),
(3606, '1-3-8', '6', '115-5-8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-6-body-115.625.pdf');

-- GAP = 1 3/8" with WIDTH = 8' (17 BODY values)
INSERT INTO `wpg0_profiles` (`category_id`, `gap`, `panel_width`, `body_height`, `pdf_url`) VALUES
(3606, '1-3-8', '8', '37-5-8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-8-body-37.625.pdf'),
(3606, '1-3-8', '8', '42-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-8-body-42.5.pdf'),
(3606, '1-3-8', '8', '47-3-8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-8-body-47.375.pdf'),
(3606, '1-3-8', '8', '52-1-4', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-8-body-52.25.pdf'),
(3606, '1-3-8', '8', '57-1-8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-8-body-57.125.pdf'),
(3606, '1-3-8', '8', '62', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-8-body-62.pdf'),
(3606, '1-3-8', '8', '66-7-8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-8-body-66.875.pdf'),
(3606, '1-3-8', '8', '71-3-4', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-8-body-71.75.pdf'),
(3606, '1-3-8', '8', '76-5-8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-8-body-76.625.pdf'),
(3606, '1-3-8', '8', '81-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-8-body-81.5.pdf'),
(3606, '1-3-8', '8', '86-3-8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-8-body-86.375.pdf'),
(3606, '1-3-8', '8', '91-1-4', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-8-body-91.25.pdf'),
(3606, '1-3-8', '8', '96-1-8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-8-body-96.125.pdf'),
(3606, '1-3-8', '8', '101', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-8-body-101.pdf'),
(3606, '1-3-8', '8', '105-7-8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-8-body-105.875.pdf'),
(3606, '1-3-8', '8', '110-3-4', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-8-body-110.75.pdf'),
(3606, '1-3-8', '8', '115-5-8', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-sp-gap-1.375-width-8-body-115.625.pdf');

-- ==============================================================================
-- PRIVACY SECTION (No GAP, just WIDTH + BODY)
-- ==============================================================================

-- WIDTH = 6' with 21 BODY values
INSERT INTO `wpg0_profiles` (`category_id`, `panel_width`, `body_height`, `pdf_url`) VALUES
(3606, '6', '35', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-35.pdf'),
(3606, '6', '38-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-38.5.pdf'),
(3606, '6', '42', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-42.pdf'),
(3606, '6', '45-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-45.5.pdf'),
(3606, '6', '49', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-49.pdf'),
(3606, '6', '52-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-52.5.pdf'),
(3606, '6', '56', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-56.pdf'),
(3606, '6', '59-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-59.5.pdf'),
(3606, '6', '63', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-63.pdf'),
(3606, '6', '66-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-66.5.pdf'),
(3606, '6', '70', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-70.pdf'),
(3606, '6', '73-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-73.5.pdf'),
(3606, '6', '77', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-77.pdf'),
(3606, '6', '80-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-80.5.pdf'),
(3606, '6', '84', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-84.pdf'),
(3606, '6', '87-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-87.5.pdf'),
(3606, '6', '91', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-91.pdf'),
(3606, '6', '94-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-94.5.pdf'),
(3606, '6', '98', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-98.pdf'),
(3606, '6', '101-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-101.5.pdf'),
(3606, '6', '105', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-6-body-105.pdf');

-- WIDTH = 8' with 21 BODY values
INSERT INTO `wpg0_profiles` (`category_id`, `panel_width`, `body_height`, `pdf_url`) VALUES
(3606, '8', '35', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-35.pdf'),
(3606, '8', '38-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-38.5.pdf'),
(3606, '8', '42', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-42.pdf'),
(3606, '8', '45-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-45.5.pdf'),
(3606, '8', '49', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-49.pdf'),
(3606, '8', '52-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-52.5.pdf'),
(3606, '8', '56', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-56.pdf'),
(3606, '8', '59-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-59.5.pdf'),
(3606, '8', '63', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-63.pdf'),
(3606, '8', '66-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-66.5.pdf'),
(3606, '8', '70', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-70.pdf'),
(3606, '8', '73-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-73.5.pdf'),
(3606, '8', '77', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-77.pdf'),
(3606, '8', '80-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-80.5.pdf'),
(3606, '8', '84', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-84.pdf'),
(3606, '8', '87-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-87.5.pdf'),
(3606, '8', '91', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-91.pdf'),
(3606, '8', '94-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-94.5.pdf'),
(3606, '8', '98', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-98.pdf'),
(3606, '8', '101-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-101.5.pdf'),
(3606, '8', '105', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-pr-width-8-body-105.pdf');

-- ==============================================================================
-- MIXED SECTION (RAIL + WIDTH + BODY)
-- Note: Need to add rail_type column or use gap column for rail values
-- For now using gap column to store rail type (3-rail, 4-rail)
-- ==============================================================================

-- RAIL = 3-rail with WIDTH = 6' (21 BODY values)
INSERT INTO `wpg0_profiles` (`category_id`, `gap`, `panel_width`, `body_height`, `pdf_url`) VALUES
(3606, '3-rail', '6', '37', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-37.pdf'),
(3606, '3-rail', '6', '40-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-40.5.pdf'),
(3606, '3-rail', '6', '44', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-44.pdf'),
(3606, '3-rail', '6', '47-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-47.5.pdf'),
(3606, '3-rail', '6', '51', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-51.pdf'),
(3606, '3-rail', '6', '54-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-54.5.pdf'),
(3606, '3-rail', '6', '58', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-58.pdf'),
(3606, '3-rail', '6', '61-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-61.5.pdf'),
(3606, '3-rail', '6', '65', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-65.pdf'),
(3606, '3-rail', '6', '68-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-68.5.pdf'),
(3606, '3-rail', '6', '72', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-72.pdf'),
(3606, '3-rail', '6', '75-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-75.5.pdf'),
(3606, '3-rail', '6', '79', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-79.pdf'),
(3606, '3-rail', '6', '82-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-82.5.pdf'),
(3606, '3-rail', '6', '86', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-86.pdf'),
(3606, '3-rail', '6', '89-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-89.5.pdf'),
(3606, '3-rail', '6', '93', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-93.pdf'),
(3606, '3-rail', '6', '96-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-96.5.pdf'),
(3606, '3-rail', '6', '100', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-100.pdf'),
(3606, '3-rail', '6', '103-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-103.5.pdf'),
(3606, '3-rail', '6', '107', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-6-body-107.pdf');

-- RAIL = 3-rail with WIDTH = 8' (21 BODY values)
INSERT INTO `wpg0_profiles` (`category_id`, `gap`, `panel_width`, `body_height`, `pdf_url`) VALUES
(3606, '3-rail', '8', '37', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-37.pdf'),
(3606, '3-rail', '8', '40-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-40.5.pdf'),
(3606, '3-rail', '8', '44', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-44.pdf'),
(3606, '3-rail', '8', '47-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-47.5.pdf'),
(3606, '3-rail', '8', '51', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-51.pdf'),
(3606, '3-rail', '8', '54-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-54.5.pdf'),
(3606, '3-rail', '8', '58', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-58.pdf'),
(3606, '3-rail', '8', '61-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-61.5.pdf'),
(3606, '3-rail', '8', '65', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-65.pdf'),
(3606, '3-rail', '8', '68-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-68.5.pdf'),
(3606, '3-rail', '8', '72', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-72.pdf'),
(3606, '3-rail', '8', '75-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-75.5.pdf'),
(3606, '3-rail', '8', '79', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-79.pdf'),
(3606, '3-rail', '8', '82-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-82.5.pdf'),
(3606, '3-rail', '8', '86', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-86.pdf'),
(3606, '3-rail', '8', '89-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-89.5.pdf'),
(3606, '3-rail', '8', '93', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-93.pdf'),
(3606, '3-rail', '8', '96-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-96.5.pdf'),
(3606, '3-rail', '8', '100', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-100.pdf'),
(3606, '3-rail', '8', '103-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-103.5.pdf'),
(3606, '3-rail', '8', '107', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-3rail-width-8-body-107.pdf');

-- RAIL = 4-rail with WIDTH = 6' (22 BODY values)
INSERT INTO `wpg0_profiles` (`category_id`, `gap`, `panel_width`, `body_height`, `pdf_url`) VALUES
(3606, '4-rail', '6', '34-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-34.5.pdf'),
(3606, '4-rail', '6', '38', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-38.pdf'),
(3606, '4-rail', '6', '41-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-41.5.pdf'),
(3606, '4-rail', '6', '45', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-45.pdf'),
(3606, '4-rail', '6', '48-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-48.5.pdf'),
(3606, '4-rail', '6', '52', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-52.pdf'),
(3606, '4-rail', '6', '55-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-55.5.pdf'),
(3606, '4-rail', '6', '59', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-59.pdf'),
(3606, '4-rail', '6', '62-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-62.5.pdf'),
(3606, '4-rail', '6', '66', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-66.pdf'),
(3606, '4-rail', '6', '69-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-69.5.pdf'),
(3606, '4-rail', '6', '73', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-73.pdf'),
(3606, '4-rail', '6', '76-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-76.5.pdf'),
(3606, '4-rail', '6', '80', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-80.pdf'),
(3606, '4-rail', '6', '83-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-83.5.pdf'),
(3606, '4-rail', '6', '87', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-87.pdf'),
(3606, '4-rail', '6', '90-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-90.5.pdf'),
(3606, '4-rail', '6', '94', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-94.pdf'),
(3606, '4-rail', '6', '97-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-97.5.pdf'),
(3606, '4-rail', '6', '101', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-101.pdf'),
(3606, '4-rail', '6', '104-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-104.5.pdf'),
(3606, '4-rail', '6', '108', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-6-body-108.pdf');

-- RAIL = 4-rail with WIDTH = 8' (22 BODY values)
INSERT INTO `wpg0_profiles` (`category_id`, `gap`, `panel_width`, `body_height`, `pdf_url`) VALUES
(3606, '4-rail', '8', '34-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-34.5.pdf'),
(3606, '4-rail', '8', '38', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-38.pdf'),
(3606, '4-rail', '8', '41-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-41.5.pdf'),
(3606, '4-rail', '8', '45', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-45.pdf'),
(3606, '4-rail', '8', '48-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-48.5.pdf'),
(3606, '4-rail', '8', '52', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-52.pdf'),
(3606, '4-rail', '8', '55-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-55.5.pdf'),
(3606, '4-rail', '8', '59', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-59.pdf'),
(3606, '4-rail', '8', '62-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-62.5.pdf'),
(3606, '4-rail', '8', '66', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-66.pdf'),
(3606, '4-rail', '8', '69-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-69.5.pdf'),
(3606, '4-rail', '8', '73', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-73.pdf'),
(3606, '4-rail', '8', '76-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-76.5.pdf'),
(3606, '4-rail', '8', '80', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-80.pdf'),
(3606, '4-rail', '8', '83-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-83.5.pdf'),
(3606, '4-rail', '8', '87', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-87.pdf'),
(3606, '4-rail', '8', '90-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-90.5.pdf'),
(3606, '4-rail', '8', '94', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-94.pdf'),
(3606, '4-rail', '8', '97-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-97.5.pdf'),
(3606, '4-rail', '8', '101', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-101.pdf'),
(3606, '4-rail', '8', '104-1-2', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-104.5.pdf'),
(3606, '4-rail', '8', '108', 'https://staging2.wholesalefencing.com/wp-content/uploads/profiles/ha-mix-4rail-width-8-body-108.pdf');

-- ==============================================================================
-- SUMMARY
-- ==============================================================================
-- Total records inserted:
-- - Semi-Privacy: GAP 1/2 (44 records), GAP 1 (40 records), GAP 1-3/8 (34 records) = 118 records
-- - Privacy: WIDTH 6 & 8 (42 records) = 42 records
-- - Mixed: 3-rail (42 records), 4-rail (44 records) = 86 records
-- GRAND TOTAL: 246 records
--
-- After running this SQL:
-- 1. Upload the actual PDF files to wp-content/uploads/profiles/ folder
-- 2. Update the pdf_url values in this SQL with correct URLs
-- 3. Run this SQL in phpMyAdmin
