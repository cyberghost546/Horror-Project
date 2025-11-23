-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 23, 2025 at 03:46 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `silent_evidence`
--

-- --------------------------------------------------------

--
-- Table structure for table `carousel_slides`
--

CREATE TABLE `carousel_slides` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carousel_slides`
--

INSERT INTO `carousel_slides` (`id`, `title`, `caption`, `image_url`, `sort_order`, `is_active`) VALUES
(1, 'Do Not Look Behind You', 'Some stories follow you home.', 'uploads/slides/1763297425_The_One_Who_Wakes_First.png', 1, 1),
(2, 'Voices in the Attic', 'You are not alone in your own house.', 'https://images.unsplash.com/photo-1500080209535-717dd4ebaa6b?q=80', 2, 1),
(3, 'The Hallway That Watches You', 'You only see it when the lights are off.', 'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?q=80', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `cat_key` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  `tag` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `parent` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `cat_key`, `label`, `tag`, `description`, `parent`) VALUES
(1, 'true', 'True stories', 'TRUE', 'Real experiences users claim actually happened.', NULL),
(2, 'paranormal', 'Paranormal', 'PARANORMAL', 'Ghosts, spirits, haunted houses, cursed objects.', NULL),
(3, 'urban', 'Urban legends', 'URBAN', 'Stories that spread online and feel too real.', NULL),
(4, 'short', 'Short nightmares', 'SHORT', 'Quick reads that hit fast.', NULL),
(5, 'haunted', 'Haunted places', 'HAUNTED', 'Real locations with disturbing history.', 'paranormal'),
(6, 'ghosts', 'Ghost encounters', 'GHOSTS', 'Unexplainable sightings and hauntings.', 'paranormal'),
(7, 'missing', 'Missing persons', 'MISSING', 'Cases that leave more questions than answers.', 'true'),
(8, 'crime', 'Crime and mystery', 'CRIME', 'Dark events that defy explanation.', 'true'),
(9, 'sleep', 'Sleep paralysis', 'SLEEP', 'The figures you cannot move away from.', 'paranormal'),
(10, 'forest', 'Forest horror', 'FOREST', 'What hides between the trees.', 'paranormal'),
(11, 'night', 'Night shift stories', 'NIGHT', 'Late hours that get way too strange.', 'true'),
(12, 'calls', 'Strange phone calls', 'CALLS', 'Voices that should not exist.', 'urban'),
(13, 'creatures', 'Creature sightings', 'CREATURES', 'Encounters with things not human.', 'urban'),
(14, 'abandoned', 'Abandoned places', 'ABANDONED', 'Ruins that feel alive inside.', 'paranormal'),
(15, 'psychological', 'Psychological horror', 'PSYCHO', 'Mind-bending stories that mess with your head.', 'short');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_settings`
--

CREATE TABLE `homepage_settings` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `show_latest` tinyint(1) NOT NULL DEFAULT 1,
  `show_popular` tinyint(1) NOT NULL DEFAULT 1,
  `show_featured` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `homepage_settings`
--

