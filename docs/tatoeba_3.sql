-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 30, 2008 at 08:21 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `tatoeba_3`
--

-- --------------------------------------------------------

--
-- Table structure for table `acos`
--

CREATE TABLE IF NOT EXISTS `acos` (
  `id` int(10) NOT NULL auto_increment,
  `parent_id` int(10) default NULL,
  `model` varchar(255) default NULL,
  `foreign_key` int(10) default NULL,
  `alias` varchar(255) default NULL,
  `lft` int(10) default NULL,
  `rght` int(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

--
-- Dumping data for table `acos`
--

INSERT INTO `acos` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, NULL, NULL, NULL, 'controllers', 1, 92),
(2, 1, NULL, NULL, 'Pages', 2, 5),
(3, 2, NULL, NULL, 'display', 3, 4),
(4, 1, NULL, NULL, 'Groups', 6, 17),
(5, 4, NULL, NULL, 'index', 7, 8),
(6, 4, NULL, NULL, 'view', 9, 10),
(7, 4, NULL, NULL, 'add', 11, 12),
(8, 4, NULL, NULL, 'edit', 13, 14),
(9, 4, NULL, NULL, 'delete', 15, 16),
(10, 1, NULL, NULL, 'Sentences', 18, 35),
(11, 10, NULL, NULL, 'index', 19, 20),
(12, 10, NULL, NULL, 'show', 21, 22),
(13, 10, NULL, NULL, 'add', 23, 24),
(14, 10, NULL, NULL, 'delete', 25, 26),
(15, 10, NULL, NULL, 'edit', 27, 28),
(16, 10, NULL, NULL, 'translate', 29, 30),
(17, 10, NULL, NULL, 'save_translation', 31, 32),
(18, 1, NULL, NULL, 'Translations', 36, 37),
(19, 1, NULL, NULL, 'Users', 38, 59),
(20, 19, NULL, NULL, 'index', 39, 40),
(21, 19, NULL, NULL, 'view', 41, 42),
(22, 19, NULL, NULL, 'add', 43, 44),
(23, 19, NULL, NULL, 'edit', 45, 46),
(24, 19, NULL, NULL, 'delete', 47, 48),
(25, 19, NULL, NULL, 'login', 49, 50),
(26, 19, NULL, NULL, 'logout', 51, 52),
(27, 19, NULL, NULL, 'initDB', 53, 54),
(28, 1, NULL, NULL, 'SuggestedModifications', 60, 75),
(29, 28, NULL, NULL, 'index', 61, 62),
(30, 28, NULL, NULL, 'view', 63, 64),
(31, 28, NULL, NULL, 'add', 65, 66),
(32, 28, NULL, NULL, 'edit', 67, 68),
(33, 28, NULL, NULL, 'delete', 69, 70),
(34, 28, NULL, NULL, 'save_suggestion', 71, 72),
(35, 1, NULL, NULL, 'LatestActivities', 76, 79),
(36, 35, NULL, NULL, 'index', 77, 78),
(37, 10, NULL, NULL, 'search', 33, 34),
(38, 1, NULL, NULL, 'SentenceComments', 80, 87),
(39, 38, NULL, NULL, 'index', 81, 82),
(40, 38, NULL, NULL, 'add', 83, 84),
(41, 38, NULL, NULL, 'save', 85, 86),
(42, 28, NULL, NULL, 'refuse', 73, 74),
(43, 19, NULL, NULL, 'register', 55, 56),
(44, 19, NULL, NULL, 'new_password', 57, 58),
(45, 1, NULL, NULL, 'UsersStatistics', 88, 91),
(46, 45, NULL, NULL, 'index', 89, 90);

-- --------------------------------------------------------

--
-- Table structure for table `aros`
--

CREATE TABLE IF NOT EXISTS `aros` (
  `id` int(10) NOT NULL auto_increment,
  `parent_id` int(10) default NULL,
  `model` varchar(255) default NULL,
  `foreign_key` int(10) default NULL,
  `alias` varchar(255) default NULL,
  `lft` int(10) default NULL,
  `rght` int(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `aros`
--

INSERT INTO `aros` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, NULL, 'Group', 1, NULL, 1, 2),
(2, NULL, 'Group', 2, NULL, 3, 4),
(3, NULL, 'Group', 3, NULL, 5, 6),
(4, NULL, 'Group', 4, NULL, 7, 8);

-- --------------------------------------------------------

--
-- Table structure for table `aros_acos`
--

CREATE TABLE IF NOT EXISTS `aros_acos` (
  `id` int(10) NOT NULL auto_increment,
  `aro_id` int(10) NOT NULL,
  `aco_id` int(10) NOT NULL,
  `_create` varchar(2) NOT NULL default '0',
  `_read` varchar(2) NOT NULL default '0',
  `_update` varchar(2) NOT NULL default '0',
  `_delete` varchar(2) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `ARO_ACO_KEY` (`aro_id`,`aco_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `aros_acos`
--

INSERT INTO `aros_acos` (`id`, `aro_id`, `aco_id`, `_create`, `_read`, `_update`, `_delete`) VALUES
(1, 1, 1, '1', '1', '1', '1'),
(2, 2, 1, '-1', '-1', '-1', '-1'),
(3, 2, 28, '1', '1', '1', '1'),
(4, 2, 38, '1', '1', '1', '1'),
(5, 2, 10, '1', '1', '1', '1'),
(6, 2, 15, '1', '1', '1', '1'),
(7, 3, 1, '-1', '-1', '-1', '-1'),
(8, 3, 40, '1', '1', '1', '1'),
(9, 3, 15, '1', '1', '1', '1'),
(10, 4, 1, '-1', '-1', '-1', '-1'),
(11, 4, 40, '1', '1', '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `created`, `modified`) VALUES
(1, 'admin', '2008-12-02 02:07:57', '2008-12-02 02:07:57'),
(2, 'moderator', '2008-12-02 02:08:03', '2008-12-02 02:08:03'),
(3, 'trusted_user', '2008-12-02 02:08:13', '2008-12-02 02:08:13'),
(4, 'user', '2008-12-02 02:08:18', '2008-12-02 02:08:18'),
(5, 'pending_user', '2008-12-30 05:34:48', '2008-12-30 05:34:48');

-- --------------------------------------------------------

--
-- Table structure for table `sentences`
--

CREATE TABLE IF NOT EXISTS `sentences` (
  `id` int(11) NOT NULL auto_increment,
  `lang` varchar(2) default NULL,
  `text` varchar(500) character set utf8 collate utf8_unicode_ci NOT NULL,
  `correctness` smallint(2) default NULL,
  `user_id` int(11) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ;

--
-- Dumping data for table `sentences`
--

INSERT INTO `sentences` (`id`, `lang`, `text`, `correctness`, `user_id`, `created`, `modified`) VALUES
(1, 'fr', 'Cette phrase a été ajoutée par trang3.', 2, NULL, '2008-11-22 18:31:30', '2008-11-22 18:31:30'),
(2, 'en', 'This sentence has been added by trang3.', 2, NULL, '2008-11-22 18:32:50', '2008-11-22 18:32:50'),
(3, 'fr', 'J''ai très très très froid.', 2, NULL, '2008-11-22 18:33:23', '2008-11-22 18:33:23'),
(4, 'en', 'I am very very very cold.', 2, NULL, '2008-11-22 18:33:41', '2008-11-22 18:33:41'),
(5, '', 'Let''s see if the language detection works.', 1, NULL, '2008-11-23 16:42:38', '2008-11-23 16:42:38'),
(6, '', 'Let''s try again.', 1, NULL, '2008-11-23 16:44:54', '2008-11-23 16:44:54'),
(7, '', 'Things never work on the first try.', 1, NULL, '2008-11-23 16:45:43', '2008-11-23 16:45:43'),
(8, 'en', 'Add a sentence!', 1, NULL, '2008-11-23 16:47:04', '2008-11-23 16:47:04'),
(9, 'en', 'sigh', 1, NULL, '2008-11-23 16:51:18', '2008-11-23 16:51:18'),
(10, 'fr', 'Il fait très froid.', 1, NULL, '2008-11-23 16:52:39', '2008-11-23 16:52:39'),
(11, 'en', 'Erase una vez...', 1, NULL, '2008-11-23 16:57:24', '2008-11-23 16:57:24'),
(12, 'fr', 'Voici une nouvelle phrase', 1, NULL, '2008-11-23 17:02:32', '2008-11-23 17:02:32'),
(13, 'en', 'I fear something bad will happen...', 1, NULL, '2008-11-23 17:06:28', '2008-11-23 17:06:28'),
(14, 'fr', 'C''est probablement pas la meilleure chose à faire.', 1, NULL, '2008-11-23 17:07:43', '2008-11-23 17:07:43'),
(15, 'en', 'Ahh, why why why, doesn''t it work.', 1, NULL, '2008-11-23 17:23:45', '2008-11-23 17:23:45'),
(16, 'en', 'Not it''s not better...', 1, NULL, '2008-11-23 17:24:22', '2008-11-23 17:24:22'),
(17, 'fr', 'Ça marche pas avec l''espagnol??', 1, NULL, '2008-11-23 17:24:39', '2008-11-23 17:24:39'),
(18, 'fr', 'Je crois que non.', 1, NULL, '2008-11-23 17:25:28', '2008-11-23 17:25:28'),
(19, 'zh', '免费下载:外汇基础知识精选！入门必备 了解外汇,如何降低风险,判断交易时机 ', 1, NULL, '2008-11-23 17:50:14', '2008-11-23 17:50:14'),
(20, 'zh', '免费下载:外汇基础知识精选！入门必备 了解外汇,如何降低风险,判断交易时机 ', 1, NULL, '2008-11-23 17:52:51', '2008-11-23 17:52:51'),
(21, 'en', 'This is nice.', 1, NULL, '2008-11-23 17:54:55', '2008-11-23 17:54:55'),
(22, 'en', 'It is very cold.', 1, NULL, '2008-11-23 17:57:55', '2008-11-23 17:57:55'),
(23, 'de', 'Das Symbol ''&'' steht für ''und''.', NULL, NULL, NULL, NULL),
(24, 'en', 'The sign ''&'' stands for ''and''.', NULL, NULL, NULL, NULL),
(25, 'fr', 'Le symbole ''&'' signifie ''et''.', NULL, NULL, NULL, NULL),
(26, 'jp', '＆という記号は、ａｎｄを指す。', NULL, NULL, NULL, NULL),
(27, 'pt', 'O sinal ''&'' significa ''e''. test', NULL, NULL, NULL, '2008-11-30 16:00:21'),
(28, 'en', 'The mark "&" stands for "and".', NULL, NULL, NULL, NULL),
(29, 'fr', 'Le signe "&" signifie "et".', NULL, NULL, NULL, NULL),
(30, 'jp', '＆のマークはａｎｄの文字を表す。', NULL, NULL, NULL, NULL),
(31, 'pt', 'A marca ''&'' significa ''e''.\r\n', NULL, NULL, NULL, NULL),
(32, 'en', '(On a bicycle) Whew! This is a tough hill. But coming back sure will be a breeze.', NULL, NULL, NULL, NULL),
(33, 'fr', '(A vélo) Whoua! C''est une côte raide. Mais la descente sera un vrai plaisir.', NULL, NULL, NULL, NULL),
(34, 'jp', '（自転車に乗って）フーッ、この坂道はきついよ。でも帰りは楽だよね。', NULL, NULL, NULL, NULL),
(35, 'pt', '(Numa bicicleta) Ufa! Esta ladeira está difícil. Mas a volta será moleza.\r\n', NULL, NULL, NULL, NULL),
(36, 'en', 'As it is, prices are going up every week.', NULL, NULL, NULL, NULL),
(37, 'jp', '（予想に反して）実のところ物価は毎週上昇している。', NULL, NULL, NULL, NULL),
(38, 'pt', 'Na realidade, o custo de vida está subindo cada semana.\r\n', NULL, NULL, NULL, NULL),
(39, 'en', 'I was acutely aware that..', NULL, NULL, NULL, NULL),
(40, 'fr', 'J''étais tout à fait conscient que ...', NULL, NULL, NULL, NULL),
(41, 'jp', '～と痛切に感じている。', NULL, NULL, NULL, NULL),
(42, 'pt', 'Estava bem consciente que ... \r\n', NULL, NULL, NULL, NULL),
(43, 'en', 'test There is a certain amount of truth in ~.', NULL, NULL, NULL, '2008-11-30 16:08:25'),
(44, 'fr', 'Il y a une part de vérité dans ~', NULL, NULL, NULL, NULL),
(45, 'jp', '～にも一面の真理がある。', NULL, NULL, NULL, NULL),
(46, 'pt', 'Há um pouco de verdade em ...\r\n', NULL, NULL, NULL, NULL),
(47, 'en', 'Go to the doctor to get your prescription!', NULL, NULL, NULL, NULL),
(48, 'fr', 'Va voir un docteur pour te faire faire une ordonnance.', NULL, NULL, NULL, NULL),
(49, 'jp', '処方箋をもらうために医者に行きなさい。', NULL, NULL, NULL, NULL),
(50, 'pt', 'Vai ao médico pegar a receita!\r\n', NULL, NULL, NULL, NULL),
(51, 'en', '"I sailed around the Mediterranean in a schooner when I was seventeen," she recited slowly and carefully. [F]', NULL, NULL, NULL, NULL),
(52, 'fr', 'J''ai navigué autour de la Méditerranée dans une goélette quand j''avais dix-sept ans, déclama-t''elle lentement et soigneusement.', NULL, NULL, NULL, NULL),
(53, 'jp', '「１７歳の時スクーナー船で地中海を航海したわ」彼女はゆっくりと注意深く言う。', NULL, NULL, NULL, NULL),
(54, 'pt', '"Quando tive 17 anos de idade, velejei pelo Mediterrâneo numa escuna", ela recitou lenta e cuidadosamente.', NULL, NULL, NULL, NULL),
(55, 'en', '"Six pence per second" Bob reminds her.', NULL, NULL, NULL, NULL),
(56, 'fr', '"Six pences à la seconde", lui rappela Bob.', NULL, NULL, NULL, NULL),
(57, 'jp', '「１秒６ペンスだからね」とボブが念を押す。', NULL, NULL, NULL, NULL),
(58, 'pt', 'Bob a lembra: "6 pence por segundo".\r\n', NULL, NULL, NULL, NULL),
(59, 'en', '"Four pounds fifty" says Bob.', NULL, NULL, NULL, NULL),
(60, 'fr', '"Quatre livres cinquante" dit Bob.', NULL, NULL, NULL, NULL),
(61, 'jp', '「４ポンド５０ペンス」とボブが言う。', NULL, NULL, NULL, NULL),
(62, 'pt', 'Bob diz: "Quatro libras e cinquenta".\r\n', NULL, NULL, NULL, NULL),
(63, 'en', '"Four pounds, ninety pence" Bob answers.', NULL, NULL, NULL, NULL),
(64, 'fr', '"Quatre livres, quatre-vingt-dix pences", répond Bob.', NULL, NULL, NULL, NULL),
(65, 'jp', '「４ポンド９０ペンスだよ」とボブが答える。', NULL, NULL, NULL, NULL),
(66, 'en', '"I saw her five days ago", he said.', NULL, NULL, NULL, NULL),
(67, 'fr', 'Il a dit : "Je l''ai vue il y a cinq jours".', NULL, NULL, NULL, NULL),
(68, 'jp', '「５日前に彼女にあった」と彼は言った。', NULL, NULL, NULL, NULL),
(69, 'fr', 'Le mot "downtown" désigne le quartier marchand d''une ville.', NULL, NULL, NULL, NULL),
(70, 'en', 'How do you spell ''pretty''?', NULL, NULL, NULL, NULL),
(71, 'fr', 'Comment épelle-t-on "pretty"?', NULL, NULL, NULL, NULL),
(72, 'jp', '「ｐｒｅｔｔｙ」の綴りは？', NULL, NULL, NULL, NULL),
(73, 'en', 'What does "There is a tide" imply? (Shakespeare''s Julius Caesar)', NULL, NULL, NULL, NULL),
(74, 'fr', 'Que faut-il entendre par "There is a tide"? (Jules Cesar, de Shakespeare)', NULL, NULL, NULL, NULL),
(75, 'jp', '「Ｔｈｅｒｅ　ｉｓ　ａ　ｔｉｄｅ」とはどういう意味ですか。（シェークスピアのジュリアス・シーザー）', NULL, NULL, NULL, NULL),
(76, 'en', '"What does U. F. O. stand for?" "It means Unidentified Flying Object, I guess."', NULL, NULL, NULL, NULL),
(77, 'fr', '"Que veut dire les initiales O.V.N.I.?" "Cela signifie Objet Volant Non-Identifié je crois."', NULL, NULL, NULL, NULL),
(78, 'jp', '「Ｕ．Ｆ．Ｏとは何を表しているの」「未確認飛行物体のことだと思う」', NULL, NULL, NULL, NULL),
(79, 'fr', '"Ah, je n''avais pas remarqué", dit le vieil homme. "Que devrions-nous faire?"', NULL, NULL, NULL, NULL),
(80, 'fr', '"Oh" s''écria le vieil homme avec enthousiasme, "Maintenant je peux choisir le chat le plus joli et le prendre avec moi à  la maison !"', NULL, NULL, NULL, NULL),
(81, 'en', 'Say ''ahhh''.', NULL, NULL, NULL, NULL),
(82, 'fr', 'Dites "ah".', NULL, NULL, NULL, NULL),
(83, 'jp', '「アー」といってください。', NULL, NULL, NULL, NULL),
(84, 'en', '''Ah'' is an interjection.', NULL, NULL, NULL, NULL),
(85, 'fr', '"Ah" est une interjection.', NULL, NULL, NULL, NULL),
(86, 'jp', '「ああ」は感嘆詞だ。', NULL, NULL, NULL, NULL),
(87, 'en', 'He''s a nice guy - that''s unanimous.', NULL, NULL, NULL, NULL),
(88, 'fr', 'Tout le monde s''accorde à dire que c''est quelqu''un de bien.', NULL, NULL, NULL, NULL),
(89, 'jp', '「あいつはいい奴だ」と皆が異口同音に言う。', NULL, NULL, NULL, NULL),
(90, 'en', '"Done!" says the angel, and disappears in a cloud of smoke and a bolt of lightning.', NULL, NULL, NULL, NULL),
(91, 'fr', '"Exaucé!" dit l''ange, et il disparut en un éclair dans un nuage de fumée .', NULL, NULL, NULL, NULL),
(92, 'jp', '「あっぱれ！」天使はそういうと、稲妻が走るもやもやの煙の中へ消えていった。', NULL, NULL, NULL, NULL),
(93, 'en', '"Where is your house?" "It is over there."', NULL, NULL, NULL, NULL),
(94, 'fr', '"Où est ta maison ?" "Elle est de l''autre côté."', NULL, NULL, NULL, NULL),
(95, 'jp', '「あなたの家はどこですか」「それは向こうです」', NULL, NULL, NULL, NULL),
(96, 'en', '"Do you really wish that?" asked the little white rabbit.', NULL, NULL, NULL, NULL),
(97, 'fr', '"Est-ce vraiment là ton souhait?", demanda le petit lapin blanc.', NULL, NULL, NULL, NULL),
(98, 'jp', '「あなたの願い事は本当にそれなの？」と小さい白いウサギが聞きました。', NULL, NULL, NULL, NULL),
(99, 'de', '"Was ist dein {Wunsch}{4}?" fragte das {kleine}{1} {weiße}{2} {Kaninchen}{3}.', NULL, NULL, NULL, NULL),
(100, 'en', '"What is your {wish}{4}?" asked the {little}{1} {white}{2} {rabbit}{3}.', NULL, NULL, NULL, NULL),
(101, 'es', '"¿Cual es tu {deseo}{4}? preguntó el {pequeño}{1} {conejo}{3} {blanco}{2}.', NULL, NULL, NULL, NULL),
(102, 'fr', '"Quel est ton {souhait}{4}?" demanda le {petit}{1} {lapin}{3} {blanc}{2}.', NULL, NULL, NULL, NULL),
(103, 'jp', '「あなたの{願い事}{4}はなに？」と{小さい}{1}{白い}{2}{ウサギ}{3}が聞きました。', NULL, NULL, NULL, NULL),
(104, 'vn', '{Con thỏ}{3} {trắng}{2} và {bé}{1} hỏi, "{Ước}{4} bạn là gì?"', NULL, NULL, NULL, NULL),
(105, 'en', '"What make is your car?" "It is a Ford."', NULL, NULL, NULL, NULL),
(106, 'fr', '"De quelle marque est ta voiture?" "C''est une Ford."', NULL, NULL, NULL, NULL),
(107, 'jp', '「あなたの自動車はどこの製品ですか」「フォード社のです」', NULL, NULL, NULL, NULL),
(108, 'en', '"Can I use your dictionary? "Yes, here you are."', NULL, NULL, NULL, NULL),
(109, 'fr', '"Puis-je utiliser votre dictionnaire ?" "Oui, je vous en prie."', NULL, NULL, NULL, NULL),
(110, 'jp', '「あなたの辞書を使ってもいいですか」「はい、どうぞ」', NULL, NULL, NULL, NULL),
(111, 'en', '"Where are your books?" "They are on the desk."', NULL, NULL, NULL, NULL),
(112, 'fr', '"Où sont vos livres?" "Ils sont sur le bureau."', NULL, NULL, NULL, NULL),
(113, 'jp', '「あなたの本はどこですか」「それらは机の上です」', NULL, NULL, NULL, NULL),
(114, 'en', '"What are you always thinking about?" asked the little white rabbit.', NULL, NULL, NULL, NULL),
(115, 'fr', '"A quoi es-tu toujours en train de penser?" demanda le petit lapin blanc.', NULL, NULL, NULL, NULL),
(116, 'jp', '「あなたは、いつも何を考えているの？」と小さい白いウサギが聞きました。', NULL, NULL, NULL, NULL),
(117, 'en', '"You are Israel''s teacher," said Jesus.', NULL, NULL, NULL, NULL),
(118, 'fr', '"Tu es l''enseignant d''Israël", dit Jésus.', NULL, NULL, NULL, NULL),
(119, 'jp', '「あなたはイスラエルの教師でしょう」とイエスはいった。', NULL, NULL, NULL, NULL),
(120, 'en', '"When do you watch TV?" "I watch TV after dinner."', NULL, NULL, NULL, NULL),
(121, 'fr', '"Quand regardes-tu la télévision?" "Après avoir dîné."', NULL, NULL, NULL, NULL),
(122, 'jp', '「あなたはいつテレビを見ますか」「夕食後です」', NULL, NULL, NULL, NULL),
(123, 'en', '"When do you swim?" "I swim in July."', NULL, NULL, NULL, NULL),
(124, 'fr', '"Quand vas-tu nager?" "En Juillet."', NULL, NULL, NULL, NULL),
(125, 'jp', '「あなたはいつ泳ぎますか」「７月です」', NULL, NULL, NULL, NULL),
(126, 'en', '"When do you get up?" "I get up at eight."', NULL, NULL, NULL, NULL),
(127, 'fr', '"A quelle heure vous levez-vous ?" "A 8 heures."', NULL, NULL, NULL, NULL),
(128, 'jp', '「あなたはいつ起きますか」「８時です」', NULL, NULL, NULL, NULL),
(129, 'de', '"Möchtest du ein T-Shirt?" "Ja, ich möchte eine rotes."', NULL, NULL, NULL, NULL),
(130, 'en', '"Do you want a shirt?" "Yes, I want a red one."', NULL, NULL, NULL, NULL),
(131, 'fr', '"Tu veux un T-shirt?" "Oui, j''en voudrais un rouge."', NULL, NULL, NULL, NULL),
(132, 'jp', '「あなたはシャツがほしいですか」「はい、赤いシャツがほしいです」', NULL, NULL, NULL, NULL),
(133, 'en', '"Have you finished it?" "On the contrary, I''ve just begun."', NULL, NULL, NULL, NULL),
(134, 'fr', '"As-tu terminé?" "Loin de là, je viens juste de commencer."', NULL, NULL, NULL, NULL),
(135, 'jp', '「あなたはそれを終えましたか」「それどころか、今始めたところです」', NULL, NULL, NULL, NULL),
(136, 'en', '"Where do you live?" "I live in Tokyo."', NULL, NULL, NULL, NULL),
(137, 'fr', '"Où habitez-vous ?" "J''habite à Tokyo."', NULL, NULL, NULL, NULL),
(138, 'jp', '「あなたはどこに住んでるのでしょうか」「東京です」', NULL, NULL, NULL, NULL),
(139, 'en', '"What do you want?" "I want a dog."', NULL, NULL, NULL, NULL),
(140, 'fr', '"Que voudriez-vous ?" "Je voudrais un chien."', NULL, NULL, NULL, NULL),
(141, 'jp', '「あなたは何が欲しいですか」「犬が欲しいです」', NULL, NULL, NULL, NULL),
(142, 'en', '"I believe you like your job" "On the contrary, I hate it".', NULL, NULL, NULL, NULL),
(143, 'fr', '"J''imagine que votre travail vous plaît." "Pas du tout, je le déteste."', NULL, NULL, NULL, NULL),
(144, 'jp', '「あなたは仕事が気に入っていると思います」「いやそれどころか、嫌いです」', NULL, NULL, NULL, NULL),
(145, 'en', '"Did you watch TV last week?" "No, I didn''t."', NULL, NULL, NULL, NULL),
(146, 'fr', '"Avez-vous regardé la télévision la semaine dernière ?" "Non."', NULL, NULL, NULL, NULL),
(147, 'jp', '「あなたは先週テレビを見ましたか」「いいえ」', NULL, NULL, NULL, NULL),
(148, 'en', '"Are you a teacher?" "So I am."', NULL, NULL, NULL, NULL),
(149, 'fr', '"Vous êtes enseignant?" "Oui, c''est cela."', NULL, NULL, NULL, NULL),
(150, 'jp', '「あなたは先生ですか」「その通りです」', NULL, NULL, NULL, NULL),
(151, 'en', 'She winked at me, as much as to say, I love you. [M]', NULL, NULL, NULL, NULL),
(152, 'fr', 'Elle me fit un clin d''oeil qui semblait signifier "je t''aime".', NULL, NULL, NULL, NULL),
(153, 'jp', '「あなたを愛しているのよ」と言わんばかりに彼女は僕にウィンクした。', NULL, NULL, NULL, NULL),
(154, 'en', '"Are you students?" "Yes, we are."', NULL, NULL, NULL, NULL),
(155, 'fr', '"Vous êtes des étudiants?" "Oui, c''est exact".', NULL, NULL, NULL, NULL),
(156, 'jp', '「あなた達は学生ですか」「はい、そうです」', NULL, NULL, NULL, NULL),
(157, 'en', '"I can''t think with that noise", she said as she stared at the typewriter. [F]', NULL, NULL, NULL, NULL),
(158, 'fr', '"Je ne peux pas réflechir avec ce bruit", dit-elle en regardant la machine à écrire.', NULL, NULL, NULL, NULL),
(159, 'jp', '「あの音で考え事ができないわ」と、彼女はタイプライターを見つめながら言った。', NULL, NULL, NULL, NULL),
(160, 'en', '"Thirty dollars is a lot for that small room," he thought.', NULL, NULL, NULL, NULL),
(161, 'fr', '"Trente dollars représentent beaucoup pour cette petite pièce", pensa-t-il.', NULL, NULL, NULL, NULL),
(162, 'jp', '「あの狭い部屋に３０ドルあんまりだ」と彼は思いました。', NULL, NULL, NULL, NULL),
(163, 'en', '"Who is that girl?" "She is Keiko."', NULL, NULL, NULL, NULL),
(164, 'fr', '"Qui est cette fille?" "C''est Keiko."', NULL, NULL, NULL, NULL),
(165, 'jp', '「あの少女は誰ですか」「ケイコです」', NULL, NULL, NULL, NULL),
(166, 'en', '"The castle is haunted," he said with a shiver.', NULL, NULL, NULL, NULL),
(167, 'fr', '"Ce château est hanté", dit-il en frémissant.', NULL, NULL, NULL, NULL),
(168, 'jp', '「あの城には幽霊がいる」と彼は震えながら言った。', NULL, NULL, NULL, NULL),
(169, 'en', '"Will they go on strike again?" "I''m afraid so."', NULL, NULL, NULL, NULL),
(170, 'fr', 'Ces gens-là vont-ils de nouveau entrer en grève?" "Il semble bien que oui."', NULL, NULL, NULL, NULL),
(171, 'jp', '「あの人たちは、またストをやるんだろうか」「どうもそうらしいね」', NULL, NULL, NULL, NULL),
(172, 'en', '"Who is that man?" "Mr Kato."', NULL, NULL, NULL, NULL),
(173, 'fr', '"Qui est cette personne?" "C''est M. Kato."', NULL, NULL, NULL, NULL),
(174, 'jp', '「あの人は誰なんでしょうか」「加藤さんですよ」', NULL, NULL, NULL, NULL),
(175, 'en', '"You had better not wear the red dress." "Why not?"', NULL, NULL, NULL, NULL),
(176, 'fr', '"Tu ferais mieux de ne pas mettre cette robe rouge." "Pourquoi cela?"', NULL, NULL, NULL, NULL),
(177, 'jp', '「あの赤い服を着るのはよしなさい」「なぜいけないの」', NULL, NULL, NULL, NULL),
(178, 'en', '"I want that book", he said to himself.', NULL, NULL, NULL, NULL),
(179, 'fr', 'Je veux ce livre, se dit-il.', NULL, NULL, NULL, NULL),
(180, 'jp', '「あの本がほしい」と彼は心の中で思いました。', NULL, NULL, NULL, NULL),
(181, 'en', 'They say love is blind.', NULL, NULL, NULL, NULL),
(182, 'fr', 'Comme on dit, l''amour est aveugle.', NULL, NULL, NULL, NULL),
(183, 'jp', '「あばたもえくぼ」って言うからね。', NULL, NULL, NULL, NULL),
(184, 'en', '"Thank you, I''d love to have another piece of cake," said the shy young man.', NULL, NULL, NULL, NULL),
(185, 'fr', '"Merci, je reprendrais bien du gâteau", dit le jeune homme timide.', NULL, NULL, NULL, NULL),
(186, 'jp', '「ありがとう。もう一つケーキをいただきます。」と内気な青年は言った。', NULL, NULL, NULL, NULL),
(187, 'en', '"Are the drinks free?" "Only for the ladies."', NULL, NULL, NULL, NULL),
(188, 'fr', '"Est-ce que les boissons sont gratuites?" "Seulement pour les filles."', NULL, NULL, NULL, NULL),
(189, 'jp', '「アルコール類はただですか」「ご婦人方に限ります」', NULL, NULL, NULL, NULL),
(190, 'en', '"Are those your books?" "No, they aren''t."', NULL, NULL, NULL, NULL),
(191, 'fr', '"Est-ce que ce sont vos livres?" "Non, ce ne sont pas mes livres."', NULL, NULL, NULL, NULL),
(192, 'jp', 'test 「あれらはあなたの本ですか」「いいえ、違います」', NULL, NULL, NULL, '2008-11-30 16:06:20'),
(193, 'en', '"Yes, I was," said the student.', NULL, NULL, NULL, NULL),
(194, 'fr', '"Si, j''y étais", répondit cet étudiant.', NULL, NULL, NULL, NULL),
(195, 'jp', '「いいえ、いました」とその学生は答えた。', NULL, NULL, NULL, NULL),
(196, 'fr', '"Non, c''est moi! C''est moi! C''est moi", s''écrièrent des centaines, des milliers, des millions, des milliards de chats, chacun se considérant comme le plus beau.', NULL, NULL, NULL, NULL),
(197, 'en', '"No, I''m not," replied the Englishman coldly.', NULL, NULL, NULL, NULL),
(198, 'fr', '"Non, ce n''est pas le cas", répliqua l''Anglais froidement.', NULL, NULL, NULL, NULL),
(199, 'jp', '「いいえ、違います」とイギリス人はさめた返事をしました。', NULL, NULL, NULL, NULL),
(200, 'en', '"No," repeated the Englishman.', NULL, NULL, NULL, NULL),
(201, 'fr', '"Non", répéta l''Anglais.', NULL, NULL, NULL, NULL),
(202, 'jp', '「いいえ」とイギリス人は繰り返しました。', NULL, NULL, NULL, NULL),
(203, 'en', '"Is there a book on the chair?" "Yes, there is."', NULL, NULL, NULL, NULL),
(204, 'fr', '"Y a-t-il un livre sur la chaise?" "Oui, il y en a un."', NULL, NULL, NULL, NULL),
(205, 'jp', '「イスの上に本がありますか」「はい、あります」', NULL, NULL, NULL, NULL),
(206, 'en', '"Won''t you come with us?" "I''d be glad to."', NULL, NULL, NULL, NULL),
(207, 'fr', '"Tu ne veux pas venir avec nous?" "Si, avec plaisir."', NULL, NULL, NULL, NULL),
(208, 'jp', '「いっしょにきませんか」「ええ、喜んで」', NULL, NULL, NULL, NULL),
(209, 'en', '"When did you buy it?" "Let''s see. I bought it last week."', NULL, NULL, NULL, NULL),
(210, 'fr', '"Quand l''as-tu acheté?" "Voyons voir... La semaine dernière."', NULL, NULL, NULL, NULL),
(211, 'jp', '「いつそれを買ったの」「ええと、先週でした」', NULL, NULL, NULL, NULL),
(212, 'en', '"Forever and always?" asked the little black rabbit.', NULL, NULL, NULL, NULL),
(213, 'fr', '"Pour toujours et toujours?" demanda le petit lapin noir.', NULL, NULL, NULL, NULL),
(214, 'jp', '「いつも、そしていつまでも？」と小さい黒いウサギはききました。', NULL, NULL, NULL, NULL),
(215, 'en', '"Forever and always!" replied the little white rabbit.', NULL, NULL, NULL, NULL),
(216, 'fr', '"Pour toujours et à  tout jamais!" répondit le petit lapin blanc.', NULL, NULL, NULL, NULL),
(217, 'jp', '「いつも、そしていつまでもよ！」と小さい白いウサギはいいました。', NULL, NULL, NULL, NULL),
(218, 'en', '"When will you be back?" "It all depends on the weather."', NULL, NULL, NULL, NULL),
(219, 'fr', '"Quand reviendras-tu?" "Ca dépendra du temps qu''il fera."', NULL, NULL, NULL, NULL),
(220, 'jp', '「いつ戻りますか」「天候次第です」', NULL, NULL, NULL, NULL),
(221, 'en', '"No," he said in a determined manner.', NULL, NULL, NULL, NULL),
(222, 'fr', '"Non", dit-il d''un air catégorique.', NULL, NULL, NULL, NULL),
(223, 'jp', '「いやだ」ときっぱりした態度でこたえた。', NULL, NULL, NULL, NULL),
(224, 'en', '"How long does it take to get to Vienna on foot?" he inquired.', NULL, NULL, NULL, NULL),
(225, 'fr', '"Combien de temps cela prend-il pour se rendre à Vienne à pied?", s''enquit-il.', NULL, NULL, NULL, NULL),
(226, 'jp', '「ウィーンまでは歩いてどのくらいかかりますか」、と彼はたずねた。', NULL, NULL, NULL, NULL),
(227, 'en', '"How long does it take to get to Vienna on foot?" "Sorry, I''m a stranger here."', NULL, NULL, NULL, NULL),
(228, 'fr', '"Combien de temps faut-il pour aller à Vienne à pied?" "Désolé, je ne suis pas d''ici."', NULL, NULL, NULL, NULL),
(229, 'jp', '「ウィーンまでは歩くとどのくらいかかりますか」「すみません。この辺に詳しくないんです。」', NULL, NULL, NULL, NULL),
(230, 'en', '"Would you like to work for me, Tony?" asked Mr Wood.', NULL, NULL, NULL, NULL),
(231, 'fr', '"Accepterais-tu de travailler pour moi Tony?" demanda M.Wood.', NULL, NULL, NULL, NULL),
(232, 'jp', '「うちで働いてみたいかね」とウッドさんが尋ねました。', NULL, NULL, NULL, NULL),
(233, 'en', '"Hee hee," his mother chuckled, shaking her head.', NULL, NULL, NULL, NULL),
(234, 'fr', '"Hi hi", sa mère gloussa, secouant la tête.', NULL, NULL, NULL, NULL),
(235, 'jp', '「うへへ」おっかさんは首を振りながらクスクス笑った。', NULL, NULL, NULL, NULL),
(236, 'en', '"Oh, yes," he answered.', NULL, NULL, NULL, NULL),
(237, 'fr', '"Oui, oui" répondit-il', NULL, NULL, NULL, NULL),
(238, 'jp', '「うんうん」彼は言った。', NULL, NULL, NULL, NULL),
(239, 'fr', '"Oui, un gentil petit chaton ébouriffé" dit la très vieille femme .', NULL, NULL, NULL, NULL),
(240, 'en', '"Yes, it is," said the Little House to herself. [F]', NULL, NULL, NULL, NULL),
(241, 'jp', '「ええ、ここがいいわ」と小さいおうちも言いました。', NULL, NULL, NULL, NULL),
(242, 'en', '"Ah, that''s true," Susan puts in, "I just wanted to call to ..."', NULL, NULL, NULL, NULL),
(243, 'jp', '「ええ、そうね」とスーザンが言葉をさしはさむ。「私が電話したのは・・・」', NULL, NULL, NULL, NULL),
(244, 'en', '"Yes, all right," says Mrs. Lee. [F]', NULL, NULL, NULL, NULL),
(245, 'fr', '«Oui, d''accord,» dit Mme Lee.', NULL, NULL, NULL, NULL),
(246, 'jp', '「ええ、わかったわ」とリー夫人が言う。', NULL, NULL, NULL, NULL),
(247, 'en', '"Let me see .... Do you have tomato juice?" says Hiroshi.', NULL, NULL, NULL, NULL),
(248, 'fr', '"laissez-moi regarder... Avez vous du jus de tomate?" dit Hiroshi', NULL, NULL, NULL, NULL),
(249, 'jp', '「えーっと・・・トマトジュースはありますか」と博が言います。', NULL, NULL, NULL, NULL),
(250, 'en', 'Hey, you shut up! You talk too much, the gangster said. [M]', NULL, NULL, NULL, NULL),
(251, 'fr', '"Hé toi, la ferme! Tu parles trop", dit le gangster.', NULL, NULL, NULL, NULL),
(252, 'jp', '「おい、だまれ。口数が多いぞ」とそのギャングの一味が言った。', NULL, NULL, NULL, NULL),
(253, 'en', '"Come, boy," she called, "come and play."', NULL, NULL, NULL, NULL),
(254, 'fr', '"Viens, petit," appela-t-elle,"viens jouer".', NULL, NULL, NULL, NULL),
(255, 'jp', '「おいでおいで」彼女は叫びました。「こっちであそぼ」', NULL, NULL, NULL, NULL),
(256, 'en', '"Are you from Australia?" asked the Filipino.', NULL, NULL, NULL, NULL),
(257, 'fr', '"Australie ?" demanda le philippin', NULL, NULL, NULL, NULL),
(258, 'jp', '「オーストラリアからですか」とフィリピン人はたずねました。', NULL, NULL, NULL, NULL),
(259, 'en', 'She said, "It''s not funny! How would you like it if someone did that to you - what would you do?" [F]', NULL, NULL, NULL, NULL),
(260, 'fr', 'Elle dit "Ce n''est pas drôle! Qu''est-ce que tu dirais si quelqu''un te faisait ça, qu''est-ce que tu aurais fait ?"', NULL, NULL, NULL, NULL),
(261, 'jp', '「おかしいことなんかじゃないわ。誰かがあなたにそんなことをしたら、あなたどう思う。どうする？」と彼女は言った。', NULL, NULL, NULL, NULL),
(262, 'en', 'Grandpa bought it for me!', NULL, NULL, NULL, NULL),
(263, 'fr', '"Grand-père me l''a acheté!"\n', NULL, NULL, NULL, NULL),
(264, 'jp', 'おじいちゃんに買ってもらったんだー！', NULL, NULL, NULL, NULL),
(265, 'en', '"That''s very nice of you," Willie answered.', NULL, NULL, NULL, NULL),
(266, 'fr', '"C''est très gentil de ta part" répondit Willie.', NULL, NULL, NULL, NULL),
(267, 'jp', '「おっさん、やさしいなー」ウィリーは言った。', NULL, NULL, NULL, NULL),
(268, 'en', '"Good morning", said Tom with a smile.', NULL, NULL, NULL, NULL),
(269, 'fr', '"Bonjour", dit Tom en souriant.', NULL, NULL, NULL, NULL),
(270, 'jp', '「おはよう」とトムは微笑みながら言った。', NULL, NULL, NULL, NULL),
(271, 'en', '"Your army is impotent against mine!!" he laughed.', NULL, NULL, NULL, NULL),
(272, 'fr', '"Ton armée est impuissante face à la mienne" dit-il en riant.', NULL, NULL, NULL, NULL),
(273, 'jp', '「おまえの軍など我が軍に対しては無力だよ」と、彼は笑っていった。', NULL, NULL, NULL, NULL),
(274, 'fr', '"Mon dieu!" s''écria-t-elle, "Que fais tu? J''ai demandé un chaton. Et qu''est-ce que je vois?-', NULL, NULL, NULL, NULL),
(275, 'en', '"Have you finished?" "On the contrary, I have not even begun yet."', NULL, NULL, NULL, NULL),
(276, 'fr', '"As-tu finis?" "Au contraire, je n''ai pas encore commencé"', NULL, NULL, NULL, NULL),
(277, 'jp', '「おわったの」「それどころかまだ始めていないよ」', NULL, NULL, NULL, NULL),
(278, 'en', '"Don''t say such rubbish!" said the farmer.', NULL, NULL, NULL, NULL),
(279, 'fr', '"Ne dis pas de stupidités!" dit le fermier.', NULL, NULL, NULL, NULL),
(280, 'jp', '「おんどりゃー馬鹿言ってんじゃねー」農家は言った。', NULL, NULL, NULL, NULL),
(281, 'en', 'When I told her I''d never seen such a homely girl, she accused me of sexual harassment.', NULL, NULL, NULL, NULL),
(282, 'jp', '「お前みてえなおかちめんこは初めて見た」と言ったら、「セクハラだ」と言われた。', NULL, NULL, NULL, NULL),
(283, 'en', '"May I use the phone?" "Please feel free."', NULL, NULL, NULL, NULL),
(284, 'fr', '"Puis-je utiliser le téléphone?" "Je vous en prie"', NULL, NULL, NULL, NULL),
(285, 'jp', '「お電話をお借りしてもいいですか」「どうぞ、どうぞ。」', NULL, NULL, NULL, NULL),
(286, 'en', '"Let me ask you something, Dad," she began, in a tone of patiently controlled exasperation that every experienced parent is familiar with.', NULL, NULL, NULL, NULL),
(287, 'fr', '"Laisse-moi te demander quelque chose, papa," commença-t-elle, sur un ton d''exaspération patiemment controlée que tout parent expérimenté connaît.', NULL, NULL, NULL, NULL),
(288, 'jp', '「お父さん、質問してもいい？」と彼女は経験を積んだ親なら誰でもおなじみの、我慢強く苛立ちを抑えた調子で口火を切った。', NULL, NULL, NULL, NULL),
(289, 'en', '"The key," he added, "is in the lock".', NULL, NULL, NULL, NULL),
(290, 'fr', '"La clé est dans la serrure", ajouta-t-il.', NULL, NULL, NULL, NULL),
(291, 'jp', '「かぎは錠前に差し込んである」と、彼は付け加えた。', NULL, NULL, NULL, NULL),
(292, 'en', 'Have you ever read Gulliver''s Travels?', NULL, NULL, NULL, NULL),
(293, 'fr', 'As-tu lu "Les voyages de Gulliver"?', NULL, NULL, NULL, NULL),
(294, 'jp', '「ガリバー旅行記」を読んだことがありますか。', NULL, NULL, NULL, NULL),
(295, 'en', '"Can you play the guitar?" "Yes, I can."', NULL, NULL, NULL, NULL),
(296, 'fr', '"Sais-tu jouer de la guitare?" "Oui."', NULL, NULL, NULL, NULL),
(297, 'jp', '「ギターがひけますか」「はい、ひけます」', NULL, NULL, NULL, NULL),
(298, 'en', 'You didn''t do a very good job, I said. [M]', NULL, NULL, NULL, NULL),
(299, 'fr', '"Vous n''avez pas fait du très bon travail", dis-je.', NULL, NULL, NULL, NULL),
(300, 'jp', '「きみらはあんまりいい仕事をしていないね」私は言った。', NULL, NULL, NULL, NULL),
(301, 'en', '"How about playing catch?" "Sure, why not?"', NULL, NULL, NULL, NULL),
(302, 'fr', 'Et si onfaisait du catch? "oui, pourquoi pas?".', NULL, NULL, NULL, NULL),
(303, 'jp', '「キャッチボールしようか」「よし、是非やろう」', NULL, NULL, NULL, NULL),
(304, 'en', '"Do you like cake?" "Yes, I do."', NULL, NULL, NULL, NULL),
(305, 'fr', '" aimez vous les gateaux"     "oui,j''aime ca"', NULL, NULL, NULL, NULL),
(306, 'jp', '「ケーキはお好きですか」「はい、好きです」', NULL, NULL, NULL, NULL),
(307, 'en', '"The Gettysburg Address" is a concise speech.', NULL, NULL, NULL, NULL),
(308, 'jp', '「ゲティスバーグ演説」は簡潔スピーチです。', NULL, NULL, NULL, NULL),
(309, 'en', '"Is Ken busy?" "Yes, he is."', NULL, NULL, NULL, NULL),
(310, 'fr', '"Est-ce que Ken est occupé?" "Oui."\n', NULL, NULL, NULL, NULL),
(311, 'jp', '「ケンは忙しいですか」「はい」', NULL, NULL, NULL, NULL),
(312, 'en', 'I am adding a sentence as a test.', 4, NULL, '2008-11-30 16:10:33', '2008-11-30 16:10:33');

-- --------------------------------------------------------

--
-- Table structure for table `sentences_translations`
--

CREATE TABLE IF NOT EXISTS `sentences_translations` (
  `sentence_id` int(11) NOT NULL,
  `translation_id` int(11) NOT NULL,
  `distance` smallint(2) NOT NULL default '1',
  UNIQUE KEY `sentence_id` (`sentence_id`,`translation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sentences_translations`
--

INSERT INTO `sentences_translations` (`sentence_id`, `translation_id`, `distance`) VALUES
(1, 2, 1),
(2, 1, 1),
(3, 4, 1),
(4, 3, 1),
(10, 22, 1),
(22, 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sentence_comments`
--

CREATE TABLE IF NOT EXISTS `sentence_comments` (
  `id` int(11) NOT NULL auto_increment,
  `sentence_id` int(11) NOT NULL,
  `lang` varchar(2) collate utf8_unicode_ci NOT NULL,
  `text` text collate utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `last_edit_datetime` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `sentence_comments`
--

INSERT INTO `sentence_comments` (`id`, `sentence_id`, `lang`, `text`, `user_id`, `datetime`, `last_edit_datetime`) VALUES
(1, 189, 'fr', 'J''ai envie de manger une tarte au pomme.', 1, '2008-11-29 16:22:56', NULL),
(2, 16, 'es', 'No soy occupada pero no quiero hablar por el moment.', 1, '2008-11-29 16:24:01', NULL),
(3, 308, 'en', 'I like this sentence.', 1, '2008-11-29 16:25:15', NULL),
(4, 233, 'en', 'And what if I don''t redirect?', 1, '2008-11-29 16:25:49', NULL),
(5, 146, 'en', 'There is little we can do about it...', 1, '2008-11-29 16:27:04', NULL),
(6, 146, 'en', 'but what about now?', 1, '2008-11-29 16:27:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sentence_logs`
--

CREATE TABLE IF NOT EXISTS `sentence_logs` (
  `id` int(11) NOT NULL auto_increment,
  `sentence_id` int(11) NOT NULL,
  `sentence_lang` varchar(2) default NULL,
  `sentence_text` varchar(500) character set utf8 collate utf8_unicode_ci NOT NULL,
  `action` enum('insert','update','delete') NOT NULL,
  `user_id` int(11) default NULL,
  `datetime` datetime NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `sentence_logs`
--

INSERT INTO `sentence_logs` (`id`, `sentence_id`, `sentence_lang`, `sentence_text`, `action`, `user_id`, `datetime`, `ip`) VALUES
(1, 6676, 'fr', 'J''ai bu une tasse de cafe au bistrot.', 'update', 1, '2008-12-23 12:52:09', ''),
(2, 6676, 'fr', 'J''ai bu une tasse de café au bistrot.', 'update', 1, '2008-12-23 12:52:18', ''),
(3, 10797, 'fr', 'Ma fille a atteint l''âge de penser au mariage.', 'update', 1, '2008-12-23 12:53:00', ''),
(4, 329428, 'en', 'I wish it was faster.', 'insert', 11, '2009-01-08 02:51:59', '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `suggested_modifications`
--

CREATE TABLE IF NOT EXISTS `suggested_modifications` (
  `id` int(11) NOT NULL auto_increment,
  `sentence_id` int(11) NOT NULL,
  `sentence_lang` varchar(2) default NULL,
  `correction_text` varchar(500) character set utf8 collate utf8_unicode_ci NOT NULL,
  `submit_user_id` int(11) default NULL,
  `submit_datetime` datetime NOT NULL,
  `apply_user_id` int(11) NOT NULL,
  `apply_datetime` datetime NOT NULL,
  `was_applied` tinyint(1) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `suggested_modifications`
--

INSERT INTO `suggested_modifications` (`id`, `sentence_id`, `sentence_lang`, `correction_text`, `submit_user_id`, `submit_datetime`, `apply_user_id`, `apply_datetime`, `was_applied`, `ip`) VALUES
(1, 329428, 'en', 'I wish it was faster...', 11, '2009-01-08 02:53:40', 0, '0000-00-00 00:00:00', 0, ''),
(2, 254170, 'en', 'The car overtook me...', NULL, '2009-01-08 02:58:52', 0, '0000-00-00 00:00:00', 0, '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `translation_logs`
--

CREATE TABLE IF NOT EXISTS `translation_logs` (
  `id` int(11) NOT NULL auto_increment,
  `sentence_id` int(11) NOT NULL,
  `sentence_lang` varchar(2) default NULL,
  `translation_id` int(11) NOT NULL,
  `translation_lang` varchar(2) default NULL,
  `translation_text` varchar(500) character set utf8 collate utf8_unicode_ci NOT NULL,
  `action` enum('insert','delete') NOT NULL,
  `user_id` int(11) default NULL,
  `datetime` datetime NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `translation_logs`
--

INSERT INTO `translation_logs` (`id`, `sentence_id`, `sentence_lang`, `translation_id`, `translation_lang`, `translation_text`, `action`, `user_id`, `datetime`, `ip`) VALUES
(1, 275938, 'en', 329426, 'fr', 'Veuillez envoyer quelqu''un à ma chambre.', 'insert', NULL, '2009-01-07 22:48:55', ''),
(2, 246548, 'en', 329427, 'fr', 'Je le dois à mon oncle d''avoir réussi dans mon entreprise.', 'insert', 11, '2009-01-08 02:17:30', ''),
(3, 329428, 'en', 329429, 'fr', 'Si seulement c''était plus rapide.', 'insert', 11, '2009-01-08 02:53:11', '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(20) collate utf8_unicode_ci NOT NULL,
  `password` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `email` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `lang` varchar(2) collate utf8_unicode_ci NOT NULL default 'en',
  `since` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_time_active` int(11) NOT NULL default '0',
  `level` tinyint(4) NOT NULL default '1',
  `group_id` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `login` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `lang`, `since`, `last_time_active`, `level`, `group_id`) VALUES
(1, 'trang', '14390ccca0d51157846829eb5b9831f9', 'tranglich@gmail.com', 'fr', '2008-11-22 17:46:50', 0, 1, 1),
(2, 'trang1', '14390ccca0d51157846829eb5b9831f9', 'tranglich@hotmail.com', 'en', '2008-11-22 17:47:08', 0, 1, 2),
(3, 'trang2', '14390ccca0d51157846829eb5b9831f9', 'hognocph@etu.utc.fr', 'fr', '2008-11-22 17:47:29', 0, 1, 3),
(4, 'trang3', '14390ccca0d51157846829eb5b9831f9', 'trang@babbel.com', 'fr', '2008-11-22 17:47:50', 0, 1, 4);


-- --------------------------------------------------------

--
-- Structure for view `contributions`
--
DROP VIEW IF EXISTS `contributions`;

CREATE VIEW `contributions` AS
SELECT `sentence_id`
 , `sentence_lang`
 , `translation_id`
 , `translation_lang`
 , `translation_text` as `text`
 , `action`
 , `user_id`
 , `datetime` FROM `translation_logs` 
UNION 
SELECT `sentence_id`
 , `sentence_lang`
 , ''
 , ''
 , `sentence_text`
 , `action`
 , `user_id`
 , `datetime` FROM `sentence_logs`
UNION
SELECT `sentence_id`
 , `sentence_lang`
 , ''
 , ''
 , `correction_text`
 , 'suggest'
 , `submit_user_id`
 , `submit_datetime` FROM `suggested_modifications`
ORDER BY `datetime` DESC;


-- --------------------------------------------------------

--
-- Structure for view `users_statistics`
--
DROP VIEW IF EXISTS `users_statistics`;

CREATE VIEW `users_statistics` AS
SELECT user_id, COUNT(*) as quantity, action, translation_id != '' as is_translation
FROM contributions
GROUP BY user_id, action, translation_id = ''

