-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2026 at 02:29 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `frio`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `created_at`) VALUES
(2, 'frio', '21232f297a57a5a743894a0e4a801fc3', '2026-05-25 12:17:42');

-- --------------------------------------------------------

--
-- Table structure for table `banner_slider`
--

CREATE TABLE `banner_slider` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `button_link` varchar(255) DEFAULT NULL,
  `image` text NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `text_align` varchar(20) NOT NULL DEFAULT 'center'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banner_slider`
--

INSERT INTO `banner_slider` (`id`, `name`, `description`, `button_link`, `image`, `display_order`, `active`, `created_at`, `text_align`) VALUES
(1, '', '', '', 'assets/imag/banners/banner_1780031910_0a5e86f07837b0eb8d22fe9ea0154d7c.jpg', 1, 1, '2026-05-29 05:14:13', 'center'),
(6, '', '', '', 'assets/imag/banners/banner_1780049698_58ec0999f4a8cae5c361de1e34a1b399.jpg', 5, 1, '2026-05-29 10:14:58', 'center'),
(7, '', '', '', 'assets/imag/banners/banner_1780050604_f18aa1f2588ec92b2bb2219034f1cb45.jpg', 6, 1, '2026-05-29 10:30:04', 'center');

-- --------------------------------------------------------

--
-- Table structure for table `catalogue`
--

CREATE TABLE `catalogue` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `pdf_file` text NOT NULL,
  `preview_image` text NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 0,
  `download_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `catalogue`
--

