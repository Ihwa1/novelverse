-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2026 at 09:35 AM
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
-- Database: `novelverse`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `user_id` int(11) NOT NULL,
  `series_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chapters`
--

CREATE TABLE `chapters` (
  `id` int(11) NOT NULL,
  `series_id` int(11) NOT NULL,
  `chapter_number` varchar(20) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `is_free` tinyint(1) DEFAULT 1,
  `pages_json` longtext DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `release_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `views` int(11) NOT NULL DEFAULT 0,
  `external_id` varchar(64) DEFAULT NULL,
  `md_chapter_id` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chapters`
--

INSERT INTO `chapters` (`id`, `series_id`, `chapter_number`, `title`, `is_free`, `pages_json`, `content`, `release_date`, `views`, `external_id`, `md_chapter_id`) VALUES
(12, 2, '1', 'Oneshot', 1, '[\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/1-c698e1379389d0e60b47843be454c9685720aa88a634d20c65ed38b791c9c5d3.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/2-4efa45c4403da254a70417fca316cd7ade3cfec29c13821c0844beb8fe1c8eba.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/3-cba77c43228d76d822b9b9107824dfffd0cb695b74675700b1320795520afaf6.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/4-4e9d40cee34985311fd0edd8d3cdc06072a0ee3aaeefef824ec909e8d71fe398.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/5-7f1171b3f9b3af8020145ae4c1947f344f6db6c5f1852d3fc51dc7913700d92c.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/6-797a323e600b9e0ecb4da8d5719e6868580c74927c8ec43b5f37a0638cf8eff8.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/7-3809223da12ed94abad9e9246264ca925f052cc122424279a12ce21bf994785e.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/8-26f9baa8936d1a4df64b4a979053d061030598ea494d2835af0badf336ea2921.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/9-ed0b1e03e3398d0b3509057a64cd6923520d9c7309a4ffebe195b8a40543cf41.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/10-f91cc493e44b04e32a29271549e0880932e88f6dbaeb19aa4bed0c21882f9df9.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/11-66592dbd742b3b62ff9b6421df0105284bacb91bc42f12e0234ce3fa3b8bac45.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/12-e86b0cfc570536d2feaf06e185bc6c79f235352c402158576e64500c61973f15.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/13-c74617fab8383962048d658f586a176ac1de5deadb890d10b72660dda25544c1.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/14-1b304697e2d7dd8edd88c5ab7283217b23c662f78fcb95d00e95a44a47532ca2.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/15-b4a376a527d6c2b001e9353ab1c9d56c3b7dbf42d070c7189b4637e6ac6dd7c5.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/16-bdc39b857a5007318f5c39998401c0717448fcc5ece02f086b3531abf07feb3c.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/17-3357e371e0938db8e6a20cd60e043efceb2b5d131e8205b00a979d2e3e220cf9.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/18-8126838a85d9780a8c8bee1d3549f3013eff93aeada5ee238c6f04ed642498aa.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/19-2e2bbb36842362de1bdd4c6aad228b37333472b9adc5ccaf72aebcb9fd891d07.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/20-da7563acb263169c9dc524656eee363a9708d3d2d529a4ed4e68b18a688d92fd.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/21-3e6a86068b5237a0f4ec231cb0611bdf51edffbbbf8ef17aad5504d85839a172.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/22-9bd0a6c04138b432c9cf75045908f234e35eb9cc11b2c88ad856dd3cc410c67b.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/23-b8cf7ed3364d7c3c9a6689dac68b4ecb0a357f0ae0f2d095630b0711194c2f48.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/24-0677a687b1ccf2d791ba5b604ae93462f377ac940549d4f7c9514456a31db336.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/25-4b74faef2075b1dbc70d2f29d95539d3a38992a0951e1700c2a1108d73aa4dba.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/26-852dbec01c06139f2366f0d741effb770cbfdb6409eca2f966236926387cb43f.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/27-2fc2edc587419915fe2c9e8a7d57113d405a5871a7bc84cb61da94092f851f20.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/28-884bf939497d35157c661ec20fb5f0806ac9b08e20db67a2993bba6e39a1cdd2.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/29-73feee4da51e1ed98412d3f62fec805707fb34597296f345379c3672d92bd983.jpg\"]', NULL, '2023-08-30 16:00:00', 2, NULL, NULL),
(13, 6, '1', '', 1, NULL, NULL, '2024-03-04 21:11:19', 6, '8155d766-6757-4be3-956d-92f66d8358c9', NULL),
(14, 6, '2', '', 1, NULL, NULL, '2024-03-04 21:14:52', 1, '2437c7a3-b085-4f12-8713-c1c0195b7ace', NULL),
(15, 6, '3', '', 1, NULL, NULL, '2024-03-04 21:18:59', 0, 'ebaa2da5-b805-429b-85e2-94dab303e832', NULL),
(16, 6, '4', '', 0, NULL, NULL, '2024-03-05 20:08:04', 0, '57b3a2dd-5a58-4ea5-9f38-8eebdb5c2f52', NULL),
(17, 6, '5', '', 0, NULL, NULL, '2024-03-07 20:22:55', 0, 'e29f673e-e354-4705-aa07-c436a034d0d0', NULL),
(18, 6, '6', '', 0, NULL, NULL, '2024-03-13 19:53:50', 0, '3cc77ee8-569d-44b8-aead-de10af8d5d98', NULL),
(19, 6, '7', '', 0, NULL, NULL, '2024-03-13 19:59:47', 0, 'e254707b-5ce2-4716-bf57-7e1d89c85876', NULL),
(20, 6, '8', '', 0, NULL, NULL, '2024-03-15 22:31:36', 0, 'f655e085-1983-4467-9509-8cb831e59124', NULL),
(21, 6, '9', '', 0, NULL, NULL, '2024-03-15 22:56:33', 0, 'c3af2828-9c25-4bf5-a4f3-fdecad59f560', NULL),
(22, 6, '10', '', 0, NULL, NULL, '2024-03-22 14:09:30', 1, '45a7574a-5c90-436c-ab8f-79348b613ec3', NULL),
(23, 6, '11', '', 0, NULL, NULL, '2024-03-22 14:13:14', 1, 'fcf13bf9-d544-4d75-8e8d-0996ac0f4d45', NULL),
(24, 6, '12', '', 0, NULL, NULL, '2024-04-02 17:00:51', 0, '6fcae3a9-f0dc-4c20-9270-b9c698896800', NULL),
(25, 6, '13', '', 0, NULL, NULL, '2024-04-02 17:27:31', 0, '85e5fc63-046d-4a4b-bd15-34e8f937f300', NULL),
(26, 6, '14', '', 0, NULL, NULL, '2024-04-02 20:37:36', 0, 'f58a253e-610f-4065-a9a7-d6aa2f636fad', NULL),
(27, 6, '15', '', 0, NULL, NULL, '2024-04-08 20:02:54', 0, '1c85d684-ede7-4ed7-8556-8f2fb1e474bd', NULL),
(28, 6, '16', '', 0, NULL, NULL, '2024-04-18 15:11:55', 0, 'a3f1512c-3001-4c3a-98e6-da569ed8e5f5', NULL),
(29, 6, '17', '', 0, NULL, NULL, '2024-04-24 19:23:57', 0, '5689acc5-caba-4c22-87e9-336625331aa1', NULL),
(30, 6, '18', '', 0, NULL, NULL, '2024-04-24 19:27:58', 0, '0fb6bba2-3e7d-40eb-ad97-f591f05b7f7c', NULL),
(31, 6, '19', '', 0, NULL, NULL, '2024-04-30 14:28:30', 0, '657a474b-1996-4804-9718-a896f3e4fd0a', NULL),
(32, 6, '20', '', 0, NULL, NULL, '2024-05-09 20:13:48', 1, 'df7a6647-e7fc-404c-b11a-790db48c3e9d', NULL),
(33, 6, '21', '', 0, NULL, NULL, '2024-05-16 15:33:01', 1, '494ca72c-32ca-4b88-97c7-48f3e7ac48d2', NULL),
(34, 6, '22', '', 0, NULL, NULL, '2024-06-04 13:02:10', 2, '1bb29cd1-0ffe-477d-b7e6-f43e426c442a', NULL),
(35, 6, '23', '', 0, NULL, NULL, '2024-06-04 13:03:36', 1, '202d360b-500b-4302-ad59-132060dd9718', NULL),
(36, 6, '24', '', 0, NULL, NULL, '2024-06-04 13:05:24', 0, '25149708-37f3-45a2-b498-18103825a92f', NULL),
(37, 6, '25', '', 0, NULL, NULL, '2024-06-10 07:54:12', 0, 'c571654e-c96c-4747-9dd9-05e1e4612572', NULL),
(38, 6, '26', '', 0, NULL, NULL, '2024-06-17 10:19:24', 0, 'ef1836e6-f0cc-4d5f-9bd4-f84e958e3c5f', NULL),
(39, 6, '27', '', 0, NULL, NULL, '2024-06-24 11:58:57', 0, '06313bc4-2fc5-4f15-8378-727cf0c60947', NULL),
(40, 6, '28', '', 0, NULL, NULL, '2024-07-01 10:02:44', 0, 'cd35ee9a-1210-4a99-86ae-92395b3ef3f6', NULL),
(41, 6, '29', '', 0, NULL, NULL, '2024-07-08 13:09:09', 0, 'c8af1e3b-9a88-450b-b7a4-e3f0e575aad7', NULL),
(42, 6, '30', '', 0, NULL, NULL, '2024-07-16 06:05:34', 0, '2e981b5a-9f12-433e-bbfa-131705bbdcbb', NULL),
(43, 6, '31', '', 0, NULL, NULL, '2024-07-24 21:25:46', 0, '5ff6de05-6430-4282-b7c1-0862060e8c63', NULL),
(44, 6, '32', '', 0, NULL, NULL, '2024-08-11 00:36:54', 0, '094c4a6d-fffd-4dfd-adb3-db50bd38974b', NULL),
(45, 6, '33', '', 0, NULL, NULL, '2024-08-11 00:37:55', 0, '43d2f490-f124-4146-b7da-bb4e0d823fcc', NULL),
(46, 6, '34', '', 0, NULL, NULL, '2024-08-12 08:18:21', 0, '3fa77099-3744-4b32-94cc-99f4053317ff', NULL),
(47, 6, '35', '', 0, NULL, NULL, '2024-08-20 01:04:26', 0, '6df7e2ad-5b12-4e2a-990a-baf0907db2d5', NULL),
(48, 6, '36', '', 0, NULL, NULL, '2024-08-27 03:14:50', 0, '37452538-e4e9-4bd8-b89f-a8e215057a81', NULL),
(49, 6, '37', '', 0, NULL, NULL, '2024-09-08 15:45:37', 0, '58fbea53-bf37-4437-bb81-361d221afe9b', NULL),
(50, 6, '38', '', 0, NULL, NULL, '2024-09-12 06:12:21', 0, '59051e5d-60b2-44a0-844e-f234a7c926e3', NULL),
(51, 6, '39', '', 0, NULL, NULL, '2024-09-17 14:47:41', 0, 'd5df24be-38c2-4f1a-8602-18b6a3706f0b', NULL),
(52, 6, '40', '', 0, NULL, NULL, '2024-09-27 18:16:54', 0, 'd11e7eee-dad1-4ff9-ae2c-93825b2da597', NULL),
(53, 6, '41', '', 0, NULL, NULL, '2025-03-03 21:20:21', 0, 'd40e5083-b153-4860-8070-4020ead6cbdc', NULL),
(54, 6, '42', '', 0, NULL, NULL, '2025-03-03 21:21:57', 0, '8d9906cd-780b-4119-b044-2108bc710d1c', NULL),
(55, 6, '43', '', 0, NULL, NULL, '2025-03-03 21:23:42', 0, '9a0628e6-df6f-4791-84c3-5a0fa3722375', NULL),
(56, 6, '44', '', 0, NULL, NULL, '2025-03-03 21:25:41', 0, 'fbe8a5fc-e59b-4e7e-9d28-961ac5d7ab70', NULL),
(57, 6, '45', '', 0, NULL, NULL, '2025-03-03 21:27:25', 0, '091963d3-2920-48ff-83ad-700688db762f', NULL),
(58, 6, '46', '', 0, NULL, NULL, '2025-03-03 21:29:05', 0, 'ba624bf7-77de-4746-97e1-f1097e3d281e', NULL),
(59, 6, '47', '', 0, NULL, NULL, '2025-03-03 21:31:04', 0, 'df03544c-ed95-4bf5-87fe-ff0e71bf6a9c', NULL),
(60, 6, '48', '', 0, NULL, NULL, '2025-03-03 21:33:00', 0, '890e8b3f-84f6-4d36-b41c-d34e9604165f', NULL),
(61, 6, '49', '', 0, NULL, NULL, '2025-03-03 21:34:43', 0, '64fe8e08-31b9-47b7-87c3-42c7ae9a9bc1', NULL),
(62, 6, '50', '', 0, NULL, NULL, '2025-03-03 21:36:21', 0, '3700fa4b-5c60-460d-a817-e08a926e75a4', NULL),
(63, 6, '51', '', 0, NULL, NULL, '2025-03-03 21:38:22', 0, '31a30a03-e2cc-46e8-90fe-fc5e6f338688', NULL),
(64, 6, '52', '', 0, NULL, NULL, '2025-03-03 21:40:12', 0, '81fe3c78-1d0c-4b22-a488-858c05d06ba7', NULL),
(65, 6, '53', '', 0, NULL, NULL, '2025-03-03 21:42:04', 0, '83c73070-f65f-4402-89a7-515e4fa99886', NULL),
(66, 6, '54', '', 0, NULL, NULL, '2025-03-03 21:43:47', 0, '01e93760-15f3-49e5-921e-73bddafdde79', NULL),
(67, 6, '55', '', 0, NULL, NULL, '2025-03-03 21:45:33', 0, 'cbf0fa36-b34d-4038-899d-493566ee2464', NULL),
(68, 6, '56', '', 0, NULL, NULL, '2025-03-03 21:47:25', 0, '6d43e123-18f2-42d3-8108-1a07ce2cbb68', NULL),
(69, 6, '57', '', 0, NULL, NULL, '2025-03-03 21:49:05', 0, 'de481116-d6c2-45d0-aee7-ae3c9a60d4b7', NULL),
(70, 6, '58', '', 0, NULL, NULL, '2025-03-03 21:50:43', 0, '7a22111c-5079-457b-a063-e78f4213e7ee', NULL),
(71, 6, '59', '', 0, NULL, NULL, '2025-03-03 21:52:50', 0, '37ad3ac0-50f0-4ed8-8894-3cf41e6b9676', NULL),
(72, 6, '60', '', 0, NULL, NULL, '2025-03-03 21:54:32', 0, 'f1f3383d-d89d-4425-8dbd-ee81f205cb0e', NULL),
(73, 6, '61', '', 0, NULL, NULL, '2025-03-03 21:56:50', 0, '7251374b-dddd-44d3-98a8-d70a51d47ba7', NULL),
(74, 6, '62', '', 0, NULL, NULL, '2025-03-03 21:58:57', 0, '55ecc051-4bd0-43c6-aad5-dc0c71df151c', NULL),
(75, 6, '63', '', 0, NULL, NULL, '2025-03-03 22:00:44', 0, 'e4b3d8f0-55e2-4d8b-af00-941413750240', NULL),
(76, 6, '64', 'Purpose', 0, NULL, NULL, '2025-03-10 19:51:13', 0, '28732e00-69d6-49fe-9a41-191b8ffb32e5', NULL),
(77, 6, '65', '', 0, NULL, NULL, '2025-03-21 20:35:05', 0, '3815d31c-e5c0-4163-b0b8-965af02afd57', NULL),
(78, 6, '66', '', 0, NULL, NULL, '2025-03-30 16:04:10', 0, '6957b412-c507-4767-87ff-d81695622af5', NULL),
(79, 6, '67', '', 0, NULL, NULL, '2025-04-06 15:00:54', 0, '87b0fa15-864d-4358-bc43-91b15f8318b1', NULL),
(80, 6, '68', '', 0, NULL, NULL, '2025-04-07 20:26:48', 0, '53534ec0-5ea3-4246-aa81-4f5b0470c764', NULL),
(81, 6, '69', 'Sharp Ki', 0, NULL, NULL, '2025-04-14 18:34:37', 0, '987eac63-e5d1-4a6c-a27c-00e9d9bfa075', NULL),
(82, 6, '70', '', 0, NULL, NULL, '2025-04-21 15:44:15', 0, '04c75da7-b6b5-4f82-9a24-864066a83ba5', NULL),
(83, 6, '71', '', 0, NULL, NULL, '2025-04-28 19:53:26', 0, 'eeb3e911-e7e6-42c3-ab36-971c3039ebf9', NULL),
(84, 6, '72', 'Lord of the Dead (1)', 0, NULL, NULL, '2025-05-06 19:12:05', 0, '1ec6ab9f-a425-467e-ba24-9d60ae3b7d76', NULL),
(85, 6, '73', 'Lord of the Dead (2)', 0, NULL, NULL, '2025-05-12 21:58:37', 0, '172a77c1-b596-45a8-9c7a-62a62f052992', NULL),
(86, 7, '1', '', 1, NULL, NULL, '2026-10-07 16:44:13', 7, '8d15e8cc-d91d-490e-9c09-dc381cf2b1f8', NULL),
(87, 7, '2', '', 1, NULL, NULL, '2026-10-07 16:44:58', 3, '6df610a5-70e4-4fc7-a93c-6a381b1cfa04', NULL),
(88, 7, '3', '', 0, NULL, NULL, '2026-10-07 16:45:29', 1, 'abe98c4e-0819-43c3-9276-e54d34e69fc0', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `series_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('gcash','card') NOT NULL,
  `paymongo_intent_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','paid','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reading_progress`
--

CREATE TABLE `reading_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `series_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reading_progress`
--

INSERT INTO `reading_progress` (`id`, `user_id`, `series_id`, `chapter_id`, `updated_at`) VALUES
(1, 1, 2, 12, '2026-10-06 08:34:03'),
(3, 2, 6, 34, '2026-10-08 05:44:18'),
(6, 2, 7, 86, '2026-10-08 09:58:31'),
(7, 1, 7, 86, '2026-10-07 16:47:52'),
(24, 3, 6, 23, '2026-10-08 10:14:53');

-- --------------------------------------------------------

--
-- Table structure for table `series`
--

CREATE TABLE `series` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `type` enum('manga','manhwa','manhua','novel') NOT NULL,
  `description` text DEFAULT NULL,
  `cover_url` varchar(500) DEFAULT NULL,
  `author` varchar(120) DEFAULT NULL,
  `artist` varchar(120) DEFAULT NULL,
  `status` enum('ongoing','completed','hiatus') DEFAULT 'ongoing',
  `is_novel` tinyint(1) DEFAULT 0,
  `novel_price` decimal(10,2) DEFAULT 0.00,
  `free_chapters` int(11) DEFAULT 3,
  `external_source` varchar(50) DEFAULT NULL,
  `external_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `views` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `series`
--

INSERT INTO `series` (`id`, `title`, `slug`, `type`, `description`, `cover_url`, `author`, `artist`, `status`, `is_novel`, `novel_price`, `free_chapters`, `external_source`, `external_id`, `created_at`, `views`, `created_by`) VALUES
(2, 'Sono Koi, Damasarete Imasenka?', 'sono-koi-damasarete-imasenka', 'manga', 'A love story that began as a scam! A roller coaster romance!', 'https://uploads.mangadex.org/covers/e89d4b05-714f-4e64-8105-4243858909f0/a69cb42c-a731-4779-8fe4-040b6413de4a.jpg', 'Tiv', 'Tiv', 'completed', 0, 0.00, 1, 'mangadex', 'e89d4b05-714f-4e64-8105-4243858909f0', '2026-10-01 15:56:41', 0, NULL),
(6, 'Myst, Might, Mayhem', 'myst-might-mayhem', 'manhwa', 'All Jeong wants is a second chance -- for revenge! His grandfather’s gruesome death has transformed him into the bloodthirsty and ruthless Scythe Demon. He will stop at nothing until he has killed the man responsible. Yet rage and determination alone are not enough. He must train in martial arts if he ever wants to stand a chance against such a formidable foe. Will he succeed in his revenge on the murderer of his grandfather?\n\n**Original Webtoon:**  \n- [Naver Webtoon](https://comic.naver.com/webtoon/list?titleId=821597)\n- [Naver Series](https://series.naver.com/comic/detail.series?productNo=10782038)\n\n**Official Translations:**  \n- [S.Chinese](https://www.dongmanmanhua.cn/ANCIENTCHINESE/guaililuanshen/list?title_no=2722)\n- [T.Chinese](https://www.webtoons.com/zh-hant/martial-arts/myst-might-mayhem/list?title_no=6345)\n- [Indonesian](https://www.webtoons.com/id/fantasy/myst-might-mayhem/list?title_no=6367)\n- [Thai](https://www.webtoons.com/th/fantasy/myst-might-mayhem/list?title_no=6511)\n- [Japanese](https://manga.line.me/product/periodic?id=Z0003353)\n- [French](https://www.webtoons.com/fr/action/myst-might-mayhem/list?title_no=6711)', 'https://uploads.mangadex.org/covers/df5f8e3d-64a1-4287-8f08-4a732e9cb757/d44fa20d-f221-476a-a7e7-460b6e7ff7ba.jpg', NULL, NULL, 'hiatus', 0, 0.00, 3, 'mangadex', 'df5f8e3d-64a1-4287-8f08-4a732e9cb757', '2026-10-07 14:30:06', 0, NULL),
(7, 'Yoimiya Nagi ga Mane ni Yashoku o Tsukuru Wake', 'yoimiya-nagi-ga-mane-ni-yashoku-o-tsukuru-wake', 'manga', 'Yoimiya Nagi ga Mane ni Yashoku o Tsukuru Wake', 'uploads/covers/cover_7_1791436739.jpg', '', NULL, 'ongoing', 0, 0.00, 2, NULL, NULL, '2026-10-07 16:19:31', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `role` enum('reader','author') NOT NULL DEFAULT 'reader'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `created_at`, `is_admin`, `role`) VALUES
(1, 'testing', 'test@gmail.com', '$2y$10$QEDJtzjV9bCFo5x7FsIbSerm.R9AuwOD8iul8vsnfmfGWePs2.HW2', '2026-10-01 16:36:47', 1, 'reader'),
(2, 'tester author', 'tester@gmail.com', '$2y$10$FXmRYGagTO4QXqIf07kGg.1EdndWdMccU7tfnka1/ZCjFkHj1xw3K', '2026-10-07 13:24:22', 0, 'author'),
(3, 'justine', 'just@gmail.com', '$2y$10$MTCWawS62nDEZKRIWkkrz.EjZsV6dmI6bWlohM4QGmlsIEU9y0CWC', '2026-10-08 10:13:16', 0, 'reader'),
(4, 'just', 'justine@gmail.com', '$2y$10$RUVYEm7hj/MV/9TTM3fbWe/EXBxt3tLckU/lY6VEIEDBOenW5OPHi', '2026-10-08 10:15:22', 0, 'author');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`user_id`,`series_id`),
  ADD KEY `series_id` (`series_id`);

--
-- Indexes for table `chapters`
--
ALTER TABLE `chapters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_series_chapter` (`series_id`,`chapter_number`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_purchase` (`user_id`,`series_id`),
  ADD KEY `series_id` (`series_id`);

--
-- Indexes for table `reading_progress`
--
ALTER TABLE `reading_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_series` (`user_id`,`series_id`);

--
-- Indexes for table `series`
--
ALTER TABLE `series`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `uniq_slug` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chapters`
--
ALTER TABLE `chapters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reading_progress`
--
ALTER TABLE `reading_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `series`
--
ALTER TABLE `series`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD CONSTRAINT `bookmarks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookmarks_ibfk_2` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`);

--
-- Constraints for table `chapters`
--
ALTER TABLE `chapters`
  ADD CONSTRAINT `chapters_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
