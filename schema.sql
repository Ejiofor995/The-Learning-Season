-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2026 at 03:09 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mercado`
--
CREATE DATABASE IF NOT EXISTS `mercado` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;
USE `mercado`;

-- --------------------------------------------------------

--
-- Table structure for table `affiliates`
--

CREATE TABLE `affiliates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `public_name` varchar(150) NOT NULL,
  `referral_code` varchar(80) NOT NULL,
  `status` enum('pending','active','suspended') NOT NULL DEFAULT 'pending',
  `cookie_window_days` smallint(5) UNSIGNED NOT NULL DEFAULT 30,
  `payout_method` varchar(40) DEFAULT NULL,
  `payout_account_encrypted` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `affiliates`
--

INSERT INTO `affiliates` (`id`, `user_id`, `public_name`, `referral_code`, `status`, `cookie_window_days`, `payout_method`, `payout_account_encrypted`, `created_at`, `updated_at`) VALUES
(1, 3, 'Taylor Recommends', 'TAYLORDEMO', 'active', 30, NULL, NULL, '2026-11-05 02:05:20', '2026-11-05 02:05:20');

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_attributions`
--

CREATE TABLE `affiliate_attributions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `visitor_key_hash` char(64) NOT NULL,
  `affiliate_link_id` bigint(20) UNSIGNED NOT NULL,
  `first_touch_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `converted_order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `converted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `affiliate_attributions`
--

INSERT INTO `affiliate_attributions` (`id`, `visitor_key_hash`, `affiliate_link_id`, `first_touch_at`, `expires_at`, `converted_order_id`, `converted_at`) VALUES
(1, 'a817fe133af4320a05be59c68b8da2aa9767b5522c61be7214d5f82bb35a0071', 1, '2026-11-05 02:05:22', '2026-12-05 02:05:22', 1, '2026-11-05 02:05:22');

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_clicks`
--

CREATE TABLE `affiliate_clicks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `affiliate_link_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `clicked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `affiliate_clicks`
--

INSERT INTO `affiliate_clicks` (`id`, `affiliate_link_id`, `user_id`, `ip_address`, `user_agent`, `clicked_at`) VALUES
(1, 1, NULL, '192.0.2.10', 'Mercado demo seed', '2026-11-05 02:05:22');

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_conversions`
--

CREATE TABLE `affiliate_conversions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `affiliate_link_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `commission_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `affiliate_conversions`
--

INSERT INTO `affiliate_conversions` (`id`, `affiliate_link_id`, `order_id`, `commission_amount`, `status`, `created_at`) VALUES
(1, 1, 1, 7.80, 'approved', '2026-11-05 02:05:22');

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_links`
--

CREATE TABLE `affiliate_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `affiliate_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(80) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `affiliate_links`
--

INSERT INTO `affiliate_links` (`id`, `affiliate_id`, `product_id`, `code`, `is_active`, `created_at`) VALUES
(1, 3, 1, 'TAYLOR-CONVERSION-DEMO', 1, '2026-11-05 02:05:21');

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_order_commissions`
--

CREATE TABLE `affiliate_order_commissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `affiliate_id` bigint(20) UNSIGNED NOT NULL,
  `affiliate_link_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `order_item_id` bigint(20) UNSIGNED NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL,
  `commission_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected','paid','reversed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `affiliate_order_commissions`
--

INSERT INTO `affiliate_order_commissions` (`id`, `affiliate_id`, `affiliate_link_id`, `order_id`, `order_item_id`, `commission_rate`, `commission_amount`, `status`, `created_at`, `approved_at`) VALUES
(1, 3, 1, 1, 1, 20.00, 7.80, 'paid', '2026-11-05 02:05:22', '2026-11-05 02:05:22');

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_payouts`
--

CREATE TABLE `affiliate_payouts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `affiliate_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'USD',
  `status` enum('pending','processing','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
  `payout_reference` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `affiliate_payouts`
--

INSERT INTO `affiliate_payouts` (`id`, `affiliate_id`, `amount`, `currency`, `status`, `payout_reference`, `created_at`, `processed_at`) VALUES
(1, 3, 7.80, 'USD', 'paid', 'demo-affiliate-payout-0001', '2026-11-05 02:05:24', '2026-11-05 02:05:24');

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_payout_items`
--