INSERT INTO `catalogue` (`id`, `name`, `pdf_file`, `preview_image`, `display_order`, `active`, `download_count`, `created_at`) VALUES
(1, 'Frio India', 'assets/pdf/catalogue/brochure_1780031233_1c99b04367b0283411b286ed9b3c3849.pdf', 'assets/imag/catalogue/cover_1780031233_768bc31832adf036f1c429ea3536defb.png', 1, 1, 0, '2026-05-29 05:07:13');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` text DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `code`, `name`, `description`, `image`, `icon`, `active`, `display_order`, `created_at`) VALUES
(1, '#FR-8828', 'FLARE NUTS', '', '[\"assets\\/imag\\/category\\/cat_1780473368_637192773ddae64e9d718cd2331ab442_0.jpg\",\"assets\\/imag\\/category\\/cat_1780464186_c08ece62fb3082153b9e82e3ce03f8d2_0.jpg\",\"assets\\/imag\\/category\\/cat_1780464186_b03c8c4db44753be6857a4c3975e9873_1.jpg\",\"assets\\/imag\\/category\\/cat_1780464186_83d570e69e4969b87ecf352e2091c1ae_2.jpg\"]', NULL, 1, 1, '2026-05-28 11:33:49'),
(2, '#FR-8857', 'FLARE UNION', '', '[\"assets\\/imag\\/category\\/cat_1780473396_0c85c90c567d0eb53d2ef71061d0885a_0.jpg\",\"assets\\/imag\\/category\\/cat_1780464277_86daa41ee473f0b11aaec5aebd14e332_0.jpg\",\"assets\\/imag\\/category\\/cat_1780464277_60b1cdc61e1d8ec624d39847e1da82db_1.jpg\",\"assets\\/imag\\/category\\/cat_1780464277_38586cdc38f522ab3ff0e53a8fa33b5d_2.jpg\",\"assets\\/imag\\/category\\/cat_1780464277_2014e9645c5b16e9a2c399c60218b227_3.jpg\"]', NULL, 1, 2, '2026-05-28 11:34:06'),
(3, '#FR-8858', 'FLARE ELBOW/TEE', '', '[\"assets\\/imag\\/category\\/cat_1780464314_8dc29fd3326d43ec73b6cbc4700cc408_0.jpg\",\"assets\\/imag\\/category\\/cat_1780464314_934836e0f4c19abcf41fc75c99b2ee20_1.jpg\",\"assets\\/imag\\/category\\/cat_1780464314_cc90dfabd074e3fafe754e661cab6d7b_2.jpg\",\"assets\\/imag\\/category\\/cat_1780464314_aea0d0b24f81be5d88dd70b900f99948_3.jpg\"]', NULL, 1, 3, '2026-05-28 11:34:25'),
(4, '#FR-8842', 'CHARGING ADAPTOR/VALVES/ORIFICE', '', '[\"assets\\/imag\\/category\\/cat_1780473456_01b54dddf589051791f39febdd5da996_0.jpg\",\"assets\\/imag\\/category\\/cat_1780464363_366dc81c84d3f697f848a242792a9a9b_0.jpg\",\"assets\\/imag\\/category\\/cat_1780464363_d53d77bbc6fd2a0fd8bcb9a799f37295_1.jpg\",\"assets\\/imag\\/category\\/cat_1780464363_3e68a2be5eb51074f47dcbab5138ab91_2.jpg\",\"assets\\/imag\\/category\\/cat_1780464363_bca6a0edf6b00f4fdf209faf36be28a2_3.jpg\"]', NULL, 1, 4, '2026-05-28 11:35:02');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `type`, `first_name`, `last_name`, `email`, `phone`, `message`, `is_read`, `created_at`) VALUES
(1, 'contact', 'Divyaraj', 'gohil', 'aaa@gmail.com', '7984297377', 'hello that is demo message', 0, '2026-06-02 09:54:10'),
(2, 'catalogue', 'Divyaraj', 'gohil', 'aaa@gmail.com', '7984297377', 'Catalogue Downloaded: Frio India', 0, '2026-06-02 09:57:47'),
(3, 'contact', 'divyarajsinh', 'gohil', 'divyarajgohil6299@gmail.com', '7984297377', 'hello how are you this form fill form a mobil responsive', 0, '2026-06-02 10:12:40'),
(4, 'catalogue', 'Divyaraj', 'gohil', 'aaa@gmail.com', '7984297377', 'Catalogue Viewed: Frio India', 0, '2026-06-02 10:28:53'),
(5, 'catalogue', 'Divyaraj', 'gohil', 'aaa@gmail.com', '7984297377', 'Catalogue Viewed: Frio India', 0, '2026-06-02 10:58:27'),
(6, 'contact', 'Divyaraj', 'gohil', 'aaa@gmail.com', '7984297377', 'hello123', 0, '2026-06-02 12:41:36'),
(7, 'contact', 'Divyaraj', 'gohil', 'aaa@gmail.com', '7984297377', 'demo message', 0, '2026-06-02 12:54:48'),
(8, 'contact', 'Divyaraj', 'gohil', 'aaa@gmail.com', '0084297377', 'demo messagee', 0, '2026-06-02 12:55:52'),
(9, 'contact', 'Divyaraj', 'gohil', 'aaa@gmail.com', '7984297377', 'ANSACNSALKCN', 0, '2026-06-02 13:02:41');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `code` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` text DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `category_id`, `code`, `name`, `description`, `image`, `active`, `display_order`, `created_at`) VALUES
(1, 1, 'FN-78', 'Brass Flare Nut', '<p data-start=\"21\" data-end=\"383\">Our Brass Flare Nuts are manufactured with high-quality brass material for reliable and leak-proof connections in refrigeration, air conditioning, gas, and fluid transfer applications. Designed for durability and precision, these flare nuts provide excellent corrosion resistance, long service life, and secure fitting performance under high-pressure conditions.</p>\r\n<p data-start=\"385\" data-end=\"598\">Available in multiple sizes ranging from <strong data-start=\"426\" data-end=\"442\">1/4\" to 7/8\"</strong>, these fittings are ideal for industrial, commercial, and HVAC systems. The smooth finish and accurate threading ensure easy installation and a tight seal.</p>\r\n<h4 data-start=\"600\" data-end=\"614\">Features:</h4>\r\n<ul data-start=\"615\" data-end=\"850\">\r\n<li data-section-id=\"alxlqq\" data-start=\"615\" data-end=\"651\">Premium quality brass construction</li>\r\n<li data-section-id=\"1w7xtt6\" data-start=\"652\" data-end=\"682\">Corrosion and rust resistant</li>\r\n<li data-section-id=\"11gsunt\" data-start=\"683\" data-end=\"727\">Precision threading for secure connections</li>\r\n<li data-section-id=\"5edack\" data-start=\"728\" data-end=\"773\">Suitable for HVAC and refrigeration systems</li>\r\n<li data-section-id=\"18o4y9r\" data-start=\"774\" data-end=\"812\">Durable and long-lasting performance</li>\r\n<li data-section-id=\"1czwoxq\" data-start=\"813\" data-end=\"850\">Available in various standard sizes</li>\r\n</ul>\r\n<h4 data-start=\"852\" data-end=\"870\">Applications:</h4>\r\n<ul data-start=\"871\" data-end=\"1000\" data-is-last-node=\"\" data-is-only-node=\"\">\r\n<li data-section-id=\"9c6s7t\" data-start=\"871\" data-end=\"894\">Refrigeration systems</li>\r\n<li data-section-id=\"2p9pec\" data-start=\"895\" data-end=\"919\">Air conditioning units</li>\r\n<li data-section-id=\"1bwcdak\" data-start=\"920\" data-end=\"935\">Gas pipelines</li>\r\n<li data-section-id=\"b1afij\" data-start=\"936\" data-end=\"962\">Industrial fluid systems</li>\r\n<li data-section-id=\"nyo2dr\" data-start=\"963\" data-end=\"1000\" data-is-last-node=\"\">Hydraulic and pneumatic connections</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1779971824_9f90b2d886536f82972b88d20248df54.jpg', 1, 1, '2026-05-28 11:39:28'),
(2, 1, 'FLN-78', 'Brass Flare Long Nut', '<p data-start=\"26\" data-end=\"317\">Our Brass Flare Long Nuts are designed for strong, secure, and leak-resistant connections in refrigeration, HVAC, gas, and industrial piping systems. Manufactured from premium-grade brass, these long nuts provide enhanced grip, durability, and superior resistance against corrosion and wear.</p>\r\n<p data-start=\"319\" data-end=\"574\">The extended body design ensures better tightening support and improved connection stability, making them suitable for high-pressure applications. Available in sizes from <strong data-start=\"490\" data-end=\"506\">1/4\" to 7/8\"</strong>, these fittings deliver reliable performance and long service life.</p>\r\n<h4 data-start=\"576\" data-end=\"590\">Features:</h4>\r\n<ul data-start=\"591\" data-end=\"817\">\r\n<li data-section-id=\"1b1693w\" data-start=\"591\" data-end=\"620\">High-quality brass material</li>\r\n<li data-section-id=\"1ta7fm1\" data-start=\"621\" data-end=\"669\">Long body design for better grip and stability</li>\r\n<li data-section-id=\"1w7xtt6\" data-start=\"670\" data-end=\"700\">Corrosion and rust resistant</li>\r\n<li data-section-id=\"1fnhx9v\" data-start=\"701\" data-end=\"741\">Precision threading for secure fitting</li>\r\n<li data-section-id=\"1ouxjt0\" data-start=\"742\" data-end=\"775\">Strong and durable construction</li>\r\n<li data-section-id=\"8ykqz1\" data-start=\"776\" data-end=\"817\">Suitable for high-pressure applications</li>\r\n</ul>\r\n<h4 data-start=\"819\" data-end=\"837\">Applications:</h4>\r\n<ul data-start=\"838\" data-end=\"957\" data-is-last-node=\"\" data-is-only-node=\"\">\r\n<li data-section-id=\"1tr1blu\" data-start=\"838\" data-end=\"852\">HVAC systems</li>\r\n<li data-section-id=\"amcntm\" data-start=\"853\" data-end=\"874\">Refrigeration units</li>\r\n<li data-section-id=\"uybgs5\" data-start=\"875\" data-end=\"900\">Gas and fluid pipelines</li>\r\n<li data-section-id=\"d4k3ep\" data-start=\"901\" data-end=\"923\">Industrial machinery</li>\r\n<li data-section-id=\"1se12f0\" data-start=\"924\" data-end=\"957\" data-is-last-node=\"\">Hydraulic and pneumatic systems</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1779971878_9f1dabf9cac12adce87334e825bd9d5c.jpg', 1, 2, '2026-05-28 11:39:54'),
(3, 1, 'FDN-38', 'Brass Flare Dead Nut', '<p data-start=\"26\" data-end=\"358\">Our Brass Flare Dead Nuts are manufactured using premium-quality brass to provide secure sealing and dependable performance in refrigeration, HVAC, gas, and industrial piping applications. Designed for durability and precision, these nuts ensure leak-proof connections and excellent resistance against corrosion, pressure, and wear.</p>\r\n<p data-start=\"360\" data-end=\"618\">The compact and sturdy construction makes them ideal for applications requiring reliable end connections and long-lasting performance. Available in sizes from <strong data-start=\"519\" data-end=\"535\">1/4\" to 7/8\"</strong>, these flare dead nuts are suitable for various commercial and industrial systems.</p>\r\n<h4 data-start=\"620\" data-end=\"634\">Features:</h4>\r\n<ul data-start=\"635\" data-end=\"851\">\r\n<li data-section-id=\"r0y4k2\" data-start=\"635\" data-end=\"666\">High-grade brass construction</li>\r\n<li data-section-id=\"1d22jgn\" data-start=\"667\" data-end=\"702\">Leak-resistant and durable design</li>\r\n<li data-section-id=\"1w7xtt6\" data-start=\"703\" data-end=\"733\">Corrosion and rust resistant</li>\r\n<li data-section-id=\"ov3eeq\" data-start=\"734\" data-end=\"776\">Precision threading for accurate fitting</li>\r\n<li data-section-id=\"y8y2eq\" data-start=\"777\" data-end=\"813\">Suitable for high-pressure systems</li>\r\n<li data-section-id=\"zl2x7e\" data-start=\"814\" data-end=\"851\">Long-lasting industrial performance</li>\r\n</ul>\r\n<h4 data-start=\"853\" data-end=\"871\">Applications:</h4>\r\n<ul data-start=\"872\" data-end=\"1018\" data-is-last-node=\"\" data-is-only-node=\"\">\r\n<li data-section-id=\"9c6s7t\" data-start=\"872\" data-end=\"895\">Refrigeration systems</li>\r\n<li data-section-id=\"sx4owr\" data-start=\"896\" data-end=\"916\">HVAC installations</li>\r\n<li data-section-id=\"9bpby1\" data-start=\"917\" data-end=\"949\">Gas and fluid transfer systems</li>\r\n<li data-section-id=\"c81hmr\" data-start=\"950\" data-end=\"988\">Hydraulic and pneumatic applications</li>\r\n<li data-section-id=\"1ksls6\" data-start=\"989\" data-end=\"1018\" data-is-last-node=\"\">Industrial pipe connections</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1779971897_2dd28434da17304628c31ae6f452d1c2.jpg', 1, 3, '2026-05-28 11:40:22'),
(4, 1, 'FRN-7834', 'Brass Flare Reducing Nut', '<div class=\"qMYqUG_convSearchResultHighlightRoot\">\r\n<div class=\"\" data-turn-id-container=\"request-WEB:916cf0f3-4384-4936-98e3-134f99e7f57e-5\" data-is-intersecting=\"true\">\r\n<section class=\"text-token-text-primary w-full focus:outline-none has-data-writing-block:pointer-events-none [&amp;:has([data-writing-block])&gt;*]:pointer-events-auto R6Vx5W_threadScrollVars scroll-mb-[calc(var(--scroll-root-safe-area-inset-bottom,0px)+var(--thread-response-height))] scroll-mt-[calc(var(--header-height)+min(200px,max(70px,20svh)))]\" dir=\"auto\" data-turn-id=\"request-WEB:916cf0f3-4384-4936-98e3-134f99e7f57e-5\" data-turn-id-container=\"request-WEB:916cf0f3-4384-4936-98e3-134f99e7f57e-5\" data-testid=\"conversation-turn-12\" data-scroll-anchor=\"false\" data-turn=\"assistant\">\r\n<div class=\"text-base my-auto mx-auto pb-10 [--thread-content-margin:var(--thread-content-margin-xs,calc(var(--spacing)*4))] @w-sm/main:[--thread-content-margin:var(--thread-content-margin-sm,calc(var(--spacing)*6))] @w-lg/main:[--thread-content-margin:var(--thread-content-margin-lg,calc(var(--spacing)*16))] px-(--thread-content-margin)\">\r\n<div class=\"[--thread-content-max-width:40rem] @w-lg/main:[--thread-content-max-width:48rem] mx-auto max-w-(--thread-content-max-width) flex-1 group/turn-messages focus-visible:outline-hidden relative flex w-full min-w-0 flex-col agent-turn\">\r\n<div class=\"flex max-w-full flex-col gap-4 grow\">\r\n<div class=\"min-h-8 text-message relative flex w-full flex-col items-end gap-2 text-start break-words whitespace-normal outline-none keyboard-focused:focus-ring [.text-message+&amp;]:mt-1\" dir=\"auto\" tabindex=\"0\" data-message-author-role=\"assistant\" data-message-id=\"8120013d-3ed4-4ebd-b007-afc22e197699\" data-message-model-slug=\"gpt-5-5\" data-turn-start-message=\"true\">\r\n<div class=\"flex w-full flex-col gap-1 empty:hidden\">\r\n<div class=\"markdown prose dark:prose-invert wrap-break-word w-full dark markdown-new-styling\">\r\n<p data-start=\"30\" data-end=\"361\">Our Brass Flare Reducing Nuts are engineered for secure and efficient connections between different pipe sizes in refrigeration, HVAC, gas, and industrial fluid systems. Manufactured from high-quality brass, these reducing nuts provide excellent strength, corrosion resistance, and leak-proof performance for long-term reliability.</p>\r\n<p data-start=\"363\" data-end=\"624\">Designed for precision fitting, these nuts allow smooth size transitions while maintaining strong and stable connections under high-pressure conditions. Available in multiple reducing size combinations, they are ideal for commercial and industrial applications.</p>\r\n<h4 data-start=\"626\" data-end=\"640\">Features:</h4>\r\n<ul data-start=\"641\" data-end=\"879\">\r\n<li data-section-id=\"1gyle93\" data-start=\"641\" data-end=\"675\">Premium-grade brass construction</li>\r\n<li data-section-id=\"164gq81\" data-start=\"676\" data-end=\"722\">Designed for connecting different pipe sizes</li>\r\n<li data-section-id=\"1w7xtt6\" data-start=\"723\" data-end=\"753\">Corrosion and rust resistant</li>\r\n<li data-section-id=\"mr5v0u\" data-start=\"754\" data-end=\"798\">Precision threading for leak-proof fitting</li>\r\n<li data-section-id=\"18o4y9r\" data-start=\"799\" data-end=\"837\">Durable and long-lasting performance</li>\r\n<li data-section-id=\"8ykqz1\" data-start=\"838\" data-end=\"879\">Suitable for high-pressure applications</li>\r\n</ul>\r\n<h4 data-start=\"881\" data-end=\"899\">Applications:</h4>\r\n<ul data-start=\"900\" data-end=\"1033\" data-is-last-node=\"\" data-is-only-node=\"\">\r\n<li data-section-id=\"1tr1blu\" data-start=\"900\" data-end=\"914\">HVAC systems</li>\r\n<li data-section-id=\"amcntm\" data-start=\"915\" data-end=\"936\">Refrigeration units</li>\r\n<li data-section-id=\"16fggfm\" data-start=\"937\" data-end=\"967\">Gas and fluid transfer lines</li>\r\n<li data-section-id=\"1se12f0\" data-start=\"968\" data-end=\"1001\">Hydraulic and pneumatic systems</li>\r\n<li data-section-id=\"1fbevr7\" data-start=\"1002\" data-end=\"1033\" data-is-last-node=\"\">Industrial piping connections</li>\r\n</ul>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n<div class=\"mt-3 w-full empty:hidden\">&nbsp;</div>\r\n</div>\r\n</div>\r\n</section>\r\n</div>\r\n</div>', 'assets/imag/product/gallery/gal_1_1779971914_ef508497e996d9fbf718422ad5bd2610.jpg', 1, 4, '2026-05-28 11:40:54'),
(5, 1, 'CC-34', 'Brass Cylinder Caps', '<p data-start=\"25\" data-end=\"362\">Our Brass Cylinder Caps are manufactured from high-quality brass material to provide secure sealing and reliable protection for refrigeration, HVAC, gas, and industrial piping systems. Designed with precision threading and durable construction, these caps ensure leak-proof performance and excellent resistance to corrosion and pressure.</p>\r\n<p data-start=\"364\" data-end=\"532\">Available in multiple FPT sizes, these cylinder caps are ideal for safely closing pipe ends and maintaining system efficiency in commercial and industrial applications.</p>\r\n<h4 data-start=\"534\" data-end=\"548\">Features:</h4>\r\n<ul data-start=\"549\" data-end=\"774\">\r\n<li data-section-id=\"2ox2jj\" data-start=\"549\" data-end=\"585\">Premium-quality brass construction</li>\r\n<li data-section-id=\"18kitmc\" data-start=\"586\" data-end=\"621\">Strong and leak-resistant sealing</li>\r\n<li data-section-id=\"1w7xtt6\" data-start=\"622\" data-end=\"652\">Corrosion and rust resistant</li>\r\n<li data-section-id=\"1fnhx9v\" data-start=\"653\" data-end=\"693\">Precision threading for secure fitting</li>\r\n<li data-section-id=\"18o4y9r\" data-start=\"694\" data-end=\"732\">Durable and long-lasting performance</li>\r\n<li data-section-id=\"8ykqz1\" data-start=\"733\" data-end=\"774\">Suitable for high-pressure applications</li>\r\n</ul>\r\n<h4 data-start=\"776\" data-end=\"794\">Applications:</h4>\r\n<ul data-start=\"795\" data-end=\"929\" data-is-last-node=\"\" data-is-only-node=\"\">\r\n<li data-section-id=\"9c6s7t\" data-start=\"795\" data-end=\"818\">Refrigeration systems</li>\r\n<li data-section-id=\"sx4owr\" data-start=\"819\" data-end=\"839\">HVAC installations</li>\r\n<li data-section-id=\"uybgs5\" data-start=\"840\" data-end=\"865\">Gas and fluid pipelines</li>\r\n<li data-section-id=\"1se12f0\" data-start=\"866\" data-end=\"899\">Hydraulic and pneumatic systems</li>\r\n<li data-section-id=\"yqlpef\" data-start=\"900\" data-end=\"929\" data-is-last-node=\"\">Industrial pipe end sealing</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1779971959_1bf71ad5a9cc20959a9767690b20c249.jpg', 1, 5, '2026-05-28 11:41:35'),
(6, 1, 'SVC-58', 'Brass Split Valve Caps', '<p data-start=\"28\" data-end=\"317\">Our Brass Split Valve Caps are designed for secure sealing and reliable protection in refrigeration, HVAC, and industrial valve systems. Manufactured from premium-quality brass, these caps provide excellent durability, corrosion resistance, and leak-proof performance for long-lasting use.</p>\r\n<p data-start=\"319\" data-end=\"595\">With precision threading and a strong construction, these valve caps ensure proper fitting and protection against dust, leakage, and pressure loss. Available in multiple sizes from <strong data-start=\"500\" data-end=\"516\">1/4\" to 5/8\"</strong>, they are suitable for a wide range of commercial and industrial applications.</p>\r\n<h4 data-start=\"597\" data-end=\"611\">Features:</h4>\r\n<ul data-start=\"612\" data-end=\"844\">\r\n<li data-section-id=\"dth94a\" data-start=\"612\" data-end=\"645\">High-quality brass construction</li>\r\n<li data-section-id=\"1d22jgn\" data-start=\"646\" data-end=\"681\">Leak-resistant and durable design</li>\r\n<li data-section-id=\"1w7xtt6\" data-start=\"682\" data-end=\"712\">Corrosion and rust resistant</li>\r\n<li data-section-id=\"1fnhx9v\" data-start=\"713\" data-end=\"753\">Precision threading for secure fitting</li>\r\n<li data-section-id=\"17d0r3m\" data-start=\"754\" data-end=\"798\">Long service life and reliable performance</li>\r\n<li data-section-id=\"1adpa1w\" data-start=\"799\" data-end=\"844\">Suitable for refrigeration and HVAC systems</li>\r\n</ul>\r\n<h4 data-start=\"846\" data-end=\"864\">Applications:</h4>\r\n<ul data-start=\"865\" data-end=\"994\" data-is-last-node=\"\" data-is-only-node=\"\">\r\n<li data-section-id=\"110upk5\" data-start=\"865\" data-end=\"897\">Split air conditioning systems</li>\r\n<li data-section-id=\"amcntm\" data-start=\"898\" data-end=\"919\">Refrigeration units</li>\r\n<li data-section-id=\"sx4owr\" data-start=\"920\" data-end=\"940\">HVAC installations</li>\r\n<li data-section-id=\"15qy0i\" data-start=\"941\" data-end=\"964\">Gas and fluid systems</li>\r\n<li data-section-id=\"avwbz8\" data-start=\"965\" data-end=\"994\" data-is-last-node=\"\">Industrial valve protection</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1779971982_2e800385648ff01c841801160044eee6.jpg', 1, 6, '2026-05-28 11:42:07'),
(7, 2, 'FU-78', 'Brass Flare Union', '<p data-start=\"23\" data-end=\"353\">Our Brass Flare Unions are manufactured using premium-quality brass to provide strong, leak-proof, and durable connections in refrigeration, HVAC, gas, and fluid transfer systems. Designed with precision threading and excellent finishing, these unions ensure secure fitting and reliable performance under high-pressure conditions.</p>\r\n<p data-start=\"355\" data-end=\"529\">Available in multiple sizes from <strong data-start=\"388\" data-end=\"405\">3/16\" to 7/8\"</strong>, these flare unions are ideal for connecting tubing and pipe systems efficiently in commercial and industrial applications.</p>\r\n<h4 data-start=\"531\" data-end=\"545\">Features:</h4>\r\n<ul data-start=\"546\" data-end=\"796\">\r\n<li data-section-id=\"dth94a\" data-start=\"546\" data-end=\"579\">High-quality brass construction</li>\r\n<li data-section-id=\"r76l62\" data-start=\"580\" data-end=\"627\">Leak-resistant and corrosion-resistant design</li>\r\n<li data-section-id=\"188fm16\" data-start=\"628\" data-end=\"670\">Precision threads for secure connections</li>\r\n<li data-section-id=\"18o4y9r\" data-start=\"671\" data-end=\"709\">Durable and long-lasting performance</li>\r\n<li data-section-id=\"1adpa1w\" data-start=\"710\" data-end=\"755\">Suitable for refrigeration and HVAC systems</li>\r\n<li data-section-id=\"sxga7q\" data-start=\"756\" data-end=\"796\">Excellent pressure handling capability</li>\r\n</ul>\r\n<h4 data-start=\"798\" data-end=\"816\">Applications:</h4>\r\n<ul data-start=\"817\" data-end=\"947\">\r\n<li data-section-id=\"9c6s7t\" data-start=\"817\" data-end=\"840\">Refrigeration systems</li>\r\n<li data-section-id=\"sx4owr\" data-start=\"841\" data-end=\"861\">HVAC installations</li>\r\n<li data-section-id=\"1bwcdak\" data-start=\"862\" data-end=\"877\">Gas pipelines</li>\r\n<li data-section-id=\"1se12f0\" data-start=\"878\" data-end=\"911\">Hydraulic and pneumatic systems</li>\r\n<li data-section-id=\"wlpco0\" data-start=\"912\" data-end=\"947\">Industrial fluid transfer systems</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1779972004_f4ebd4f1c8683ec69c6e290c71f68ebd.jpg', 1, 7, '2026-05-28 12:13:45'),
(8, 2, 'FRU-5878', 'Brass Reducing Union', '<p data-start=\"980\" data-end=\"1298\">Our Brass Reducing Unions are specially designed for connecting different pipe or tube sizes while maintaining secure and efficient fluid flow. Manufactured from premium-grade brass, these fittings offer excellent durability, corrosion resistance, and leak-proof performance for industrial and commercial applications.</p>\r\n<p data-start=\"1300\" data-end=\"1464\">These reducing unions are widely used in refrigeration, HVAC, gas, and hydraulic systems where size conversion is required without compromising connection strength.</p>\r\n<h4 data-start=\"1466\" data-end=\"1480\">Features:</h4>\r\n<ul data-start=\"1481\" data-end=\"1699\">\r\n<li data-section-id=\"1jj6six\" data-start=\"1481\" data-end=\"1505\">Premium brass material</li>\r\n<li data-section-id=\"b3ygrp\" data-start=\"1506\" data-end=\"1547\">Designed for different size connections</li>\r\n<li data-section-id=\"1w7xtt6\" data-start=\"1548\" data-end=\"1578\">Corrosion and rust resistant</li>\r\n<li data-section-id=\"3nxvk0\" data-start=\"1579\" data-end=\"1623\">Precision machining for leak-proof fitting</li>\r\n<li data-section-id=\"1ouxjt0\" data-start=\"1624\" data-end=\"1657\">Strong and durable construction</li>\r\n<li data-section-id=\"8ykqz1\" data-start=\"1658\" data-end=\"1699\">Suitable for high-pressure applications</li>\r\n</ul>\r\n<h4 data-start=\"1701\" data-end=\"1719\">Applications:</h4>\r\n<ul data-start=\"1720\" data-end=\"1851\">\r\n<li data-section-id=\"k2l3fy\" data-start=\"1720\" data-end=\"1752\">HVAC and refrigeration systems</li>\r\n<li data-section-id=\"uybgs5\" data-start=\"1753\" data-end=\"1778\">Gas and fluid pipelines</li>\r\n<li data-section-id=\"qv7pe3\" data-start=\"1779\" data-end=\"1798\">Hydraulic systems</li>\r\n<li data-section-id=\"roeyb5\" data-start=\"1799\" data-end=\"1822\">Pneumatic connections</li>\r\n<li data-section-id=\"jz4ci3\" data-start=\"1823\" data-end=\"1851\">Industrial piping networks</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1779972026_e66b9f72eeafd41e36d04cccd001c078.jpg', 1, 8, '2026-05-28 12:18:09'),
(9, 2, 'HU-341', 'Brass Half Union', '<p data-start=\"1891\" data-end=\"2161\">Our Brass Half Unions (FL x NPT) are engineered for reliable flare-to-thread connections in refrigeration, HVAC, and industrial piping systems. Made from high-quality brass, these fittings provide excellent strength, corrosion resistance, and secure sealing performance.</p>\r\n<p data-start=\"2163\" data-end=\"2310\">The combination of flare and NPT threading ensures easy installation and dependable connections for various commercial and industrial applications.</p>\r\n<h4 data-start=\"2312\" data-end=\"2326\">Features:</h4>\r\n<ul data-start=\"2327\" data-end=\"2549\">\r\n<li data-section-id=\"r0y4k2\" data-start=\"2327\" data-end=\"2358\">High-grade brass construction</li>\r\n<li data-section-id=\"j8vp8c\" data-start=\"2359\" data-end=\"2393\">Flare to NPT threaded connection</li>\r\n<li data-section-id=\"n090fz\" data-start=\"2394\" data-end=\"2437\">Leak-proof and corrosion-resistant design</li>\r\n<li data-section-id=\"ov3eeq\" data-start=\"2438\" data-end=\"2480\">Precision threading for accurate fitting</li>\r\n<li data-section-id=\"iceduj\" data-start=\"2481\" data-end=\"2512\">Durable and long service life</li>\r\n<li data-section-id=\"y8y2eq\" data-start=\"2513\" data-end=\"2549\">Suitable for high-pressure systems</li>\r\n</ul>\r\n<h4 data-start=\"2551\" data-end=\"2569\">Applications:</h4>\r\n<ul data-start=\"2570\" data-end=\"2716\" data-is-last-node=\"\" data-is-only-node=\"\">\r\n<li data-section-id=\"9c6s7t\" data-start=\"2570\" data-end=\"2593\">Refrigeration systems</li>\r\n<li data-section-id=\"sx4owr\" data-start=\"2594\" data-end=\"2614\">HVAC installations</li>\r\n<li data-section-id=\"9bpby1\" data-start=\"2615\" data-end=\"2647\">Gas and fluid transfer systems</li>\r\n<li data-section-id=\"c81hmr\" data-start=\"2648\" data-end=\"2686\">Hydraulic and pneumatic applications</li>\r\n<li data-section-id=\"1ksls6\" data-start=\"2687\" data-end=\"2716\" data-is-last-node=\"\">Industrial pipe connections</li>\r\n</ul>\r\n<p>&nbsp;</p>', 'assets/imag/product/gallery/gal_1_1779972044_18b219eac8728579be613071cde99899.jpg', 1, 9, '2026-05-28 12:25:20'),
(10, 2, 'CA-1412', 'Brass Cylinder Adapter', '<p data-start=\"39\" data-end=\"411\">Our Brass Cylinder Adapters (FL x FPT) are precision-engineered fittings designed to provide secure and leak-proof flare-to-female pipe thread connections in refrigeration, HVAC, and industrial piping systems. Manufactured from premium-quality brass, these adapters offer excellent corrosion resistance, durability, and reliable performance under high-pressure conditions.</p>\r\n<p data-start=\"413\" data-end=\"538\">These adapters are ideal for applications requiring strong threaded connections with easy installation and long service life.</p>\r\n<h4 data-start=\"540\" data-end=\"554\">Features:</h4>\r\n<ul data-start=\"555\" data-end=\"786\">\r\n<li data-section-id=\"dth94a\" data-start=\"555\" data-end=\"588\">High-quality brass construction</li>\r\n<li data-section-id=\"1qeextp\" data-start=\"589\" data-end=\"629\">Flare to female pipe thread connection</li>\r\n<li data-section-id=\"1w7xtt6\" data-start=\"630\" data-end=\"660\">Corrosion and rust resistant</li>\r\n<li data-section-id=\"1fnhx9v\" data-start=\"661\" data-end=\"701\">Precision threading for secure fitting</li>\r\n<li data-section-id=\"1d2hs48\" data-start=\"702\" data-end=\"738\">Durable and leak-proof performance</li>\r\n<li data-section-id=\"k6itff\" data-start=\"739\" data-end=\"786\">Suitable for industrial and HVAC applications</li>\r\n</ul>\r\n<h4 data-start=\"788\" data-end=\"806\">Applications:</h4>\r\n<ul data-start=\"807\" data-end=\"952\">\r\n<li data-section-id=\"9c6s7t\" data-start=\"807\" data-end=\"830\">Refrigeration systems</li>\r\n<li data-section-id=\"sx4owr\" data-start=\"831\" data-end=\"851\">HVAC installations</li>\r\n<li data-section-id=\"9bpby1\" data-start=\"852\" data-end=\"884\">Gas and fluid transfer systems</li>\r\n<li data-section-id=\"c81hmr\" data-start=\"885\" data-end=\"923\">Hydraulic and pneumatic applications</li>\r\n<li data-section-id=\"jz4ci3\" data-start=\"924\" data-end=\"952\">Industrial piping networks</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1779972066_4e7b3971a7b148637edf8f79f080880c.jpg', 1, 10, '2026-05-28 12:28:33'),
(11, 2, 'CU-14', 'Brass Condenser Union', '<p data-start=\"986\" data-end=\"1250\">Our Brass Condenser Unions are designed for strong and reliable connections in refrigeration, condenser, and HVAC systems. Made from premium-grade brass, these unions provide excellent sealing performance, durability, and resistance against corrosion and pressure.</p>\r\n<p data-start=\"1252\" data-end=\"1399\">Available in various standard sizes, these fittings ensure efficient fluid flow and secure installation for commercial and industrial applications.</p>\r\n<h4 data-start=\"1401\" data-end=\"1415\">Features:</h4>\r\n<ul data-start=\"1416\" data-end=\"1631\">\r\n<li data-section-id=\"1jj6six\" data-start=\"1416\" data-end=\"1440\">Premium brass material</li>\r\n<li data-section-id=\"n090fz\" data-start=\"1441\" data-end=\"1484\">Leak-proof and corrosion-resistant design</li>\r\n<li data-section-id=\"188cl3j\" data-start=\"1485\" data-end=\"1513\">Precision-machined threads</li>\r\n<li data-section-id=\"1ufdfx2\" data-start=\"1514\" data-end=\"1558\">Durable construction for long service life</li>\r\n<li data-section-id=\"y8y2eq\" data-start=\"1559\" data-end=\"1595\">Suitable for high-pressure systems</li>\r\n<li data-section-id=\"cgzv8s\" data-start=\"1596\" data-end=\"1631\">Easy installation and maintenance</li>\r\n</ul>\r\n<h4 data-start=\"1633\" data-end=\"1651\">Applications:</h4>\r\n<ul data-start=\"1652\" data-end=\"1776\">\r\n<li data-section-id=\"1d5optj\" data-start=\"1652\" data-end=\"1671\">Condenser systems</li>\r\n<li data-section-id=\"amcntm\" data-start=\"1672\" data-end=\"1693\">Refrigeration units</li>\r\n<li data-section-id=\"sx4owr\" data-start=\"1694\" data-end=\"1714\">HVAC installations</li>\r\n<li data-section-id=\"9bpby1\" data-start=\"1715\" data-end=\"1747\">Gas and fluid transfer systems</li>\r\n<li data-section-id=\"57ghp2\" data-start=\"1748\" data-end=\"1776\">Industrial cooling systems</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1779972090_6f7051eefa7e87975fc1ce5bb2608024.jpg', 1, 11, '2026-05-28 12:30:52'),
(12, 2, 'FC-1414', 'Brass Female Coupling', '<p data-start=\"1810\" data-end=\"2100\">Our Brass Female Couplings are manufactured using high-quality brass to provide secure female-thread connections in refrigeration, HVAC, plumbing, and industrial systems. These couplings offer excellent durability, corrosion resistance, and reliable sealing performance for long-term usage.</p>\r\n<p data-start=\"2102\" data-end=\"2247\">Designed with precision threading and a compact structure, these fittings ensure stable and leak-resistant connections in demanding applications.</p>\r\n<h4 data-start=\"2249\" data-end=\"2263\">Features:</h4>\r\n<ul data-start=\"2264\" data-end=\"2469\">\r\n<li data-section-id=\"r0y4k2\" data-start=\"2264\" data-end=\"2295\">High-grade brass construction</li>\r\n<li data-section-id=\"1hx6853\" data-start=\"2296\" data-end=\"2324\">Female threaded connection</li>\r\n<li data-section-id=\"1w7xtt6\" data-start=\"2325\" data-end=\"2355\">Corrosion and rust resistant</li>\r\n<li data-section-id=\"p66bv6\" data-start=\"2356\" data-end=\"2387\">Leak-proof and durable design</li>\r\n<li data-section-id=\"y3pvuk\" data-start=\"2388\" data-end=\"2430\">Precision machining for accurate fitting</li>\r\n<li data-section-id=\"svjo98\" data-start=\"2431\" data-end=\"2469\">Suitable for industrial applications</li>\r\n</ul>\r\n<h4 data-start=\"2471\" data-end=\"2489\">Applications:</h4>\r\n<ul data-start=\"2490\" data-end=\"2624\">\r\n<li data-section-id=\"1tr1blu\" data-start=\"2490\" data-end=\"2504\">HVAC systems</li>\r\n<li data-section-id=\"z5w8xs\" data-start=\"2505\" data-end=\"2534\">Refrigeration installations</li>\r\n<li data-section-id=\"uybgs5\" data-start=\"2535\" data-end=\"2560\">Gas and fluid pipelines</li>\r\n<li data-section-id=\"1se12f0\" data-start=\"2561\" data-end=\"2594\">Hydraulic and pneumatic systems</li>\r\n<li data-section-id=\"1ksls6\" data-start=\"2595\" data-end=\"2624\">Industrial pipe connections</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1779972216_3a099e70d4a01ff759853be6a9f92e2c.jpg', 1, 12, '2026-05-28 12:43:36'),
(13, 2, 'GA-341', 'Brass Gauge Adapter', '<p data-start=\"2667\" data-end=\"2969\">Our Brass Gauge Adapters (FL x FPT) are specially designed for connecting gauges and instrumentation systems in refrigeration, HVAC, and industrial applications. Manufactured from premium-quality brass, these adapters provide excellent strength, corrosion resistance, and secure leak-proof connections.</p>\r\n<p data-start=\"2971\" data-end=\"3123\">The flare-to-female pipe thread design ensures accurate fitting and reliable pressure handling, making them ideal for commercial and industrial systems.</p>\r\n<h4 data-start=\"3125\" data-end=\"3139\">Features:</h4>\r\n<ul data-start=\"3140\" data-end=\"3379\">\r\n<li data-section-id=\"1e2acq1\" data-start=\"3140\" data-end=\"3172\">Premium-quality brass material</li>\r\n<li data-section-id=\"1qeextp\" data-start=\"3173\" data-end=\"3213\">Flare to female pipe thread connection</li>\r\n<li data-section-id=\"ov3eeq\" data-start=\"3214\" data-end=\"3256\">Precision threading for accurate fitting</li>\r\n<li data-section-id=\"1jl2qz3\" data-start=\"3257\" data-end=\"3291\">Corrosion and pressure resistant</li>\r\n<li data-section-id=\"18o4y9r\" data-start=\"3292\" data-end=\"3330\">Durable and long-lasting performance</li>\r\n<li data-section-id=\"5s4mhy\" data-start=\"3331\" data-end=\"3379\">Suitable for gauge and instrumentation systems</li>\r\n</ul>\r\n<h4 data-start=\"3381\" data-end=\"3399\">Applications:</h4>\r\n<ul data-start=\"3400\" data-end=\"3548\" data-is-last-node=\"\" data-is-only-node=\"\">\r\n<li data-section-id=\"c67xmh\" data-start=\"3400\" data-end=\"3428\">Pressure gauge connections</li>\r\n<li data-section-id=\"9c6s7t\" data-start=\"3429\" data-end=\"3452\">Refrigeration systems</li>\r\n<li data-section-id=\"sx4owr\" data-start=\"3453\" data-end=\"3473\">HVAC installations</li>\r\n<li data-section-id=\"9bpby1\" data-start=\"3474\" data-end=\"3506\">Gas and fluid transfer systems</li>\r\n<li data-section-id=\"1vupz7y\" data-start=\"3507\" data-end=\"3548\" data-is-last-node=\"\">Industrial instrumentation applications</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1779972672_cf743a05801120038f7d2a24c68865f4.jpg', 1, 13, '2026-05-28 12:51:12'),
(14, 2, 'FA-31614', 'Brass Flare Adapter', '<p data-start=\"34\" data-end=\"393\">Our Brass Flare Adapters are manufactured with high-quality precision brass to provide secure, leak-proof, and durable connections in refrigeration, air conditioning, hydraulic, and industrial piping systems. Designed for excellent corrosion resistance and long service life, these adapters ensure reliable performance even under high-pressure applications.</p>\r\n<p data-start=\"395\" data-end=\"691\">Available in multiple size combinations, the FL x FF adapters are ideal for connecting flare fittings with female flare connections while maintaining smooth flow and strong sealing performance. The precision threading and premium finish make installation easy and dependable for professional use.</p>\r\n<h3 data-section-id=\"q4c1cr\" data-start=\"693\" data-end=\"706\">Features:</h3>\r\n<ul data-start=\"707\" data-end=\"983\">\r\n<li data-section-id=\"f8etxw\" data-start=\"707\" data-end=\"742\">Made from premium quality brass</li>\r\n<li data-section-id=\"61ayd2\" data-start=\"743\" data-end=\"781\">High corrosion and rust resistance</li>\r\n<li data-section-id=\"a5br3g\" data-start=\"782\" data-end=\"818\">Strong and leak-proof connection</li>\r\n<li data-section-id=\"wplrka\" data-start=\"819\" data-end=\"862\">Precision threads for easy installation</li>\r\n<li data-section-id=\"12ji479\" data-start=\"863\" data-end=\"933\">Suitable for refrigeration, HVAC, gas, and industrial applications</li>\r\n<li data-section-id=\"ed4gfh\" data-start=\"934\" data-end=\"983\">Available in various sizes and configurations</li>\r\n</ul>\r\n<h3 data-section-id=\"5ms9th\" data-start=\"985\" data-end=\"1002\">Applications:</h3>\r\n<ul data-start=\"1003\" data-end=\"1164\">\r\n<li data-section-id=\"ca3ot5\" data-start=\"1003\" data-end=\"1028\">Refrigeration systems</li>\r\n<li data-section-id=\"u4z2dw\" data-start=\"1029\" data-end=\"1055\">Air conditioning units</li>\r\n<li data-section-id=\"cw3xbw\" data-start=\"1056\" data-end=\"1091\">Hydraulic and pneumatic systems</li>\r\n<li data-section-id=\"1w3bw17\" data-start=\"1092\" data-end=\"1127\">Industrial fluid transfer lines</li>\r\n<li data-section-id=\"trxw2q\" data-start=\"1128\" data-end=\"1164\">Gas and pressure control systems</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1779973683_473814459ce8af91def5cea78f66df4b.jpg', 1, 14, '2026-05-28 13:08:03'),
(15, 2, 'FSP-316', 'Brass Flare Seal Plug', '<p data-start=\"26\" data-end=\"344\">Our Brass Flare Seal Plugs are manufactured using high-quality precision brass to provide secure sealing and dependable performance in refrigeration, HVAC, pneumatic, and industrial systems. These plugs are designed to close unused flare connections safely while preventing leakage and maintaining system efficiency.</p>\r\n<p data-start=\"346\" data-end=\"625\">With excellent corrosion resistance, durable construction, and precision threading, Brass Flare Seal Plugs ensure long service life and reliable operation even in demanding applications. Their smooth finish and accurate fit allow quick and easy installation for professional use.</p>\r\n<h3 data-section-id=\"q4c1cr\" data-start=\"627\" data-end=\"640\">Features:</h3>\r\n<ul data-start=\"641\" data-end=\"908\">\r\n<li data-section-id=\"f8etxw\" data-start=\"641\" data-end=\"676\">Made from premium quality brass</li>\r\n<li data-section-id=\"1y65lwc\" data-start=\"677\" data-end=\"720\">Excellent corrosion and rust resistance</li>\r\n<li data-section-id=\"kypdml\" data-start=\"721\" data-end=\"766\">Leak-proof and secure sealing performance</li>\r\n<li data-section-id=\"pg5nkp\" data-start=\"767\" data-end=\"805\">Precision threads for easy fitting</li>\r\n<li data-section-id=\"168mjy2\" data-start=\"806\" data-end=\"847\">Durable and long-lasting construction</li>\r\n<li data-section-id=\"x8h9z7\" data-start=\"848\" data-end=\"908\">Suitable for refrigeration, HVAC, and industrial systems</li>\r\n</ul>\r\n<h3 data-section-id=\"5ms9th\" data-start=\"910\" data-end=\"927\">Applications:</h3>\r\n<ul data-start=\"928\" data-end=\"1064\">\r\n<li data-section-id=\"ca3ot5\" data-start=\"928\" data-end=\"953\">Refrigeration systems</li>\r\n<li data-section-id=\"u4z2dw\" data-start=\"954\" data-end=\"980\">Air conditioning units</li>\r\n<li data-section-id=\"1vuxfcy\" data-start=\"981\" data-end=\"1002\">Pneumatic systems</li>\r\n<li data-section-id=\"vm6q50\" data-start=\"1003\" data-end=\"1029\">Hydraulic applications</li>\r\n<li data-section-id=\"hp7htq\" data-start=\"1030\" data-end=\"1064\">Industrial fluid and gas lines</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1779974081_a71f3b5ba1ffae7fb93f6a8ab89286d5.jpg', 1, 15, '2026-05-28 13:14:41'),
(16, 1, 'SP-18', 'Brass Seal Plug', '<p data-start=\"1092\" data-end=\"1420\">Our Brass NPT Plugs are precision-engineered from high-grade brass material to deliver reliable sealing and strong performance in threaded piping systems. Designed with accurate NPT threading, these plugs provide tight, leak-free closure for unused pipe openings in industrial, plumbing, pneumatic, and hydraulic applications.</p>\r\n<p data-start=\"1422\" data-end=\"1684\">These plugs offer superior durability, corrosion resistance, and excellent pressure handling capability, making them suitable for both residential and industrial environments. Their precision finish ensures easy installation and dependable long-term performance.</p>\r\n<h3 data-section-id=\"q4c1cr\" data-start=\"1686\" data-end=\"1699\">Features:</h3>\r\n<ul data-start=\"1700\" data-end=\"1943\">\r\n<li data-section-id=\"14v17qw\" data-start=\"1700\" data-end=\"1743\">Manufactured from premium quality brass</li>\r\n<li data-section-id=\"1cad0qi\" data-start=\"1744\" data-end=\"1789\">High corrosion and temperature resistance</li>\r\n<li data-section-id=\"e0qg4q\" data-start=\"1790\" data-end=\"1834\">Precision NPT threads for secure fitting</li>\r\n<li data-section-id=\"1fdi069\" data-start=\"1835\" data-end=\"1869\">Leak-proof sealing performance</li>\r\n<li data-section-id=\"3usy2s\" data-start=\"1870\" data-end=\"1905\">Strong and durable construction</li>\r\n<li data-section-id=\"f8hmgc\" data-start=\"1906\" data-end=\"1943\">Easy installation and maintenance</li>\r\n</ul>\r\n<h3 data-section-id=\"5ms9th\" data-start=\"1945\" data-end=\"1962\">Applications:</h3>\r\n<ul data-start=\"1963\" data-end=\"2110\" data-is-last-node=\"\" data-is-only-node=\"\">\r\n<li data-section-id=\"1dqrzjs\" data-start=\"1963\" data-end=\"1983\">Plumbing systems</li>\r\n<li data-section-id=\"1sisibv\" data-start=\"1984\" data-end=\"2005\">Hydraulic systems</li>\r\n<li data-section-id=\"1gveurh\" data-start=\"2006\" data-end=\"2032\">Pneumatic applications</li>\r\n<li data-section-id=\"1wnakop\" data-start=\"2033\" data-end=\"2067\">Gas and fluid transfer systems</li>\r\n<li data-section-id=\"244epc\" data-start=\"2068\" data-end=\"2110\" data-is-last-node=\"\">Industrial and mechanical piping systems</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1779974205_5154aae6144df4dcd335979d7886a796.jpg', 1, 16, '2026-05-28 13:16:45'),
(17, 3, 'FE-34', 'Brass Flare Elbow', '<p data-start=\"22\" data-end=\"389\">Our Brass Flare Elbows are manufactured using high-quality precision brass to provide reliable performance and efficient directional flow control in refrigeration, HVAC, pneumatic, and industrial piping systems. Designed for secure and leak-proof connections, these elbows allow smooth flow transition while changing the direction of piping with maximum efficiency.</p>\r\n<p data-start=\"391\" data-end=\"695\">With excellent corrosion resistance, durable construction, and precision flare threading, Brass Flare Elbows ensure long service life and dependable operation even in high-pressure applications. Their accurate design and premium finish make installation simple, secure, and suitable for professional use.</p>\r\n<h3 data-section-id=\"q4c1cr\" data-start=\"697\" data-end=\"710\">Features:</h3>\r\n<ul data-start=\"711\" data-end=\"976\">\r\n<li data-section-id=\"f8etxw\" data-start=\"711\" data-end=\"746\">Made from premium quality brass</li>\r\n<li data-section-id=\"61ayd2\" data-start=\"747\" data-end=\"785\">High corrosion and rust resistance</li>\r\n<li data-section-id=\"14s1t9y\" data-start=\"786\" data-end=\"823\">Leak-proof and durable connection</li>\r\n<li data-section-id=\"1vv3w0s\" data-start=\"824\" data-end=\"870\">Precision flare threads for secure fitting</li>\r\n<li data-section-id=\"1hn0a5k\" data-start=\"871\" data-end=\"910\">Smooth directional flow performance</li>\r\n<li data-section-id=\"r2xkgs\" data-start=\"911\" data-end=\"976\">Suitable for refrigeration, HVAC, and industrial applications</li>\r\n</ul>\r\n<h3 data-section-id=\"5ms9th\" data-start=\"978\" data-end=\"995\">Applications:</h3>\r\n<ul data-start=\"996\" data-end=\"1155\" data-is-last-node=\"\" data-is-only-node=\"\">\r\n<li data-section-id=\"ca3ot5\" data-start=\"996\" data-end=\"1021\">Refrigeration systems</li>\r\n<li data-section-id=\"u4z2dw\" data-start=\"1022\" data-end=\"1048\">Air conditioning units</li>\r\n<li data-section-id=\"cw3xbw\" data-start=\"1049\" data-end=\"1084\">Hydraulic and pneumatic systems</li>\r\n<li data-section-id=\"1w3bw17\" data-start=\"1085\" data-end=\"1120\">Industrial fluid transfer lines</li>\r\n<li data-section-id=\"t5tsy\" data-start=\"1121\" data-end=\"1155\" data-is-last-node=\"\">Gas and pressure control systems</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1779975632_3e27891c6c67de2b91fec2c1369f874b.jpg', 1, 17, '2026-05-28 13:40:32'),
(18, 3, 'HE-31618', 'Brass Half Elbow', '<p data-start=\"32\" data-end=\"377\">Our Brass Half Elbows are manufactured using premium quality brass to provide reliable and leak-proof connections in refrigeration, HVAC, pneumatic, and industrial piping systems. Designed with FL x NPT threading, these fittings allow smooth directional flow changes while ensuring secure and durable performance in high-pressure applications.</p>\r\n<p data-start=\"379\" data-end=\"645\">With precision threads, excellent corrosion resistance, and strong construction, Brass Half Elbows are ideal for long-lasting use in demanding environments. Their compact design and accurate finish make installation easy and dependable for professional applications.</p>\r\n<h3 data-start=\"647\" data-end=\"660\">Features:</h3>\r\n<ul data-start=\"661\" data-end=\"885\">\r\n<li data-start=\"661\" data-end=\"703\">Made from high-quality precision brass</li>\r\n<li data-start=\"704\" data-end=\"736\">Corrosion and rust resistant</li>\r\n<li data-start=\"737\" data-end=\"774\">Leak-proof and durable connection</li>\r\n<li data-start=\"775\" data-end=\"807\">Precision FL x NPT threading</li>\r\n<li data-start=\"808\" data-end=\"847\">Smooth directional flow performance</li>\r\n<li data-start=\"848\" data-end=\"885\">Easy installation and maintenance</li>\r\n</ul>\r\n<h3 data-start=\"887\" data-end=\"904\">Applications:</h3>\r\n<ul data-start=\"905\" data-end=\"1051\">\r\n<li data-start=\"905\" data-end=\"930\">Refrigeration systems</li>\r\n<li data-start=\"931\" data-end=\"952\">HVAC applications</li>\r\n<li data-start=\"953\" data-end=\"988\">Pneumatic and hydraulic systems</li>\r\n<li data-start=\"989\" data-end=\"1018\">Industrial piping systems</li>\r\n<li data-start=\"1019\" data-end=\"1051\">Gas and fluid transfer lines</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780028447_4b73bce24ee6f97b9016d53ee7cbcadc.jpg', 1, 18, '2026-05-29 04:20:47'),
(19, 3, 'FRE-14516', 'Brass Flare Reducing Elbow', '<p data-start=\"1089\" data-end=\"1411\">Our Brass Flare Reducing Elbows are designed for efficient connection between different pipe sizes while providing smooth directional flow control in refrigeration and industrial systems. Manufactured from premium quality brass, these fittings ensure excellent strength, corrosion resistance, and leak-proof performance.</p>\r\n<p data-start=\"1413\" data-end=\"1649\">These reducing elbows are precision-engineered for secure flare connections and reliable operation in high-pressure applications. Their durable construction and accurate dimensions make them suitable for professional and industrial use.</p>\r\n<h3 data-start=\"1651\" data-end=\"1664\">Features:</h3>\r\n<ul data-start=\"1665\" data-end=\"1905\">\r\n<li data-start=\"1665\" data-end=\"1708\">Manufactured from premium quality brass</li>\r\n<li data-start=\"1709\" data-end=\"1759\">Allows connection between different pipe sizes</li>\r\n<li data-start=\"1760\" data-end=\"1794\">Excellent corrosion resistance</li>\r\n<li data-start=\"1795\" data-end=\"1826\">Leak-proof flare connection</li>\r\n<li data-start=\"1827\" data-end=\"1867\">Durable and long-lasting performance</li>\r\n<li data-start=\"1868\" data-end=\"1905\">Precision finish for easy fitting</li>\r\n</ul>\r\n<h3 data-start=\"1907\" data-end=\"1924\">Applications:</h3>\r\n<ul data-start=\"1925\" data-end=\"2085\">\r\n<li data-start=\"1925\" data-end=\"1950\">Refrigeration systems</li>\r\n<li data-start=\"1951\" data-end=\"1977\">Air conditioning units</li>\r\n<li data-start=\"1978\" data-end=\"2013\">Hydraulic and pneumatic systems</li>\r\n<li data-start=\"2014\" data-end=\"2056\">Industrial fluid transfer applications</li>\r\n<li data-start=\"2057\" data-end=\"2085\">Gas and pressure systems</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780028716_582191938f9b27446e8a8739b5434bcf.jpg', 1, 19, '2026-05-29 04:25:16'),
(20, 3, 'FRT-1438', 'Brass Flare Reducing Tee', '<p data-start=\"2121\" data-end=\"2420\">Our Brass Flare Reducing Tees are precision-manufactured to provide efficient branch connections between different pipe sizes in refrigeration, HVAC, and industrial piping systems. Made from high-grade brass, these fittings offer strong construction, reliable sealing, and long-lasting durability.</p>\r\n<p data-start=\"2422\" data-end=\"2616\">Designed for smooth fluid distribution and secure flare connections, Brass Flare Reducing Tees ensure excellent performance under high-pressure conditions while maintaining leak-proof operation.</p>\r\n<h3 data-start=\"2618\" data-end=\"2631\">Features:</h3>\r\n<ul data-start=\"2632\" data-end=\"2870\">\r\n<li data-start=\"2632\" data-end=\"2667\">Made from premium quality brass</li>\r\n<li data-start=\"2668\" data-end=\"2712\">Connects multiple pipe sizes efficiently</li>\r\n<li data-start=\"2713\" data-end=\"2755\">Strong and leak-proof flare connection</li>\r\n<li data-start=\"2756\" data-end=\"2788\">Corrosion and rust resistant</li>\r\n<li data-start=\"2789\" data-end=\"2832\">Durable construction for industrial use</li>\r\n<li data-start=\"2833\" data-end=\"2870\">Easy installation and maintenance</li>\r\n</ul>\r\n<h3 data-start=\"2872\" data-end=\"2889\">Applications:</h3>\r\n<ul data-start=\"2890\" data-end=\"3039\">\r\n<li data-start=\"2890\" data-end=\"2915\">Refrigeration systems</li>\r\n<li data-start=\"2916\" data-end=\"2937\">HVAC applications</li>\r\n<li data-start=\"2938\" data-end=\"2973\">Pneumatic and hydraulic systems</li>\r\n<li data-start=\"2974\" data-end=\"3004\">Industrial piping networks</li>\r\n<li data-start=\"3005\" data-end=\"3039\">Gas and fluid transfer systems</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780028931_87d9865b1708898635113594bf9cf9d8.jpg', 1, 20, '2026-05-29 04:28:51'),
(21, 3, 'HT-1418', 'Brass Half Tee', '<p data-start=\"3081\" data-end=\"3367\">Our Brass Half Tees are designed to provide reliable three-way connections in refrigeration, HVAC, and industrial piping systems. Manufactured from high-quality brass, these fittings ensure secure sealing, excellent durability, and smooth fluid distribution in demanding applications.</p>\r\n<p data-start=\"3369\" data-end=\"3521\">With precision FL x NPT x FL threading, these tees offer strong and leak-proof performance while maintaining long service life and corrosion resistance.</p>\r\n<h3 data-start=\"3523\" data-end=\"3536\">Features:</h3>\r\n<ul data-start=\"3537\" data-end=\"3750\">\r\n<li data-start=\"3537\" data-end=\"3575\">Premium quality brass construction</li>\r\n<li data-start=\"3576\" data-end=\"3613\">Precision FL x NPT x FL threading</li>\r\n<li data-start=\"3614\" data-end=\"3652\">Leak-proof and durable performance</li>\r\n<li data-start=\"3653\" data-end=\"3687\">Excellent corrosion resistance</li>\r\n<li data-start=\"3688\" data-end=\"3717\">Smooth fluid distribution</li>\r\n<li data-start=\"3718\" data-end=\"3750\">Easy and secure installation</li>\r\n</ul>\r\n<h3 data-start=\"3752\" data-end=\"3769\">Applications:</h3>\r\n<ul data-start=\"3770\" data-end=\"3921\">\r\n<li data-start=\"3770\" data-end=\"3795\">Refrigeration systems</li>\r\n<li data-start=\"3796\" data-end=\"3822\">Air conditioning units</li>\r\n<li data-start=\"3823\" data-end=\"3858\">Hydraulic and pneumatic systems</li>\r\n<li data-start=\"3859\" data-end=\"3888\">Industrial piping systems</li>\r\n<li data-start=\"3889\" data-end=\"3921\">Gas and fluid transfer lines</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780029253_d90491805c727c7f13dce3eb6e21e556.jpg', 1, 21, '2026-05-29 04:34:13');
INSERT INTO `product` (`id`, `category_id`, `code`, `name`, `description`, `image`, `active`, `display_order`, `created_at`) VALUES
(22, 3, 'FT-14', 'Brass Flare Tee', '<p data-start=\"3948\" data-end=\"4235\">Our Brass Flare Tees are precision-engineered for efficient three-way flow distribution in refrigeration, HVAC, and industrial systems. Manufactured from premium brass material, these fittings provide secure flare connections, excellent durability, and reliable leak-proof performance.</p>\r\n<p data-start=\"4237\" data-end=\"4391\">Designed for smooth flow control and long-lasting operation, Brass Flare Tees are suitable for high-pressure applications and professional industrial use.</p>\r\n<h3 data-start=\"4393\" data-end=\"4406\">Features:</h3>\r\n<ul data-start=\"4407\" data-end=\"4649\">\r\n<li data-start=\"4407\" data-end=\"4439\">Made from high-quality brass</li>\r\n<li data-start=\"4440\" data-end=\"4482\">Strong and leak-proof flare connection</li>\r\n<li data-start=\"4483\" data-end=\"4515\">Corrosion and rust resistant</li>\r\n<li data-start=\"4516\" data-end=\"4562\">Durable construction for long service life</li>\r\n<li data-start=\"4563\" data-end=\"4605\">Precision design for easy installation</li>\r\n<li data-start=\"4606\" data-end=\"4649\">Suitable for high-pressure applications</li>\r\n</ul>\r\n<h3 data-start=\"4651\" data-end=\"4668\">Applications:</h3>\r\n<ul data-start=\"4669\" data-end=\"4815\" data-is-last-node=\"\" data-is-only-node=\"\">\r\n<li data-start=\"4669\" data-end=\"4694\">Refrigeration systems</li>\r\n<li data-start=\"4695\" data-end=\"4711\">HVAC systems</li>\r\n<li data-start=\"4712\" data-end=\"4747\">Pneumatic and hydraulic systems</li>\r\n<li data-start=\"4748\" data-end=\"4782\">Industrial piping applications</li>\r\n<li data-start=\"4783\" data-end=\"4815\" data-is-last-node=\"\">Gas and fluid transfer systems</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780029437_c582518ee2a999cee777bd666d0350ba.jpg', 1, 22, '2026-05-29 04:37:17'),
(23, 4, 'CA14FF14F', 'Brass Charging Adapter', '<p data-start=\"27\" data-end=\"353\">Our Brass Charging Adapters are precision-manufactured from high-quality brass to provide secure and efficient connections in refrigeration, HVAC, and air conditioning systems. Designed for reliable charging and servicing operations, these adapters ensure leak-proof performance, excellent durability, and long service life.</p>\r\n<p data-start=\"355\" data-end=\"548\">With accurate threading and corrosion-resistant construction, Brass Charging Adapters are ideal for professional applications requiring dependable pressure handling and smooth refrigerant flow.</p>\r\n<h3 data-start=\"550\" data-end=\"563\">Features:</h3>\r\n<ul data-start=\"564\" data-end=\"801\">\r\n<li data-start=\"564\" data-end=\"599\">Made from premium quality brass</li>\r\n<li data-start=\"600\" data-end=\"637\">Leak-proof and durable connection</li>\r\n<li data-start=\"638\" data-end=\"680\">Precision threading for secure fitting</li>\r\n<li data-start=\"681\" data-end=\"715\">Excellent corrosion resistance</li>\r\n<li data-start=\"716\" data-end=\"763\">Suitable for refrigeration and HVAC systems</li>\r\n<li data-start=\"764\" data-end=\"801\">Easy installation and maintenance</li>\r\n</ul>\r\n<h3 data-start=\"803\" data-end=\"820\">Applications:</h3>\r\n<ul data-start=\"821\" data-end=\"975\">\r\n<li data-start=\"821\" data-end=\"846\">Refrigeration systems</li>\r\n<li data-start=\"847\" data-end=\"877\">Air conditioning servicing</li>\r\n<li data-start=\"878\" data-end=\"911\">HVAC maintenance applications</li>\r\n<li data-start=\"912\" data-end=\"944\">Refrigerant charging systems</li>\r\n<li data-start=\"945\" data-end=\"975\">Industrial cooling systems</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780029663_d74e77923db58d23a22883320ee9be01.jpg', 1, 24, '2026-05-29 04:41:03'),
(24, 4, 'CV-14', 'Brass Charging Valve - Access Valve', '<p data-start=\"1022\" data-end=\"1309\">Our Brass Charging Valves are designed for safe and efficient refrigerant charging, recovery, and servicing applications. Manufactured using high-grade brass, these valves provide reliable sealing, smooth operation, and excellent durability in refrigeration and air conditioning systems.</p>\r\n<h3 data-start=\"1311\" data-end=\"1324\">Features:</h3>\r\n<ul data-start=\"1325\" data-end=\"1516\">\r\n<li data-start=\"1325\" data-end=\"1355\">Premium brass construction</li>\r\n<li data-start=\"1356\" data-end=\"1387\">Reliable leak-proof sealing</li>\r\n<li data-start=\"1388\" data-end=\"1418\">Corrosion resistant finish</li>\r\n<li data-start=\"1419\" data-end=\"1453\">Smooth and efficient operation</li>\r\n<li data-start=\"1454\" data-end=\"1494\">Durable and long-lasting performance</li>\r\n<li data-start=\"1495\" data-end=\"1516\">Easy installation</li>\r\n</ul>\r\n<h3 data-start=\"1518\" data-end=\"1535\">Applications:</h3>\r\n<ul data-start=\"1536\" data-end=\"1670\">\r\n<li data-start=\"1536\" data-end=\"1552\">HVAC systems</li>\r\n<li data-start=\"1553\" data-end=\"1580\">Refrigeration servicing</li>\r\n<li data-start=\"1581\" data-end=\"1618\">Refrigerant charging applications</li>\r\n<li data-start=\"1619\" data-end=\"1638\">Cooling systems</li>\r\n<li data-start=\"1639\" data-end=\"1670\">Industrial maintenance work</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780029735_405032506a1e1a1a6c9a301558e5709b.jpg', 1, 25, '2026-05-29 04:42:15'),
(25, 4, 'CARA-1414', 'Brass Carrier Adapter', '<p data-start=\"1703\" data-end=\"2001\">Our Brass Carrier Adapters are manufactured with precision brass material to ensure secure and dependable connections in refrigeration and air conditioning systems. Designed for durability and leak-proof performance, these adapters provide efficient compatibility between different system fittings.</p>\r\n<h3 data-start=\"2003\" data-end=\"2016\">Features:</h3>\r\n<ul data-start=\"2017\" data-end=\"2199\">\r\n<li data-start=\"2017\" data-end=\"2052\">High-quality brass construction</li>\r\n<li data-start=\"2053\" data-end=\"2088\">Precision threading and fitment</li>\r\n<li data-start=\"2089\" data-end=\"2115\">Leak-proof performance</li>\r\n<li data-start=\"2116\" data-end=\"2148\">Corrosion and rust resistant</li>\r\n<li data-start=\"2149\" data-end=\"2179\">Durable for industrial use</li>\r\n<li data-start=\"2180\" data-end=\"2199\">Easy to install</li>\r\n</ul>\r\n<h3 data-start=\"2201\" data-end=\"2218\">Applications:</h3>\r\n<ul data-start=\"2219\" data-end=\"2361\">\r\n<li data-start=\"2219\" data-end=\"2244\">Refrigeration systems</li>\r\n<li data-start=\"2245\" data-end=\"2273\">Air conditioning systems</li>\r\n<li data-start=\"2274\" data-end=\"2292\">HVAC servicing</li>\r\n<li data-start=\"2293\" data-end=\"2328\">Industrial cooling applications</li>\r\n<li data-start=\"2329\" data-end=\"2361\">Refrigerant transfer systems</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780029834_2e67bc93a105e7a9ac08b43dfd81d3e1.jpg', 1, 25, '2026-05-29 04:43:54'),
(26, 4, 'NC-14', 'Brass Nurling Cap/Nurling Cap Type', '<p data-start=\"2413\" data-end=\"2695\">Our Brass Nurling Caps are designed for secure sealing and easy handling in refrigeration and industrial systems. Manufactured from premium quality brass, these caps provide excellent durability, corrosion resistance, and reliable protection for service ports and valve connections.</p>\r\n<h3 data-start=\"2697\" data-end=\"2710\">Features:</h3>\r\n<ul data-start=\"2711\" data-end=\"2886\">\r\n<li data-start=\"2711\" data-end=\"2737\">Premium brass material</li>\r\n<li data-start=\"2738\" data-end=\"2773\">Strong and durable construction</li>\r\n<li data-start=\"2774\" data-end=\"2804\">Corrosion resistant finish</li>\r\n<li data-start=\"2805\" data-end=\"2835\">Secure sealing performance</li>\r\n<li data-start=\"2836\" data-end=\"2864\">Easy grip knurled design</li>\r\n<li data-start=\"2865\" data-end=\"2886\">Long service life</li>\r\n</ul>\r\n<h3 data-start=\"2888\" data-end=\"2905\">Applications:</h3>\r\n<ul data-start=\"2906\" data-end=\"3029\">\r\n<li data-start=\"2906\" data-end=\"2931\">Refrigeration systems</li>\r\n<li data-start=\"2932\" data-end=\"2953\">HVAC applications</li>\r\n<li data-start=\"2954\" data-end=\"2974\">Valve protection</li>\r\n<li data-start=\"2975\" data-end=\"3004\">Industrial piping systems</li>\r\n<li data-start=\"3005\" data-end=\"3029\">Service port sealing</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780029972_22fb8c77657c2447042077e1d31a6464.jpg', 1, 26, '2026-05-29 04:46:12'),
(27, 4, 'CUP-14F18BSP', 'Brass Charging Union With Pin', '<p data-start=\"3070\" data-end=\"3342\">Our Brass Charging Unions with Pin are engineered for efficient refrigerant charging and secure system connections in HVAC and refrigeration applications. Made from high-quality brass, these unions ensure leak-proof performance, durability, and reliable pressure handling.</p>\r\n<h3 data-start=\"3344\" data-end=\"3357\">Features:</h3>\r\n<ul data-start=\"3358\" data-end=\"3549\">\r\n<li data-start=\"3358\" data-end=\"3387\">Made from precision brass</li>\r\n<li data-start=\"3388\" data-end=\"3419\">Leak-proof union connection</li>\r\n<li data-start=\"3420\" data-end=\"3453\">Excellent pressure resistance</li>\r\n<li data-start=\"3454\" data-end=\"3490\">Corrosion resistant construction</li>\r\n<li data-start=\"3491\" data-end=\"3527\">Durable and reliable performance</li>\r\n<li data-start=\"3528\" data-end=\"3549\">Easy installation</li>\r\n</ul>\r\n<h3 data-start=\"3551\" data-end=\"3568\">Applications:</h3>\r\n<ul data-start=\"3569\" data-end=\"3715\">\r\n<li data-start=\"3569\" data-end=\"3596\">Refrigeration servicing</li>\r\n<li data-start=\"3597\" data-end=\"3625\">Air conditioning systems</li>\r\n<li data-start=\"3626\" data-end=\"3646\">HVAC maintenance</li>\r\n<li data-start=\"3647\" data-end=\"3679\">Refrigerant charging systems</li>\r\n<li data-start=\"3680\" data-end=\"3715\">Industrial cooling applications</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780030096_8d41e8565697e74b96fae2c47aa4b8e2.jpg', 1, 27, '2026-05-29 04:48:16'),
(28, 4, 'NU-1458', 'Brass Nitrogen Union', '<p data-start=\"3747\" data-end=\"4005\">Our Brass Nitrogen Unions are designed for secure and reliable nitrogen flow connections in refrigeration and industrial systems. Manufactured using high-quality brass, these unions offer excellent durability, precision fitment, and long-lasting performance.</p>\r\n<h3 data-start=\"4007\" data-end=\"4020\">Features:</h3>\r\n<ul data-start=\"4021\" data-end=\"4210\">\r\n<li data-start=\"4021\" data-end=\"4059\">Premium quality brass construction</li>\r\n<li data-start=\"4060\" data-end=\"4085\">Leak-proof connection</li>\r\n<li data-start=\"4086\" data-end=\"4118\">Corrosion resistant material</li>\r\n<li data-start=\"4119\" data-end=\"4153\">Durable and strong performance</li>\r\n<li data-start=\"4154\" data-end=\"4177\">Precision threading</li>\r\n<li data-start=\"4178\" data-end=\"4210\">Easy fitting and maintenance</li>\r\n</ul>\r\n<h3 data-start=\"4212\" data-end=\"4229\">Applications:</h3>\r\n<ul data-start=\"4230\" data-end=\"4369\">\r\n<li data-start=\"4230\" data-end=\"4259\">Nitrogen charging systems</li>\r\n<li data-start=\"4260\" data-end=\"4289\">Refrigeration maintenance</li>\r\n<li data-start=\"4290\" data-end=\"4308\">HVAC servicing</li>\r\n<li data-start=\"4309\" data-end=\"4335\">Industrial gas systems</li>\r\n<li data-start=\"4336\" data-end=\"4369\">Pressure testing applications</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780030166_e38089e7ab2c9e37d67c4872e71e83a6.jpg', 1, 28, '2026-05-29 04:49:26'),
(29, 4, 'CNOPC- 14F18N', 'Brass Charging Nipple Only With Pin & Cap', '<p data-start=\"4422\" data-end=\"4692\">Our Brass Charging Nipples with Pin &amp; Cap are precision-engineered for efficient refrigerant charging and servicing applications. These fittings provide secure and leak-proof performance while ensuring durability and reliable operation in refrigeration and HVAC systems.</p>\r\n<h3 data-start=\"4694\" data-end=\"4707\">Features:</h3>\r\n<ul data-start=\"4708\" data-end=\"4889\">\r\n<li data-start=\"4708\" data-end=\"4739\">High-quality brass material</li>\r\n<li data-start=\"4740\" data-end=\"4774\">Leak-proof sealing performance</li>\r\n<li data-start=\"4775\" data-end=\"4805\">Durable pin and cap design</li>\r\n<li data-start=\"4806\" data-end=\"4836\">Corrosion resistant finish</li>\r\n<li data-start=\"4837\" data-end=\"4867\">Reliable pressure handling</li>\r\n<li data-start=\"4868\" data-end=\"4889\">Easy installation</li>\r\n</ul>\r\n<h3 data-start=\"4891\" data-end=\"4908\">Applications:</h3>\r\n<ul data-start=\"4909\" data-end=\"5055\">\r\n<li data-start=\"4909\" data-end=\"4934\">Refrigeration systems</li>\r\n<li data-start=\"4935\" data-end=\"4953\">HVAC servicing</li>\r\n<li data-start=\"4954\" data-end=\"4991\">Refrigerant charging applications</li>\r\n<li data-start=\"4992\" data-end=\"5024\">Air conditioning maintenance</li>\r\n<li data-start=\"5025\" data-end=\"5055\">Industrial cooling systems</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780030262_86a7e36e18fcefd454fd5fd753b1339e.jpg', 1, 29, '2026-05-29 04:51:02'),
(30, 4, 'SV-14', 'Air Conditioner Valve - Straight & L Type', '<p data-start=\"5108\" data-end=\"5374\">Our Air Conditioner Valves are designed for smooth refrigerant flow control and reliable system performance in air conditioning and refrigeration applications. Manufactured from durable brass material, these valves provide leak-proof operation and long service life.</p>\r\n<h3 data-start=\"5376\" data-end=\"5389\">Features:</h3>\r\n<ul data-start=\"5390\" data-end=\"5595\">\r\n<li data-start=\"5390\" data-end=\"5420\">Durable brass construction</li>\r\n<li data-start=\"5421\" data-end=\"5463\">Available in Straight &amp; L Type designs</li>\r\n<li data-start=\"5464\" data-end=\"5501\">Reliable flow control performance</li>\r\n<li data-start=\"5502\" data-end=\"5532\">Corrosion resistant finish</li>\r\n<li data-start=\"5533\" data-end=\"5557\">Leak-proof operation</li>\r\n<li data-start=\"5558\" data-end=\"5595\">Easy installation and maintenance</li>\r\n</ul>\r\n<h3 data-start=\"5597\" data-end=\"5614\">Applications:</h3>\r\n<ul data-start=\"5615\" data-end=\"5744\">\r\n<li data-start=\"5615\" data-end=\"5643\">Air conditioning systems</li>\r\n<li data-start=\"5644\" data-end=\"5667\">Refrigeration units</li>\r\n<li data-start=\"5668\" data-end=\"5684\">HVAC systems</li>\r\n<li data-start=\"5685\" data-end=\"5709\">Cooling applications</li>\r\n<li data-start=\"5710\" data-end=\"5744\">Industrial refrigerant systems</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780030375_91eecfd92900d9b976bdb77231a2151c.jpg', 1, 30, '2026-05-29 04:52:55'),
(31, 4, '410-A', '410 Adaptor', '<p data-start=\"5767\" data-end=\"6023\">Our 410 Adaptors are precision-designed for safe and efficient refrigerant charging and servicing applications. Manufactured from high-quality brass, these adaptors provide excellent compatibility, secure fitting, and reliable performance for R410 systems.</p>\r\n<h3 data-start=\"6025\" data-end=\"6038\">Features:</h3>\r\n<ul data-start=\"6039\" data-end=\"6244\">\r\n<li data-start=\"6039\" data-end=\"6073\">Premium quality brass material</li>\r\n<li data-start=\"6074\" data-end=\"6105\">Secure and accurate fitment</li>\r\n<li data-start=\"6106\" data-end=\"6142\">Corrosion resistant construction</li>\r\n<li data-start=\"6143\" data-end=\"6181\">Durable and leak-proof performance</li>\r\n<li data-start=\"6182\" data-end=\"6216\">Suitable for R410 applications</li>\r\n<li data-start=\"6217\" data-end=\"6244\">Easy to use and install</li>\r\n</ul>\r\n<h3 data-start=\"6246\" data-end=\"6263\">Applications:</h3>\r\n<ul data-start=\"6264\" data-end=\"6415\">\r\n<li data-start=\"6264\" data-end=\"6297\">R410 air conditioning systems</li>\r\n<li data-start=\"6298\" data-end=\"6316\">HVAC servicing</li>\r\n<li data-start=\"6317\" data-end=\"6346\">Refrigeration maintenance</li>\r\n<li data-start=\"6347\" data-end=\"6379\">Refrigerant charging systems</li>\r\n<li data-start=\"6380\" data-end=\"6415\">Industrial cooling applications</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780030444_da84e130200b2c506ed36183de5bf73e.jpg', 1, 31, '2026-05-29 04:54:04'),
(32, 4, 'AB 480', 'Air Conditioner Bracket', '<p data-start=\"6450\" data-end=\"6715\">Our Air Conditioner Brackets are manufactured for strong and reliable support of air conditioning outdoor units. Designed for durability and stability, these brackets provide safe installation and long-lasting performance in residential and commercial applications.</p>\r\n<h3 data-start=\"6717\" data-end=\"6730\">Features:</h3>\r\n<ul data-start=\"6731\" data-end=\"6920\">\r\n<li data-start=\"6731\" data-end=\"6766\">Strong and durable construction</li>\r\n<li data-start=\"6767\" data-end=\"6797\">Corrosion resistant finish</li>\r\n<li data-start=\"6798\" data-end=\"6828\">High load-bearing capacity</li>\r\n<li data-start=\"6829\" data-end=\"6859\">Stable and secure mounting</li>\r\n<li data-start=\"6860\" data-end=\"6881\">Easy installation</li>\r\n<li data-start=\"6882\" data-end=\"6920\">Suitable for various AC unit sizes</li>\r\n</ul>\r\n<h3 data-start=\"6922\" data-end=\"6939\">Applications:</h3>\r\n<ul data-start=\"6940\" data-end=\"7112\">\r\n<li data-start=\"6940\" data-end=\"6980\">Residential air conditioning systems</li>\r\n<li data-start=\"6981\" data-end=\"7014\">Commercial HVAC installations</li>\r\n<li data-start=\"7015\" data-end=\"7040\">Outdoor unit mounting</li>\r\n<li data-start=\"7041\" data-end=\"7076\">Wall-mounted AC support systems</li>\r\n<li data-start=\"7077\" data-end=\"7112\">Industrial cooling applications</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780030517_0972abfbaa8d3f70dd7588418ccba7d8.jpg', 1, 32, '2026-05-29 04:55:17'),
(33, 4, 'AV 14F 14N', 'Brass Angle Packed Received Valve', '<p data-start=\"7157\" data-end=\"7447\">Our Brass Angle Packed Received Valves are precision-manufactured to provide efficient refrigerant flow control and secure system connections in refrigeration and HVAC applications. These valves ensure leak-proof performance, durability, and smooth operation under high-pressure conditions.</p>\r\n<h3 data-start=\"7449\" data-end=\"7462\">Features:</h3>\r\n<ul data-start=\"7463\" data-end=\"7667\">\r\n<li data-start=\"7463\" data-end=\"7493\">Premium brass construction</li>\r\n<li data-start=\"7494\" data-end=\"7528\">Leak-proof sealing performance</li>\r\n<li data-start=\"7529\" data-end=\"7561\">Precision angle valve design</li>\r\n<li data-start=\"7562\" data-end=\"7592\">Corrosion resistant finish</li>\r\n<li data-start=\"7593\" data-end=\"7631\">Durable and long-lasting operation</li>\r\n<li data-start=\"7632\" data-end=\"7667\">Smooth refrigerant flow control</li>\r\n</ul>\r\n<h3 data-start=\"7669\" data-end=\"7686\">Applications:</h3>\r\n<ul data-start=\"7687\" data-end=\"7826\">\r\n<li data-start=\"7687\" data-end=\"7712\">Refrigeration systems</li>\r\n<li data-start=\"7713\" data-end=\"7734\">HVAC applications</li>\r\n<li data-start=\"7735\" data-end=\"7763\">Air conditioning systems</li>\r\n<li data-start=\"7764\" data-end=\"7794\">Industrial cooling systems</li>\r\n<li data-start=\"7795\" data-end=\"7826\">Refrigerant control systems</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780030684_18e822202b48c4cfcb77525e79b14d7f.jpg', 1, 33, '2026-05-29 04:58:04'),
(34, 4, 'CTV-14', 'Brass Can Tap Valve', '<p data-start=\"7857\" data-end=\"8096\">Our Brass Can Tap Valves are designed for safe and efficient refrigerant can access and charging operations. Manufactured using high-quality brass, these valves provide secure fitment, smooth performance, and reliable leak-proof operation.</p>\r\n<h3 data-start=\"8098\" data-end=\"8111\">Features:</h3>\r\n<ul data-start=\"8112\" data-end=\"8300\">\r\n<li data-start=\"8112\" data-end=\"8143\">High-quality brass material</li>\r\n<li data-start=\"8144\" data-end=\"8181\">Durable and reliable construction</li>\r\n<li data-start=\"8182\" data-end=\"8216\">Leak-proof sealing performance</li>\r\n<li data-start=\"8217\" data-end=\"8248\">Easy refrigerant can access</li>\r\n<li data-start=\"8249\" data-end=\"8279\">Corrosion resistant finish</li>\r\n<li data-start=\"8280\" data-end=\"8300\">Smooth operation</li>\r\n</ul>\r\n<h3 data-start=\"8302\" data-end=\"8319\">Applications:</h3>\r\n<ul data-start=\"8320\" data-end=\"8466\">\r\n<li data-start=\"8320\" data-end=\"8357\">Refrigerant charging applications</li>\r\n<li data-start=\"8358\" data-end=\"8376\">HVAC servicing</li>\r\n<li data-start=\"8377\" data-end=\"8406\">Refrigeration maintenance</li>\r\n<li data-start=\"8407\" data-end=\"8435\">Air conditioning systems</li>\r\n<li data-start=\"8436\" data-end=\"8466\">Industrial cooling systems</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780030743_b6662d71c408718e2c3dd1b77d0852fc.jpg', 1, 34, '2026-05-29 04:59:03'),
(35, 4, 'HCV-14', 'Hydro Carbon Valve', '<p data-start=\"8496\" data-end=\"8765\">Our Hydro Carbon Valves are precision-engineered for reliable refrigerant flow control in hydrocarbon-based refrigeration systems. Manufactured from premium brass material, these valves provide excellent durability, corrosion resistance, and secure sealing performance.</p>\r\n<h3 data-start=\"8767\" data-end=\"8780\">Features:</h3>\r\n<ul data-start=\"8781\" data-end=\"8982\">\r\n<li data-start=\"8781\" data-end=\"8819\">Premium quality brass construction</li>\r\n<li data-start=\"8820\" data-end=\"8857\">Reliable flow control performance</li>\r\n<li data-start=\"8858\" data-end=\"8880\">Leak-proof sealing</li>\r\n<li data-start=\"8881\" data-end=\"8911\">Corrosion resistant finish</li>\r\n<li data-start=\"8912\" data-end=\"8944\">Durable and long-lasting use</li>\r\n<li data-start=\"8945\" data-end=\"8982\">Easy installation and maintenance</li>\r\n</ul>\r\n<h3 data-start=\"8984\" data-end=\"9001\">Applications:</h3>\r\n<ul data-start=\"9002\" data-end=\"9159\">\r\n<li data-start=\"9002\" data-end=\"9039\">Hydrocarbon refrigeration systems</li>\r\n<li data-start=\"9040\" data-end=\"9061\">HVAC applications</li>\r\n<li data-start=\"9062\" data-end=\"9094\">Refrigerant charging systems</li>\r\n<li data-start=\"9095\" data-end=\"9130\">Industrial cooling applications</li>\r\n<li data-start=\"9131\" data-end=\"9159\">Gas flow control systems</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780030804_945ccfb10b37e2975735ece9a8e1704b.jpg', 1, 35, '2026-05-29 05:00:04'),
(36, 4, 'O-49', 'Orifice', '<p data-start=\"9178\" data-end=\"9448\">Our Brass Orifices are designed for accurate refrigerant flow regulation and efficient system performance in refrigeration and air conditioning applications. Manufactured with precision engineering, these orifices ensure consistent flow control and dependable operation.</p>\r\n<h3 data-start=\"9450\" data-end=\"9463\">Features:</h3>\r\n<ul data-start=\"9464\" data-end=\"9663\">\r\n<li data-start=\"9464\" data-end=\"9497\">Precision-manufactured design</li>\r\n<li data-start=\"9498\" data-end=\"9535\">Accurate refrigerant flow control</li>\r\n<li data-start=\"9536\" data-end=\"9566\">Durable brass construction</li>\r\n<li data-start=\"9567\" data-end=\"9599\">Corrosion resistant material</li>\r\n<li data-start=\"9600\" data-end=\"9641\">Reliable and long-lasting performance</li>\r\n<li data-start=\"9642\" data-end=\"9663\">Easy installation</li>\r\n</ul>\r\n<h3 data-start=\"9665\" data-end=\"9682\">Applications:</h3>\r\n<ul data-start=\"9683\" data-end=\"9815\" data-is-last-node=\"\" data-is-only-node=\"\">\r\n<li data-start=\"9683\" data-end=\"9708\">Refrigeration systems</li>\r\n<li data-start=\"9709\" data-end=\"9735\">Air conditioning units</li>\r\n<li data-start=\"9736\" data-end=\"9757\">HVAC applications</li>\r\n<li data-start=\"9758\" data-end=\"9777\">Cooling systems</li>\r\n<li data-start=\"9778\" data-end=\"9815\" data-is-last-node=\"\">Industrial refrigerant flow control</li>\r\n</ul>', 'assets/imag/product/gallery/gal_1_1780030920_2af0b33f5d7e0e72fe00d1aaed9a1b87.jpg', 1, 37, '2026-05-29 05:02:00');

