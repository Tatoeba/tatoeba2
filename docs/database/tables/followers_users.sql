--
-- Table structure for table `followers_users`
--
-- This table indicates which user is following which user.
--
-- follower_id Id of the follower.
-- user_id     If of the user who is followed.
--
-- NOTE: This is a feature that is not integrated yet. We still need to decide on
-- how that system is going to work.
--

CREATE TABLE IF NOT EXISTS `followers_users` (
  `follower_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `follower_id` (`follower_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;