INSERT INTO `homepage_settings` (`id`, `show_latest`, `show_popular`, `show_featured`, `updated_at`) VALUES
(1, 1, 1, 1, '2025-11-15 09:56:28');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stories`
--

CREATE TABLE `stories` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `category` enum('true','paranormal','urban','short','haunted','ghosts','missing','crime','sleep','forest','night','calls','creatures','abandoned','psychological') NOT NULL DEFAULT 'true',
  `content` longtext NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `views` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `likes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stories`
--

INSERT INTO `stories` (`id`, `user_id`, `title`, `slug`, `category`, `content`, `image_path`, `is_published`, `is_featured`, `views`, `likes`, `created_at`, `updated_at`) VALUES
(1, 2, 'h ;jk k;j kn', 'h-jk-kj-kn', 'true', '; bjnlnni\'k', NULL, 1, 0, 1, 0, '2025-11-15 10:59:33', '2025-11-15 14:06:55'),
(2, 2, 'brgbarbr', 'brgbarbr', 'true', 'bzdrfbrrwe', NULL, 1, 0, 17, 0, '2025-11-15 11:00:40', '2025-11-15 19:45:01'),
(3, 1, 'test 1', 'test-1', 'true', 'cduonvdsmas amefkdsamcawefi ef jeidcdbvkldnvureyxz Jcudv sdoknvruvbco klnv\' oivdjn hewbj;dnvurj audvhudjlvn;aowuv', NULL, 1, 0, 1, 0, '2025-11-16 12:37:49', '2025-11-16 12:39:18'),
(4, 1, 'The One Who Wakes First', 'test-1-2', 'short', 'The first thing that hit you was the cold. Not normal cold. It felt like someone opened a freezer in front of your face. You woke up in your bed, but the room looked wrong. The walls looked stretched. The corners looked sharper. Your posters looked faded like someone scrubbed the color off.\r\n\r\nYou sat up fast. Your chest felt tight. Your breath came out in little gasps. You tried to shake it off. You told yourself you were dreaming. It sounded weak, but you said it anyway.\r\n\r\nYou slid your feet to the floor. The wood felt wet. Not soaked, but damp like someone wiped it with a cold cloth. Your skin crawled. You checked the window. Closed. Checked the door. Closed.\r\n\r\nThen the tapping started.\r\n\r\nOne tap. Then another. It came from under the bed. Slow. Calm. Like whatever was down there had all night.\r\n\r\nYou froze. Your throat tightened. You tried to move your foot back onto the bed, but your legs felt heavy. The tapping got faster. A little rhythm. Like it was trying to get your attention.\r\n\r\nYou bent down. Only a little. Just enough to look at the gap under your bed. Dark. Too dark. The kind of dark your eyes should adjust to, but didn’t.\r\n\r\nThen a voice whispered your name.\r\n\r\nSoft. Close. Right under you.\r\n\r\nYou jerked back so hard the bed frame rattled. You grabbed your phone, but the screen stayed black. No battery percentage. No icons. Just a reflection of your own face. Even that looked off. Your eyes in the reflection looked a little too wide.\r\n\r\nYou stepped away from the bed. The voice laughed. Quiet. Slow. It sounded like it was enjoying your reaction. You hated that. You felt your face heat up with panic.\r\n\r\nYou backed up to the door. You reached for the handle. It felt ice cold. You twisted it hard. It didn’t move. You tried again. Nothing.\r\n\r\nThe tapping under the bed stopped.\r\n\r\nThe silence felt heavier than the sound.\r\n\r\nThen something pressed a single finger against the underside of the mattress. The whole bed lifted a few centimeters like someone strong was pushing it up with ease. You yelped and stumbled back from the door. Your heartbeat went wild. You felt it in your neck.\r\n\r\nThe bed slammed back to the floor. The sound echoed through the room. You covered your ears even though it was already over.\r\n\r\nA low breath came from under the bed. Slow. Calm. The same rhythm as the tapping from before.\r\n\r\nThen the voice whispered again.\r\n\r\n“I’m not under here anymore.”\r\n\r\nYou spun around so fast you almost fell. Your room looked empty. Quiet. Still.\r\n\r\nThen the light in the hallway flicked on by itself. It showed a long shadow stretching into your room. Not yours.\r\n\r\nIt stood at the doorway.\r\n\r\nThe shadow didn’t move. It just stood there, stretched long across the floor, reaching almost to your feet. You tried to pull back, but your body refused to listen. Your muscles tightened. Your chest felt locked. You could only stare at the doorway, waiting for something to step in.\r\n\r\nYour heartbeat thudded so loud it almost drowned the silence. Almost.\r\n\r\nThen the figure leaned in.\r\n\r\nNot a full step. Just a slight tilt of the head past the door frame. Enough for you to see the outline of a human shape. Tall. Thin. Shoulders a little too narrow. Neck a little too long. You squinted, hoping it would fade like some optical trick. It didn’t.\r\n\r\nThe voice drifted in again. This time from the hallway.\r\n\r\n“You always wake up so slow.”\r\n\r\nYou shook your head hard. “I’m dreaming.”\r\n\r\nThe figure chuckled. It sounded dry. Like it scraped along the walls.\r\n\r\n“You think this feels like a dream?”\r\n\r\nYou didn’t answer. You didn’t trust your voice. You took a shaky step backward. Your heel bumped the side of your desk. The vibration made your empty water bottle rattle. That tiny noise felt way too loud.\r\n\r\nThe figure stepped into the doorway.\r\n\r\nNot fully. Just one foot. Bare. Pale. The toes long. Slightly curved. Like they never fit shoes right. The foot pressed lightly against the wooden floor, but it didn’t make a sound. Not even a creak.\r\n\r\nYour throat tightened more.\r\n\r\n“Let me show you,” it whispered.\r\n\r\nThe hallway lights flickered. Once. Twice. Then they stayed on, but the brightness twisted. The walls in the hall shifted, stretching upward like they were being pulled. The ceiling rose with them. The hallway looked longer now. Much longer.\r\n\r\nYou ran for the window. You didn’t think. You just moved. You grabbed the curtains and yanked them aside.\r\n\r\nThe outside looked wrong.\r\n\r\nYour street wasn’t there. No houses. No trees. No road. Only a flat empty field of black soil. The sky looked frozen with one pale streak of light that never moved.\r\n\r\nYour breath hitched.\r\n\r\n“Where do you think you’re going?”\r\n\r\nThe voice came from behind you. Much closer.\r\n\r\nYou spun around.\r\n\r\nThe figure was in your room now. Fully inside. It stood by the foot of your bed. Its body looked human at first glance, but the proportions were wrong. The arms hung too low. The fingers too long. The head tilted slowly to one side.\r\n\r\nYou backed up against the window. You felt the cold glass bite into your skin.\r\n\r\n“I don’t want this,” you whispered.\r\n\r\nThe figure took one soft step toward you.\r\n\r\n“That’s the point.”\r\n\r\nYou tried to scream. Nothing came out. Not even air.\r\n\r\nThe figure reached a hand toward your face. Its fingers stopped just before touching your skin. You felt the cold radiating off it. Your eyes watered.\r\n\r\n“Wake up,” it said.\r\n\r\nYour vision blurred. The room stretched again. The floor dropped away for a second then snapped back. You felt dizzy. Sick. You blinked hard. You hoped you’d open your eyes in your real room.\r\n\r\nInstead, you opened them to darkness.\r\n\r\nA pitch black void.\r\n\r\nYou couldn’t even see your hands. The air felt thick. Heavy. No floor under your feet. You floated. You reached out, desperate to feel something.\r\n\r\nNothing.\r\n\r\nThen, in that endless dark, the voice whispered right behind your ear.\r\n\r\n“You never woke up.”\r\n\r\nYou snapped your head toward the sound, but there was no direction here. The dark felt alive. It pressed against your skin. Every breath felt like you were inhaling thick dust. You tried to move your arms. They felt slow, like you were pushing through heavy water.\r\n\r\nA dim glow appeared far ahead. A tiny pin of light. You focused on it fast, desperate for anything real. It pulsed once. Then again. Each pulse matched your heartbeat. You pushed your body toward it. You didn’t know how you moved. You just willed yourself forward.\r\n\r\nThe light grew. You saw something inside it. A doorway. A real one. A plain wooden door with chips along the frame. Your door. From your room. You felt actual hope hit you in the chest.\r\n\r\nYou reached for the knob.\r\n\r\nYour fingers touched the metal.\r\n\r\nA hand grabbed your wrist from behind.\r\n\r\nIts skin felt dry, cracked, cold. The grip tightened until your bones hurt. You tried to pull away. The grip got stronger.\r\n\r\n“You always run to the wrong door,” the voice said.\r\n\r\nYou twisted around. The glow behind you revealed the figure for the first time. Its face looked like a rough sketch of a person. Features carved into pale skin. Eyes too dark. Mouth stretched too wide, like it was drawn in with a shaky hand. It stared at you without blinking.\r\n\r\n“I gave you something real,” it said. “And you keep trying to escape it.”\r\n\r\nYou shook your head fast. “This isn’t real.”\r\n\r\nIt leaned closer. Its breath felt cold on your face. “You keep saying that. You never think about why you need to say it.”\r\n\r\nYou kicked at the darkness. You pulled your wrist again. The grip loosened a little. You pushed harder. Your fingers brushed the door knob again. You grabbed it with both hands and yanked.\r\n\r\nThe door flew open.\r\n\r\nYou fell through it.\r\n\r\nYou hit the floor hard. Wood. Real wood. You gasped and scrambled to your feet. Your room was back. Normal walls. Normal corners. Posters with real color. Your bed looked untouched. Your phone sat charging on your nightstand, glowing softly.\r\n\r\nYou grabbed it and checked the screen. Time looked normal. Battery normal. Everything normal. Relief washed through you so fast you almost cried.\r\n\r\nYou sat on the bed and rubbed your eyes.\r\n\r\nThen you noticed the underside of your mattress.\r\n\r\nA single pale handprint pressed into the fabric.\r\n\r\nFresh.\r\n\r\nThe fabric slowly sank inward.\r\n\r\nSomething pushed upward.\r\n\r\nThe pressure under the mattress grew. Slow. Intentional. You stared at the handprint as it deepened, pushing the fabric inward like a finger pressing into soft clay. You took one small step back. Your breath trembled. You tried to tell yourself it was leftover dream panic, but your body knew better.\r\n\r\nThe mattress shifted. Something slid along the underside. You heard the faint scrape of nails dragging across wood.\r\n\r\nYou stumbled toward the door. You grabbed the handle. It turned this time. You pulled it open fast.\r\n\r\nThe hallway lights flickered again.\r\n\r\nYour stomach dropped.\r\n\r\nYou stepped out. The moment your foot hit the hall carpet, the lights snapped off. Total darkness swallowed everything. You reached for your phone, but the screen died the moment you tapped it. You slapped the side of it, trying to wake it again. No use.\r\n\r\nBehind you, your bedroom door creaked open on its own.\r\n\r\nYou didn’t look back. You didn’t want to see what stood there.\r\n\r\nYou moved down the hall with slow steps, hands against the wall to guide yourself. The air felt cooler with every step. Your fingers brushed the family photos on the wall. You felt the frames tilt, like someone pushed them out of alignment.\r\n\r\nThen a voice whispered from behind you.\r\n\r\n“Why walk away from your room?”\r\n\r\nYou kept moving. Your legs shook, but you forced them forward. You reached the end of the hall. You felt the familiar texture of the light switch. You flipped it up.\r\n\r\nNothing.\r\n\r\nYou flipped it down.\r\n\r\nNothing.\r\n\r\nYou breathed through your teeth, panic creeping up your spine.\r\n\r\nThen you heard it. A dragging sound. Slow. Heavy. A shuffle that felt too close to the floor. It came from the bedroom doorway. It moved into the hall. It followed your steps.\r\n\r\nYou pushed into the bathroom and slammed the door shut. You locked it. You stepped back until your shoulders hit the wall.\r\n\r\nIt was quiet for a moment.\r\n\r\nThen the doorknob rattled.\r\n\r\nOnce. Twice.\r\n\r\nThen a soft knock.\r\n\r\nThree taps.\r\n\r\nYour mouth went dry. You covered it with your hand to keep yourself from making a sound.\r\n\r\nYou stared at the door. The gap under it glowed with a faint pale light. Soft white. Not warm. Not natural. It got brighter. Something moved past the gap. A long shadow.\r\n\r\nThen you heard scraping on the mirror behind you.\r\n\r\nYou froze.\r\n\r\nYou didn’t want to turn around. You knew nothing should be behind you. You pressed your back tighter to the wall, your eyes fixed on the bathroom door.\r\n\r\nThe scraping got louder.\r\n\r\nGlass on glass.\r\n\r\nA slow curved line.\r\n\r\nThen another.\r\n\r\nYour breathing got uneven. You turned your head a tiny bit. Just enough to see the mirror from the corner of your eye.\r\n\r\nA sentence formed across the fogless surface.\r\n\r\nLetter by letter.\r\n\r\n“I followed you.”\r\n\r\nYour throat closed.\r\n\r\nThe knocking started again.\r\n\r\nLouder.\r\n\r\nA calm voice spoke through the door.\r\n\r\n“Look closer at the mirror.”\r\n\r\nYou kept your eyes on the mirror even though every part of your body begged you to look away. The letters stopped moving. The room went silent again. You felt your pulse pounding against your skin. You took one slow step toward the mirror. Your reflection looked normal at first. Same hair. Same hoodie. Same scared face.\r\n\r\nThen your reflection blinked.\r\n\r\nYou didn’t blink.\r\n\r\nYour stomach tightened. You backed up fast. Your reflection smiled. The smile stretched too wide. The cheeks pulled up too high. The eyes stayed blank. No fear. No panic. Just a slow, growing grin like it waited for this moment.\r\n\r\nYou shook your head. “No. No no no.”\r\n\r\nThe knocking on the door stopped.\r\n\r\nSilence filled the room again. Thick. Heavy.\r\n\r\nYour reflection lifted a hand and pressed it against the inside of the mirror. The glass rippled around its palm like water. You watched your own face move closer inside the mirror until its forehead pressed against the glass.\r\n\r\nThen it whispered through the reflection.\r\n\r\n“Let me out.”\r\n\r\nYou stepped back until your heel hit the wall. The mirror started to bulge outward. The glass stretched like someone behind it pushed with both hands. Cracks spread along the edges. Tiny snapping sounds filled the room.\r\n\r\nYou grabbed the sink and held on. Your breath shook. You felt the floor vibrate beneath you.\r\n\r\nA single crack split through the middle of the mirror.\r\n\r\nYour reflection pushed its fingers through. The fingers looked pale. Longer than yours. They curled around the edges of the broken glass.\r\n\r\nIt pulled.\r\n\r\nThe mirror burst open. Shards flew across the bathroom floor. You shielded your face with your arm. When you looked again, your reflection stepped out of the broken frame. It stood in front of you. Same clothes. Same hair. Same height.\r\n\r\nBut the smile didn’t fade.\r\n\r\nYou squeezed against the wall. “Stay away from me.”\r\n\r\nYour reflection tilted its head. “Why? You made me.”\r\n\r\nYou shook your head. “I didn’t.”\r\n\r\n“You did every time you tried to wake up,” it said. “You pushed deeper instead of out. So I took the exit.”\r\n\r\nIt stepped closer. Each step quiet. Smooth. Controlled.\r\n\r\nYou pressed your back against the wall harder. The air felt colder with every inch it closed.\r\n\r\n“I don’t want this,” you said.\r\n\r\n“Yes you do,” it whispered. “That is why this place grew. That is why the hall stretched. That is why the room changed. You fed it.”\r\n\r\nYou shook your head again. “I want to wake up.”\r\n\r\n“You already did,” it said. “You woke up here.”\r\n\r\nYou felt the words sink into your chest. You didn’t want to accept it. You looked at the broken mirror. No way back. Only cracks and darkness behind the frame.\r\n\r\nYour reflection reached out and touched your cheek. The touch felt real. Too real.\r\n\r\n“You belong here now,” it said. “Let me take your place out there.”\r\n\r\nYou grabbed its wrist. You pushed it away. The strength surprised you. The reflection stumbled back one step. Its smile faded for the first time.\r\n\r\nGood.\r\n\r\nYou pushed off the wall and went straight for the bathroom door. You unlocked it. You threw it open.\r\n\r\nThe hallway looked normal again.\r\n\r\nLights steady. Walls straight. Photos in place.\r\n\r\nYou sprinted.\r\n\r\nYour reflection hissed behind you. Its footsteps hit the tiles fast. You reached the front door. You grabbed the handle. You twisted hard.\r\n\r\nIt opened.\r\n\r\nCold night air hit your face. Real air. Crisp and normal. You pushed outside. You slammed the door behind you. You held it shut.\r\n\r\nYour reflection slammed into the door from the other side. The wood shook. You felt one hard shove. Then another. The door creaked under the pressure. You pushed with both hands, putting your whole body weight on it.\r\n\r\nThen the knocking stopped.\r\n\r\nSilence.\r\n\r\nYou waited. Ten seconds. Twenty. Thirty.\r\n\r\nNothing.\r\n\r\nYou slowly eased your grip. You backed away from the door. You stared at it, waiting for the knob to turn. It stayed still.\r\n\r\nYou walked down the porch steps. Your street looked normal. Houses. Trees. Streetlights. You took a deep breath. You felt the tension in your shoulders loosen.\r\n\r\nYou turned around to look at the house one more time.\r\n\r\nYour reflection stood in the upstairs window.\r\n\r\nIt smiled.\r\n\r\nIt lifted a hand.\r\n\r\nThen it slowly closed the curtains.\r\n\r\nYou ran.\r\n\r\nAnd the worst part was simple.\r\n\r\nYou knew it would wait for you to come back.\r\n\r\nThe nightmare ended when you woke up inside it.\r\n\r\nAnd now it had your room.\r\n\r\nYour house.\r\n\r\nYour reflection.\r\n\r\nAnd all the time in the world.', 'uploads/stories/story_1_1763296803_8e0d6ef6.png', 1, 0, 2, 1, '2025-11-16 13:16:40', '2025-11-22 23:14:27');

-- --------------------------------------------------------

--
-- Table structure for table `story_bookmarks`
--

CREATE TABLE `story_bookmarks` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `story_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `story_comments`
--