-- --------------------------------------------------------

--
-- Table structure for table `product_image`
--

CREATE TABLE `product_image` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image` text NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_image`
--

INSERT INTO `product_image` (`id`, `product_id`, `image`, `display_order`, `created_at`) VALUES
(1, 1, 'assets/imag/product/gallery/gal_1_1779971824_9f90b2d886536f82972b88d20248df54.jpg', 1, '2026-05-28 11:39:28'),
(2, 2, 'assets/imag/product/gallery/gal_1_1779971878_9f1dabf9cac12adce87334e825bd9d5c.jpg', 1, '2026-05-28 11:39:54'),
(3, 3, 'assets/imag/product/gallery/gal_1_1779971897_2dd28434da17304628c31ae6f452d1c2.jpg', 1, '2026-05-28 11:40:22'),
(4, 4, 'assets/imag/product/gallery/gal_1_1779971914_ef508497e996d9fbf718422ad5bd2610.jpg', 1, '2026-05-28 11:40:54'),
(5, 5, 'assets/imag/product/gallery/gal_1_1779971959_1bf71ad5a9cc20959a9767690b20c249.jpg', 1, '2026-05-28 11:41:35'),
(6, 6, 'assets/imag/product/gallery/gal_1_1779971982_2e800385648ff01c841801160044eee6.jpg', 1, '2026-05-28 11:42:07'),
(7, 7, 'assets/imag/product/gallery/gal_1_1779972004_f4ebd4f1c8683ec69c6e290c71f68ebd.jpg', 1, '2026-05-28 12:13:45'),
(8, 8, 'assets/imag/product/gallery/gal_1_1779972026_e66b9f72eeafd41e36d04cccd001c078.jpg', 1, '2026-05-28 12:18:09'),
(9, 9, 'assets/imag/product/gallery/gal_1_1779972044_18b219eac8728579be613071cde99899.jpg', 1, '2026-05-28 12:25:20'),
(10, 10, 'assets/imag/product/gallery/gal_1_1779972066_4e7b3971a7b148637edf8f79f080880c.jpg', 1, '2026-05-28 12:28:33'),
(11, 11, 'assets/imag/product/gallery/gal_1_1779972090_6f7051eefa7e87975fc1ce5bb2608024.jpg', 1, '2026-05-28 12:30:52'),
(12, 12, 'assets/imag/product/gallery/gal_1_1779972216_3a099e70d4a01ff759853be6a9f92e2c.jpg', 1, '2026-05-28 12:43:36'),
(13, 13, 'assets/imag/product/gallery/gal_1_1779972672_cf743a05801120038f7d2a24c68865f4.jpg', 1, '2026-05-28 12:51:12'),
(14, 14, 'assets/imag/product/gallery/gal_1_1779973683_473814459ce8af91def5cea78f66df4b.jpg', 1, '2026-05-28 13:08:03'),
(15, 15, 'assets/imag/product/gallery/gal_1_1779974081_a71f3b5ba1ffae7fb93f6a8ab89286d5.jpg', 1, '2026-05-28 13:14:41'),
(16, 16, 'assets/imag/product/gallery/gal_1_1779974205_5154aae6144df4dcd335979d7886a796.jpg', 1, '2026-05-28 13:16:45'),
(17, 17, 'assets/imag/product/gallery/gal_1_1779975632_3e27891c6c67de2b91fec2c1369f874b.jpg', 1, '2026-05-28 13:40:32'),
(18, 18, 'assets/imag/product/gallery/gal_1_1780028447_4b73bce24ee6f97b9016d53ee7cbcadc.jpg', 1, '2026-05-29 04:20:47'),
(19, 19, 'assets/imag/product/gallery/gal_1_1780028716_582191938f9b27446e8a8739b5434bcf.jpg', 1, '2026-05-29 04:25:16'),
(20, 20, 'assets/imag/product/gallery/gal_1_1780028931_87d9865b1708898635113594bf9cf9d8.jpg', 1, '2026-05-29 04:28:51'),
(21, 21, 'assets/imag/product/gallery/gal_1_1780029253_d90491805c727c7f13dce3eb6e21e556.jpg', 1, '2026-05-29 04:34:13'),
(22, 22, 'assets/imag/product/gallery/gal_1_1780029437_c582518ee2a999cee777bd666d0350ba.jpg', 1, '2026-05-29 04:37:17'),
(23, 23, 'assets/imag/product/gallery/gal_1_1780029663_d74e77923db58d23a22883320ee9be01.jpg', 1, '2026-05-29 04:41:03'),
(24, 24, 'assets/imag/product/gallery/gal_1_1780029735_405032506a1e1a1a6c9a301558e5709b.jpg', 1, '2026-05-29 04:42:15'),
(25, 25, 'assets/imag/product/gallery/gal_1_1780029834_2e67bc93a105e7a9ac08b43dfd81d3e1.jpg', 1, '2026-05-29 04:43:54'),
(26, 26, 'assets/imag/product/gallery/gal_1_1780029972_22fb8c77657c2447042077e1d31a6464.jpg', 1, '2026-05-29 04:46:12'),
(27, 27, 'assets/imag/product/gallery/gal_1_1780030096_8d41e8565697e74b96fae2c47aa4b8e2.jpg', 1, '2026-05-29 04:48:16'),
(28, 28, 'assets/imag/product/gallery/gal_1_1780030166_e38089e7ab2c9e37d67c4872e71e83a6.jpg', 1, '2026-05-29 04:49:26'),
(29, 29, 'assets/imag/product/gallery/gal_1_1780030262_86a7e36e18fcefd454fd5fd753b1339e.jpg', 1, '2026-05-29 04:51:02'),
(30, 30, 'assets/imag/product/gallery/gal_1_1780030375_91eecfd92900d9b976bdb77231a2151c.jpg', 1, '2026-05-29 04:52:55'),
(31, 31, 'assets/imag/product/gallery/gal_1_1780030444_da84e130200b2c506ed36183de5bf73e.jpg', 1, '2026-05-29 04:54:04'),
(32, 32, 'assets/imag/product/gallery/gal_1_1780030517_0972abfbaa8d3f70dd7588418ccba7d8.jpg', 1, '2026-05-29 04:55:17'),
(33, 33, 'assets/imag/product/gallery/gal_1_1780030684_18e822202b48c4cfcb77525e79b14d7f.jpg', 1, '2026-05-29 04:58:04'),
(34, 34, 'assets/imag/product/gallery/gal_1_1780030743_b6662d71c408718e2c3dd1b77d0852fc.jpg', 1, '2026-05-29 04:59:03'),
(35, 35, 'assets/imag/product/gallery/gal_1_1780030804_945ccfb10b37e2975735ece9a8e1704b.jpg', 1, '2026-05-29 05:00:04'),
(36, 36, 'assets/imag/product/gallery/gal_1_1780030920_2af0b33f5d7e0e72fe00d1aaed9a1b87.jpg', 1, '2026-05-29 05:02:00');

