-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2026 at 05:29 PM
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
  `md_chapter_id` varchar(64) DEFAULT NULL,
  `is_premium` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chapters`
--

INSERT INTO `chapters` (`id`, `series_id`, `chapter_number`, `title`, `is_free`, `pages_json`, `content`, `release_date`, `views`, `external_id`, `md_chapter_id`, `is_premium`) VALUES
(12, 2, '1', 'Oneshot', 1, '[\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/1-c698e1379389d0e60b47843be454c9685720aa88a634d20c65ed38b791c9c5d3.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/2-4efa45c4403da254a70417fca316cd7ade3cfec29c13821c0844beb8fe1c8eba.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/3-cba77c43228d76d822b9b9107824dfffd0cb695b74675700b1320795520afaf6.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/4-4e9d40cee34985311fd0edd8d3cdc06072a0ee3aaeefef824ec909e8d71fe398.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/5-7f1171b3f9b3af8020145ae4c1947f344f6db6c5f1852d3fc51dc7913700d92c.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/6-797a323e600b9e0ecb4da8d5719e6868580c74927c8ec43b5f37a0638cf8eff8.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/7-3809223da12ed94abad9e9246264ca925f052cc122424279a12ce21bf994785e.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/8-26f9baa8936d1a4df64b4a979053d061030598ea494d2835af0badf336ea2921.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/9-ed0b1e03e3398d0b3509057a64cd6923520d9c7309a4ffebe195b8a40543cf41.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/10-f91cc493e44b04e32a29271549e0880932e88f6dbaeb19aa4bed0c21882f9df9.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/11-66592dbd742b3b62ff9b6421df0105284bacb91bc42f12e0234ce3fa3b8bac45.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/12-e86b0cfc570536d2feaf06e185bc6c79f235352c402158576e64500c61973f15.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/13-c74617fab8383962048d658f586a176ac1de5deadb890d10b72660dda25544c1.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/14-1b304697e2d7dd8edd88c5ab7283217b23c662f78fcb95d00e95a44a47532ca2.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/15-b4a376a527d6c2b001e9353ab1c9d56c3b7dbf42d070c7189b4637e6ac6dd7c5.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/16-bdc39b857a5007318f5c39998401c0717448fcc5ece02f086b3531abf07feb3c.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/17-3357e371e0938db8e6a20cd60e043efceb2b5d131e8205b00a979d2e3e220cf9.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/18-8126838a85d9780a8c8bee1d3549f3013eff93aeada5ee238c6f04ed642498aa.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/19-2e2bbb36842362de1bdd4c6aad228b37333472b9adc5ccaf72aebcb9fd891d07.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/20-da7563acb263169c9dc524656eee363a9708d3d2d529a4ed4e68b18a688d92fd.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/21-3e6a86068b5237a0f4ec231cb0611bdf51edffbbbf8ef17aad5504d85839a172.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/22-9bd0a6c04138b432c9cf75045908f234e35eb9cc11b2c88ad856dd3cc410c67b.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/23-b8cf7ed3364d7c3c9a6689dac68b4ecb0a357f0ae0f2d095630b0711194c2f48.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/24-0677a687b1ccf2d791ba5b604ae93462f377ac940549d4f7c9514456a31db336.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/25-4b74faef2075b1dbc70d2f29d95539d3a38992a0951e1700c2a1108d73aa4dba.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/26-852dbec01c06139f2366f0d741effb770cbfdb6409eca2f966236926387cb43f.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/27-2fc2edc587419915fe2c9e8a7d57113d405a5871a7bc84cb61da94092f851f20.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/28-884bf939497d35157c661ec20fb5f0806ac9b08e20db67a2993bba6e39a1cdd2.jpg\",\"https://cmdxd98sb0x3yprd.mangadex.network/data/13aa84026d6c6aa50a6d27c7b2becd59/29-73feee4da51e1ed98412d3f62fec805707fb34597296f345379c3672d92bd983.jpg\"]', NULL, '2023-08-30 16:00:00', 2, NULL, NULL, 0),
(13, 6, '1', '', 1, NULL, NULL, '2024-03-04 21:11:19', 6, '8155d766-6757-4be3-956d-92f66d8358c9', NULL, 0),
(14, 6, '2', '', 1, NULL, NULL, '2024-03-04 21:14:52', 1, '2437c7a3-b085-4f12-8713-c1c0195b7ace', NULL, 0),
(15, 6, '3', '', 1, NULL, NULL, '2024-03-04 21:18:59', 0, 'ebaa2da5-b805-429b-85e2-94dab303e832', NULL, 0),
(16, 6, '4', '', 0, NULL, NULL, '2024-03-05 20:08:04', 0, '57b3a2dd-5a58-4ea5-9f38-8eebdb5c2f52', NULL, 0),
(17, 6, '5', '', 0, NULL, NULL, '2024-03-07 20:22:55', 0, 'e29f673e-e354-4705-aa07-c436a034d0d0', NULL, 0),
(18, 6, '6', '', 0, NULL, NULL, '2024-03-13 19:53:50', 0, '3cc77ee8-569d-44b8-aead-de10af8d5d98', NULL, 0),
(19, 6, '7', '', 0, NULL, NULL, '2024-03-13 19:59:47', 0, 'e254707b-5ce2-4716-bf57-7e1d89c85876', NULL, 0),
(20, 6, '8', '', 0, NULL, NULL, '2024-03-15 22:31:36', 0, 'f655e085-1983-4467-9509-8cb831e59124', NULL, 0),
(21, 6, '9', '', 0, NULL, NULL, '2024-03-15 22:56:33', 0, 'c3af2828-9c25-4bf5-a4f3-fdecad59f560', NULL, 0),
(22, 6, '10', '', 0, NULL, NULL, '2024-03-22 14:09:30', 1, '45a7574a-5c90-436c-ab8f-79348b613ec3', NULL, 0),
(23, 6, '11', '', 0, NULL, NULL, '2024-03-22 14:13:14', 1, 'fcf13bf9-d544-4d75-8e8d-0996ac0f4d45', NULL, 0),
(24, 6, '12', '', 0, NULL, NULL, '2024-04-02 17:00:51', 0, '6fcae3a9-f0dc-4c20-9270-b9c698896800', NULL, 0),
(25, 6, '13', '', 0, NULL, NULL, '2024-04-02 17:27:31', 0, '85e5fc63-046d-4a4b-bd15-34e8f937f300', NULL, 0),
(26, 6, '14', '', 0, NULL, NULL, '2024-04-02 20:37:36', 0, 'f58a253e-610f-4065-a9a7-d6aa2f636fad', NULL, 0),
(27, 6, '15', '', 0, NULL, NULL, '2024-04-08 20:02:54', 0, '1c85d684-ede7-4ed7-8556-8f2fb1e474bd', NULL, 0),
(28, 6, '16', '', 0, NULL, NULL, '2024-04-18 15:11:55', 0, 'a3f1512c-3001-4c3a-98e6-da569ed8e5f5', NULL, 0),
(29, 6, '17', '', 0, NULL, NULL, '2024-04-24 19:23:57', 0, '5689acc5-caba-4c22-87e9-336625331aa1', NULL, 0),
(30, 6, '18', '', 0, NULL, NULL, '2024-04-24 19:27:58', 0, '0fb6bba2-3e7d-40eb-ad97-f591f05b7f7c', NULL, 0),
(31, 6, '19', '', 0, NULL, NULL, '2024-04-30 14:28:30', 0, '657a474b-1996-4804-9718-a896f3e4fd0a', NULL, 0),
(32, 6, '20', '', 0, NULL, NULL, '2024-05-09 20:13:48', 1, 'df7a6647-e7fc-404c-b11a-790db48c3e9d', NULL, 0),
(33, 6, '21', '', 0, NULL, NULL, '2024-05-16 15:33:01', 1, '494ca72c-32ca-4b88-97c7-48f3e7ac48d2', NULL, 0),
(34, 6, '22', '', 0, NULL, NULL, '2024-06-04 13:02:10', 2, '1bb29cd1-0ffe-477d-b7e6-f43e426c442a', NULL, 0),
(35, 6, '23', '', 0, NULL, NULL, '2024-06-04 13:03:36', 1, '202d360b-500b-4302-ad59-132060dd9718', NULL, 0),
(36, 6, '24', '', 0, NULL, NULL, '2024-06-04 13:05:24', 0, '25149708-37f3-45a2-b498-18103825a92f', NULL, 0),
(37, 6, '25', '', 0, NULL, NULL, '2024-06-10 07:54:12', 0, 'c571654e-c96c-4747-9dd9-05e1e4612572', NULL, 0),
(38, 6, '26', '', 0, NULL, NULL, '2024-06-17 10:19:24', 0, 'ef1836e6-f0cc-4d5f-9bd4-f84e958e3c5f', NULL, 0),
(39, 6, '27', '', 0, NULL, NULL, '2024-06-24 11:58:57', 0, '06313bc4-2fc5-4f15-8378-727cf0c60947', NULL, 0),
(40, 6, '28', '', 0, NULL, NULL, '2024-07-01 10:02:44', 0, 'cd35ee9a-1210-4a99-86ae-92395b3ef3f6', NULL, 0),
(41, 6, '29', '', 0, NULL, NULL, '2024-07-08 13:09:09', 0, 'c8af1e3b-9a88-450b-b7a4-e3f0e575aad7', NULL, 0),
(42, 6, '30', '', 0, NULL, NULL, '2024-07-16 06:05:34', 0, '2e981b5a-9f12-433e-bbfa-131705bbdcbb', NULL, 0),
(43, 6, '31', '', 0, NULL, NULL, '2024-07-24 21:25:46', 0, '5ff6de05-6430-4282-b7c1-0862060e8c63', NULL, 0),
(44, 6, '32', '', 0, NULL, NULL, '2024-08-11 00:36:54', 0, '094c4a6d-fffd-4dfd-adb3-db50bd38974b', NULL, 0),
(45, 6, '33', '', 0, NULL, NULL, '2024-08-11 00:37:55', 0, '43d2f490-f124-4146-b7da-bb4e0d823fcc', NULL, 0),
(46, 6, '34', '', 0, NULL, NULL, '2024-08-12 08:18:21', 0, '3fa77099-3744-4b32-94cc-99f4053317ff', NULL, 0),
(47, 6, '35', '', 0, NULL, NULL, '2024-08-20 01:04:26', 0, '6df7e2ad-5b12-4e2a-990a-baf0907db2d5', NULL, 0),
(48, 6, '36', '', 0, NULL, NULL, '2024-08-27 03:14:50', 0, '37452538-e4e9-4bd8-b89f-a8e215057a81', NULL, 0),
(49, 6, '37', '', 0, NULL, NULL, '2024-09-08 15:45:37', 0, '58fbea53-bf37-4437-bb81-361d221afe9b', NULL, 0),
(50, 6, '38', '', 0, NULL, NULL, '2024-09-12 06:12:21', 0, '59051e5d-60b2-44a0-844e-f234a7c926e3', NULL, 0),
(51, 6, '39', '', 0, NULL, NULL, '2024-09-17 14:47:41', 0, 'd5df24be-38c2-4f1a-8602-18b6a3706f0b', NULL, 0),
(52, 6, '40', '', 0, NULL, NULL, '2024-09-27 18:16:54', 0, 'd11e7eee-dad1-4ff9-ae2c-93825b2da597', NULL, 0),
(53, 6, '41', '', 0, NULL, NULL, '2025-03-03 21:20:21', 0, 'd40e5083-b153-4860-8070-4020ead6cbdc', NULL, 0),
(54, 6, '42', '', 0, NULL, NULL, '2025-03-03 21:21:57', 0, '8d9906cd-780b-4119-b044-2108bc710d1c', NULL, 0),
(55, 6, '43', '', 0, NULL, NULL, '2025-03-03 21:23:42', 0, '9a0628e6-df6f-4791-84c3-5a0fa3722375', NULL, 0),
(56, 6, '44', '', 0, NULL, NULL, '2025-03-03 21:25:41', 0, 'fbe8a5fc-e59b-4e7e-9d28-961ac5d7ab70', NULL, 0),
(57, 6, '45', '', 0, NULL, NULL, '2025-03-03 21:27:25', 0, '091963d3-2920-48ff-83ad-700688db762f', NULL, 0),
(58, 6, '46', '', 0, NULL, NULL, '2025-03-03 21:29:05', 0, 'ba624bf7-77de-4746-97e1-f1097e3d281e', NULL, 0),
(59, 6, '47', '', 0, NULL, NULL, '2025-03-03 21:31:04', 0, 'df03544c-ed95-4bf5-87fe-ff0e71bf6a9c', NULL, 0),
(60, 6, '48', '', 0, NULL, NULL, '2025-03-03 21:33:00', 0, '890e8b3f-84f6-4d36-b41c-d34e9604165f', NULL, 0),
(61, 6, '49', '', 0, NULL, NULL, '2025-03-03 21:34:43', 0, '64fe8e08-31b9-47b7-87c3-42c7ae9a9bc1', NULL, 0),
(62, 6, '50', '', 0, NULL, NULL, '2025-03-03 21:36:21', 0, '3700fa4b-5c60-460d-a817-e08a926e75a4', NULL, 0),
(63, 6, '51', '', 0, NULL, NULL, '2025-03-03 21:38:22', 0, '31a30a03-e2cc-46e8-90fe-fc5e6f338688', NULL, 0),
(64, 6, '52', '', 0, NULL, NULL, '2025-03-03 21:40:12', 0, '81fe3c78-1d0c-4b22-a488-858c05d06ba7', NULL, 0),
(65, 6, '53', '', 0, NULL, NULL, '2025-03-03 21:42:04', 0, '83c73070-f65f-4402-89a7-515e4fa99886', NULL, 0),
(66, 6, '54', '', 0, NULL, NULL, '2025-03-03 21:43:47', 0, '01e93760-15f3-49e5-921e-73bddafdde79', NULL, 0),
(67, 6, '55', '', 0, NULL, NULL, '2025-03-03 21:45:33', 0, 'cbf0fa36-b34d-4038-899d-493566ee2464', NULL, 0),
(68, 6, '56', '', 0, NULL, NULL, '2025-03-03 21:47:25', 0, '6d43e123-18f2-42d3-8108-1a07ce2cbb68', NULL, 0),
(69, 6, '57', '', 0, NULL, NULL, '2025-03-03 21:49:05', 0, 'de481116-d6c2-45d0-aee7-ae3c9a60d4b7', NULL, 0),
(70, 6, '58', '', 0, NULL, NULL, '2025-03-03 21:50:43', 0, '7a22111c-5079-457b-a063-e78f4213e7ee', NULL, 0),
(71, 6, '59', '', 0, NULL, NULL, '2025-03-03 21:52:50', 0, '37ad3ac0-50f0-4ed8-8894-3cf41e6b9676', NULL, 0),
(72, 6, '60', '', 0, NULL, NULL, '2025-03-03 21:54:32', 0, 'f1f3383d-d89d-4425-8dbd-ee81f205cb0e', NULL, 0),
(73, 6, '61', '', 0, NULL, NULL, '2025-03-03 21:56:50', 0, '7251374b-dddd-44d3-98a8-d70a51d47ba7', NULL, 0),
(74, 6, '62', '', 0, NULL, NULL, '2025-03-03 21:58:57', 0, '55ecc051-4bd0-43c6-aad5-dc0c71df151c', NULL, 0),
(75, 6, '63', '', 0, NULL, NULL, '2025-03-03 22:00:44', 0, 'e4b3d8f0-55e2-4d8b-af00-941413750240', NULL, 0),
(76, 6, '64', 'Purpose', 0, NULL, NULL, '2025-03-10 19:51:13', 0, '28732e00-69d6-49fe-9a41-191b8ffb32e5', NULL, 0),
(77, 6, '65', '', 0, NULL, NULL, '2025-03-21 20:35:05', 0, '3815d31c-e5c0-4163-b0b8-965af02afd57', NULL, 0),
(78, 6, '66', '', 0, NULL, NULL, '2025-03-30 16:04:10', 0, '6957b412-c507-4767-87ff-d81695622af5', NULL, 0),
(79, 6, '67', '', 0, NULL, NULL, '2025-04-06 15:00:54', 0, '87b0fa15-864d-4358-bc43-91b15f8318b1', NULL, 0),
(80, 6, '68', '', 0, NULL, NULL, '2025-04-07 20:26:48', 0, '53534ec0-5ea3-4246-aa81-4f5b0470c764', NULL, 0),
(81, 6, '69', 'Sharp Ki', 0, NULL, NULL, '2025-04-14 18:34:37', 0, '987eac63-e5d1-4a6c-a27c-00e9d9bfa075', NULL, 0),
(82, 6, '70', '', 0, NULL, NULL, '2025-04-21 15:44:15', 0, '04c75da7-b6b5-4f82-9a24-864066a83ba5', NULL, 0),
(83, 6, '71', '', 0, NULL, NULL, '2025-04-28 19:53:26', 0, 'eeb3e911-e7e6-42c3-ab36-971c3039ebf9', NULL, 0),
(84, 6, '72', 'Lord of the Dead (1)', 0, NULL, NULL, '2025-05-06 19:12:05', 0, '1ec6ab9f-a425-467e-ba24-9d60ae3b7d76', NULL, 0),
(85, 6, '73', 'Lord of the Dead (2)', 0, NULL, NULL, '2025-05-12 21:58:37', 0, '172a77c1-b596-45a8-9c7a-62a62f052992', NULL, 0),
(86, 7, '1', '', 1, NULL, NULL, '2026-10-07 16:44:13', 7, '8d15e8cc-d91d-490e-9c09-dc381cf2b1f8', NULL, 0),
(87, 7, '2', '', 1, NULL, NULL, '2026-10-07 16:44:58', 3, '6df610a5-70e4-4fc7-a93c-6a381b1cfa04', NULL, 0),
(88, 7, '3', '', 0, NULL, NULL, '2026-10-07 16:45:29', 1, 'abe98c4e-0819-43c3-9276-e54d34e69fc0', NULL, 0),
(1552, 20, '0', 'The Beginning of Everything', 1, NULL, NULL, '2026-10-13 08:40:13', 0, 'rv7o6', NULL, 0),
(1556, 20, '1', 'The Top of the Tower', 1, NULL, NULL, '2026-10-13 08:40:13', 0, 'qr8nr', NULL, 0),
(1560, 20, '2', 'The Opening Act', 1, NULL, NULL, '2026-10-13 08:40:13', 0, 'pqVm6', NULL, 0),
(1564, 20, '3', 'The Mangrove Tree of Greed (1)', 1, NULL, NULL, '2026-10-13 08:40:13', 0, 'noJkl', NULL, 0),
(1568, 20, '4', 'The Mangrove Tree of Greed (2)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'mnXj0', NULL, 0),
(1572, 20, '5', 'The National Museum of Korea (1)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'klMLX', NULL, 0),
(1576, 20, '6', 'The National Museum of Korea (2)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'gGXE6', NULL, 0),
(1580, 20, '7', 'The National Museum of Korea (3)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, '0m37X', NULL, 0),
(1584, 20, '8', 'The National Museum of Korea (4)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, '9yXN3', NULL, 0),
(1588, 20, '9', 'Labyrinthus (1)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, '8xYNo', NULL, 0),
(1592, 20, '10', 'Labyrinthus (2)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, '7wJ0A', NULL, 0),
(1596, 20, '11', 'Labyrinthus (3)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, '6vVN9', NULL, 0),
(1600, 20, '12', 'Labyrinthus (4)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, '5r04R', NULL, 0),
(1604, 20, '13', 'Labyrinthus (5)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, '3pJXQ', NULL, 0),
(1608, 20, '14', 'Labyrinthus (6)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, '2oJNv', NULL, 0),
(1612, 20, '15', 'Vet Players of the Tower', 0, NULL, NULL, '2026-10-13 08:40:13', 0, '1n7Nj', NULL, 0),
(1616, 20, '16', 'Awakening Test', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'ZoXn6', NULL, 0),
(1620, 20, '17', 'Ruins, Cloister of the Fallen', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'YnXmp', NULL, 0),
(1624, 20, '18', 'The Saintess of Ambsterdam', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'X0V2m', NULL, 0),
(1628, 20, '19', 'Cheon Yusung of the Sword Star (1)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'Qu6jDq5L', NULL, 0),
(1632, 20, '20', 'Cheon Yusung of the Sword Star (2)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'Q2LBY', NULL, 0),
(1636, 20, '21', 'Master of the Cloister of the Fallen (1)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'IllOCjCh', NULL, 0),
(1640, 20, '22', 'Master of the Cloister of the Fallen (2)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'O29zN', NULL, 0),
(1644, 20, '23', 'Master of the Cloister of the Fallen (3)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'WlzL4', NULL, 0),
(1648, 20, '24', 'Raven Guild (1)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'jkNDP', NULL, 0),
(1652, 20, '25', 'Raven Guild (2)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'vz6wM', NULL, 0),
(1656, 20, '26', 'How Vet Players Grow Stronger (1)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, '0mkML', NULL, 0),
(1660, 20, '27', 'How Vet Players Grow Stronger (2)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, '7WcFICXF', NULL, 0),
(1664, 20, '28', 'How Vet Players Grow Stronger (3)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'RgNRX_Je', NULL, 0),
(1668, 20, '29', 'Infinite Numbers (1)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, '2oKPW', NULL, 0),
(1672, 20, '30', 'Infinite Numbers (2)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'qrgY0', NULL, 0),
(1676, 20, '31', 'A Swordman\'s Proposal', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'L2LWr', NULL, 0),
(1680, 20, '32', 'Press Conferance', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'YRpMY', NULL, 0),
(1684, 20, '33', 'Black Market (1)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, '5gQxB', NULL, 0),
(1688, 20, '34', 'Black Market (2)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'wlrow', NULL, 0),
(1692, 20, '35', 'Black Market (3)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'EB66v', NULL, 0),
(1696, 20, '36', 'The Head of the Ataraxia Family', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'mPX10', NULL, 0),
(1700, 20, '37', 'Karma', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'VKMxv', NULL, 0),
(1704, 20, '38', 'The End of the 3rd Floor, the Heartless Army (1)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'wcKdSCzo', NULL, 0),
(1708, 20, '39', 'The End of the 3rd Floor, the Heartless Army (2)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'q91KR', NULL, 0),
(1712, 20, '40', 'The End of the 3rd Floor, the Heartless Army (3)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'JRvqg', NULL, 0),
(1716, 20, '41', 'Thousand-Armed and Thousand-Eyed Avalokiteshvara (1)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'QBEp0', NULL, 0),
(1720, 20, '42', 'Thousand-Armed and Thousand-Eyed Avalokiteshvara (2)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'OzBnY', NULL, 0),
(1724, 20, '43', 'The Demon Society', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'MxzPO', NULL, 0),
(1728, 20, '44', '1 Man Army (1)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'KvxNM', NULL, 0),
(1732, 20, '45', '1 Man Army (2)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'JrwMJ', NULL, 0),
(1736, 20, '46', '1 Man Army (3)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'qnxlr', NULL, 0),
(1740, 20, '47', 'Magic Discharge Disorder (1)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, '2NVnM', NULL, 0),
(1744, 20, '48', 'Magic Discharge Disorder (2)', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'PE0Jy', NULL, 0),
(1748, 20, '49', 'A Tower Resident', 0, NULL, NULL, '2026-10-13 08:40:13', 0, 'GV9V5', NULL, 0),
(1752, 20, '50', 'Base Defensive Battle', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'rlVK4', NULL, 0),
(1756, 20, '51', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'l9VZ1', NULL, 0),
(1760, 20, '52', 'The Five Noble Clans (2)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'g9O56', NULL, 0),
(1764, 20, '53', 'Defensive Tactics of a Vet Player (1)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'J9oPD', NULL, 0),
(1768, 20, '54', 'Pendariel, The Matrix of the Dead (1)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'yvKyE', NULL, 0),
(1772, 20, '55', 'Pendariel, The Matrix of the Dead (2)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'wqOQz', NULL, 0),
(1776, 20, '56', 'Pendariel, The Matrix of the Dead (3)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'lWO9V', NULL, 0),
(1780, 20, '57', 'The Great Magic Library', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'kWM2v', NULL, 0),
(1784, 20, '58', 'Psychiatric Ward (1)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '1knmV', NULL, 0),
(1788, 20, '59', 'Psychiatric Ward (2)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '0jZqL', NULL, 0),
(1792, 20, '60', 'Psychiatric Ward (3)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'zgXbObSj', NULL, 0),
(1796, 20, '61', 'Ritual of Living Sacrifice (1)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '5oBWx', NULL, 0),
(1800, 20, '62', 'Ritual of Living Sacrifice (2)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'Lp7RW', NULL, 0),
(1804, 20, '63', 'Ritual of Living Sacrifice (3)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'lypjl', NULL, 0),
(1808, 20, '64', 'Filth-Eating Jar', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'MqBm1', NULL, 0),
(1812, 20, '65', 'Filth-Eating Jar (2)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'vM5w0', NULL, 0),
(1816, 20, '66', 'Each Own\'s Daily Life', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '4rPOg', NULL, 0),
(1820, 20, '67', 'A Game Vet\'s Build-up Methods', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '17xl91', NULL, 0),
(1824, 20, '68', 'The Grey Temple (1)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'eh_OIQ97', NULL, 0),
(1828, 20, '69', 'The Grey Temple (2)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '0n48L', NULL, 0),
(1832, 20, '70', 'Medusa Raid (1)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'GJn3Q', NULL, 0),
(1836, 20, '71', 'Medusa Raid (2)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'n0gn4', NULL, 0),
(1840, 20, '72', 'How Game Vets Train Pets (1)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '2POBv', NULL, 0),
(1844, 20, '73', 'How Game Vets Train Pets (2)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '2PJEv', NULL, 0),
(1848, 20, '74', 'Boundary-Breaking Mirror (1)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'Grd0YsHx', NULL, 0),
(1852, 20, '75', 'Boundry-Breaking Mirror (2)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'GJrwJ', NULL, 0),
(1856, 20, '76', 'Forest of Elves', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '3gqP9', NULL, 0),
(1860, 20, '77', 'The Moonlight Festival (1)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'BkP1VPNb', NULL, 0),
(1864, 20, '78', 'The Way Game Vets SHoot ARrows', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '1OKXm', NULL, 0),
(1868, 20, '79', 'An Uninvited Guest (1)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'n0BPl', NULL, 0),
(1872, 20, '80', 'An Uninvitied Guest (2)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'vBqR0', NULL, 0),
(1876, 20, '81', 'An Uninvitied Guest (3)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'MicD8jGO', NULL, 0),
(1880, 20, '82', 'The Moonlight Festival (2)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'AWFcyL3B', NULL, 0),
(1884, 20, '83', 'Vet Player Corporation\'s Shareholder Meeting', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'ADBweWQV', NULL, 0),
(1888, 20, '84', 'The 7th Floor, Land of the Bitter Cold - Tundra', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'sZeaiBz4', NULL, 0),
(1892, 20, '85', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'm5vUGBIM', NULL, 0),
(1895, 20, '86', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'IdLKSNeK', NULL, 0),
(1898, 20, '87', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '6W267g2P', NULL, 0),
(1901, 20, '88', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'fVLi9XD_', NULL, 0),
(1904, 20, '89', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'ubNmdsFc', NULL, 0),
(1907, 20, '90', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '0AFNJE0H', NULL, 0),
(1910, 20, '91', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'cflcODX8', NULL, 0),
(1913, 20, '92', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'mFomodyH', NULL, 0),
(1916, 20, '93', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'r1IkGUQt', NULL, 0),
(1919, 20, '94', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'onWvB7PF', NULL, 0),
(1922, 20, '95', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'NnVgnHSm', NULL, 0),
(1925, 20, '96', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '2l6sYrbo', NULL, 0),
(1928, 20, '97', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'KBryTxcg', NULL, 0),
(1931, 20, '98', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '2tUeQS5V', NULL, 0),
(1934, 20, '99', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'bTzjZNDM', NULL, 0),
(1937, 20, '100', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'hXmpEW0J', NULL, 0),
(1940, 20, '101', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'zdoTHP3n', NULL, 0),
(1943, 20, '102', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'qidtu4Lx', NULL, 0),
(1946, 20, '103', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '2ULx3iX8', NULL, 0),
(1949, 20, '104', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'RIntXsBX', NULL, 0),
(1952, 20, '105', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'YCx5prqe', NULL, 0),
(1955, 20, '106', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'SxGKQoWC', NULL, 0),
(1958, 20, '107', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'nfXSsAAl', NULL, 0),
(1961, 20, '108', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'rFzvjz4d', NULL, 0),
(1964, 20, '109', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'R4iOdI2n', NULL, 0),
(1967, 20, '110', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'BEbb5HvJ', NULL, 0),
(1970, 20, '111', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'Drhg2Onn', NULL, 0),
(1973, 20, '112', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '15HOAwy3', NULL, 0),
(1976, 20, '113', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'AXKf4rgW', NULL, 0),
(1979, 20, '114', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '9tKEDTND', NULL, 0),
(1982, 20, '115', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'BitaZbjz', NULL, 0),
(1985, 20, '116', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '6bSJe13D', NULL, 0),
(1988, 20, '117', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'RQDiIsVJ', NULL, 0),
(1992, 20, '118', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'aSFfTrJZ', NULL, 0),
(1995, 20, '119', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'qoQxzivf', NULL, 0),
(1998, 20, '120', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'Mougf3ix', NULL, 0),
(2002, 20, '121', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'CYG8l6Ka', NULL, 0),
(2005, 20, '122', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '1aLaBQ3v', NULL, 0),
(2008, 20, '123', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'Tqk1gaQ_', NULL, 0),
(2011, 20, '124', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'VjLdv0PZ', NULL, 0),
(2014, 20, '125', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'itsyHxr_', NULL, 0),
(2018, 20, '126', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'xqhVmc_h', NULL, 0),
(2020, 20, '127', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'pRnZsXFW', NULL, 0),
(2022, 20, '128', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'nIE6wMIP', NULL, 0),
(2024, 20, '129', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'yhaCUX2J', NULL, 0),
(2027, 20, '130', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'plHN8Fa9', NULL, 0),
(2030, 20, '131', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'xP0Lfex2', NULL, 0),
(2032, 20, '132', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'GbDjPEsK', NULL, 0),
(2035, 20, '133', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'qlnnviKQ', NULL, 0),
(2037, 20, '134', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '49XFrmao', NULL, 0),
(2039, 20, '135', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '5IGq6_0l', NULL, 0),
(2041, 20, '136', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'tLLUfYfE', NULL, 0),
(2043, 20, '137', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'GuareDty', NULL, 0),
(2045, 20, '138', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'efIRAH7Z', NULL, 0),
(2047, 20, '139', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'lO_SsOjW', NULL, 0),
(2049, 20, '140', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'NIqMcbA7', NULL, 0),
(2051, 20, '141', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'NEesBNhM', NULL, 0),
(2053, 20, '142', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'qoi5Ozl9', NULL, 0),
(2055, 20, '143', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'qKJcllBA', NULL, 0),
(2057, 20, '144', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'qFcvYieX', NULL, 0),
(2059, 20, '145', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'dvzL5ohe', NULL, 0),
(2061, 20, '146', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'wEY7HpaE', NULL, 0),
(2063, 20, '147', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'BkaqBbpD', NULL, 0),
(2065, 20, '148', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'wm0oFdED', NULL, 0),
(2067, 20, '149', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'sCq47Jsn', NULL, 0),
(2069, 20, '150', 'The Return of The Heroes', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'H7KyM_M1', NULL, 0),
(2072, 20, '151', 'The Weight Of The Name Reinhardt (1)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'KD4u3Xqe', NULL, 0),
(2075, 20, '152', 'The Weight Of The Name Reinhardt (2)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'a4DK8U_6', NULL, 0),
(2078, 20, '153', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'MYywIDE6', NULL, 0),
(2080, 20, '154', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'c6bFePO6', NULL, 0),
(2082, 20, '155', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'KaSMT7M_', NULL, 0),
(2084, 20, '156', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'EKwBi_Ze', NULL, 0),
(2086, 20, '157', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '8_9SilOp', NULL, 0),
(2088, 20, '158', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'km0lc6lq', NULL, 0),
(2090, 20, '159', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'Z65FaKAm', NULL, 0),
(2092, 20, '160', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'g4NJt9CY', NULL, 0),
(2094, 20, '161', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '90Gi86ie', NULL, 0),
(2096, 20, '162', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'yG0ePS8M', NULL, 0),
(2098, 20, '163', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'SwbzhuW4', NULL, 0),
(2100, 20, '164', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'pGIKvTc3', NULL, 0),
(2103, 20, '165', 'The Strongest Support (1)', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'ngPln9rr', NULL, 0),
(2106, 20, '166', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'jQl2AiZJ', NULL, 0),
(2108, 20, '167', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'q9_nWWjZ', NULL, 0),
(2110, 20, '168', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '9LL4x1vw', NULL, 0),
(2112, 20, '169', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'I1nWlS2e', NULL, 0),
(2114, 20, '170', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'TY5sc3pM', NULL, 0),
(2116, 20, '171', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'Mc_oKkGg', NULL, 0),
(2118, 20, '172', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'lfGQD9WG', NULL, 0),
(2120, 20, '173', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'TJpJfCct', NULL, 0),
(2122, 20, '174', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'nWZ78CUA', NULL, 0),
(2124, 20, '175', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'aC6HAEys', NULL, 0),
(2126, 20, '176', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '70Ht14fo', NULL, 0),
(2128, 20, '177', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'pgBaoKqA', NULL, 0),
(2130, 20, '178', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'zwgs2ANd', NULL, 0),
(2132, 20, '179', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'Mv0lBPIx', NULL, 0),
(2134, 20, '180', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'CIntVXmE', NULL, 0),
(2136, 20, '181', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'xa1gnpYk', NULL, 0),
(2138, 20, '182', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'AuJhfEd5', NULL, 0),
(2140, 20, '183', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'TF9ZiAEV', NULL, 0),
(2142, 20, '184', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'glPY2mMw', NULL, 0),
(2144, 20, '185', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'dnU5_9Pf', NULL, 0),
(2146, 20, '186', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'dxpuRYct', NULL, 0),
(2148, 20, '187', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'lfUNoNw6', NULL, 0),
(2150, 20, '188', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'YJqilrqX', NULL, 0),
(2152, 20, '189', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'GLTbLBvL', NULL, 0),
(2154, 20, '190', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'iuwRFmJP', NULL, 0),
(2156, 20, '191', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'jw22ki85', NULL, 0),
(2158, 20, '192', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'Ii5VUvQ3', NULL, 0),
(2160, 20, '193', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'sHFOwniH', NULL, 0),
(2162, 20, '194', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'rt8LdwRu', NULL, 0),
(2164, 20, '195', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'N6ZQcW_4', NULL, 0),
(2166, 20, '196', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'XOzH200w', NULL, 0),
(2168, 20, '197', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'dvBgaEI5', NULL, 0),
(2170, 20, '198', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'bOPu1w9Z', NULL, 0),
(2172, 20, '199', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'DhgAbOmi', NULL, 0),
(2174, 20, '200', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'BoFdVAgY', NULL, 0),
(2176, 20, '201', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'ItlaiqcT', NULL, 0),
(2178, 20, '202', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '5TJFHokn', NULL, 0),
(2180, 20, '203', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'qPTEFosS', NULL, 0),
(2182, 20, '204', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '7SMJ6H4U', NULL, 0),
(2184, 20, '205', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'PjSzcMGv', NULL, 0),
(2186, 20, '206', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'vGsBCaua', NULL, 0),
(2188, 20, '207', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'NCJhTDd1', NULL, 0),
(2190, 20, '208', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '0_VeSuZd', NULL, 0),
(2192, 20, '209', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '2_oUtbK1', NULL, 0),
(2194, 20, '210', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'vHwo6XeO', NULL, 0),
(2196, 20, '211', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'EJ7rrS4P', NULL, 0),
(2198, 20, '212', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'q_q7XaY3', NULL, 0),
(2200, 20, '213', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '0oFAL8RV', NULL, 0),
(2202, 20, '214', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'o3S5KjKs', NULL, 0),
(2204, 20, '215', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '8MyShutK', NULL, 0),
(2206, 20, '216', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'gmMstwx3', NULL, 0),
(2208, 20, '217', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'o5AXIgz5', NULL, 0),
(2210, 20, '218', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'IjeXei_j', NULL, 0),
(2212, 20, '219', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'lPl2XXTu', NULL, 0),
(2214, 20, '220', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'toWsJVum', NULL, 0),
(2216, 20, '221', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'QDWK2MZV', NULL, 0),
(2218, 20, '222', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'g_V_0rIR', NULL, 0),
(2220, 20, '223', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'WjSK6azK', NULL, 0),
(2222, 20, '224', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'AqVX04rj', NULL, 0),
(2224, 20, '225', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'lh_RyGv2', NULL, 0),
(2226, 20, '226', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'nDrwTqrs', NULL, 0),
(2228, 20, '227', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'n27Noz8T', NULL, 0),
(2230, 20, '228', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'SA31PwN4', NULL, 0),
(2232, 20, '229', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'KVOqBLOF', NULL, 0),
(2234, 20, '230', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'EzcQYnba', NULL, 0),
(2236, 20, '231', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'ZLDBV44t', NULL, 0),
(2238, 20, '232', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'eupsTXEc', NULL, 0),
(2240, 20, '233', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'tIQFbYx4', NULL, 0),
(2242, 20, '234', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'QFI_zo0N', NULL, 0),
(2244, 20, '235', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'Osg25raa', NULL, 0),
(2246, 20, '236', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'I5FCt9_9', NULL, 0),
(2248, 20, '237', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'onBcovF1', NULL, 0),
(2250, 20, '238', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'QqPyoWSL', NULL, 0),
(2251, 20, '239', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'zGQ4KDdQ', NULL, 0),
(2252, 20, '240', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'bTGV5mha', NULL, 0),
(2253, 20, '241', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '1GhzD77s', NULL, 0),
(2254, 20, '242', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'fSPTYKzJ', NULL, 0),
(2255, 20, '243', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'vDAK9_u6', NULL, 0),
(2256, 20, '244', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'DkUPeglV', NULL, 0),
(2257, 20, '245', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'UrP1LxXG', NULL, 0),
(2258, 20, '246', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'zCg8RXNd', NULL, 0),
(2259, 20, '247', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'vkZdUQKp', NULL, 0),
(2260, 20, '248', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'XeYKWn5r', NULL, 0),
(2261, 20, '249', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'Yiv1eodh', NULL, 0),
(2262, 20, '250', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '3Bvtrprs', NULL, 0),
(2263, 20, '251', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'G1Lx0JpN', NULL, 0),
(2264, 20, '252', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'a_M_kX7w', NULL, 0),
(2265, 20, '253', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '6lJdwsER', NULL, 0),
(2266, 20, '254', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'XBfTj0mt', NULL, 0),
(2268, 20, '255', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'ZvCSBVIF', NULL, 0),
(2269, 20, '256', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '7XwqGDzJ', NULL, 0),
(2270, 20, '257', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'j3miVh73', NULL, 0),
(2271, 20, '258', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'nLauqJec', NULL, 0),
(2272, 20, '259', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'Ti3k4Xcc', NULL, 0),
(2273, 20, '260', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'x_WKwoM2', NULL, 0),
(2274, 20, '261', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'c2rAGJop', NULL, 0),
(2275, 20, '262', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'vrHqxP6H', NULL, 0),
(2276, 20, '263', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'nori7aFl', NULL, 0),
(2277, 20, '264', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'aS2jqyYb', NULL, 0),
(2278, 20, '265', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'OmFyD95c', NULL, 0),
(2279, 20, '266', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'w1WHulbn', NULL, 0),
(2280, 20, '267', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'ynIiLSOT', NULL, 0),
(2281, 20, '268', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'e0gk1o7c', NULL, 0),
(2282, 20, '269', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'iQO4tWBJ', NULL, 0),
(2283, 20, '270', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'dwRg1zxw', NULL, 0),
(2284, 20, '271', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'yVb5WBDh', NULL, 0),
(2285, 20, '272', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'v6BgnTSy', NULL, 0),
(2286, 20, '273', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'XzWagPqK', NULL, 0),
(2287, 20, '274', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '21uQ51iv', NULL, 0),
(2288, 20, '275', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '5S8UOLhb', NULL, 0),
(2289, 20, '276', 'Chapter 276', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'egzAKMrm', NULL, 0),
(2291, 20, '277', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'EUlnpbT_', NULL, 0),
(2292, 20, '278', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'OTSFTZIm', NULL, 0),
(2293, 20, '279', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, 'ca262oSi', NULL, 0),
(2294, 20, '280', '', 0, NULL, NULL, '2026-10-13 08:40:14', 0, '1IB2KYQs', NULL, 0),
(2295, 21, '1', '', 1, NULL, NULL, '2026-09-24 20:03:54', 0, 'b33e7bc1-a5fc-4a2e-a673-86a47139b2aa', NULL, 0),
(2334, 24, '1', 'Full text', 1, NULL, NULL, '2026-10-14 09:31:56', 0, '1342', NULL, 0);

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
  `created_by` int(11) DEFAULT NULL,
  `genres` varchar(255) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `series`
--

INSERT INTO `series` (`id`, `title`, `slug`, `type`, `description`, `cover_url`, `author`, `artist`, `status`, `is_novel`, `novel_price`, `free_chapters`, `external_source`, `external_id`, `created_at`, `views`, `created_by`, `genres`, `rating`) VALUES
(2, 'Sono Koi, Damasarete Imasenka?', 'sono-koi-damasarete-imasenka', 'manga', 'A love story that began as a scam! A roller coaster romance!', 'https://uploads.mangadex.org/covers/e89d4b05-714f-4e64-8105-4243858909f0/a69cb42c-a731-4779-8fe4-040b6413de4a.jpg', 'Tiv', 'Tiv', 'completed', 0, 0.00, 1, 'mangadex', 'e89d4b05-714f-4e64-8105-4243858909f0', '2026-10-01 15:56:41', 0, NULL, NULL, NULL),
(6, 'Myst, Might, Mayhem', 'myst-might-mayhem', 'manhwa', 'All Jeong wants is a second chance -- for revenge! His grandfather’s gruesome death has transformed him into the bloodthirsty and ruthless Scythe Demon. He will stop at nothing until he has killed the man responsible. Yet rage and determination alone are not enough. He must train in martial arts if he ever wants to stand a chance against such a formidable foe. Will he succeed in his revenge on the murderer of his grandfather?\n\n**Original Webtoon:**  \n- [Naver Webtoon](https://comic.naver.com/webtoon/list?titleId=821597)\n- [Naver Series](https://series.naver.com/comic/detail.series?productNo=10782038)\n\n**Official Translations:**  \n- [S.Chinese](https://www.dongmanmanhua.cn/ANCIENTCHINESE/guaililuanshen/list?title_no=2722)\n- [T.Chinese](https://www.webtoons.com/zh-hant/martial-arts/myst-might-mayhem/list?title_no=6345)\n- [Indonesian](https://www.webtoons.com/id/fantasy/myst-might-mayhem/list?title_no=6367)\n- [Thai](https://www.webtoons.com/th/fantasy/myst-might-mayhem/list?title_no=6511)\n- [Japanese](https://manga.line.me/product/periodic?id=Z0003353)\n- [French](https://www.webtoons.com/fr/action/myst-might-mayhem/list?title_no=6711)', 'https://uploads.mangadex.org/covers/df5f8e3d-64a1-4287-8f08-4a732e9cb757/d44fa20d-f221-476a-a7e7-460b6e7ff7ba.jpg', NULL, NULL, 'hiatus', 0, 0.00, 3, 'mangadex', 'df5f8e3d-64a1-4287-8f08-4a732e9cb757', '2026-10-07 14:30:06', 0, NULL, NULL, NULL),
(7, 'Yoimiya Nagi ga Mane ni Yashoku o Tsukuru Wake', 'yoimiya-nagi-ga-mane-ni-yashoku-o-tsukuru-wake', 'manga', 'Yoimiya Nagi ga Mane ni Yashoku o Tsukuru Wake', 'uploads/covers/cover_7_1791436739.jpg', '', NULL, 'ongoing', 0, 0.00, 2, NULL, NULL, '2026-10-07 16:19:31', 0, 2, NULL, NULL),
(20, 'Solo Max-Level Newbie', 'solo-max-level-newbie', 'manhwa', 'Kang Jinhyeok, a gaming streamer and content creator, is the only person who has cleared the game, [Tower of Trials]. However, as the game’s popularity declines, it becomes increasingly difficult for him to continue making a living as a content creator. \n\nJinhyeok prepares to end his 11-year-long relationship with the game and say goodbye to his humble group of subscribers. But on that very day, the Tower of Trials appears in reality with a message saying that humanity will perish unless all players clear each floor within 90 days.\n\nJinhyeok, who knows the game better than anyone, decides to use his knowledge to his advantage and dominate the tower.', 'https://meo.comick.pictures/1vZZZB.jpg', '', NULL, 'ongoing', 0, 0.00, 3, 'manhwa', 'GpIzxNnb', '2026-10-13 14:40:08', 0, NULL, NULL, NULL),
(21, 'Cool na Eikoku Bishoujo wa Orenchi ni Homestay Shitairashii', 'cool-na-eikoku-bishoujo-wa-orenchi-ni-homestay-shitairashii', 'manga', 'High school student Miyamoto Shouhei hails from a distinguished samurai lineage that has endured for generations.\n\nOne day, the Miyamoto Household welcomes an exchange student from England—Eleanor—for a homestay!\n\nCool and fluent in Japanese, Eleanor seems to have an ulterior motive for coming to Japan beyond simply learning about the culture…', 'https://uploads.mangadex.org/covers/2867b9cd-d71e-47e4-84dc-da00bc7a7db4/ac2ab84a-2f6c-4e83-b234-28d01c12bcda.jpg', '', NULL, 'ongoing', 0, 0.00, 3, 'mangadex', '2867b9cd-d71e-47e4-84dc-da00bc7a7db4', '2026-10-14 02:46:24', 0, NULL, NULL, NULL),
(24, 'Pride and Prejudice', 'pride-and-prejudice', 'novel', '\"Pride and Prejudice\" by Jane Austen is a novel published in 1813. It follows Elizabeth Bennet, who must learn to see past first impressions and hasty judgments. With five daughters and an estate that can only pass to male heirs, the Bennet family faces financial pressure to marry well. When wealthy Mr. Darcy arrives in their countryside neighborhood, his pride and Elizabeth\'s prejudice set the stage for misunderstandings, hidden truths, and unexpected revelations about character and love. (This is an automatically generated summary.)', 'https://www.gutenberg.org/cache/epub/1342/pg1342.cover.medium.jpg', 'Austen, Jane', NULL, 'completed', 1, 0.00, 3, 'novel', '1342', '2026-10-14 15:31:56', 0, NULL, NULL, NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2335;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