CREATE TABLE `story_comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `story_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `story_comments`
--

INSERT INTO `story_comments` (`id`, `story_id`, `user_id`, `parent_id`, `content`, `created_at`) VALUES
(2, 2, 1, NULL, 'test one two tree', '2025-11-15 14:14:24'),
(4, 4, 1, NULL, 'This story is amazing', '2025-11-22 22:30:29'),
(6, 4, 1, 4, 'fwe', '2025-11-22 23:12:10');

-- --------------------------------------------------------

--
-- Table structure for table `story_likes`
--

CREATE TABLE `story_likes` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `story_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `story_likes`
--

INSERT INTO `story_likes` (`id`, `user_id`, `story_id`, `created_at`) VALUES
(19, 1, 4, '2025-11-22 23:14:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `display_name`, `avatar`, `bio`, `role`, `created_at`, `last_login`) VALUES
(1, 'chrismolina', 'chris@chris.com', '$2y$10$GyO.1rYTydKDmmVk49at2uSnxvLDm4YBbD0/Dr5NO1E/i8mRPDfui', 'chris molina', 'uploads/avatars/avatar_1_1763198680.jpg', '', 'admin', '2025-11-15 10:19:37', '2025-11-23 15:45:30'),
(2, 'testtest', 'test@test.com', '$2y$10$E9NT4Y1RWYFavJ2FFPG7V.3Lr4DhMUguizsZgs3aXoJyCDgLC5D..', 'test test', 'uploads/avatars/avatar_2_1763200718.webp', '', 'user', '2025-11-15 10:57:57', '2025-11-15 10:58:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carousel_slides`
--
ALTER TABLE `carousel_slides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cat_key` (`cat_key`);

--
-- Indexes for table `homepage_settings`
--
ALTER TABLE `homepage_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `stories`
--
ALTER TABLE `stories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_slug` (`slug`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `story_bookmarks`
--
ALTER TABLE `story_bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bookmarks_user_story` (`user_id`,`story_id`);

--
-- Indexes for table `story_comments`
--
ALTER TABLE `story_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_parent_id` (`parent_id`);

--
-- Indexes for table `story_likes`
--
ALTER TABLE `story_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_likes_user_story` (`user_id`,`story_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_username` (`username`),
  ADD UNIQUE KEY `uniq_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carousel_slides`
--
ALTER TABLE `carousel_slides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stories`
--
ALTER TABLE `stories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `story_bookmarks`
--
ALTER TABLE `story_bookmarks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `story_comments`
--
ALTER TABLE `story_comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `story_likes`
--
ALTER TABLE `story_likes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_password_resets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stories`
--
ALTER TABLE `stories`
  ADD CONSTRAINT `fk_stories_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;