-- --------------------------------------------------------

--
-- Table structure for table `product_variation`
--

CREATE TABLE `product_variation` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `no` varchar(50) DEFAULT NULL,
  `code` varchar(100) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `size` varchar(100) NOT NULL,
  `image` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variation`
--

INSERT INTO `product_variation` (`id`, `product_id`, `no`, `code`, `name`, `size`, `image`, `display_order`, `active`, `created_at`) VALUES
(1, 1, '7', 'FN-78', '', '7/8\"', '', 0, 1, '2026-05-28 11:46:17'),
(2, 1, '6', 'FN-34', '', '3/4\"', '', 1, 1, '2026-05-28 11:46:17'),
(3, 1, '5', 'FN-58', '', '5/8\"', '', 2, 1, '2026-05-28 11:46:17'),
(4, 1, '4', 'FN-12', '', '1/2\"', '', 3, 1, '2026-05-28 11:46:17'),
(5, 1, '3', 'FN-38', '', '3/8\"', '', 4, 1, '2026-05-28 11:46:17'),
(6, 1, '2', 'FN-516', '', '5/16\"', '', 5, 1, '2026-05-28 11:46:17'),
(7, 1, '1', 'FN-14', '', '1/4\"', '', 6, 1, '2026-05-28 11:46:17'),
(8, 2, '7', 'FLN-78', '', '7/8\"', '', 0, 1, '2026-05-28 11:49:50'),
(9, 2, '6', 'FLN-34', '', '3/4\"', '', 1, 1, '2026-05-28 11:49:50'),
(10, 2, '5', 'FLN-58', '', '5/8\"', '', 2, 1, '2026-05-28 11:49:50'),
(11, 2, '4', 'FLN-12', '', '1/2\"', '', 3, 1, '2026-05-28 11:49:50'),
(12, 2, '3', 'FLN-38', '', '3/8\"', '', 4, 1, '2026-05-28 11:49:50'),
(13, 2, '2', 'FLN-516', '', '5/16\"', '', 5, 1, '2026-05-28 11:49:50'),
(14, 2, '1', 'FLN-14', '', '1/4\"', '', 6, 1, '2026-05-28 11:49:50'),
(15, 3, '3', 'FDN-38', '', '3/8\"', '', 0, 1, '2026-05-28 11:53:42'),
(16, 3, '2', 'FDN-516', '', '5/16\"', '', 1, 1, '2026-05-28 11:53:42'),
(17, 3, '4', 'FDN-12', '', '1/2\"', '', 2, 1, '2026-05-28 11:53:42'),
(18, 3, '1', 'FDN-14', '', '1/4\"', '', 3, 1, '2026-05-28 11:53:42'),
(19, 3, '6', 'FDN-34', '', '3/4\"', '', 4, 1, '2026-05-28 11:53:42'),
(20, 3, '5', 'FDN-58', '', '5/8\"', '', 5, 1, '2026-05-28 11:53:42'),
(21, 3, '7', 'FDN-78', '', '7/8\"', '', 6, 1, '2026-05-28 11:53:42'),
(22, 4, '14', 'FRN-7834', '', '7/8\" X 3/4\"', '', 0, 1, '2026-05-28 12:03:47'),
(23, 4, '13', 'FRN-7858', '', '7/8\" X 5/8\"', '', 1, 1, '2026-05-28 12:03:47'),
(24, 4, '12', 'FRN-3458', '', '3/4\" X 5/8\"', '', 2, 1, '2026-05-28 12:03:47'),
(25, 4, '11', 'FRN-3412', '', '3/4\" X 1/2\"', '', 3, 1, '2026-05-28 12:03:47'),
(26, 4, '10', 'FRN-3438', '', '3/4\" X 3/8\"', '', 4, 1, '2026-05-28 12:03:47'),
(27, 4, '9', 'FRN-34516', '', '3/4\" X 5/16\"', '', 5, 1, '2026-05-28 12:03:47'),
(28, 4, '8', 'FRN-3414', '', '3/4\" X 1/4\"', '', 6, 1, '2026-05-28 12:03:47'),
(29, 4, '7', 'FRN-5812', '', '5/8\" X 1/2\"', '', 7, 1, '2026-05-28 12:03:47'),
(30, 4, '6', 'FRN-5838', '', '5/8\" X 3/8\"', '', 8, 1, '2026-05-28 12:03:47'),
(31, 4, '5', 'FRN-5814', '', '5/8\" X 1/4\"', '', 9, 1, '2026-05-28 12:03:47'),
(32, 4, '4', 'FRN-1238', '', '1/2\" X 3/8\"', '', 10, 1, '2026-05-28 12:03:47'),
(33, 4, '3', 'FRN-1214', '', '1/2\" X 1/4\"', '', 11, 1, '2026-05-28 12:03:47'),
(34, 4, '2', 'FRN-38516', '', '3/8\" X 5/16\"', '', 12, 1, '2026-05-28 12:03:47'),
(35, 4, '1', 'FRN-3814', '', '3/8\" X 1/4\"', '', 13, 1, '2026-05-28 12:03:47'),
(36, 5, '4', 'CC-34', '', '3/4 FPT', '', 0, 1, '2026-05-28 12:04:48'),
(37, 5, '3', 'CC-58', '', '5/8 FPT', '', 1, 1, '2026-05-28 12:04:48'),
(38, 5, '2', 'CC-12', '', '1/2 FPT', '', 2, 1, '2026-05-28 12:04:48'),
(39, 5, '1', 'CC-38', '', '3/8 FPT', '', 3, 1, '2026-05-28 12:04:48'),
(40, 6, '4', 'SVC-58', '', '5/8\"', '', 0, 1, '2026-05-28 12:05:35'),
(41, 6, '3', 'SVC-12', '', '1/2\"', '', 1, 1, '2026-05-28 12:05:35'),
(42, 6, '2', 'SVC-38', '', '3/8\"', '', 2, 1, '2026-05-28 12:05:35'),
(43, 6, '1', 'SVC-14', '', '1/4\"', '', 3, 1, '2026-05-28 12:05:35'),
(44, 7, '8', 'FU-78', '', '7/8\"', '', 0, 1, '2026-05-28 12:13:45'),
(45, 7, '7', 'FU-34', '', '3/4\"', '', 1, 1, '2026-05-28 12:13:45'),
(46, 7, '6', 'FU-58', '', '5/8\"', '', 2, 1, '2026-05-28 12:13:45'),
(47, 7, '5', 'FU-12', '', '1/2\"', '', 3, 1, '2026-05-28 12:13:45'),
(48, 7, '4', 'FU-38', '', '3/8\"', '', 4, 1, '2026-05-28 12:13:45'),
(49, 7, '3', 'FU-516', '', '5/16\"', '', 5, 1, '2026-05-28 12:13:45'),
(50, 7, '2', 'FU-14', '', '1/4\"', '', 6, 1, '2026-05-28 12:13:45'),
(51, 7, '1', 'FU-316', '', '3/16\"', '', 7, 1, '2026-05-28 12:13:45'),
(52, 8, '15', 'FRU-5878', '', '5/8\" X 7/8\"', '', 0, 1, '2026-05-28 12:18:09'),
(53, 8, '14', 'FRU-5834', '', '5/8\" X 3/4\"', '', 1, 1, '2026-05-28 12:18:09'),
(54, 8, '13', 'FRU-1234', '', '1/2\" X 3/4\"', '', 2, 1, '2026-05-28 12:18:09'),
(55, 8, '12', 'FRU-1258', '', '1/2\" X 5/8\"', '', 3, 1, '2026-05-28 12:18:09'),
(56, 8, '11', 'FRU-3834', '', '1/2\" X 5/16\"', '', 4, 1, '2026-05-28 12:18:09'),
(57, 8, '10', 'FRU-3834', '', '3/8\" X 3/4\"', '', 5, 1, '2026-05-28 12:18:09'),
(58, 8, '9', 'FRU-3812', '', '3/8\" X 5/8\"', '', 6, 1, '2026-05-28 12:18:09'),
(59, 8, '8', 'FRU-3812', '', '3/8\" X 1/2\"', '', 7, 1, '2026-05-28 12:18:09'),
(60, 8, '7', 'FRU-38516', '', '3/8\" X 5/16\"', '', 8, 1, '2026-05-28 12:18:09'),
(61, 8, '6', 'FRU-1434', '', '1/4\" X 3/4\"', '', 9, 1, '2026-05-28 12:18:09'),
(62, 8, '5', 'FRU-1458', '', '1/4\" X 5/8\"', '', 10, 1, '2026-05-28 12:18:09'),
(63, 8, '4', 'FRU-1412', '', '1/4\" X 1/2\"', '', 11, 1, '2026-05-28 12:18:09'),
(64, 8, '3', 'FRU-1438', '', '1/4\" X 3/8\"', '', 12, 1, '2026-05-28 12:18:09'),
(65, 8, '2', 'FRU-14516', '', '1/4\" X 5/16', '', 13, 1, '2026-05-28 12:18:09'),
(66, 8, '1', 'FRU-31614', '', '3/16\" X 1/4\"', '', 14, 1, '2026-05-28 12:18:09'),
(67, 9, '28', 'HU-341', '', '3/4\" x 1\"', '', 0, 1, '2026-05-28 12:25:20'),
(68, 9, '27', 'HU-3434', '', '3/4\" x 3/4\"', '', 1, 1, '2026-05-28 12:25:20'),
(69, 9, '26', 'HU-3458', '', '3/4\" x 5/8\"', '', 2, 1, '2026-05-28 12:25:20'),
(70, 9, '25', 'HU-3412', '', '3/4\" x 1/2\"', '', 3, 1, '2026-05-28 12:25:20'),
(71, 9, '24', 'HU-3438', '', '3/4\" x 3/8\"', '', 4, 1, '2026-05-28 12:25:20'),
(72, 9, '23', 'HU-3414', '', '3/4\" x 1/4\"', '', 5, 1, '2026-05-28 12:25:20'),
(73, 9, '22', 'HU-581', '', '5/8\" x 1\"', '', 6, 1, '2026-05-28 12:25:20'),
(74, 9, '21', 'HU-5834', '', '5/8\" x 3/4\"', '', 7, 1, '2026-05-28 12:25:20'),
(75, 9, '20', 'HU-5858', '', '5/8\" x 5/8\"', '', 8, 1, '2026-05-28 12:25:20'),
(76, 9, '19', 'HU-5812', '', '5/8\" x 1/2\"', '', 9, 1, '2026-05-28 12:25:20'),
(77, 9, '18', 'HU-5838', '', '5/8\" x 3/8\"', '', 10, 1, '2026-05-28 12:25:20'),
(78, 9, '17', 'HU-5814', '', '5/8\" x 1/4\"', '', 11, 1, '2026-05-28 12:25:20'),
(79, 9, '16', 'HU-121', '', '1/2\" x 1\"', '', 12, 1, '2026-05-28 12:25:20'),
(80, 9, '15', 'HU-1234', '', '1/2\" x 3/4\"', '', 13, 1, '2026-05-28 12:25:20'),
(81, 9, '14', 'HU-1258', '', '1/2\" x 5/8\"', '', 14, 1, '2026-05-28 12:25:20'),
(82, 9, '13', 'HU-1212', '', '1/2\" x 1/2\"', '', 15, 1, '2026-05-28 12:25:20'),
(83, 9, '12', 'HU-1238', '', '1/2\" x 3/8\"', '', 16, 1, '2026-05-28 12:25:20'),
(84, 9, '11', 'HU-1214', '', '1/2\" x 1/4\"', '', 17, 1, '2026-05-28 12:25:20'),
(85, 9, '10', 'HU-3834', '', '3/8\" x 3/4\"', '', 18, 1, '2026-05-28 12:25:20'),
(86, 9, '9', 'HU-3858', '', '3/8\" x 5/8\"', '', 19, 1, '2026-05-28 12:25:20'),
(87, 9, '8', 'HU-3812', '', '3/8\" x 1/2\"', '', 20, 1, '2026-05-28 12:25:20'),
(88, 9, '7', 'HU-3838', '', '3/8\" x 3/8\"', '', 21, 1, '2026-05-28 12:25:20'),
(89, 9, '6', 'HU-3814', '', '3/8\" x 1/4\"', '', 22, 1, '2026-05-28 12:25:20'),
(90, 9, '5', 'HU-1434', '', '1/4\" x 3/4\"', '', 23, 1, '2026-05-28 12:25:20'),
(91, 9, '4', 'HU-1458', '', '1/4\" x 5/8\"', '', 24, 1, '2026-05-28 12:25:20'),
(92, 9, '3', 'HU-1412', '', '1/4\" x 1/2\"', '', 25, 1, '2026-05-28 12:25:20'),
(93, 9, '2', 'HU-1438', '', '1/4\" x 3/8\"', '', 26, 1, '2026-05-28 12:25:20'),
(94, 9, '1', 'HU-1414', '', '1/4\" x 1/4\"', '', 27, 1, '2026-05-28 12:25:20'),
(95, 10, '3', 'CA-1412', '', '1/4\" X 1/2\"', '', 0, 1, '2026-05-28 12:28:33'),
(96, 10, '2', 'CA-1458', '', '1/4\" X 5/8\"', '', 1, 1, '2026-05-28 12:28:33'),
(97, 10, '1', 'CA-1434', '', '1/4\" X 3/4\"', '', 2, 1, '2026-05-28 12:28:33'),
(98, 11, '9', 'CU-14', '', '1/4\"', '', 0, 1, '2026-05-28 12:30:52'),
(99, 11, '8', 'CU-516', '', '5/16\"', '', 1, 1, '2026-05-28 12:30:52'),
(100, 11, '7', 'CU-38', '', '3/8\"', '', 2, 1, '2026-05-28 12:30:52'),
(101, 11, '6', 'CU-12', '', '1/2\"', '', 3, 1, '2026-05-28 12:30:52'),
(102, 11, '5', 'CU-58', '', '5/8\"', '', 4, 1, '2026-05-28 12:30:52'),
(103, 11, '4', 'CU-34', '', '3/4', '', 5, 1, '2026-05-28 12:30:52'),
(104, 11, '3', 'CU-78', '', '7/8\"', '', 6, 1, '2026-05-28 12:30:52'),
(105, 11, '2', 'CU-5878', '', '5/8\" X 7/8\"', '', 7, 1, '2026-05-28 12:30:52'),
(106, 11, '1', 'CU-58118', '', '5/8\" X 1.1/8\"', '', 8, 1, '2026-05-28 12:30:52'),
(107, 12, '4', 'FC-1414', '', '1/4\" X 1/4\"', '', 0, 1, '2026-05-28 12:43:36'),
(108, 12, '3', 'FC-3838', '', '3/8\" X 3/8\"', '', 1, 1, '2026-05-28 12:43:36'),
(109, 12, '2', 'FC-1212', '', '1/2\" X 1/2\"', '', 2, 1, '2026-05-28 12:43:36'),
(110, 12, '1', 'FC-5858', '', '5/8\" X 5/8\"', '', 3, 1, '2026-05-28 12:43:36'),
(111, 13, '28', 'GA-341', '', '3/4\" X 1\"', '', 0, 1, '2026-05-28 12:51:12'),
(112, 13, '27', 'GA-3434', '', '3/4\" X 3/4\"', '', 1, 1, '2026-05-28 12:51:12'),
(113, 13, '26', 'GA-3458', '', '3/4\" X 5/8\"', '', 2, 1, '2026-05-28 12:51:12'),
(114, 13, '25', 'GA-3412', '', '3/4\" X 1/2\"', '', 3, 1, '2026-05-28 12:51:12'),
(115, 13, '24', 'GA-581', '', '5/8\" X 1\"', '', 4, 1, '2026-05-28 12:51:12'),
(116, 13, '23', 'GA-5834', '', '5/8\" X 3/4\"', '', 5, 1, '2026-05-28 12:51:12'),
(117, 13, '22', 'GA-5858', '', '5/8\" X 5/8\"', '', 6, 1, '2026-05-28 12:51:12'),
(118, 13, '21', 'GA-5812', '', '5/8\" X 1/2\"', '', 7, 1, '2026-05-28 12:51:12'),
(119, 13, '20', 'GA-5838', '', '5/8\" X 3/8\"', '', 8, 1, '2026-05-28 12:51:12'),
(120, 13, '19', 'GA-5814', '', '5/8\" X 1/4\"', '', 9, 1, '2026-05-28 12:51:12'),
(121, 13, '18', 'GA-1234', '', '1/2\" X 3/4\"', '', 10, 1, '2026-05-28 12:51:12'),
(122, 13, '17', 'GA-1258', '', '1/2\" X 5/8\"', '', 11, 1, '2026-05-28 12:51:12'),
(123, 13, '16', 'GA-1212', '', '1/2\" X 1/2\"', '', 12, 1, '2026-05-28 12:51:12'),
(124, 13, '15', 'GA-1238', '', '1/2\" X 3/8\"', '', 13, 1, '2026-05-28 12:51:12'),
(125, 13, '14', 'GA-1214', '', '1/2\" X 1/4\"', '', 14, 1, '2026-05-28 12:51:12'),
(126, 13, '13', 'GA-1218', '', '1/2\" X 1/8\"', '', 15, 1, '2026-05-28 12:51:12'),
(127, 13, '12', 'GA-3812', '', '3/8\" X 1/2\"', '', 16, 1, '2026-05-28 12:51:12'),
(128, 13, '11', 'GA-3838', '', '3/8\" X 3/8\"', '', 17, 1, '2026-05-28 12:51:12'),
(129, 13, '10', 'GA-3814', '', '3/8\" X 1/4\"', '', 18, 1, '2026-05-28 12:51:12'),
(130, 13, '9', 'GA-3818', '', '3/8\" X 1/8\"', '', 19, 1, '2026-05-28 12:51:12'),
(131, 13, '8', 'GA-51638', '', '5/16\" X 3/8\"', '', 20, 1, '2026-05-28 12:51:12'),
(132, 13, '7', 'GA-51614', '', '5/16\" X 1/4\"', '', 21, 1, '2026-05-28 12:51:12'),
(133, 13, '6', 'GA-51618', '', '5/16\" X 1/8\"', '', 22, 1, '2026-05-28 12:51:12'),
(134, 13, '5', 'GA-1458', '', '1/4\" X 5/8\"', '', 23, 1, '2026-05-28 12:51:12'),
(135, 13, '4', 'GA-1412', '', '1/4\" X 1/2\"', '', 24, 1, '2026-05-28 12:51:12'),
(136, 13, '3', 'GA-1438', '', '1/4\" X 3/8\"', '', 25, 1, '2026-05-28 12:51:12'),
(137, 13, '2', 'GA-1414', '', '1/4\" X 1/4\"', '', 26, 1, '2026-05-28 12:51:12'),
(138, 13, '1', 'GA-1418', '', '1/4\" X 1/8\"', '', 27, 1, '2026-05-28 12:51:12'),
(139, 14, '29', 'FA-31614', '', '3/16\" X 1/4\"', '', 0, 1, '2026-05-28 13:08:03'),
(140, 14, '28', 'FA-1414', '', '1/4\" X 1/4\"', '', 1, 1, '2026-05-28 13:08:03'),
(141, 14, '27', 'FA-14516', '', '1/4\" X 5/16\"', '', 2, 1, '2026-05-28 13:08:03'),
(142, 14, '26', 'FA-1438', '', '1/4\" X 3/8\"', '', 3, 1, '2026-05-28 13:08:03'),
(143, 14, '25', 'FA-1412', '', '1/4\" X 1/2\"', '', 4, 1, '2026-05-28 13:08:03'),
(144, 14, '24', 'FA-1458', '', '1/4\" X 5/8\"', '', 5, 1, '2026-05-28 13:08:03'),
(145, 14, '23', 'FA-51614', '', '5/16\" X 1/4\"', '', 6, 1, '2026-05-28 13:08:03'),
(146, 14, '22', 'FA-51638', '', '5/16\" X 3/8\"', '', 7, 1, '2026-05-28 13:08:03'),
(147, 14, '21', 'FA-3814', '', '3/8\" X 1/4\"', '', 8, 1, '2026-05-28 13:08:03'),
(148, 14, '20', 'FA-38516', '', '3/8\" X 5/16\"', '', 9, 1, '2026-05-28 13:08:03'),
(149, 14, '19', 'FA-3838', '', '3/8\" X 3/8\"', '', 10, 1, '2026-05-28 13:08:03'),
(150, 14, '18', 'FA-3812', '', '3/8\" X 1/2\"', '', 11, 1, '2026-05-28 13:08:03'),
(151, 14, '17', 'FA-3858', '', '3/8\" X 5/8\"', '', 12, 1, '2026-05-28 13:08:03'),
(152, 14, '16', 'FA-3834', '', '3/8\" X 3/4\"', '', 13, 1, '2026-05-28 13:08:03'),
(153, 14, '15', 'FA-1214', '', '1/2\" X 1/4\"', '', 14, 1, '2026-05-28 13:08:03'),
(154, 14, '14', 'FA-1238', '', '1/2\" X 3/8\"', '', 15, 1, '2026-05-28 13:08:03'),
(155, 14, '13', 'FA-1212', '', '1/2\" X 1/2\"', '', 16, 1, '2026-05-28 13:08:03'),
(156, 14, '12', 'FA-1258', '', '1/2\" X 5/8\"', '', 17, 1, '2026-05-28 13:08:03'),
(157, 14, '11', 'FA-1234', '', '1/2\" X 3/4\"', '', 18, 1, '2026-05-28 13:08:03'),
(158, 14, '10', 'FA-5814', '', '5/8\" X 1/4\"', '', 19, 1, '2026-05-28 13:08:03'),
(159, 14, '9', 'FA-5838', '', '5/8\" X 3/8\"', '', 20, 1, '2026-05-28 13:08:03'),
(160, 14, '8', 'FA-5812', '', '5/8\" X 1/2\"', '', 21, 1, '2026-05-28 13:08:03'),
(161, 14, '7', 'FA-5858', '', '5/8\" X 5/8\"', '', 22, 1, '2026-05-28 13:08:03'),
(162, 14, '6', 'FA-5834', '', '5/8\" X 3/4\"', '', 23, 1, '2026-05-28 13:08:03'),
(163, 14, '5', 'FA-3414', '', '3/4\" X 1/4\"', '', 24, 1, '2026-05-28 13:08:03'),
(164, 14, '4', 'FA-3438', '', '3/4\" X 3/8\"', '', 25, 1, '2026-05-28 13:08:03'),
(165, 14, '3', 'FA-3412', '', '3/4\" X 1/2\"', '', 26, 1, '2026-05-28 13:08:03'),
(166, 14, '2', 'FA-3458', '', '3/4\" X 5/8\"', '', 27, 1, '2026-05-28 13:08:03'),
(167, 14, '1', 'FA-3434', '', '3/4\" X 3/4\"', '', 28, 1, '2026-05-28 13:08:03'),
(168, 15, '7', 'FSP-316', '', '3/16\"', '', 0, 1, '2026-05-28 13:14:41'),
(169, 15, '6', 'FSP-14', '', '1/4\"', '', 1, 1, '2026-05-28 13:14:41'),
(170, 15, '5', 'FSP-516', '', '5/16\"', '', 2, 1, '2026-05-28 13:14:41'),
(171, 15, '4', 'FSP-38', '', '3/8\"', '', 3, 1, '2026-05-28 13:14:41'),
(172, 15, '3', 'FSP-12', '', '1/2\"', '', 4, 1, '2026-05-28 13:14:41'),
(173, 15, '2', 'FSP-58', '', '5/8\"', '', 5, 1, '2026-05-28 13:14:41'),
(174, 15, '1', 'FSP-34', '', '3/4\"', '', 6, 1, '2026-05-28 13:14:41'),
(175, 16, '6', 'SP-18', '', '1/8\"', '', 0, 1, '2026-05-28 13:16:45'),
(176, 16, '5', 'SP-14', '', '1/4\"', '', 1, 1, '2026-05-28 13:16:45'),
(177, 16, '4', 'SP-38', '', '3/8\"', '', 2, 1, '2026-05-28 13:16:45'),
(178, 16, '3', 'SP-12', '', '1/2\"', '', 3, 1, '2026-05-28 13:16:45'),
(179, 16, '2', 'SP-58', '', '5/8\"', '', 4, 1, '2026-05-28 13:16:45'),
(180, 16, '1', 'SP-34', '', '3/4\"', '', 5, 1, '2026-05-28 13:16:45'),
(181, 17, '6', 'FE-34', '', '3/4\"', '', 0, 1, '2026-05-28 13:40:32'),
(182, 17, '5', 'FE-58', '', '5/8\"', '', 1, 1, '2026-05-28 13:40:32'),
(183, 17, '4', 'FE-14', '', '1/4\"', '', 2, 1, '2026-05-28 13:40:32'),
(184, 17, '3', 'FE-516', '', '5/16\"', '', 3, 1, '2026-05-28 13:40:32'),
(185, 17, '2', 'FE-38', '', '3/8\"', '', 4, 1, '2026-05-28 13:40:32'),
(186, 17, '1', 'FE-12', '', '1/2\"', '', 5, 1, '2026-05-28 13:40:32'),
(187, 18, '29', 'HE-31618', '', '3/16\" X 1/8\"', '', 0, 1, '2026-05-29 04:20:47'),
(188, 18, '28', 'HE-1418', '', '1/4\" X 1/8\"', '', 1, 1, '2026-05-29 04:20:47'),
(189, 18, '27', 'HE-1414', '', '1/4\" X 1/4\"', '', 2, 1, '2026-05-29 04:20:47'),
(190, 18, '26', 'HE-1438', '', '1/4\" X 3/8\"', '', 3, 1, '2026-05-29 04:20:47'),
(191, 18, '25', 'HE-1412', '', '1/4\" X 1/2\"', '', 4, 1, '2026-05-29 04:20:47'),
(192, 18, '24', 'HE-51618', '', '5/16\" X 1/8\"', '', 5, 1, '2026-05-29 04:20:47'),
(193, 18, '23', 'HE-51614', '', '5/16\" X 1/4\"', '', 6, 1, '2026-05-29 04:20:47'),
(194, 18, '22', 'HE-51638', '', '5/16\" X 3/8\"', '', 7, 1, '2026-05-29 04:20:47'),
(195, 18, '21', 'HE-3818', '', '3/8\" X 1/8\"', '', 8, 1, '2026-05-29 04:20:47'),
(196, 18, '20', 'HE-3814', '', '3/8\" X 1/4\"', '', 9, 1, '2026-05-29 04:20:47'),
(197, 18, '19', 'HE-3838', '', '3/8\" X 3/8\"', '', 10, 1, '2026-05-29 04:20:47'),
(198, 18, '18', 'HE-3812', '', '3/8\" X 1/2\"', '', 11, 1, '2026-05-29 04:20:47'),
(199, 18, '17', 'HE-1218', '', '1/2\" X 1/8\"', '', 12, 1, '2026-05-29 04:20:47'),
(200, 18, '16', 'HE-1214', '', '1/2\" X 1/4\"', '', 13, 1, '2026-05-29 04:20:47'),
(201, 18, '15', 'HE-1238', '', '1/2\" X 3/8\"', '', 14, 1, '2026-05-29 04:20:47'),
(202, 18, '14', 'HE-1212', '', '1/2\" X 1/2\"', '', 15, 1, '2026-05-29 04:20:47'),
(203, 18, '13', 'HE-1258', '', '1/2\" X 5/8\"', '', 16, 1, '2026-05-29 04:20:47'),
(204, 18, '12', 'HE-5818', '', '5/8\" X 1/8\"', '', 17, 1, '2026-05-29 04:20:47'),
(205, 18, '11', 'HE-5814', '', '5/8\" X 1/4\"', '', 18, 1, '2026-05-29 04:20:47'),
(206, 18, '10', 'HE-5838', '', '5/8\" X 3/8\"', '', 19, 1, '2026-05-29 04:20:47'),
(207, 18, '9', 'HE-5812', '', '5/8\" X 1/2\"', '', 20, 1, '2026-05-29 04:20:47'),
(208, 18, '8', 'HE-5858', '', '5/8\" X 5/8\"', '', 21, 1, '2026-05-29 04:20:47'),
(209, 18, '7', 'HE-5834', '', '5/8\" X 3/4\"', '', 22, 1, '2026-05-29 04:20:47'),
(210, 18, '6', 'HE-581', '', '5/8\" X 1\"', '', 23, 1, '2026-05-29 04:20:47'),
(211, 18, '5', 'HE-3414', '', '3/4\" X 1/4\"', '', 24, 1, '2026-05-29 04:20:47'),
(212, 18, '4', 'HE-3438', '', '3/4\" X 3/8\"', '', 25, 1, '2026-05-29 04:20:47'),
(213, 18, '3', 'HE-3412', '', '3/4\" X 1/2\"', '', 26, 1, '2026-05-29 04:20:47'),
(214, 18, '2', 'HE-3434', '', '3/4\" X 3/4\"', '', 27, 1, '2026-05-29 04:20:47'),
(215, 18, '1', 'HE-341', '', '3/4\" X 1\"', '', 28, 1, '2026-05-29 04:20:47'),
(216, 19, '12', 'FRE-14516', '', '1/4\" X 5/16\"', '', 0, 1, '2026-05-29 04:25:16'),
(217, 19, '11', 'FRE-1438', '', '1/4\" X 3/8\"', '', 1, 1, '2026-05-29 04:25:16'),
(218, 19, '10', 'FRE-1412', '', '1/4\" X 5/8\"', '', 2, 1, '2026-05-29 04:25:16'),
(219, 19, '9', 'FRE-1458', '', '1/4\" X 5/8\"', '', 3, 1, '2026-05-29 04:25:16'),
(220, 19, '8', 'FRE-1434', '', '1/4\" X 3/4\"', '', 4, 1, '2026-05-29 04:25:16'),
(221, 19, '7', 'FRE-38516', '', '3/8\" X 5/16\"', '', 5, 1, '2026-05-29 04:25:16'),
(222, 19, '6', 'FRE-3812', '', '3/8\" X 1/2\"', '', 6, 1, '2026-05-29 04:25:16'),
(223, 19, '5', 'FRE-3858', '', '3/8\" X 5/8\"', '', 7, 1, '2026-05-29 04:25:16'),
(224, 19, '4', 'FRE-3834', '', '3/8\" X 3/4\"', '', 8, 1, '2026-05-29 04:25:16'),
(225, 19, '3', 'FRE-1258', '', '1/2\" X 5/8\"', '', 9, 1, '2026-05-29 04:25:16'),
(226, 19, '2', 'FRE-1234', '', '1/2\" X 3/4\"', '', 10, 1, '2026-05-29 04:25:16'),
(227, 19, '1', 'FRE-5834', '', '5/8\" X 3/4\"', '', 11, 1, '2026-05-29 04:25:16'),
(228, 20, '13', 'FRT-1438', '', '1/4\" X 3/8\" X 1/4\"', '', 0, 1, '2026-05-29 04:28:51'),
(229, 20, '12', 'FRT-51614', '', '5/16\" X 1/4\" X 5/16\"', '', 1, 1, '2026-05-29 04:28:51'),
(230, 20, '11', 'FRT-3814', '', '3/8\" X 1/4\" X 3/8\"', '', 2, 1, '2026-05-29 04:28:51'),
(231, 20, '10', 'FRT-38516', '', '3/8\" X 5/16\" X 3/8\"', '', 3, 1, '2026-05-29 04:28:51'),
(232, 20, '9', 'FRT-3812', '', '3/8\" X 1/2\" X 3/8\"', '', 4, 1, '2026-05-29 04:28:51'),
(233, 20, '8', 'FRT-3858', '', '3/8\" X 5/8\" X 3/8\"', '', 5, 1, '2026-05-29 04:28:51'),
(234, 20, '7', 'FRT-1214', '', '1/2\" X 1/4\" X 1/2\"', '', 6, 1, '2026-05-29 04:28:51'),
(235, 20, '6', 'FRT-12516', '', '1/2\" X 5/16\" X 1/2\"', '', 7, 1, '2026-05-29 04:28:51'),
(236, 20, '5', 'FRT-1238', '', '1/2\" X 3/8\" X 1/2\"', '', 8, 1, '2026-05-29 04:28:51'),
(237, 20, '4', 'FRT-1258', '', '1/2\" X 5/8\" X 1/2\"', '', 9, 1, '2026-05-29 04:28:51'),
(238, 20, '3', 'FRT-5814', '', '5/8\" X 1/4\" X 5/8\"', '', 10, 1, '2026-05-29 04:28:51'),
(239, 20, '2', 'FRT-5838', '', '5/8\" X 3/8\" X 5/8\"', '', 11, 1, '2026-05-29 04:28:51'),
(240, 20, '1', 'FRT-5812', '', '5/8\" X 1/2\" X 5/8\"', '', 12, 1, '2026-05-29 04:28:51'),
(241, 21, '16', 'HT-1418', '', '1/4\" X 1/8\" X 1/4\"', '', 0, 1, '2026-05-29 04:34:13'),
(242, 21, '15', 'HT-1414', '', '1/4\" X 1/4\" X 1/4\"', '', 1, 1, '2026-05-29 04:34:13'),
(243, 21, '14', 'HT-1438', '', '1/4\" X 3/8\" X 1/4\"', '', 2, 1, '2026-05-29 04:34:13'),
(244, 21, '13', 'HT-51614', '', '5/16\" X 1/4\" X 5/16\"', '', 3, 1, '2026-05-29 04:34:13'),
(245, 21, '12', 'HT-51638', '', '5/16\" X 3/8\" X 5/16\"', '', 4, 1, '2026-05-29 04:34:13'),
(246, 21, '11', 'HT-3814', '', '3/8\" X 1/4\" X 3/8\"', '', 5, 1, '2026-05-29 04:34:13'),
(247, 21, '10', 'HT-3838', '', '3/8\" X 3/8\" X 3/8\"', '', 6, 1, '2026-05-29 04:34:13'),
(248, 21, '9', 'HT-1214', '', '1/2\" X 1/4\" X 1/2\"', '', 7, 1, '2026-05-29 04:34:13'),
(249, 21, '8', 'HT-1238', '', '1/2\" X 3/8\" X 1/2\"', '', 8, 1, '2026-05-29 04:34:13'),
(250, 21, '7', 'HT-1212', '', '1/2\" X 1/2\" X 1/2\"', '', 9, 1, '2026-05-29 04:34:13'),
(251, 21, '6', 'HT-1234', '', '1/2\" X 3/4\" X 1/2\"', '', 10, 1, '2026-05-29 04:34:13'),
(252, 21, '5', 'HT-5838', '', '5/8\" X 3/8\" X 5/8\"', '', 11, 1, '2026-05-29 04:34:13'),
(253, 21, '4', 'HT-5812', '', '5/8\" X 1/2\" X 5/8\"', '', 12, 1, '2026-05-29 04:34:13'),
(254, 21, '3', 'HT-5834', '', '5/8\" X 3/4\" X 5/8\"', '', 13, 1, '2026-05-29 04:34:13'),
(255, 21, '2', 'HT-3414', '', '3/4\" X 1/4\" X 3/4\"', '', 14, 1, '2026-05-29 04:34:13'),
(256, 21, '1', 'HT-3434', '', '3/4\" X 3/4\" X 3/4\"', '', 15, 1, '2026-05-29 04:34:13'),
(257, 22, '6', 'FT-14', '', '1/4\"', '', 0, 1, '2026-05-29 04:37:17'),
(258, 22, '5', 'FT-516', '', '5/16\"', '', 1, 1, '2026-05-29 04:37:17'),
(259, 22, '4', 'FT-38', '', '3/8\"', '', 2, 1, '2026-05-29 04:37:17'),
(260, 22, '3', 'FT-12', '', '1/2\"', '', 3, 1, '2026-05-29 04:37:17'),
(261, 22, '2', 'FT-58', '', '5/8\"', '', 4, 1, '2026-05-29 04:37:17'),
(262, 22, '1', 'FT-34', '', '3/4\"', '', 5, 1, '2026-05-29 04:37:17'),
(263, 23, '4', 'CA14FF14F', '', '1/4FF X 1/4FL', '', 0, 1, '2026-05-29 04:41:03'),
(264, 23, '3', 'CA14F14FPT', '', '1/4FL X 1/4FPT', '', 1, 1, '2026-05-29 04:41:03'),
(265, 23, '2', 'CA14FF516F', '', '1/4FF X 5/16FL', '', 2, 1, '2026-05-29 04:41:03'),
(266, 23, '1', 'CA516FF14F', '', '5/16FF X 1/4FL', '', 3, 1, '2026-05-29 04:41:03'),
(267, 24, '1', 'CV-14', '', '1/4\"', '', 0, 1, '2026-05-29 04:42:15'),
(268, 25, '2', 'CARA-1414', '', '1/4FF X 1/4FL', '', 0, 1, '2026-05-29 04:43:54'),
(269, 25, '1', 'CARA-1414WN', '', '1/4FL X 1/4FL WITH NUT', '', 1, 1, '2026-05-29 04:43:54'),
(270, 26, '2', 'NC-14', '', '1/4\"', '', 0, 1, '2026-05-29 04:46:12'),
(271, 26, '1', 'NCKT-14', '', '1/4FL', '', 1, 1, '2026-05-29 04:46:12'),
(272, 27, '4', 'CUP-14F18BSP', '', '1/4FL X 1/8BSP', '', 0, 1, '2026-05-29 04:48:16'),
(273, 27, '3', 'CUP-14F18N', '', '1/4FL X 1/8NPT', '', 1, 1, '2026-05-29 04:48:16'),
(274, 27, '2', 'CUP-14F14N', '', '1/4FL X 1/4NPT', '', 2, 1, '2026-05-29 04:48:16'),
(275, 27, '1', 'CUP-14F14FL', '', '1/4FL X 1/4FL', '', 3, 1, '2026-05-29 04:48:16'),
(276, 28, '1', 'NU-1458', '', '1/4\" X 5/8\"', '', 0, 1, '2026-05-29 04:49:26'),
(277, 29, '2', 'CNOPC- 14F18N', '', '1/4FL X 1/8NPT', '', 0, 1, '2026-05-29 04:51:02'),
(278, 29, '1', 'CNOPC- 14F14N', '', '1/4FL X 1/4NPT', '', 1, 1, '2026-05-29 04:51:02'),
(279, 30, '4', 'SV-14', '', '1/4\"', '', 0, 1, '2026-05-29 04:52:55'),
(280, 30, '3', 'SV-38', '', '3/8\"', '', 1, 1, '2026-05-29 04:52:55'),
(281, 30, '2', 'SV-12', '', '1/2\"', '', 2, 1, '2026-05-29 04:52:55'),
(282, 30, '1', 'SV-58', '', '5/8\"', '', 3, 1, '2026-05-29 04:52:55'),
(283, 31, '1', '410-A', '', '5/16\" X 1/4\"', '', 0, 1, '2026-05-29 04:54:04'),
(284, 32, '2', 'AB 480', '', '480MM (1 TO 1.5 TON)', '', 0, 1, '2026-05-29 04:55:17'),
(285, 32, '1', 'AB 600', '', '600MM (2 TO 3 TON)', '', 1, 1, '2026-05-29 04:55:17'),
(286, 33, '6', 'AV 14F 14N', '', '1/4FL X 1/4NPT', '', 0, 1, '2026-05-29 04:58:04'),
(287, 33, '5', 'AV 38F 38N', '', '3/8FL X 3/8NPT', '', 1, 1, '2026-05-29 04:58:04'),
(288, 33, '4', 'AV 14F 38N', '', '1/4FL X 3/8NPT', '', 2, 1, '2026-05-29 04:58:04'),
(289, 33, '3', 'AV 14F 14F', '', '1/4FL X 1/4FL', '', 3, 1, '2026-05-29 04:58:04'),
(290, 33, '2', 'AV 38F 14N', '', '3/8FL X 1/4NPT', '', 4, 1, '2026-05-29 04:58:04'),
(291, 33, '1', 'AV 38F 38F', '', '3/8FL X 3/8FL', '', 5, 1, '2026-05-29 04:58:04'),
(292, 34, '1', 'CTV-14', '', '1/4\"', '', 0, 1, '2026-05-29 04:59:03'),
(293, 35, '1', 'HCV-14', '', '1/4\"', '', 0, 1, '2026-05-29 05:00:04'),
(294, 36, '5', 'O-49', '', 'O-49', '', 0, 1, '2026-05-29 05:02:00'),
(295, 36, '4', 'O-59', '', 'O-59', '', 1, 1, '2026-05-29 05:02:00'),
(296, 36, '3', 'O-68', '', 'O-68', '', 2, 1, '2026-05-29 05:02:00'),
(297, 36, '2', 'O-78', '', 'O-78', '', 3, 1, '2026-05-29 05:02:00'),
(298, 36, '1', 'O-93', '', 'O-93', '', 4, 1, '2026-05-29 05:02:00');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL DEFAULT 1,
  `logo` text DEFAULT NULL,
  `office_name_1` varchar(255) DEFAULT 'Manufacturing Unit (Jamnagar Factory)',
  `office_name_2` varchar(255) DEFAULT 'Corporate Office (Bangalore Branch)',
  `address` text DEFAULT NULL,
  `address_2` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_2` varchar(255) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `phone_2` varchar(100) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `map_embed_url` text DEFAULT '',
  `whatsapp` varchar(50) DEFAULT '',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `catalogue_banner` text DEFAULT NULL,
  `contact_banner` text DEFAULT NULL,
  `inquiry_recipient_email` varchar(255) DEFAULT 'info@frio.co',
  `notification_email` varchar(255) DEFAULT 'divyarajgohil6299@gmail.com',
  `email_method` varchar(20) DEFAULT 'mail',
  `smtp_host` varchar(255) DEFAULT NULL,
  `smtp_port` int(11) DEFAULT 587,
  `smtp_user` varchar(255) DEFAULT NULL,
  `smtp_pass` varchar(255) DEFAULT NULL,
  `smtp_secure` varchar(10) DEFAULT 'tls'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `logo`, `office_name_1`, `office_name_2`, `address`, `address_2`, `email`, `email_2`, `phone`, `phone_2`, `facebook`, `instagram`, `linkedin`, `twitter`, `youtube`, `map_embed_url`, `whatsapp`, `updated_at`, `catalogue_banner`, `contact_banner`, `inquiry_recipient_email`, `notification_email`, `email_method`, `smtp_host`, `smtp_port`, `smtp_user`, `smtp_pass`, `smtp_secure`) VALUES