CREATE TABLE `affiliate_payout_items` (
  `payout_id` bigint(20) UNSIGNED NOT NULL,
  `commission_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `affiliate_payout_items`
--

INSERT INTO `affiliate_payout_items` (`payout_id`, `commission_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `downloads`
--

CREATE TABLE `downloads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `download_token` varchar(255) NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `downloads`
--

INSERT INTO `downloads` (`id`, `user_id`, `product_id`, `order_id`, `download_token`, `expires_at`, `created_at`) VALUES
(1, 1, 1, 1, 'demo-only-download-token-0001', '2026-11-12 02:05:23', '2026-11-05 02:05:23');

-- --------------------------------------------------------

--
-- Table structure for table `news_articles`
--

CREATE TABLE `news_articles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `author_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(220) NOT NULL,
  `slug` varchar(240) NOT NULL,
  `excerpt` varchar(500) DEFAULT NULL,
  `body` longtext NOT NULL,
  `cover_image` varchar(500) DEFAULT NULL,
  `status` enum('draft','review','published','archived') NOT NULL DEFAULT 'draft',
  `audience` enum('all','buyers','sellers','affiliates') NOT NULL DEFAULT 'all',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news_articles`
--

INSERT INTO `news_articles` (`id`, `author_id`, `title`, `slug`, `excerpt`, `body`, `cover_image`, `status`, `audience`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 4, 'A buyer\'s guide to choosing digital products', 'buyers-guide-digital-products', 'Compare product details, seller information, and secure delivery before you buy.', 'Review the product description, included files, and seller profile before checkout.\n\nMercado grants digital access only after a payment is verified by the server. Keep your order confirmation so support can help if anything goes wrong.', 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=900&q=80', 'published', 'buyers', '2026-11-05 02:05:24', '2026-11-05 02:05:24', '2026-11-05 02:05:24'),
(2, 4, 'Preparing your first product for the marketplace', 'seller-first-product-checklist', 'A checklist for useful previews, accurate pricing, and secure file delivery.', 'Write a clear product summary and explain exactly what the buyer receives.\n\nUse a preview image you have permission to publish, price the product carefully, and upload the deliverable through the protected product file workflow.', 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=900&q=80', 'published', 'sellers', '2026-11-05 02:05:25', '2026-11-05 02:05:25', '2026-11-05 02:05:25'),
(3, 4, 'How affiliate attribution and commissions work', 'affiliate-attribution-and-commissions', 'Understand referral links, first-touch attribution, order validation, and payouts.', 'Share referral links with an audience that will benefit from the product. Mercado records the referral and applies the configured attribution window.\n\nA commission is based on an eligible, verified order. Approval and payout are separate steps and are recorded for review.', 'https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=900&q=80', 'published', 'affiliates', '2026-11-05 02:05:25', '2026-11-05 02:05:25', '2026-11-05 02:05:25'),
(4, 4, 'Building trust with reliable digital delivery', 'reliable-digital-delivery', 'What customers should expect after payment and how secure access is protected.', 'A successful checkout is confirmed by the payment provider on the server. A browser redirect alone is not proof of payment.\n\nDownload access should be tied to a paid order, limited by a secure token, and revoked or expired according to the marketplace policy.', 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=900&q=80', 'published', 'all', '2026-11-05 02:05:25', '2026-11-05 02:05:25', '2026-11-05 02:05:25');

-- --------------------------------------------------------

--
-- Table structure for table `news_article_categories`
--

CREATE TABLE `news_article_categories` (
  `article_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news_article_categories`
--

INSERT INTO `news_article_categories` (`article_id`, `category_id`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4);

-- --------------------------------------------------------

--
-- Table structure for table `news_article_tags`
--

CREATE TABLE `news_article_tags` (
  `article_id` bigint(20) UNSIGNED NOT NULL,
  `tag_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news_article_tags`
--

INSERT INTO `news_article_tags` (`article_id`, `tag_id`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4);

-- --------------------------------------------------------

--
-- Table structure for table `news_categories`
--

CREATE TABLE `news_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news_categories`
--

INSERT INTO `news_categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Buyer Guides', 'buyer-guides', 'Product discovery, safe checkout, and digital delivery guidance for buyers.'),
(2, 'Seller Operations', 'seller-operations', 'Product publishing, store management, order handling, and payouts for sellers.'),
(3, 'Affiliate Growth', 'affiliate-growth', 'Referral links, attribution, commissions, and campaign guidance for affiliates.'),
(4, 'Marketplace Updates', 'marketplace-updates', 'Platform changes and announcements for the Mercado community.');

-- --------------------------------------------------------

--
-- Table structure for table `news_comments`
--

CREATE TABLE `news_comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `article_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `guest_name` varchar(120) DEFAULT NULL,
  `guest_email` varchar(180) DEFAULT NULL,
  `body` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news_comments`
--

INSERT INTO `news_comments` (`id`, `article_id`, `user_id`, `guest_name`, `guest_email`, `body`, `status`, `created_at`) VALUES
(1, 4, NULL, 'Demo Reader', 'reader@mercado.test', 'This guide makes the delivery process clear.', 'approved', '2026-11-05 02:05:26');

-- --------------------------------------------------------

--
-- Table structure for table `news_tags`
--

CREATE TABLE `news_tags` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `slug` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news_tags`
--

INSERT INTO `news_tags` (`id`, `name`, `slug`) VALUES
(1, 'Digital products', 'digital-products'),
(2, 'Seller tips', 'seller-tips'),
(3, 'Affiliate marketing', 'affiliate-marketing'),
(4, 'Secure delivery', 'secure-delivery');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `buyer_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `affiliate_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'USD',
  `status` enum('pending','paid','failed','refunded','fulfilled') NOT NULL DEFAULT 'pending',
  `payment_gateway` enum('stripe','paystack') DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `paid_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `buyer_id`, `seller_id`, `affiliate_id`, `subtotal`, `fee`, `total`, `currency`, `status`, `payment_gateway`, `payment_reference`, `created_at`, `paid_at`) VALUES
(1, 1, 1, 3, 39.00, 1.95, 40.95, 'USD', 'paid', 'stripe', 'demo-order-ref-0001', '2026-11-05 02:05:21', '2026-11-05 02:05:21');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`, `created_at`) VALUES
(1, 1, 1, 1, 39.00, 39.00, '2026-11-05 02:05:21');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `provider` enum('stripe','paystack') NOT NULL,
  `provider_reference` varchar(255) NOT NULL,
  `status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `mode` enum('test','live') NOT NULL DEFAULT 'test',
  `amount` decimal(10,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'USD',
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `provider`, `provider_reference`, `status`, `mode`, `amount`, `currency`, `payload`, `created_at`, `updated_at`) VALUES
(1, 1, 'stripe', 'demo-stripe-payment-0001', 'paid', 'test', 40.95, 'USD', '{\"demo\": true, \"verified_server_side\": true}', '2026-11-05 02:05:23', '2026-11-05 02:05:23');

-- --------------------------------------------------------

--
-- Table structure for table `payment_events`
--

CREATE TABLE `payment_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `provider` enum('stripe','paystack') NOT NULL,
  `provider_event_id` varchar(255) NOT NULL,
  `event_type` varchar(120) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `received_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_events`
--

INSERT INTO `payment_events` (`id`, `payment_id`, `provider`, `provider_event_id`, `event_type`, `payload`, `received_at`, `processed_at`) VALUES
(1, 1, 'stripe', 'demo-stripe-event-0001', 'payment_intent.succeeded', '{\"demo\": true, \"order_reference\": \"demo-order-ref-0001\"}', '2026-11-05 02:05:23', '2026-11-05 02:05:23');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(180) NOT NULL,
  `name` varchar(180) NOT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `category` varchar(80) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `compare_price` decimal(10,2) DEFAULT 0.00,
  `status` enum('draft','active','archived') NOT NULL DEFAULT 'draft',
  `is_digital` tinyint(1) NOT NULL DEFAULT 1,
  `affiliate_commission` decimal(5,2) NOT NULL DEFAULT 0.00,
  `thumbnail_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `seller_id`, `slug`, `name`, `summary`, `description`, `category`, `price`, `compare_price`, `status`, `is_digital`, `affiliate_commission`, `thumbnail_url`, `created_at`, `updated_at`) VALUES
(1, 1, 'conversion-accelerator', 'Conversion Accelerator Kit', 'Launch-ready funnel templates and conversion copy prompts.', 'A practical digital toolkit with landing page layouts, email sequences, and campaign checklists for independent creators.', 'Marketing', 39.00, 59.00, 'active', 1, 20.00, 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=900&q=80', '2026-11-05 02:05:20', '2026-11-05 02:05:20'),
(2, 1, 'creator-growth-system', 'Creator Growth System', 'A focused planning system for product launches and audience growth.', 'Organize product launches, community sequences, and recurring-revenue workflows with reusable worksheets and templates.', 'Creator Economy', 29.00, 45.00, 'active', 1, 15.00, 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=900&q=80', '2026-11-05 02:05:20', '2026-11-05 02:05:20'),
(3, 1, 'saas-analytics-sprint', 'SaaS Analytics Sprint', 'Dashboard templates and a practical metrics implementation guide.', 'Build a KPI dashboard, retention playbooks, and product reporting templates for early-stage software teams.', 'Product', 49.00, 69.00, 'active', 1, 15.00, 'https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&w=900&q=80', '2026-11-05 02:05:21', '2026-11-05 02:05:21'),
(4, 1, 'digital-product-blueprint', 'Digital Product Blueprint', 'A step-by-step workbook for packaging a digital offer.', 'A practical blueprint for researching, packaging, and selling digital products, including launch checklists and editable planning sheets.', 'Templates', 19.00, 29.00, 'active', 1, 20.00, 'https://images.unsplash.com/photo-1516321165247-4aa89a48be28?auto=format&fit=crop&w=900&q=80', '2026-11-05 02:05:21', '2026-11-05 02:05:21');

-- --------------------------------------------------------

--
-- Table structure for table `product_files`
--

CREATE TABLE `product_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `storage_path` varchar(255) NOT NULL,
  `mime_type` varchar(120) DEFAULT NULL,
  `size_bytes` bigint(20) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_files`
--

INSERT INTO `product_files` (`id`, `product_id`, `file_name`, `storage_path`, `mime_type`, `size_bytes`, `created_at`) VALUES
(1, 1, 'conversion-accelerator-demo.zip', 'storage/demo/conversion-accelerator-demo.zip', 'application/zip', 1024, '2026-11-05 02:05:21'),
(2, 2, 'creator-growth-system-demo.pdf', 'storage/demo/creator-growth-system-demo.pdf', 'application/pdf', 2048, '2026-11-05 02:05:21');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(4, 'admin'),
(3, 'affiliate'),
(1, 'buyer'),
(2, 'seller');

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `business_name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `payout_account` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','suspended') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`id`, `user_id`, `business_name`, `slug`, `payout_account`, `status`, `created_at`) VALUES
(1, 2, 'Northstar Digital Studio', 'northstar-digital-studio', NULL, 'approved', '2026-11-05 02:05:20');

-- --------------------------------------------------------

--
-- Table structure for table `seller_payouts`
--

CREATE TABLE `seller_payouts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','failed') NOT NULL DEFAULT 'pending',
  `payout_reference` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seller_payouts`
--

INSERT INTO `seller_payouts` (`id`, `seller_id`, `order_id`, `amount`, `status`, `payout_reference`, `created_at`, `processed_at`) VALUES
(1, 1, 1, 35.10, 'paid', 'demo-seller-payout-0001', '2026-11-05 02:05:24', '2026-11-05 02:05:24');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `display_name` varchar(120) NOT NULL,
  `participant_role` enum('buyer','seller','affiliate') NOT NULL,
  `quote` text NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `avatar_url` varchar(500) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `user_id`, `product_id`, `order_id`, `display_name`, `participant_role`, `quote`, `rating`, `avatar_url`, `status`, `created_at`, `approved_at`) VALUES
(1, 1, 1, 1, 'Avery Buyer', 'buyer', 'Checkout was straightforward and the product files were ready after the payment cleared.', 5, 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80', 'approved', '2026-11-05 02:05:26', '2026-11-05 02:05:26'),
(2, 2, NULL, NULL, 'Morgan Seller', 'seller', 'A clear place to organize digital listings, orders, and product delivery.', 5, 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80', 'approved', '2026-11-05 02:05:26', '2026-11-05 02:05:26'),
(3, 3, NULL, NULL, 'Taylor Affiliate', 'affiliate', 'Referral and commission records make it easier to follow a recommendation through to a sale.', 5, 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=200&q=80', 'approved', '2026-11-05 02:05:26', '2026-11-05 02:05:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('buyer','seller','affiliate','admin') NOT NULL DEFAULT 'buyer',
  `name` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('active','inactive','pending') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `name`, `email`, `password_hash`, `status`, `created_at`) VALUES
(1, 'buyer', 'Avery Buyer', 'buyer@mercado.test', '$2y$10$vXSbHK45yg8YbuFK/4xU5eByqpJVWllHzfd0Xthfp3fdyJvF3BWoW', 'active', '2026-11-05 02:05:20'),
(2, 'seller', 'Morgan Seller', 'seller@mercado.test', '$2y$10$vXSbHK45yg8YbuFK/4xU5eByqpJVWllHzfd0Xthfp3fdyJvF3BWoW', 'active', '2026-11-05 02:05:20'),
(3, 'affiliate', 'Taylor Affiliate', 'affiliate@mercado.test', '$2y$10$vXSbHK45yg8YbuFK/4xU5eByqpJVWllHzfd0Xthfp3fdyJvF3BWoW', 'active', '2026-11-05 02:05:20'),
(4, 'admin', 'Mercado Admin', 'admin@mercado.test', '$2y$10$vXSbHK45yg8YbuFK/4xU5eByqpJVWllHzfd0Xthfp3fdyJvF3BWoW', 'active', '2026-11-05 02:05:20');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` smallint(5) UNSIGNED NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`) VALUES
(1, 1, '2026-11-05 02:05:20'),
(2, 2, '2026-11-05 02:05:20'),
(3, 3, '2026-11-05 02:05:20'),
(4, 4, '2026-11-05 02:05:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `affiliates`
--
ALTER TABLE `affiliates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_affiliates_user` (`user_id`),
  ADD UNIQUE KEY `uq_affiliates_referral_code` (`referral_code`),
  ADD KEY `idx_affiliates_status` (`status`);

--
-- Indexes for table `affiliate_attributions`
--
ALTER TABLE `affiliate_attributions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_affiliate_attribution_visitor` (`visitor_key_hash`),
  ADD KEY `idx_affiliate_attributions_link` (`affiliate_link_id`),
  ADD KEY `idx_affiliate_attributions_expiry` (`expires_at`),
  ADD KEY `fk_affiliate_attributions_order` (`converted_order_id`);

--
-- Indexes for table `affiliate_clicks`
--
ALTER TABLE `affiliate_clicks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_affiliate_clicks_link` (`affiliate_link_id`),
  ADD KEY `fk_affiliate_clicks_user` (`user_id`);

--
-- Indexes for table `affiliate_conversions`
--
ALTER TABLE `affiliate_conversions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_affiliate_conversions_order` (`order_id`),
  ADD KEY `idx_affiliate_conversions_link` (`affiliate_link_id`);

--
-- Indexes for table `affiliate_links`
--
ALTER TABLE `affiliate_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_affiliate_links_code` (`code`),
  ADD KEY `idx_affiliate_links_affiliate` (`affiliate_id`),
  ADD KEY `idx_affiliate_links_product` (`product_id`);

--
-- Indexes for table `affiliate_order_commissions`
--
ALTER TABLE `affiliate_order_commissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_affiliate_commission_order_item` (`order_item_id`),
  ADD KEY `idx_affiliate_commissions_affiliate_status` (`affiliate_id`,`status`),
  ADD KEY `idx_affiliate_commissions_order` (`order_id`),
  ADD KEY `fk_affiliate_commissions_link` (`affiliate_link_id`);

--
-- Indexes for table `affiliate_payouts`
--
ALTER TABLE `affiliate_payouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_affiliate_payouts_affiliate_status` (`affiliate_id`,`status`);

--
-- Indexes for table `affiliate_payout_items`
--
ALTER TABLE `affiliate_payout_items`
  ADD PRIMARY KEY (`payout_id`,`commission_id`),
  ADD UNIQUE KEY `uq_affiliate_payout_commission` (`commission_id`);

--
-- Indexes for table `downloads`
--
ALTER TABLE `downloads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_downloads_token` (`download_token`),
  ADD KEY `idx_downloads_user` (`user_id`),
  ADD KEY `idx_downloads_product` (`product_id`),
  ADD KEY `idx_downloads_order` (`order_id`);

--
-- Indexes for table `news_articles`
--
ALTER TABLE `news_articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_news_articles_slug` (`slug`),
  ADD KEY `idx_news_articles_status_published` (`status`,`published_at`),
  ADD KEY `idx_news_articles_audience` (`audience`),
  ADD KEY `fk_news_articles_author` (`author_id`);

--
-- Indexes for table `news_article_categories`
--
ALTER TABLE `news_article_categories`
  ADD PRIMARY KEY (`article_id`,`category_id`),
  ADD KEY `fk_news_article_categories_category` (`category_id`);

--
-- Indexes for table `news_article_tags`
--
ALTER TABLE `news_article_tags`
  ADD PRIMARY KEY (`article_id`,`tag_id`),
  ADD KEY `fk_news_article_tags_tag` (`tag_id`);

--
-- Indexes for table `news_categories`
--
ALTER TABLE `news_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_news_categories_slug` (`slug`);

--
-- Indexes for table `news_comments`
--
ALTER TABLE `news_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_news_comments_article_status` (`article_id`,`status`),
  ADD KEY `fk_news_comments_user` (`user_id`);

--
-- Indexes for table `news_tags`
--
ALTER TABLE `news_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_news_tags_slug` (`slug`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orders_buyer` (`buyer_id`),
  ADD KEY `idx_orders_seller` (`seller_id`),
  ADD KEY `idx_orders_affiliate` (`affiliate_id`),
  ADD KEY `idx_orders_status` (`status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_items_order` (`order_id`),
  ADD KEY `idx_order_items_product` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_payments_provider_reference` (`provider_reference`),
  ADD KEY `idx_payments_order` (`order_id`),
  ADD KEY `idx_payments_status` (`status`);

--
-- Indexes for table `payment_events`
--
ALTER TABLE `payment_events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_payment_events_provider_event` (`provider`,`provider_event_id`),
  ADD KEY `idx_payment_events_payment` (`payment_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_products_slug` (`slug`),
  ADD KEY `idx_products_seller` (`seller_id`),
  ADD KEY `idx_products_category` (`category`),
  ADD KEY `idx_products_status` (`status`);

--
-- Indexes for table `product_files`
--
ALTER TABLE `product_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_files_product` (`product_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_roles_name` (`name`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_sellers_user` (`user_id`),
  ADD UNIQUE KEY `uq_sellers_slug` (`slug`);

--
-- Indexes for table `seller_payouts`
--
ALTER TABLE `seller_payouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_seller_payouts_seller` (`seller_id`),
  ADD KEY `idx_seller_payouts_order` (`order_id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_testimonials_status_created` (`status`,`created_at`),
  ADD KEY `idx_testimonials_role` (`participant_role`),
  ADD KEY `idx_testimonials_product` (`product_id`),
  ADD KEY `fk_testimonials_user` (`user_id`),
  ADD KEY `fk_testimonials_order` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD KEY `idx_users_role` (`role`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `fk_user_roles_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `affiliates`
--
ALTER TABLE `affiliates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `affiliate_attributions`
--
ALTER TABLE `affiliate_attributions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `affiliate_clicks`
--
ALTER TABLE `affiliate_clicks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `affiliate_conversions`
--
ALTER TABLE `affiliate_conversions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `affiliate_links`
--
ALTER TABLE `affiliate_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `affiliate_order_commissions`
--
ALTER TABLE `affiliate_order_commissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `affiliate_payouts`
--
ALTER TABLE `affiliate_payouts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `downloads`
--
ALTER TABLE `downloads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `news_articles`
--
ALTER TABLE `news_articles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `news_categories`
--
ALTER TABLE `news_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `news_comments`
--
ALTER TABLE `news_comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `news_tags`
--
ALTER TABLE `news_tags`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment_events`
--
ALTER TABLE `payment_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `product_files`
--
ALTER TABLE `product_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sellers`
--
ALTER TABLE `sellers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `seller_payouts`
--
ALTER TABLE `seller_payouts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `affiliates`
--
ALTER TABLE `affiliates`
  ADD CONSTRAINT `fk_affiliates_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `affiliate_attributions`
--
ALTER TABLE `affiliate_attributions`
  ADD CONSTRAINT `fk_affiliate_attributions_link` FOREIGN KEY (`affiliate_link_id`) REFERENCES `affiliate_links` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_affiliate_attributions_order` FOREIGN KEY (`converted_order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `affiliate_clicks`
--
ALTER TABLE `affiliate_clicks`
  ADD CONSTRAINT `fk_affiliate_clicks_link` FOREIGN KEY (`affiliate_link_id`) REFERENCES `affiliate_links` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_affiliate_clicks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `affiliate_conversions`
--
ALTER TABLE `affiliate_conversions`
  ADD CONSTRAINT `fk_affiliate_conversions_link` FOREIGN KEY (`affiliate_link_id`) REFERENCES `affiliate_links` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_affiliate_conversions_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `affiliate_links`
--
ALTER TABLE `affiliate_links`
  ADD CONSTRAINT `fk_affiliate_links_affiliate` FOREIGN KEY (`affiliate_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_affiliate_links_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `affiliate_order_commissions`
--
ALTER TABLE `affiliate_order_commissions`
  ADD CONSTRAINT `fk_affiliate_commissions_item` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_affiliate_commissions_link` FOREIGN KEY (`affiliate_link_id`) REFERENCES `affiliate_links` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_affiliate_commissions_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_affiliate_commissions_user` FOREIGN KEY (`affiliate_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `affiliate_payouts`
--
ALTER TABLE `affiliate_payouts`
  ADD CONSTRAINT `fk_affiliate_payouts_user` FOREIGN KEY (`affiliate_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `affiliate_payout_items`
--
ALTER TABLE `affiliate_payout_items`
  ADD CONSTRAINT `fk_affiliate_payout_items_commission` FOREIGN KEY (`commission_id`) REFERENCES `affiliate_order_commissions` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_affiliate_payout_items_payout` FOREIGN KEY (`payout_id`) REFERENCES `affiliate_payouts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `downloads`
--
ALTER TABLE `downloads`
  ADD CONSTRAINT `fk_downloads_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_downloads_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_downloads_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `news_articles`
--
ALTER TABLE `news_articles`
  ADD CONSTRAINT `fk_news_articles_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `news_article_categories`
--
ALTER TABLE `news_article_categories`
  ADD CONSTRAINT `fk_news_article_categories_article` FOREIGN KEY (`article_id`) REFERENCES `news_articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_news_article_categories_category` FOREIGN KEY (`category_id`) REFERENCES `news_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `news_article_tags`
--
ALTER TABLE `news_article_tags`
  ADD CONSTRAINT `fk_news_article_tags_article` FOREIGN KEY (`article_id`) REFERENCES `news_articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_news_article_tags_tag` FOREIGN KEY (`tag_id`) REFERENCES `news_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `news_comments`
--
ALTER TABLE `news_comments`
  ADD CONSTRAINT `fk_news_comments_article` FOREIGN KEY (`article_id`) REFERENCES `news_articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_news_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_affiliate` FOREIGN KEY (`affiliate_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orders_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orders_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment_events`
--
ALTER TABLE `payment_events`
  ADD CONSTRAINT `fk_payment_events_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_files`
--
ALTER TABLE `product_files`
  ADD CONSTRAINT `fk_product_files_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sellers`
--
ALTER TABLE `sellers`
  ADD CONSTRAINT `fk_sellers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `seller_payouts`
--
ALTER TABLE `seller_payouts`
  ADD CONSTRAINT `fk_seller_payouts_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_seller_payouts_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `fk_testimonials_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_testimonials_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_testimonials_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `fk_user_roles_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_roles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
