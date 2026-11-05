-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql211.infinityfree.com
-- Generation Time: Nov 04, 2026 at 08:47 PM
-- Server version: 11.4.13-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_43246095_komik`
--

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
(2, 'Sono Koi, Damasarete Imasenka?', 'sono-koi-damasarete-imasenka', 'manga', 'A love story that began as a scam! A roller coaster romance!', 'https://uploads.mangadex.org/covers/e89d4b05-714f-4e64-8105-4243858909f0/a69cb42c-a731-4779-8fe4-040b6413de4a.jpg', 'Tiv', 'Tiv', 'completed', 0, '0.00', 1, 'mangadex', 'e89d4b05-714f-4e64-8105-4243858909f0', '2026-10-01 15:56:41', 0, NULL, NULL, NULL),
(6, 'Myst, Might, Mayhem', 'myst-might-mayhem', 'manhwa', 'All Jeong wants is a second chance -- for revenge! His grandfather’s gruesome death has transformed him into the bloodthirsty and ruthless Scythe Demon. He will stop at nothing until he has killed the man responsible. Yet rage and determination alone are not enough. He must train in martial arts if he ever wants to stand a chance against such a formidable foe. Will he succeed in his revenge on the murderer of his grandfather?\n\n**Original Webtoon:**  \n- [Naver Webtoon](https://comic.naver.com/webtoon/list?titleId=821597)\n- [Naver Series](https://series.naver.com/comic/detail.series?productNo=10782038)\n\n**Official Translations:**  \n- [S.Chinese](https://www.dongmanmanhua.cn/ANCIENTCHINESE/guaililuanshen/list?title_no=2722)\n- [T.Chinese](https://www.webtoons.com/zh-hant/martial-arts/myst-might-mayhem/list?title_no=6345)\n- [Indonesian](https://www.webtoons.com/id/fantasy/myst-might-mayhem/list?title_no=6367)\n- [Thai](https://www.webtoons.com/th/fantasy/myst-might-mayhem/list?title_no=6511)\n- [Japanese](https://manga.line.me/product/periodic?id=Z0003353)\n- [French](https://www.webtoons.com/fr/action/myst-might-mayhem/list?title_no=6711)', 'https://s4.anilist.co/file/anilistcdn/media/manga/cover/large/....jpg', NULL, NULL, 'hiatus', 0, '0.00', 3, 'mangadex', 'df5f8e3d-64a1-4287-8f08-4a732e9cb757', '2026-10-07 14:30:06', 0, NULL, NULL, NULL),
(7, 'Yoimiya Nagi ga Mane ni Yashoku o Tsukuru Wake', 'yoimiya-nagi-ga-mane-ni-yashoku-o-tsukuru-wake', 'manga', 'Yoimiya Nagi ga Mane ni Yashoku o Tsukuru Wake', '/public/uploads/covers/cover_7_1791436739.jpg', '', NULL, 'ongoing', 0, '0.00', 2, NULL, NULL, '2026-10-07 16:19:31', 0, 2, NULL, NULL),
(21, 'Cool na Eikoku Bishoujo wa Orenchi ni Homestay Shitairashii', 'cool-na-eikoku-bishoujo-wa-orenchi-ni-homestay-shitairashii', 'manga', 'High school student Miyamoto Shouhei hails from a distinguished samurai lineage that has endured for generations.\n\nOne day, the Miyamoto Household welcomes an exchange student from England—Eleanor—for a homestay!\n\nCool and fluent in Japanese, Eleanor seems to have an ulterior motive for coming to Japan beyond simply learning about the culture…', 'https://uploads.mangadex.org/covers/2867b9cd-d71e-47e4-84dc-da00bc7a7db4/ac2ab84a-2f6c-4e83-b234-28d01c12bcda.jpg', '', NULL, 'ongoing', 0, '0.00', 3, 'mangadex', '2867b9cd-d71e-47e4-84dc-da00bc7a7db4', '2026-10-14 02:46:24', 0, NULL, NULL, NULL),
(27, 'The Greatest Estate Developer', 'the-greatest-estate-developer', 'manhwa', 'When civil engineering student Su-Ho Kim falls asleep reading a fantasy novel, he wakes up as a character in the book! Su-Ho is now in the body of Lloyd Frontera, a lazy noble who loves to drink, and whose family is in a mountain of debt. Using his engineering knowledge, Su-Ho designs inventions to avert the terrible future that lies in wait for him. With the help of a giant hamster, a knight, and the world’s magic, can Su-Ho dig his new family out of debt and build a better future?\n(Source: WEBTOON, edited)\nNote: Includes 12 extra chapters.', 'https://s4.anilist.co/file/anilistcdn/media/manga/cover/medium/bx140407-fJQr0fmqq1IO.png', '', NULL, '', 0, '0.00', 3, 'manhwa', '140407', '2026-10-25 12:04:58', 0, NULL, NULL, NULL),
(28, 'Naruto', 'naruto', 'manga', 'Twelve years ago the Village Hidden in the Leaves was attacked by a fearsome threat. A nine-tailed fox spirit claimed the life of the village leader, the Hokage, and many others. Today, the village is at peace and a troublemaking kid named Naruto is struggling to graduate from Ninja Academy. His goal may be to become the next Hokage, but his true destiny will be much more complicated. The adventure begins now!\n\n(Source: VIZ Media)', 'https://s4.anilist.co/file/anilistcdn/media/manga/cover/medium/nx30011-9yUF1dXWgDOx.jpg', '', NULL, '', 0, '0.00', 3, 'manhwa', '30011', '2026-10-25 12:05:42', 0, NULL, NULL, NULL),
(31, 'My Dress-Up Darling', 'my-dress-up-darling', 'manga', 'Traumatized by a childhood incident with a friend who took exception to his love of traditional dolls, doll-artisan hopeful Gojou Wakana passes his days as a loner, finding solace in the home ec room at his high school. To Wakana, people like beautiful Kitagawa Marin, a trendy girl who\'s always surrounded by a throng of friends, is practically an alien from another world. But when cheerful Marin--never one to be shy--spots Wakana sewing away one day after school, she barges in with the aim of roping her quiet classmate into her secret hobby: cosplay! Will Wakana\'s wounded heart be able to handle the invasion from this sexy alien?! \n\n(Source: Square Enix)\n\nNote: Includes four extra chapters.', 'https://s4.anilist.co/file/anilistcdn/media/manga/cover/medium/bx101583-VI1PT2QGGT8W.jpg', '', NULL, '', 0, '0.00', 20, 'manhwa', '101583', '2026-11-05 00:56:27', 0, NULL, NULL, NULL),
(32, 'I’m the Max-Level Newbie', 'i-m-the-max-level-newbie', 'manhwa', 'Kang Jin-Hyeok, a gaming streamer, and content creator is the only person who has cleared the game, [Tower of Trials]. However, as the game’s popularity declines, it becomes increasingly difficult for him to continue making a living as a content creator. Jinhyeok prepares to end his 11-year-long relationship with the game and say goodbye to his humble group of subscribers. But on that very day, the Tower of Trials appears in reality with a message saying that humanity will perish unless all players clear each floor within 90 days. Jin-Hyeok, who knows the game better than anyone, decides to use his knowledge to his advantage and dominate the tower. It’s time to show everyone what a hardcore gamer can do!\n\n(Source: Tappytoon, edited)', 'https://s4.anilist.co/file/anilistcdn/media/manga/cover/medium/bx137280-juUL79K7f9s7.png', '', NULL, '', 0, '0.00', 20, 'manhwa', '137280', '2026-11-05 00:57:48', 0, NULL, NULL, NULL),
(33, 'Na Honja Sword Master', 'na-honja-sword-master', 'manhwa', 'In an age when everyone draws upon the power of Constellations, there is but one man who believes in the sword. In a world where skills have become the measure of strength, one man reached the pinnacle with nothing but his sword— The Last Swordmaster, Limón Asphelder. Even if the world has forgotten him, his sword has yet to reach its end.\n\"Skills? I don’t need them. All I need is my sword.\"(Source: KakaoPage, translated)', 'https://s4.anilist.co/file/anilistcdn/media/manga/cover/medium/bx217593-3vVR0UvsR1yt.jpg', '', NULL, '', 0, '0.00', 20, 'manhwa', '217593', '2026-11-05 01:14:56', 0, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `series`
--
ALTER TABLE `series`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `uniq_slug` (`slug`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `series`
--
ALTER TABLE `series`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