(1, 'assets/imag/frio-logo-white.png', 'Visit Office', '', 'Plot No. 4654  Phase lll,\r\nDared GIDC, Jamnagar - 361004,\r\nGujarat (INDIA).', '', 'info@uniglobeoverseas.com', 'sales@uniglobeoverseas.com', '+91 9723588952, +91 9328046282, +91 9265398945', '', 'https://www.facebook.com/share/1UNY1Z57JV/', 'https://www.instagram.com/frioindia', '', '', '', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29506.676615408138!2d70.01128571828217!3d22.416429533950165!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395714caf410c4a7%3A0x68ebc92a77861b0d!2sGIDC%20Phase%20III%2C%20GIDC%20Phase-2%2C%20Dared%2C%20Jamnagar%2C%20Gujarat!5e0!3m2!1sen!2sin!4v1780049362540!5m2!1sen!2sin', '', '2026-06-03 06:48:31', 'assets/imag/banners/cat_banner_1779777140_6f3b4484b9e2467f885886e9db0079be.png', '', 'divyarajgoil6299@gmail.com', 'divyarajgohil6299@gmail.com', 'smtp', 'smtp.gmail.com', 587, 'divyarajgohil6299@gmail.com', 'ujurexthvgefvgts', 'tls');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `banner_slider`
--
ALTER TABLE `banner_slider`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `catalogue`
--
ALTER TABLE `catalogue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_image`
--
ALTER TABLE `product_image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_variation`
--
ALTER TABLE `product_variation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `banner_slider`
--
ALTER TABLE `banner_slider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `catalogue`
--
ALTER TABLE `catalogue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `product_image`
--
ALTER TABLE `product_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `product_variation`
--
ALTER TABLE `product_variation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=299;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
