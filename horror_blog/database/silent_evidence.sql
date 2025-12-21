-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 21, 2025 at 06:18 PM
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
-- Table structure for table `contact_requests`
--

CREATE TABLE `contact_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_requests`
--

INSERT INTO `contact_requests` (`id`, `user_id`, `name`, `email`, `subject`, `message`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'chris molina', 'chris@chris.com', 'help', 'test on two tree', 'open', '2025-11-30 11:06:31', '2025-11-30 11:09:07');

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
(1, 1, 1, 1, '2025-11-29 23:02:14');

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
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stories`
--

INSERT INTO `stories` (`id`, `user_id`, `title`, `slug`, `category`, `content`, `image_path`, `is_published`, `is_featured`, `views`, `likes`, `image`, `created_at`, `updated_at`) VALUES
(1, 2, 'h ;jk k;j kn', 'h-jk-kj-kn', 'true', '; bjnlnni\'k', NULL, 1, 0, 3, 0, NULL, '2025-11-15 10:59:33', '2025-11-30 00:54:35'),
(2, 2, 'brgbarbr', 'brgbarbr', 'true', 'bzdrfbrrwe', NULL, 1, 0, 17, 0, NULL, '2025-11-15 11:00:40', '2025-11-15 19:45:01'),
(3, 1, 'The Thing In The Appalachian Dark', 'test-1', 'true', 'People like to say the Appalachian Mountains are peaceful. Old. Safe. They talk about the views, the quiet, the way the fog curls over the ridges in the morning like it is tucking the world back into bed.\r\n\r\nNo one talks about what it feels like when the woods go quiet all at once.\r\n\r\nI was twenty when it happened. A broke college kid with a hand me down backpack and a cheap tent, trying to impress my roommate, Tyler, and his cousin Emma. They were the outdoorsy ones. I just wanted to prove I was not some soft city kid.\r\n\r\nWe planned a three day hike along a lesser known section of the Appalachian Trail in late October. Off season, thinner crowds, colder nights. The guy at the small town gas station told us the higher shelters were already getting frost. He said it like a warning. Tyler heard it as a challenge.\r\n\r\n“You good with cold?” he asked me, grinning while he loaded up the trunk.\r\n\r\n“I am broke and stubborn,” I said. “Cold is nothing.”\r\n\r\nEmma laughed and shook her head. “You two are idiots. That is not the same thing.”\r\n\r\nWe started hiking around noon. The trail cut through hardwood forest, leaves already flame red and copper. Every step crunched. Sunlight filtered through bare branches, and for the first few hours it really did feel peaceful. Just boots, breath, and the distant rush of some hidden creek.\r\n\r\nWe passed only one other person that day. An older man, maybe late fifties, beard more gray than brown, carrying a pack that looked older than all three of us. He stepped aside on the narrow trail and let us pass, but his eyes stayed on us a little too long.\r\n\r\n“You kids heading to Ridgeback Shelter?” he asked.\r\n\r\nTyler nodded. “That is the plan.”\r\n\r\nThe man’s jaw tightened. “Storm blowing in tonight from the west. Wind hits that ridge hard. You might want to stay lower. There is an old ranger cabin about four miles back where the trail forks. Roof is rough, but it is better than that shelter.”\r\n\r\nEmma frowned. “Is something wrong with the shelter?”\r\n\r\nHe hesitated. That is what I remember. Not what he said next, but that half second of silence.\r\n\r\n“Nothing wrong with the wood,” he finally answered. “Just… folks around here do not like sleeping on that ridge. Things walk at night up there. You hear them more than you see them.”\r\n\r\nTyler smirked. “Bears?”\r\n\r\nThe man looked at him in a way that killed the joke.\r\n\r\n“Bears you can scare off,” he said. “Just do not go out if you hear something calling your name. That is all.”\r\n\r\nHe stepped back onto the trail and walked away before any of us could answer.\r\n\r\nFor a while we joked about it.\r\n\r\n“Do not worry,” Tyler said. “If something starts whispering, I will tell it we are full on attention and trauma already.”\r\n\r\nEmma nudged him with her trekking pole. “You are the one who downloaded a ghost hunting app on your phone last week. If anything weird happens, it is your fault.”\r\n\r\nI laughed with them, but the man’s words stuck under my skin like splinters. Do not go out if you hear something calling your name. He had said it too plain, like weather or road conditions. Not as a joke. Not as a campfire story.\r\n\r\nBy late afternoon the trail steepened and the air thinned. The sky turned that flat, hard gray that means the sun is still up but does not care about you anymore. Wind started to push through the trees, colder and sharper.\r\n\r\nWe reached Ridgeback Shelter just before dark. It sat on a narrow spur of the ridge, wooden lean to set against the slope, open front facing a drop where the mountains rolled out in layers of blue and black. There was a fire ring, a bear cable, and a soggy logbook inside an old metal box.\r\n\r\n“See?” Tyler said, dumping his pack. “Perfect. Creepy old guy was just trying to hog the low cabin.”\r\n\r\nEmma rubbed her arms and looked at the horizon. “We should get a fire going before the wind picks up.”\r\n\r\nI volunteered to fetch water from the spring a quarter mile down a side trail. I wanted to be useful. To shake off that thin, nagging thread of unease.\r\n\r\nThe spring sat in a small hollow, half ringed by rocks, water seeping clear and steady into a shallow pool. As I filled the bottles, the wind dropped for a second, like someone had turned a volume knob. The quiet hit so hard it almost made me dizzy.\r\n\r\nNo wind in the leaves.\r\n\r\nNo birds.\r\n\r\nNo distant rustle of anything.\r\n\r\nJust the slow drip of water.\r\n\r\nI told myself I was being dramatic. I screwed the last cap on, slung the bottles into the sling, and started back up the narrow path. I had gone maybe thirty steps when I heard it for the first time.\r\n\r\n“Eli.”\r\n\r\nMy name.\r\n\r\nIt came from the trees to my left. Not loud. Not whispered. Just spoken, like whoever said it expected me to turn around.\r\n\r\nI froze. My first stupid thought was that Tyler had followed me, trying to mess with me. But the voice was wrong. It was higher, thinner, like the sound of someone speaking through cold metal.\r\n\r\nThe woods stayed still.\r\n\r\nI didn’t answer. I didn’t move.\r\n\r\n“Eli.”\r\nSame tone. Same distance. Like the speaker hadn’t moved an inch.\r\n\r\nI tightened my grip on the water bottles and forced myself to keep walking. Not fast. Not running. Running felt like the kind of thing prey does. But every muscle screamed at me to move faster.\r\n\r\nWhen I reached the main trail again, the wind kicked back up all at once, hard enough to shake branches. You don’t realize how alive the forest sounds until it stops. The sudden noise felt like breathing again.\r\n\r\nBy the time I got back to the shelter, my hands shook so badly that one of the bottles thumped out of the sling and hit the dirt.\r\n\r\nEmma looked up immediately. “You okay? You look pale.”\r\n\r\n“Cold,” I lied. “Just cold.”\r\n\r\nTyler was building the fire, hands outstretched to the flames like he was worshipping them. “Temperature is dropping fast. We might get snow.”\r\n\r\nWe ate instant noodles and granola bars. Normal backpacker stuff. Tried to make small talk. Tried to pretend the wind wasn’t pushing through the open front of the shelter with the force of something angry. Around nine, the fire died down and we climbed into our sleeping bags.\r\n\r\nI didn’t sleep.\r\n\r\nI just listened to the wind howl against the ridge, watched shadows shift across the wooden beams above me. Every time a branch cracked in the forest, my muscles tensed.\r\n\r\nSomewhere around midnight, everything stopped.\r\n\r\nThe wind cut off.\r\n\r\nThe forest went still.\r\n\r\nTotal silence again.\r\n\r\nI held my breath without meaning to.\r\n\r\nSomething moved outside the shelter. Not footsteps. More like the sound of weight pressing snow that wasn’t there yet.\r\n\r\nTyler stirred. “You hear that?” he whispered.\r\n\r\nBefore I could answer, a voice drifted in from the treeline.\r\n\r\nIt said my name again.\r\n\r\n“Eli.”\r\n\r\nBut this time, it sounded like Emma. Same pitch. Same softness. Same exact way she said my name earlier that day.\r\n\r\nExcept Emma was still lying beside me, her breathing quick and shallow in her sleeping bag.\r\n\r\nTyler sat up fast. “Nope. Nope. That’s not funny.”\r\n\r\nEmma clamped her hand over her mouth, eyes wide. She shook her head violently.\r\n\r\nOutside, the voice spoke again.\r\n\r\n“Come here.”\r\n\r\nNot shouted. Not demanded.\r\n\r\nInvited.\r\n\r\nThe sound came from maybe ten feet away, just beyond the fire ring.\r\n\r\nTyler leaned close to me. “If you go out there, I swear to God…”\r\n\r\n“I’m not moving,” I whispered.\r\n\r\nThe voice spoke again, softer. “Eli. Please.”\r\n\r\nMy jaw tightened. I didn’t notice I was crying until I tasted salt.\r\n\r\nEmma grabbed my sleeve. “Don’t answer it.”\r\n\r\nI didn’t.\r\n\r\nWe sat there in total darkness, backs pressed to the shelter wall, listening to something mimic the voices of the living.\r\n\r\nAfter a few minutes, the thing outside moved. Slow steps, circling the shelter. Not walking on two legs. The weight shifted wrong. Too low. Too uneven. Wood creaked as something pressed against the beams.\r\n\r\nThen it scraped one long drag across the shelter’s front edge, like testing the wood. Or marking it.\r\n\r\nThe voice came again, but quieter, almost sad.\r\n\r\n“Eli.”\r\n\r\nThen everything went silent again.\r\n\r\nThe forest stayed dead still until dawn. Not a single wind gust. Not a bird call. Nothing.\r\n\r\nAt first light, we packed in total silence. No one argued. No one tried to be brave anymore.\r\n\r\nWhen we stepped off the ridge, the wind returned like nothing had happened.\r\n\r\nHalf a mile down the trail, Emma stopped walking and whispered, “Look.”\r\n\r\nI followed her gaze.\r\n\r\nOn the side of the trail, pressed into the mud, was a footprint.\r\n\r\nNot human.\r\n\r\nNot bear.\r\n\r\nSomething in between.\r\n\r\nLong. Narrow. Toes too long. Heel too short. Like someone had taken a human foot and stretched it wrong in a place bones shouldn’t move.\r\n\r\nTyler swore under his breath. “We’re not staying another night out here.”\r\n\r\nWe practically jogged the remaining miles. By the time we reached the trailhead, the air smelled of snow.\r\n\r\nA ranger’s truck sat parked near the gate. The same older man we’d passed the day before leaned against it, drinking from a thermos. He scanned our faces once and frowned.\r\n\r\n“You heard it,” he said. Not a question.\r\n\r\nNone of us answered.\r\n\r\nHe sighed and looked toward the ridge. “It doesn’t bother most hikers. Only calls the ones who listen.”\r\n\r\n“I didn’t listen,” I said.\r\n\r\nHe shook his head. “Listening’s not the same as hearing.”\r\n\r\nHe walked back to his truck. Before he got in, he added, “Next time it calls your name, you better pray it doesn’t sound like someone you trust.”\r\n\r\nThen he drove off.\r\n\r\nWe never hiked that ridge again.\r\n\r\nBut sometimes, late at night, when everything in the world goes quiet all at once, I swear I still hear Emma’s voice standing just beyond the dark tree line.\r\n\r\nCalling my name.\r\n\r\nWe tried to pretend life went back to normal.\r\n\r\nClasses. Cafeteria food. Cheap coffee that tasted like rust water. We didn’t talk about the ridge for almost a month. It felt like keeping a wound wrapped, hoping it would heal if we didn’t look at it.\r\n\r\nBut stuff started happening.\r\n\r\nSmall at first.\r\n\r\nThe kind of stuff you try to explain away.\r\n\r\nThe first time was in my dorm. Three in the morning. I woke up because someone knocked on my door. Soft. Polite. Like a friend stopping by.\r\n\r\nMy roommate was gone for the weekend, so it made zero sense. I lay there staring at the door.\r\n\r\nKnock.\r\nPause.\r\nKnock.\r\n\r\nAnd then a voice.\r\n\r\n“Eli… you awake?”\r\n\r\nIt sounded like Tyler.\r\n\r\nSame tone. Same cadence.\r\n\r\nExcept the hall was empty when I checked the peephole.\r\n\r\nI didn’t open the door.\r\n\r\nThe next morning, Tyler swore he’d been at his girlfriend’s place across town all night.\r\n\r\nI laughed it off. Lied and said I probably dreamed it.\r\n\r\nTwo days later, Emma texted me: Do you hear it too?\r\n\r\nI didn’t answer. I didn’t want her to say it out loud, because that would make it real.\r\n\r\nA week after that, she cornered me outside the library. Her eyes were red. Not from crying. From lack of sleep.\r\n\r\n“It said my mom’s voice last night,” she whispered. “It asked me to let it in.”\r\n\r\nMy skin crawled. “Into what?”\r\n\r\n“My room,” she said, gripping her backpack straps so hard her knuckles went white. “It kept asking. Over and over. I turned on all the lights and waited for sunrise.”\r\n\r\nI swallowed hard. “Did you answer it?”\r\n\r\n“No. But it doesn’t need me to anymore. It knows my name now. It knows my voice. Sometimes it sounds like me.”\r\n\r\nThat hit me in a place I didn’t have words for.\r\n\r\nWe told Tyler everything.\r\n\r\nHe tried to laugh it off, but his hands shook. “Okay. Hypothetically. If something followed us… why? We didn’t do anything.”\r\n\r\n“Maybe you don’t have to do anything,” Emma said. “Maybe hearing it once is enough.”\r\n\r\nTyler stared down at the table. “I’ve been hearing my dad’s voice,” he said quietly. “He died when I was thirteen.”\r\n\r\nNone of us spoke for a long time.\r\n\r\nWe made a stupid pact. A scared kid pact.\r\n\r\nIf any of us heard it calling outside, we’d call each other immediately. Stay on the line. Make sure the voices weren’t real.\r\n\r\nIt worked for a while.\r\n\r\nUntil early December.\r\n\r\nIt was snowing hard that night. One of those storms where the flakes are thick and wet and all sound gets smothered. I was studying when my phone buzzed.\r\n\r\nIncoming call: Emma.\r\n\r\nI answered fast. “Hey. You okay?”\r\n\r\nSilence.\r\n\r\nNot quiet. Silence like the trail. Like everything around her was holding its breath.\r\n\r\n“Emma?” I said again.\r\n\r\nA voice finally answered.\r\n\r\nBut it wasn’t her.\r\n\r\nIt was me.\r\n\r\nMy own voice. Speaking from her phone.\r\n\r\n“Eli… come outside.”\r\n\r\nMy lungs locked up.\r\n\r\n“Emma?” I shouted. “Put Emma on the phone. Right now.”\r\n\r\nBut the voice repeated, perfect and calm.\r\n\r\n“Come outside.”\r\n\r\nI hung up, grabbed my coat, and ran across campus like something was chasing me. Snow soaked through my shoes. Wind burned my face. I didn’t stop until I reached her dorm.\r\n\r\nThe hallway lights flickered as I sprinted up the stairs.\r\n\r\nHer door was cracked open.\r\n\r\n“Emma?” I whispered.\r\n\r\nI pushed the door wider.\r\n\r\nHer room was empty.\r\n\r\nHer boots were still by the heater. Her phone sat on the floor, screen cracked. Snow pushed through the open window even though she lived on the third floor.\r\n\r\nTyler showed up ten minutes later, breathless, face white.\r\n\r\n“Where is she?” he asked.\r\n\r\nI shook my head. “She didn’t walk out. There are no tracks in the snow. None.”\r\n\r\nWe stared at the window. Snow blowing in. No footprints outside. No sign of a fall. No sign of anything.\r\n\r\nJust cold air and the distant line of the woods beyond campus.\r\n\r\nTyler whispered, “It called her.”\r\n\r\nI whispered back, “She answered without meaning to.”\r\n\r\nCampus police searched everywhere. They called it a missing person case. Posters went up. People cried in hallways.\r\n\r\nBut Tyler and I knew.\r\n\r\nWe knew exactly where she was taken.\r\n\r\nThe ridge.\r\n\r\nThe Appalachian dark.\r\n\r\nAnd the thing that knew our names.\r\n\r\nWe didn’t sleep for days after Emma vanished. Not real sleep. Just short drops into dreams that felt wrong, followed by waking up with your heart racing because you thought you heard someone whisper your name in the room.\r\n\r\nCops kept calling us. Asking the same questions. Did she seem depressed. Did she plan to run away. Did she get into a fight with anyone.\r\n\r\nWe told the truth. None of that fit her.\r\n\r\nThey didn’t care. They looked at us like we were hiding something.\r\n\r\nWe were, but not the thing they thought.\r\n\r\nAbout a week after she disappeared, Tyler texted me at two in the morning.\r\n\r\nYou awake.\r\n\r\nYeah.\r\n\r\nIt called her again.\r\n\r\nMy stomach dropped. How do you know.\r\n\r\nHe sent a voice message.\r\n\r\nThe audio was only three seconds long.\r\n\r\nBut in those three seconds, Emma’s voice said, small and shaky, “I’m cold… please help me.”\r\n\r\nI called him right away.\r\n\r\n“Did you record that outside your window?” I asked.\r\n\r\n“No,” he said. “It came from my phone speaker. I didn’t record it. It just showed up.”\r\n\r\nHe sounded broken.\r\n\r\n“We need to go back,” he said. “To the ridge. It’s the only place this started. It’s the only place it can end.”\r\n\r\nI wanted to say no. I wanted to say we should stay out of the mountains forever. But the truth sat heavy and ugly in my chest.\r\n\r\nI wasn’t going to leave Emma to whatever took her.\r\n\r\nWe met at the bus station the next morning. Both of us looked rough. Dark circles. No real food in our system. Shaky hands. We didn’t talk much on the ride back to the small mountain town. It snowed the whole way. Wet snow that stuck to the windows and made the world feel too quiet.\r\n\r\nWhen we got off the bus, the same old man from the trail was waiting by his truck. He wasn’t smiling. He wasn’t surprised.\r\n\r\n“You two should not have come back,” he said. “It knows you’re here. It’s been waiting.”\r\n\r\nTyler grabbed his jacket. “Where is she.”\r\n\r\nThe man didn’t fight him. He just nodded toward the mountains.\r\n\r\n“High ground,” he said. “Where the wind dies. It likes places where sound carries clean.”\r\n\r\n“Why did it take her,” I asked.\r\n\r\nHe looked at me like he wanted to lie, but couldn’t.\r\n\r\n“It wants what it hears. You’re not supposed to answer the voices in those woods. Once you hear it clearly, it follows you until you break.”\r\n\r\nTyler clenched his jaw. “We’re going up there either way.”\r\n\r\nThe man sighed. “Then I’m coming too. You won’t last two hours alone.”\r\n\r\nWe followed his truck along icy dirt roads until we reached a small pull off that led toward the ridge. Same trailhead. Same trees. Same cold air that felt too heavy.\r\n\r\n“Stay close,” he said. “It copies voices. It tricks your memory. If you hear something behind you, ignore it. If you see something that looks like someone you know, don’t speak to it.”\r\n\r\nWe started hiking.\r\n\r\nThe snow muffled everything. No birds. No branches cracking. No wind. Just that awful dead quiet we remembered.\r\n\r\nHalfway up the ridge, we heard it.\r\n\r\n“Tyler…”\r\n\r\nThe voice sounded exactly like Emma. Close. Weak. Like she was lying in the snow ten feet away.\r\n\r\nTyler flinched. His breathing shook.\r\n\r\n“That isn’t her,” the man said. “Keep walking.”\r\n\r\nBut the voice came again. “Please… it hurts…”\r\n\r\nTyler stopped walking. “That’s her. That’s really her.”\r\n\r\n“It’s not,” the man said. “If you answer it, it will get inside your head.”\r\n\r\nTyler took one step toward the trees.\r\n\r\nI grabbed his arm. “Don’t. She wouldn’t be calling from there. There are no tracks.”\r\n\r\nHe stared into the trees. Eyes wide. Lost.\r\n\r\nThe voice started crying. Quiet, desperate sobs. Perfect imitation.\r\n\r\nThen it said something that froze my blood.\r\n\r\n“Eli… come help me too…”\r\n\r\nThe old man stepped between us and the woods. “It knows both of you. That means it’s close.”\r\n\r\nSnow shifted behind a tree. Not a person. Not an animal. Something low to the ground. Something moving wrong, like each limb was trying to remember how to function.\r\n\r\nThe old man pulled a flare from his pack and lit it. Red light filled the trees.\r\n\r\nThe shape froze.\r\n\r\n“Back away slow,” he said. “It hates open flame.”\r\n\r\nTyler whispered, “I saw her face.”\r\n\r\n“You saw what it wanted you to see,” the man said.\r\n\r\nWe kept moving toward the ridge. The air grew colder. The quiet grew heavier. The trees grew tighter together.\r\n\r\nThen we heard a different sound.\r\n\r\nA voice that didn’t match anyone.\r\n\r\nA voice that didn’t even try.\r\n\r\nLow.\r\n\r\nFlat.\r\n\r\nLike rocks grinding.\r\n\r\n“You came too late.”\r\n\r\nThat voice rolled through the trees like it had weight. Like it didn’t ride the air. Like it pushed it.\r\n\r\nTyler backed up until he hit my shoulder. “That… that’s not Emma.”\r\n\r\nThe old man raised the flare higher. His hand shook, but his voice didn’t. “Do not answer it. Not one word.”\r\n\r\nWe pushed forward. The trail narrowed, weaving between roots and frozen brush. Snow shifted under our boots. My breath felt thick. Heavy. Like the air didn’t want me to breathe it.\r\n\r\nThen the trees opened.\r\n\r\nThe ridge spread out in front of us. Same lean to shelter. Same open front. Same fire ring, now half buried in snow.\r\n\r\nBut something else was there.\r\n\r\nFootprints.\r\nHundreds of them.\r\n\r\nNot human. Not anything you’d see in a field guide. Long, narrow shapes pressed deep into the snow. Some overlapping. Some circling the shelter the way we’d heard that night.\r\n\r\nAt the center of the prints, nestled against one of the support beams, was a scrap of fabric.\r\n\r\nEmma’s jacket sleeve.\r\n\r\nTyler dropped to his knees. “She was here,” he whispered. “She was right here.”\r\n\r\nThe old man knelt beside it. He didn’t touch it. “She was taken here. But it didn’t keep her here.”\r\n\r\nI stared at the footprints. They didn’t lead away. They didn’t lead toward the treeline. They ended abruptly, like whatever made them simply rose off the ground.\r\n\r\n“Where would it take her?” I asked.\r\n\r\nThe old man didn’t answer at first.\r\n\r\nHe just stood and pointed deeper into the woods beyond the shelter. A dense wall of pines crowded together, branches tangled like skeletal fingers.\r\n\r\n“There’s an old coal shaft down there,” he said. “Flooded. Abandoned decades ago. Locals won’t go near it. Animals don’t either.”\r\n\r\nTyler stood, eyes blazing. “She’s alive. I know she is.”\r\n\r\n“She might be,” the man said. “But not as you remember.”\r\n\r\nWe pushed past the tree line. The woods changed fast. The pines blocked the sky. Light died. Each step sank into thick, untouched snow.\r\n\r\nAnd then we heard it again.\r\n\r\nThis time, it wasn’t calling. It wasn’t mimicking.\r\n\r\nIt was breathing.\r\n\r\nSlow. Wet. Too close.\r\n\r\nThe old man held up a hand. We stopped.\r\n\r\nSomething shifted behind us. Then to our right. Then ahead. Like it was circling us again, unseen. Testing angles. Waiting.\r\n\r\n“Keep walking,” the man whispered. “Do not turn around.”\r\n\r\n“Why?” Tyler whispered back.\r\n\r\n“Because it’s not behind us,” he said. “It’s above.”\r\n\r\nI looked up.\r\n\r\nI shouldn’t have.\r\n\r\nSomething hung from the lower pine branches. Long limbs bent backward at impossible angles. Ribcage too wide. Skin pale as old snow, stretched thin around a jaw that wasn’t shaped like anything human.\r\n\r\nIts head turned toward me with a slow creak, like bone scraping bone.\r\n\r\nIts eyes were wrong.\r\n\r\nNot glowing. Not hollow.\r\n\r\nThey were reflective, like the eyes of a deer caught in headlights. Except there was no light here.\r\n\r\nTyler screamed.\r\n\r\nThe thing dropped.\r\n\r\nThe old man shoved us hard. “Go. Now.”\r\n\r\nWe ran. Branches whipped my face. Snow exploded under our boots. The thing hit the ground with a thud that shook loose powder from the trees.\r\n\r\nIt chased us.\r\n\r\nThe sound wasn’t footsteps. It was dragging. Like it pulled itself forward with arms too long, too strong, too clumsy to be natural.\r\n\r\nWe reached the clearing around the old shaft just as the clouds thinned enough to let a pale gray wash of light spill between the trees.\r\n\r\nA black opening yawned in the earth. Rusted beams framed the mouth of the old mine. Ice hung like teeth from the ceiling.\r\n\r\nTyler shouted, “Emma! Emma, are you in there?”\r\n\r\nA voice answered.\r\n\r\nBarely audible.\r\n\r\nWeak.\r\n\r\nShaking.\r\n\r\n“Tyler… help me…”\r\n\r\nHe lunged forward, but the old man grabbed him. “Listen. Listen to where it echoes from.”\r\n\r\nThe sound came again.\r\n\r\nBut the echo was wrong. It didn’t bounce from the cavern. It floated above the shaft. Hanging in the air.\r\n\r\nThe old man swore under his breath. “It’s not inside. It wants you to lean over. That’s all.”\r\n\r\nSomething scraped across the snow behind us.\r\n\r\nWe turned.\r\n\r\nThe creature crouched at the tree line. Watching. Tilting its head like it was trying to understand us.\r\n\r\nAnd then it smiled.\r\n\r\nA long, terrible stretch of skin that didn’t know how to smile right.\r\n\r\nTyler whispered, “Where is she then… where is Emma…”\r\n\r\nA voice answered from the darkness of the shaft.\r\n\r\nA voice that was neither human nor creature.\r\n\r\nA voice that curled under our skin like ice water.\r\n\r\n“Right behind you.”\r\n\r\nTyler spun around so fast he slipped on the ice. I grabbed his arm before he fell into the shaft. But the voice, that awful voice, didn’t match the creature watching us. It didn’t match Emma. It didn’t match anyone.\r\n\r\nIt sounded like something learning how to speak through someone else’s throat.\r\n\r\nThe old man stepped between us and the opening. “That thing isn’t Emma. It’s never been her.”\r\n\r\nBut Tyler shook his head. “No. No, I heard her. She’s here. She’s close.”\r\n\r\n“That’s what it wants,” the old man snapped. “It wants you leaning over the edge. It wants you lost.”\r\n\r\nSnow shifted behind us again. The creature crept closer. Slow. Deliberate. Like it wanted us to notice.\r\n\r\n“Light the other flare,” the old man hissed.\r\n\r\nI fumbled with my pack. My fingers were stiff and numb, but I got the cap off. The flare hissed to life, spraying red light over the snow and trees.\r\n\r\nThe creature recoiled instantly. Its skin twitched like someone had pressed a hot iron against it. It let out a sound that made every hair on my body stand straight up. Not a scream. Not a roar. Something like a person gasping underwater.\r\n\r\nTyler grabbed my sleeve. “There. Look.”\r\n\r\nHe pointed at the shaft.\r\n\r\nSomething moved in the darkness.\r\n\r\nNot the creature.\r\n\r\nA hand.\r\n\r\nHuman. Bare. Pale. Gripping the edge of the old rusted beam.\r\n\r\n“Emma!” Tyler lunged forward again.\r\n\r\nBut the old man slammed his arm across Tyler’s chest. Hard enough to knock the breath out of him.\r\n\r\n“That’s not a hand,” he whispered.\r\n\r\nI stared at it. At first I didn’t understand. It looked real. Fingernails. Joints. But the fingers were too long. The knuckles too smooth. The skin too perfect, like something sculpted rather than grown.\r\n\r\nAnd the arm attached to it bent at the wrong angle.\r\n\r\nThe old man lowered his voice. “Once it learns your voice, it tries the body next.”\r\n\r\nThen the thing in the shaft spoke again.\r\n\r\n“Tyler… I’m stuck… please… pull me out…”\r\n\r\nEvery word was slightly better than the last. Closer to human. Closer to Emma.\r\n\r\nCloser to trust.\r\n\r\nTyler’s eyes filled with tears. “It knows how she talks. It knows how she begs for help.”\r\n\r\n“That’s why you don’t answer,” the man told him. “Every response teaches it something new.”\r\n\r\nWe stepped back from the shaft. My flare hissed and spat sparks. The creature at the treeline shifted its weight, testing how close it could get without burning.\r\n\r\nThen, from the trees on the opposite side, we heard another voice.\r\n\r\nSoft. Small. Real.\r\n\r\n“Eli?”\r\n\r\nI turned so fast I nearly dropped the flare.\r\n\r\nEmma stood fifteen feet away.\r\n\r\nBarefoot. Hair tangled. Wearing the same jacket she’d disappeared in. Skin pale as frost.\r\n\r\nBut her eyes.\r\n\r\nHer eyes were wrong.\r\n\r\nToo wide. Too empty. Too still.\r\n\r\nLike she was awake but nothing behind the eyes knew how to be human anymore.\r\n\r\nTyler choked out a sob and ran toward her.\r\n\r\nThe old man lunged to grab him but slipped on the ice. “Tyler, stop! That’s not—”\r\n\r\nEmma tilted her head.\r\n\r\nHer smile was soft.\r\n\r\nHuman.\r\n\r\nWrong.\r\n\r\n“Tyler,” she whispered. “I knew you’d come for me.”\r\n\r\nSomething inside me told me to run. Something primal and old. But Tyler was already crossing the snow. Already reaching for her.\r\n\r\nI screamed his name.\r\n\r\nHe didn’t hear me.\r\n\r\nHe reached her.\r\n\r\n“Emma,” he whispered, pulling her close.\r\n\r\nFor half a second, she stood still in his arms.\r\n\r\nThen her jaw unhinged.\r\n\r\nSideways. Downward. Too wide. Skin splitting at the corners, teeth unfolding like something blooming.\r\n\r\nNot human teeth.\r\n\r\nLong. Needle thin.\r\n\r\nHer voice layered over itself.\r\n\r\nThree tones. Four. Echoing.\r\n\r\n“Thank you for finding me.”\r\n\r\nThe creature in the trees moved.\r\n\r\nThe hand in the shaft scraped metal.\r\n\r\nThe air went cold enough to burn.\r\n\r\nAnd Tyler realized too late that he wasn’t holding Emma.\r\n\r\nHe was holding the thing that had learned every piece of her.\r\n\r\nAnd it wasn’t done learning us.\r\n\r\nI ran.\r\n\r\nTyler tried to shove it away, but once it wrapped its arms around him, he couldn’t break free. The thing that wore Emma’s shape pulled itself closer, pressing its face against his neck. Its jaw snapped open wider, bones cracking, skin tearing. A wet hiss poured out of it.\r\n\r\nTyler screamed.\r\n\r\nThe old man yanked me backward. “Don’t look at it! Move!”\r\n\r\nBut I did look. I couldn’t not look.\r\n\r\nThe creature’s mouth wasn’t biting Tyler.\r\n\r\nIt was breathing him in.\r\n\r\nEvery inhale pulled something from him. Color drained from his skin. His voice evaporated into the cold air. He clawed at the thing’s shoulders, but his hands slipped through the skin like it wasn’t solid.\r\n\r\n“Eli—” he started.\r\n\r\nThe thing shuddered like it was swallowing his words.\r\n\r\nThen it spoke in Tyler’s exact voice.\r\n\r\n“Eli… help.”\r\n\r\nNot panicked. Calm. Practiced.\r\n\r\nA perfect copy.\r\n\r\nThe old man raised his flare and charged it. “Let him go!”\r\n\r\nThe creature shrieked.\r\n\r\nA sound that cracked the air. Snow blasted upward like a shockwave.\r\n\r\nAnd for the first time since we saw it, the creature let go. Tyler dropped into the snow, limp. Not dead… but empty. His chest rose and fell, but nothing behind his eyes moved.\r\n\r\nLike his mind had been scooped out.\r\n\r\nThe thing wearing Emma’s body recoiled from the flare, skin sizzling. Her face twisted, not in pain, but in confusion, like it couldn’t understand why its prey wasn’t coming closer anymore.\r\n\r\nThe old man yelled, “Eli, grab Tyler! Move!”\r\n\r\nI dropped to my knees and hauled Tyler up by the arms. He was breathing, but his head lolled like he couldn’t control it. His mouth opened once.\r\n\r\nNo sound came out.\r\n\r\nWe backed away from the creature.\r\n\r\nBut the shaft behind us growled.\r\n\r\nDeep. Hollow. Hungry.\r\n\r\nThe hand on the beam pulled upward, revealing something trying to climb out. Something shaped wrong. Something that looked like it was wearing human parts like clothing.\r\n\r\nThe old man set a third flare against the snow and shoved it toward the opening. The burst of red flame lit the inside of the shaft for a split second.\r\n\r\nAnd I saw faces.\r\n\r\nDozens.\r\n\r\nLayered and twisted, stretched into sheets of skin across the dark walls. Some screamed silently. Some looked peaceful. Some looked like they were waiting to wake up.\r\n\r\nOne of them was Emma.\r\n\r\nNot the creature wearing her shape.\r\nNot the thing mimicking her voice.\r\n\r\nHer real face.\r\n\r\nFrozen in the wall of the shaft. Eyes open. Mouth open. Like she had been drained of everything that made her living.\r\n\r\n“Eli…” the old man whispered. “Do not look again. Don’t give it more.”\r\n\r\nHe yanked me back.\r\n\r\nThe creature wearing Emma shrieked again and lunged.\r\n\r\nThe old man grabbed my coat and Tyler’s collar and shoved us sideways into the trees. “Run! Get off the ridge!”\r\n\r\n“But what about—” I started.\r\n\r\n“Go!”\r\n\r\nThe thing slammed into him. They hit the snow hard. He jammed the flare into its shoulder, making it thrash and scream. Its skin rippled like boiling water.\r\n\r\nI dragged Tyler with me as fast as I could. Branches whipped us. Snow blinded us. Behind us, I heard the old man yelling in a voice full of rage and something like acceptance.\r\n\r\n“Keep going! Don’t look back!”\r\n\r\nThen another sound.\r\n\r\nBones snapping.\r\n\r\nA wet thud.\r\n\r\nSilence.\r\n\r\nTyler stumbled beside me, barely conscious. His breath came in short gasps. His lips moved but no sound came out.\r\n\r\nThe trees thinned.\r\n\r\nThe ridge sloped downward.\r\n\r\nAnd then, behind us, a voice drifted through the trees.\r\n\r\nSoft.\r\n\r\nFamiliar.\r\n\r\nPerfect.\r\n\r\nIt was Emma.\r\n\r\nAnd Tyler.\r\n\r\nAnd me.\r\n\r\nAll speaking at once.\r\n\r\n“Don’t leave us.”\r\n\r\n“Come back.”\r\n\r\n“We aren’t finished learning you yet.”\r\n\r\nI didn’t stop running.\r\n\r\nI didn’t look back.\r\n\r\nI didn’t breathe until the trees opened and the trailhead came into view.\r\n\r\nTyler collapsed at the edge of the road, shaking violently. His eyes stared straight ahead, empty of anything he used to be.\r\n\r\nThe forest behind us fell silent again.\r\n\r\nNot peaceful.\r\n\r\nWaiting.\r\n\r\nI dragged Tyler down the road until the forest wasn’t pressing against our backs anymore. My lungs burned. My legs shook. But stopping felt like death, so I kept going until a distant porch light flickered through the snowfall.\r\n\r\nAn old farmhouse sat at the bottom of the hill. Faded paint. A sagging roof. Smoke curling from a crooked chimney. I didn’t care if it belonged to a stranger or a serial killer. It was human. That was enough.\r\n\r\nI pounded on the door with one hand, holding Tyler up with the other. “Please! Someone! Help!”\r\n\r\nA woman in her seventies cracked the door. Her eyes moved from my face to Tyler’s pale, shaking body.\r\n\r\n“Inside,” she said. No hesitation.\r\n\r\nShe pulled us into a warm kitchen that smelled like wood smoke and old tea. Tyler slid into a chair. His head drooped. His lips moved silently.\r\n\r\nThe woman touched his forehead and flinched. “Cold,” she whispered. “Too cold. Like something sucked the heat out of him.”\r\n\r\nI nodded because I didn’t know how to explain the truth. “Something… took our friend. And something hurt him.”\r\n\r\nShe looked at me long and steady.\r\n\r\n“You were up on Ridgeline, weren’t you.”\r\n\r\nNot a question.\r\n\r\nI sank onto a chair. “You know what that thing is?”\r\n\r\nShe closed the curtains before answering.\r\n\r\n“People around here don’t talk about it,” she said. “Talking gives it shape. Gives it knowledge. Makes it bolder.”\r\n\r\n“It already knows us,” I whispered.\r\n\r\nShe winced. “Then you’re in more danger than you understand.”\r\n\r\nTyler suddenly jerked upright, eyes wide. His mouth opened and closed. No sound. Only air. His hands clenched into fists. His nails dug into his palms hard enough to bleed.\r\n\r\n“It’s still in him,” the woman said softly. “It took something it shouldn’t have.”\r\n\r\nI grabbed Tyler’s shoulders. “Tyler, look at me. Stay with me. Please.”\r\n\r\nHe stared past me. Eyes unfocused. Like he wasn’t seeing the room at all.\r\n\r\nThe woman placed a kettle on the stove. “Let me get you heat. Maybe it’ll pull him back.”\r\n\r\nBut before she could take three steps, someone knocked on the door.\r\n\r\nA single knock.\r\n\r\nSoft.\r\n\r\nMeasured.\r\n\r\nJust like the one I heard weeks ago in my dorm.\r\n\r\nThe woman froze. “Don’t answer that.”\r\n\r\nMy heartbeat hammered against my ribs. “Who would be out there in the snow?”\r\n\r\nShe shook her head. “Not who. What.”\r\n\r\nAnother knock.\r\n\r\nSlower this time.\r\n\r\nThe voice that followed was unmistakable.\r\n\r\nEmma.\r\n\r\n“Eli… it’s freezing out here… please open the door.”\r\n\r\nTyler’s head snapped to the sound, eyes wide like a startled animal.\r\n\r\nHis mouth opened. And in a voice that wasn’t his, he whispered:\r\n\r\n“Let her in.”\r\n\r\nI stepped backward. “No. Tyler, that’s not her.”\r\n\r\nHe gripped the table. His fingers bent wrong, tendons standing out under his skin. His voice came again, cracking like ice under pressure.\r\n\r\n“Let her in.”\r\n\r\nThe woman grabbed my arm. Hard. “If you open that door, you won’t step back inside.”\r\n\r\nAnother knock. Louder. More urgent.\r\n\r\n“Eli…” Emma’s voice said, trembling. “Why won’t you help me…”\r\n\r\nSomething slid across the outside of the porch. A soft, dragging noise. Too heavy for a footstep. Too light for something crawling.\r\n\r\nThe woman whispered, “Look at Tyler.”\r\n\r\nI did.\r\n\r\nHis pupils were huge. Black swallowing all the color. Tears slid down his cheeks, but his expression wasn’t human crying. It was blank. Like a puppet whose strings were being pulled by someone who didn’t understand emotion.\r\n\r\nHe whispered in Emma’s voice.\r\n\r\n“Help me.”\r\n\r\nMy stomach turned.\r\n\r\nThe woman snatched a cast iron poker from beside the stove. “We need to get him away from the door. If it takes him fully, it’ll know everything he ever knew.”\r\n\r\nThe knocking stopped.\r\n\r\nSilence.\r\n\r\nThen a voice right behind the door. Inches away.\r\n\r\n“Eli… I see you.”\r\n\r\nThe woman grabbed Tyler under the arms. “Help me! We’re moving him to the cellar!”\r\n\r\nI wrestled his dead weight with her. He fought us. Not violently. Just wrong. His limbs bent with strange resistance, like he was moving against invisible strings.\r\n\r\nAs we dragged him toward the cellar door, the front door creaked.\r\n\r\nJust a little.\r\n\r\nJust enough for cold air to slip through.\r\n\r\nJust enough for the woman to swear under her breath.\r\n\r\n“It found the latch.”\r\n\r\nA long shadow spilled across the floorboards.\r\n\r\nNot shaped like Emma.\r\n\r\nNot shaped like anything that belonged in a house built by human hands.\r\n\r\nTyler’s voice broke into a wheezing laugh. Not his laugh. Not any laugh I’d heard before.\r\n\r\nThe thing outside whispered through the cracked door:\r\n\r\n“We’re coming in now.”\r\n\r\nAnd the cellar stairs behind us creaked.\r\n\r\nFrom below.\r\n\r\nThe cellar stairs behind us creaked.\r\nFrom below.\r\nNot from our weight.\r\nFrom something already down there.\r\n\r\nThe woman froze. “No. No… it shouldn’t be inside.”\r\n\r\nI held Tyler tighter. “What do we do?”\r\n\r\nBefore she could answer, the front door groaned wider. The shadow on the floor stretched. Long. Thin. Shifting like smoke trying to remember how to be solid.\r\n\r\nThe voice at the door spoke again.\r\nSoft. Sweet. Wrong.\r\n\r\n“Eli… we learn faster when you’re afraid.”\r\n\r\nThe cellar stairs creaked again. Something brushed the underside of the floorboards. A low, wet sound followed, like someone dragging a waterlogged coat.\r\n\r\nThe woman shoved me toward the hallway. “Get him out of this room. Now!”\r\n\r\nWe staggered backward with Tyler between us. His eyes flickered back and forth like he was watching something in the corners. Something we couldn’t see.\r\n\r\nThen he whispered:\r\n\r\n“They’re already inside.”\r\n\r\nI didn’t ask him what he meant. I didn’t want to know.\r\n\r\nThe woman kicked the cellar door shut and slid a heavy iron bolt across it. The wood shuddered. Something below pressed against it once. Twice. Testing.\r\n\r\nThe woman hissed, “It’s splitting itself. The old stories were right.”\r\n\r\nThe front door slammed open behind us.\r\n\r\nCold air ripped through the house. Every candle went out at once. The only light came from snow reflecting through the doorway.\r\n\r\nAnd a shape standing in it.\r\n\r\nEmma’s shape.\r\n\r\nExcept this time it wasn’t pretending to be warm. Or alive. Or human.\r\n\r\nHer head tilted all the way to the side, past where bone should allow. Her jaw dangled open. Her eyes—once kind—were now glossy mirrors reflecting the kitchen, the hall, and us… but nothing inside them moved.\r\n\r\nThe thing stepped over the threshold.\r\n\r\nThe shadow stretched unnaturally long. Its hand unfolded, fingers lengthening until they nearly touched the floor.\r\n\r\nTyler let out a ragged breath. “She’s… beautiful…”\r\n\r\nThe woman slapped him across the face. Hard. “That’s not her! Stay awake!”\r\n\r\nBut he only smiled through tears.\r\n\r\nThe creature reached toward him.\r\n\r\nI acted without thinking. I dragged Tyler back, almost falling into the hallway wall. Emma’s shape stopped moving and turned its head toward me instead.\r\n\r\n“Eli,” it whispered in my voice. “Come here.”\r\n\r\nAnother creak from the cellar.\r\n\r\nThen a second voice joined from beneath the floor.\r\n\r\nEmma’s real voice.\r\n\r\nSmall. Weak. Broken.\r\n\r\n“Please… please don’t let it take you too…”\r\n\r\nI felt something tear inside my chest. Hearing her real voice again—one that wasn’t mimicked—hurt worse than anything.\r\n\r\nThe woman grabbed my coat. “You want to save him? Then run. Now!”\r\n\r\n“What about you?”\r\n\r\n“I’ll slow the first one. The second is slower. Go!”\r\n\r\nI didn’t argue.\r\n\r\nI hooked my arms under Tyler’s and hauled him toward the back door. His feet dragged across the floor, leaving faint streaks in the dust.\r\n\r\nBehind us, the woman swung the iron poker at the creature wearing Emma. It shrieked. A piercing, metallic scream.\r\n\r\nThe cellar door buckled. Splintered.\r\n\r\nTwo creatures. One wearing Emma. One wearing no face at all.\r\n\r\nWe burst through the back door and into the snow.\r\n\r\nThe cold stabbed my lungs, but I kept pulling Tyler with everything I had left. The woman’s screams echoed through the house. Then a crash. Then silence.\r\n\r\nI didn’t look back.\r\n\r\nNot even once.\r\n\r\nWe reached the tree line when Tyler suddenly stopped moving. His legs stiffened. His body jerked like something was pulling him backward.\r\n\r\nHis eyes locked onto mine.\r\n\r\nAnd for one second—one tiny second—I saw him again. The real Tyler. Scared. Trapped.\r\n\r\nHe whispered without sound:\r\n\r\n“Run.”\r\n\r\nHis face twisted. His mouth stretched. His pupils went black and glossy.\r\n\r\nThen he screamed in Emma’s voice, “Eli! Don’t leave me!”\r\n\r\nBranches behind us cracked. Snow exploded upward. Something crawled out of the darkness of the farmhouse, moving low and fast.\r\n\r\nI ran.\r\n\r\nI ran until my legs almost collapsed under me. Until the snow blurred. Until the world spun.\r\n\r\nAt the edge of the woods, blue lights flashed.\r\n\r\nA sheriff’s truck.\r\nTwo deputies.\r\nGuns drawn.\r\n\r\nI stumbled forward and collapsed into the snow. I tried to warn them, but nothing came out except raw, broken breaths.\r\n\r\nThe officers pulled me to my feet. One shouted into his radio. The other pointed a flashlight into the trees.\r\n\r\n“Is someone else out there?” he asked.\r\n\r\nI tried to say no.\r\nI tried to say don’t go in.\r\nI tried to say it copies voices.\r\n\r\nBut all that came out was:\r\n\r\n“Please… please don’t answer it…”\r\n\r\nA voice drifted from the woods behind us.\r\n\r\nTyler’s voice.\r\n\r\nPerfect.\r\n\r\nCalm.\r\n\r\n“Hey! Over here! Wait for me!”\r\n\r\nThe deputy lifted his gun. “Hold on, kid, we’re coming!”\r\n\r\nI screamed.\r\n\r\nBut it was too late.\r\n\r\nThe deputy stepped toward the trees.\r\n\r\nThe flashlight beam hit the branches.\r\n\r\nAnd something stepped into the light wearing Tyler’s shape.\r\n\r\nSmiling wrong.\r\nBones bending wrong.\r\nEyes reflecting like glass.\r\n\r\nThe deputy didn’t even have time to understand.\r\n\r\nThe creature lunged.\r\n\r\nThe snow swallowed the scream.\r\n\r\nThe flashlight rolled once across the ground and went out.\r\n\r\nThe second deputy dragged me into the truck and locked the doors. He floored the gas, tires spinning on the ice until they caught and shot forward. I looked through the rear window.\r\n\r\nSomething crawled into the beam of the truck’s taillights.\r\n\r\nNot Tyler.\r\nNot Emma.\r\nNot anything human.\r\n\r\nIt stopped at the edge of the road.\r\n\r\nTilted its head.\r\n\r\nAnd whispered my name.\r\n\r\nI squeezed my eyes shut.\r\n\r\nIt didn’t matter.\r\n\r\nI heard it inside my head anyway.\r\n\r\nI survived.\r\n\r\nBut sometimes, when the world goes quiet, when snow falls heavy, when nights feel too deep…\r\n\r\nI hear my own voice\r\ncalling from the woods\r\nasking me to come back.', 'uploads/stories/story_1_1765039024_6c03373d.png', 1, 0, 4, 0, NULL, '2025-11-16 12:37:49', '2025-12-21 00:34:41');
INSERT INTO `stories` (`id`, `user_id`, `title`, `slug`, `category`, `content`, `image_path`, `is_published`, `is_featured`, `views`, `likes`, `image`, `created_at`, `updated_at`) VALUES
(4, 1, 'The One Who Wakes First', 'test-1-2', 'short', 'The first thing that hit you was the cold. Not normal cold. It felt like someone opened a freezer in front of your face. You woke up in your bed, but the room looked wrong. The walls looked stretched. The corners looked sharper. Your posters looked faded like someone scrubbed the color off.\r\n\r\nYou sat up fast. Your chest felt tight. Your breath came out in little gasps. You tried to shake it off. You told yourself you were dreaming. It sounded weak, but you said it anyway.\r\n\r\nYou slid your feet to the floor. The wood felt wet. Not soaked, but damp like someone wiped it with a cold cloth. Your skin crawled. You checked the window. Closed. Checked the door. Closed.\r\n\r\nThen the tapping started.\r\n\r\nOne tap. Then another. It came from under the bed. Slow. Calm. Like whatever was down there had all night.\r\n\r\nYou froze. Your throat tightened. You tried to move your foot back onto the bed, but your legs felt heavy. The tapping got faster. A little rhythm. Like it was trying to get your attention.\r\n\r\nYou bent down. Only a little. Just enough to look at the gap under your bed. Dark. Too dark. The kind of dark your eyes should adjust to, but didn’t.\r\n\r\nThen a voice whispered your name.\r\n\r\nSoft. Close. Right under you.\r\n\r\nYou jerked back so hard the bed frame rattled. You grabbed your phone, but the screen stayed black. No battery percentage. No icons. Just a reflection of your own face. Even that looked off. Your eyes in the reflection looked a little too wide.\r\n\r\nYou stepped away from the bed. The voice laughed. Quiet. Slow. It sounded like it was enjoying your reaction. You hated that. You felt your face heat up with panic.\r\n\r\nYou backed up to the door. You reached for the handle. It felt ice cold. You twisted it hard. It didn’t move. You tried again. Nothing.\r\n\r\nThe tapping under the bed stopped.\r\n\r\nThe silence felt heavier than the sound.\r\n\r\nThen something pressed a single finger against the underside of the mattress. The whole bed lifted a few centimeters like someone strong was pushing it up with ease. You yelped and stumbled back from the door. Your heartbeat went wild. You felt it in your neck.\r\n\r\nThe bed slammed back to the floor. The sound echoed through the room. You covered your ears even though it was already over.\r\n\r\nA low breath came from under the bed. Slow. Calm. The same rhythm as the tapping from before.\r\n\r\nThen the voice whispered again.\r\n\r\n“I’m not under here anymore.”\r\n\r\nYou spun around so fast you almost fell. Your room looked empty. Quiet. Still.\r\n\r\nThen the light in the hallway flicked on by itself. It showed a long shadow stretching into your room. Not yours.\r\n\r\nIt stood at the doorway.\r\n\r\nThe shadow didn’t move. It just stood there, stretched long across the floor, reaching almost to your feet. You tried to pull back, but your body refused to listen. Your muscles tightened. Your chest felt locked. You could only stare at the doorway, waiting for something to step in.\r\n\r\nYour heartbeat thudded so loud it almost drowned the silence. Almost.\r\n\r\nThen the figure leaned in.\r\n\r\nNot a full step. Just a slight tilt of the head past the door frame. Enough for you to see the outline of a human shape. Tall. Thin. Shoulders a little too narrow. Neck a little too long. You squinted, hoping it would fade like some optical trick. It didn’t.\r\n\r\nThe voice drifted in again. This time from the hallway.\r\n\r\n“You always wake up so slow.”\r\n\r\nYou shook your head hard. “I’m dreaming.”\r\n\r\nThe figure chuckled. It sounded dry. Like it scraped along the walls.\r\n\r\n“You think this feels like a dream?”\r\n\r\nYou didn’t answer. You didn’t trust your voice. You took a shaky step backward. Your heel bumped the side of your desk. The vibration made your empty water bottle rattle. That tiny noise felt way too loud.\r\n\r\nThe figure stepped into the doorway.\r\n\r\nNot fully. Just one foot. Bare. Pale. The toes long. Slightly curved. Like they never fit shoes right. The foot pressed lightly against the wooden floor, but it didn’t make a sound. Not even a creak.\r\n\r\nYour throat tightened more.\r\n\r\n“Let me show you,” it whispered.\r\n\r\nThe hallway lights flickered. Once. Twice. Then they stayed on, but the brightness twisted. The walls in the hall shifted, stretching upward like they were being pulled. The ceiling rose with them. The hallway looked longer now. Much longer.\r\n\r\nYou ran for the window. You didn’t think. You just moved. You grabbed the curtains and yanked them aside.\r\n\r\nThe outside looked wrong.\r\n\r\nYour street wasn’t there. No houses. No trees. No road. Only a flat empty field of black soil. The sky looked frozen with one pale streak of light that never moved.\r\n\r\nYour breath hitched.\r\n\r\n“Where do you think you’re going?”\r\n\r\nThe voice came from behind you. Much closer.\r\n\r\nYou spun around.\r\n\r\nThe figure was in your room now. Fully inside. It stood by the foot of your bed. Its body looked human at first glance, but the proportions were wrong. The arms hung too low. The fingers too long. The head tilted slowly to one side.\r\n\r\nYou backed up against the window. You felt the cold glass bite into your skin.\r\n\r\n“I don’t want this,” you whispered.\r\n\r\nThe figure took one soft step toward you.\r\n\r\n“That’s the point.”\r\n\r\nYou tried to scream. Nothing came out. Not even air.\r\n\r\nThe figure reached a hand toward your face. Its fingers stopped just before touching your skin. You felt the cold radiating off it. Your eyes watered.\r\n\r\n“Wake up,” it said.\r\n\r\nYour vision blurred. The room stretched again. The floor dropped away for a second then snapped back. You felt dizzy. Sick. You blinked hard. You hoped you’d open your eyes in your real room.\r\n\r\nInstead, you opened them to darkness.\r\n\r\nA pitch black void.\r\n\r\nYou couldn’t even see your hands. The air felt thick. Heavy. No floor under your feet. You floated. You reached out, desperate to feel something.\r\n\r\nNothing.\r\n\r\nThen, in that endless dark, the voice whispered right behind your ear.\r\n\r\n“You never woke up.”\r\n\r\nYou snapped your head toward the sound, but there was no direction here. The dark felt alive. It pressed against your skin. Every breath felt like you were inhaling thick dust. You tried to move your arms. They felt slow, like you were pushing through heavy water.\r\n\r\nA dim glow appeared far ahead. A tiny pin of light. You focused on it fast, desperate for anything real. It pulsed once. Then again. Each pulse matched your heartbeat. You pushed your body toward it. You didn’t know how you moved. You just willed yourself forward.\r\n\r\nThe light grew. You saw something inside it. A doorway. A real one. A plain wooden door with chips along the frame. Your door. From your room. You felt actual hope hit you in the chest.\r\n\r\nYou reached for the knob.\r\n\r\nYour fingers touched the metal.\r\n\r\nA hand grabbed your wrist from behind.\r\n\r\nIts skin felt dry, cracked, cold. The grip tightened until your bones hurt. You tried to pull away. The grip got stronger.\r\n\r\n“You always run to the wrong door,” the voice said.\r\n\r\nYou twisted around. The glow behind you revealed the figure for the first time. Its face looked like a rough sketch of a person. Features carved into pale skin. Eyes too dark. Mouth stretched too wide, like it was drawn in with a shaky hand. It stared at you without blinking.\r\n\r\n“I gave you something real,” it said. “And you keep trying to escape it.”\r\n\r\nYou shook your head fast. “This isn’t real.”\r\n\r\nIt leaned closer. Its breath felt cold on your face. “You keep saying that. You never think about why you need to say it.”\r\n\r\nYou kicked at the darkness. You pulled your wrist again. The grip loosened a little. You pushed harder. Your fingers brushed the door knob again. You grabbed it with both hands and yanked.\r\n\r\nThe door flew open.\r\n\r\nYou fell through it.\r\n\r\nYou hit the floor hard. Wood. Real wood. You gasped and scrambled to your feet. Your room was back. Normal walls. Normal corners. Posters with real color. Your bed looked untouched. Your phone sat charging on your nightstand, glowing softly.\r\n\r\nYou grabbed it and checked the screen. Time looked normal. Battery normal. Everything normal. Relief washed through you so fast you almost cried.\r\n\r\nYou sat on the bed and rubbed your eyes.\r\n\r\nThen you noticed the underside of your mattress.\r\n\r\nA single pale handprint pressed into the fabric.\r\n\r\nFresh.\r\n\r\nThe fabric slowly sank inward.\r\n\r\nSomething pushed upward.\r\n\r\nThe pressure under the mattress grew. Slow. Intentional. You stared at the handprint as it deepened, pushing the fabric inward like a finger pressing into soft clay. You took one small step back. Your breath trembled. You tried to tell yourself it was leftover dream panic, but your body knew better.\r\n\r\nThe mattress shifted. Something slid along the underside. You heard the faint scrape of nails dragging across wood.\r\n\r\nYou stumbled toward the door. You grabbed the handle. It turned this time. You pulled it open fast.\r\n\r\nThe hallway lights flickered again.\r\n\r\nYour stomach dropped.\r\n\r\nYou stepped out. The moment your foot hit the hall carpet, the lights snapped off. Total darkness swallowed everything. You reached for your phone, but the screen died the moment you tapped it. You slapped the side of it, trying to wake it again. No use.\r\n\r\nBehind you, your bedroom door creaked open on its own.\r\n\r\nYou didn’t look back. You didn’t want to see what stood there.\r\n\r\nYou moved down the hall with slow steps, hands against the wall to guide yourself. The air felt cooler with every step. Your fingers brushed the family photos on the wall. You felt the frames tilt, like someone pushed them out of alignment.\r\n\r\nThen a voice whispered from behind you.\r\n\r\n“Why walk away from your room?”\r\n\r\nYou kept moving. Your legs shook, but you forced them forward. You reached the end of the hall. You felt the familiar texture of the light switch. You flipped it up.\r\n\r\nNothing.\r\n\r\nYou flipped it down.\r\n\r\nNothing.\r\n\r\nYou breathed through your teeth, panic creeping up your spine.\r\n\r\nThen you heard it. A dragging sound. Slow. Heavy. A shuffle that felt too close to the floor. It came from the bedroom doorway. It moved into the hall. It followed your steps.\r\n\r\nYou pushed into the bathroom and slammed the door shut. You locked it. You stepped back until your shoulders hit the wall.\r\n\r\nIt was quiet for a moment.\r\n\r\nThen the doorknob rattled.\r\n\r\nOnce. Twice.\r\n\r\nThen a soft knock.\r\n\r\nThree taps.\r\n\r\nYour mouth went dry. You covered it with your hand to keep yourself from making a sound.\r\n\r\nYou stared at the door. The gap under it glowed with a faint pale light. Soft white. Not warm. Not natural. It got brighter. Something moved past the gap. A long shadow.\r\n\r\nThen you heard scraping on the mirror behind you.\r\n\r\nYou froze.\r\n\r\nYou didn’t want to turn around. You knew nothing should be behind you. You pressed your back tighter to the wall, your eyes fixed on the bathroom door.\r\n\r\nThe scraping got louder.\r\n\r\nGlass on glass.\r\n\r\nA slow curved line.\r\n\r\nThen another.\r\n\r\nYour breathing got uneven. You turned your head a tiny bit. Just enough to see the mirror from the corner of your eye.\r\n\r\nA sentence formed across the fogless surface.\r\n\r\nLetter by letter.\r\n\r\n“I followed you.”\r\n\r\nYour throat closed.\r\n\r\nThe knocking started again.\r\n\r\nLouder.\r\n\r\nA calm voice spoke through the door.\r\n\r\n“Look closer at the mirror.”\r\n\r\nYou kept your eyes on the mirror even though every part of your body begged you to look away. The letters stopped moving. The room went silent again. You felt your pulse pounding against your skin. You took one slow step toward the mirror. Your reflection looked normal at first. Same hair. Same hoodie. Same scared face.\r\n\r\nThen your reflection blinked.\r\n\r\nYou didn’t blink.\r\n\r\nYour stomach tightened. You backed up fast. Your reflection smiled. The smile stretched too wide. The cheeks pulled up too high. The eyes stayed blank. No fear. No panic. Just a slow, growing grin like it waited for this moment.\r\n\r\nYou shook your head. “No. No no no.”\r\n\r\nThe knocking on the door stopped.\r\n\r\nSilence filled the room again. Thick. Heavy.\r\n\r\nYour reflection lifted a hand and pressed it against the inside of the mirror. The glass rippled around its palm like water. You watched your own face move closer inside the mirror until its forehead pressed against the glass.\r\n\r\nThen it whispered through the reflection.\r\n\r\n“Let me out.”\r\n\r\nYou stepped back until your heel hit the wall. The mirror started to bulge outward. The glass stretched like someone behind it pushed with both hands. Cracks spread along the edges. Tiny snapping sounds filled the room.\r\n\r\nYou grabbed the sink and held on. Your breath shook. You felt the floor vibrate beneath you.\r\n\r\nA single crack split through the middle of the mirror.\r\n\r\nYour reflection pushed its fingers through. The fingers looked pale. Longer than yours. They curled around the edges of the broken glass.\r\n\r\nIt pulled.\r\n\r\nThe mirror burst open. Shards flew across the bathroom floor. You shielded your face with your arm. When you looked again, your reflection stepped out of the broken frame. It stood in front of you. Same clothes. Same hair. Same height.\r\n\r\nBut the smile didn’t fade.\r\n\r\nYou squeezed against the wall. “Stay away from me.”\r\n\r\nYour reflection tilted its head. “Why? You made me.”\r\n\r\nYou shook your head. “I didn’t.”\r\n\r\n“You did every time you tried to wake up,” it said. “You pushed deeper instead of out. So I took the exit.”\r\n\r\nIt stepped closer. Each step quiet. Smooth. Controlled.\r\n\r\nYou pressed your back against the wall harder. The air felt colder with every inch it closed.\r\n\r\n“I don’t want this,” you said.\r\n\r\n“Yes you do,” it whispered. “That is why this place grew. That is why the hall stretched. That is why the room changed. You fed it.”\r\n\r\nYou shook your head again. “I want to wake up.”\r\n\r\n“You already did,” it said. “You woke up here.”\r\n\r\nYou felt the words sink into your chest. You didn’t want to accept it. You looked at the broken mirror. No way back. Only cracks and darkness behind the frame.\r\n\r\nYour reflection reached out and touched your cheek. The touch felt real. Too real.\r\n\r\n“You belong here now,” it said. “Let me take your place out there.”\r\n\r\nYou grabbed its wrist. You pushed it away. The strength surprised you. The reflection stumbled back one step. Its smile faded for the first time.\r\n\r\nGood.\r\n\r\nYou pushed off the wall and went straight for the bathroom door. You unlocked it. You threw it open.\r\n\r\nThe hallway looked normal again.\r\n\r\nLights steady. Walls straight. Photos in place.\r\n\r\nYou sprinted.\r\n\r\nYour reflection hissed behind you. Its footsteps hit the tiles fast. You reached the front door. You grabbed the handle. You twisted hard.\r\n\r\nIt opened.\r\n\r\nCold night air hit your face. Real air. Crisp and normal. You pushed outside. You slammed the door behind you. You held it shut.\r\n\r\nYour reflection slammed into the door from the other side. The wood shook. You felt one hard shove. Then another. The door creaked under the pressure. You pushed with both hands, putting your whole body weight on it.\r\n\r\nThen the knocking stopped.\r\n\r\nSilence.\r\n\r\nYou waited. Ten seconds. Twenty. Thirty.\r\n\r\nNothing.\r\n\r\nYou slowly eased your grip. You backed away from the door. You stared at it, waiting for the knob to turn. It stayed still.\r\n\r\nYou walked down the porch steps. Your street looked normal. Houses. Trees. Streetlights. You took a deep breath. You felt the tension in your shoulders loosen.\r\n\r\nYou turned around to look at the house one more time.\r\n\r\nYour reflection stood in the upstairs window.\r\n\r\nIt smiled.\r\n\r\nIt lifted a hand.\r\n\r\nThen it slowly closed the curtains.\r\n\r\nYou ran.\r\n\r\nAnd the worst part was simple.\r\n\r\nYou knew it would wait for you to come back.\r\n\r\nThe nightmare ended when you woke up inside it.\r\n\r\nAnd now it had your room.\r\n\r\nYour house.\r\n\r\nYour reflection.\r\n\r\nAnd all the time in the world.', 'uploads/stories/story_1_1763296803_8e0d6ef6.png', 1, 1, 9, 1, NULL, '2025-11-16 13:16:40', '2025-12-21 00:01:14');

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

--
-- Dumping data for table `story_bookmarks`
--

INSERT INTO `story_bookmarks` (`id`, `user_id`, `story_id`, `created_at`) VALUES
(11, 1, 4, '2025-11-30 00:30:21');

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
(4, 4, 1, NULL, 'This story is amazing', '2025-11-22 22:30:29');

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
(1, 'chrismolina', 'chris@chris.com', '$2y$10$GyO.1rYTydKDmmVk49at2uSnxvLDm4YBbD0/Dr5NO1E/i8mRPDfui', 'Chris', 'uploads/avatars/avatar_1_1763198680.jpg', 'bidnv;soNEOnb\'o', 'admin', '2025-11-15 10:19:37', '2025-12-21 10:53:51'),
(2, 'testtest', 'test@test.com', '$2y$10$E9NT4Y1RWYFavJ2FFPG7V.3Lr4DhMUguizsZgs3aXoJyCDgLC5D..', 'test test', 'uploads/avatars/avatar_2_1763200718.webp', '', 'user', '2025-11-15 10:57:57', '2025-12-21 00:36:21');

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
-- Indexes for table `contact_requests`
--
ALTER TABLE `contact_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `contact_requests`
--
ALTER TABLE `contact_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
-- Constraints for table `contact_requests`
--
ALTER TABLE `contact_requests`
  ADD CONSTRAINT `contact_requests_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
