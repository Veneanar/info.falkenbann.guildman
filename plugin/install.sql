DROP TABLE IF EXISTS wcf1_gman_group;
CREATE TABLE wcf1_gman_group (
  groupID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  groupName VARCHAR(50)  NOT NULL,
  groupTeaser VARCHAR(250) NOT NULL DEFAULT '',
  wcfGroupID INT(10) NOT NULL,
  showCalender TINYINT(1) NOT NULL DEFAULT 0,
  calendarTitle VARCHAR(50) NULL DEFAULT '',
  calendartext TEXT NULL,
  fetchCalendar TINYINT(1) NOT NULL DEFAULT 0,
  calendarCategoryID INT(10) NULL,
  calendarQuery VARCHAR(25) NOT NULL DEFAULT '',
  gameRank TINYINT(1) NOT NULL DEFAULT 0,
  showRoaster TINYINT(1) NOT NULL DEFAULT 1,
  articleID INT(10) NULL,
  threadID INT(10) NULL,
  boardID INT(10) NULL,
  imageID INT(10) NULL,
  isRaidgruop TINYINT(0) NOT NULL DEFAULT 0,
  fetchWCL TINYINT(1) NOT NULL DEFAULT 0,
  wclQuery VARCHAR(100) NOT NULL DEFAULT '',
  orderNo SMALLINT(4) NOT NULL,
  lastUpdate INT(10) NOT NULL,
  KEY (wcfGroupID, articleID, threadID, boardID, imageID)
) ENGINE=InnoDB ;
DROP TABLE IF EXISTS wcf1_gman_wow_realm;
CREATE TABLE wcf1_gman_wow_realm (
  realmID INT(4) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(30)  NOT NULL,
  name VARCHAR(30) NOT NULL,
  type VARCHAR(20)  NOT NULL DEFAULT '',
  population VARCHAR(10)  NOT NULL DEFAULT '',
  queue TINYINT(1) NULL DEFAULT 0,
  status TINYINT(2) NOT NULL DEFAULT 0,
  battlegroup VARCHAR(50)  NOT NULL DEFAULT '',
  timezone VARCHAR(25)  NOT NULL DEFAULT '',
  connected_realms TEXT NULL,
  locale VARCHAR(6)  NOT NULL DEFAULT '',
  lastUpdate INT(10) NOT NULL,
  KEY (name, slug),
  UNIQUE KEY (slug)
) ENGINE=InnoDB ;
DROP TABLE IF EXISTS wcf1_gman_character;
CREATE TABLE wcf1_gman_character (
  characterID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  charname VARBINARY(70) NOT NULL,
  userID INT(10) NULL,
  isMain TINYINT(1) NOT NULL DEFAULT 0,
  inGuild  TINYINT(1) NOT NULL DEFAULT 0,
  realmSlug VARCHAR(30) NOT NULL,
  c_class TINYINT(2) NOT NULL DEFAULT 0,
  c_race TINYINT(2) NOT NULL DEFAULT 0,
  c_level TINYINT(2) NOT NULL DEFAULT 0,
  c_acms INT(11) NOT NULL DEFAULT 0,
  accountID VARCHAR(64) NOT NULL DEFAULT '',
  bnetData TEXT NULL,
  primaryGroup INT(10) NULL,
  bnetUpdate INT(10) NOT NULL,
  firstSeen INT(10) NOT NULL,
  guildRank TINYINT(2) NULL DEFAULT 11, 
  bnetError INT(10) NULL DEFAULT 0, 
  isDisabled TINYINT(1) NULL DEFAULT 0,
  tempUserID INT(10) NULL,
  internalState TINYINT(1) NULL DEFAULT 1,
  KEY (userID, charname, realmslug, primaryGroup, accountID),
  UNIQUE KEY charUQ (charname, realmSlug)
) ENGINE=InnoDB ;
DROP TABLE IF EXISTS wcf1_gman_character_mounts;
CREATE TABLE wcf1_gman_character_mounts (
  characterID INT(10) NOT NULL PRIMARY KEY,
  bnetData LONGTEXT  NULL,
  bnetUpdate INT(10) NOT NULL
) ENGINE=InnoDB;
DROP TABLE IF EXISTS wcf1_gman_character_pets;
CREATE TABLE wcf1_gman_character_pets (
  characterID INT(10) NOT NULL PRIMARY KEY,
  bnetData LONGTEXT  NULL,
  bnetUpdate INT(10) NOT NULL
) ENGINE=InnoDB;
DROP TABLE IF EXISTS wcf1_gman_character_statistics;
CREATE TABLE wcf1_gman_character_statistics (
  characterID INT(10) NOT NULL PRIMARY KEY,
  bnetData LONGTEXT  NULL,
  bnetUpdate INT(10) NOT NULL
) ENGINE=InnoDB;
DROP TABLE IF EXISTS wcf1_gman_char_to_group;
CREATE TABLE wcf1_gman_char_to_group (
  characterID INT(10) NOT NULL PRIMARY KEY,
  groupID INT(5) NOT NULL,
  KEY (characterID, groupID)
) ENGINE=InnoDB ;
DROP TABLE IF EXISTS wcf1_gman_guild;
CREATE TABLE wcf1_gman_guild (
 guildID SMALLINT(4) NOT NULL PRIMARY KEY,
 articleID INT(10) NULL,
 pageID INT(10) NULL,
 leaderID INT(10) NULL DEFAULT 0,
 birthday INT(10) NOT NULL,
 logoID INT(10) NULL,
 bnetUpdate INT(10) NOT NULL,
 bnetData TEXT NULL,
 KEY(leaderID, articleID, pageID)
) ENGINE=InnoDB ;
DROP TABLE IF EXISTS wcf1_gman_application;
CREATE TABLE wcf1_gman_application (
  appID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  threadID INT(10) NULL ,
  pollID INT(10) DEFAULT NULL ,
  characterID INT(10) NULL,
  name VARCHAR(50) NOT NULL,
  autoClose TINYINT(10) NOT NULL DEFAULT 0,
  pollEnd INT(10) NOT NULL,
  assignedOfficerID INT(10) NULL,
  interviewDate INT(10) NOT NULL ,
  appState TINYINT(1) NOT NULL DEFAULT 0,
  openDate INT(10) NOT NULL,
  appTypeID INT(10) NOT NULL,
  KEY (assignedOfficerID, threadID, pollID)
) ENGINE=InnoDB ;
DROP TABLE IF EXISTS wcf1_gman_pointtype;
CREATE TABLE wcf1_gman_pointtype (
  typeID SMALLINT(4) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  groupID INT(10) NOT NULL,
  content VARCHAR(250) NOT NULL DEFAULT ''
) ENGINE=InnoDB ;
DROP TABLE IF EXISTS wcf1_gman_pointtrans;
CREATE TABLE wcf1_gman_pointtrans (
  transID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  characterID INT(10) NOT NULL,
  groupID INT(10) NULL,
  eventID INT(10) NULL,
  amount SMALLINT(4) NOT NULL,
  typeID SMALLINT(4) NULL,
  itemID INT(10) NOT NULL DEFAULT 0,
  comment VARCHAR(250)  NOT NULL DEFAULT '',
  issuerID INT(10) NULL,
  transDate INT(10) NOT NULL,
  autoDelete TINYINT(1) NOT NULL DEFAULT 0,
  deleteDate INT(10) NULL,
  KEY (deleteDate, autoDelete, characterID, groupID, typeID)
) ENGINE=InnoDB ;

DROP TABLE IF EXISTS wcf1_gman_wow_classes;
CREATE TABLE wcf1_gman_wow_classes (
  wclassID INT(10) NOT NULL PRIMARY KEY,
  mask INT(10) NOT NULL,
  powerType VARCHAR(15)  NOT NULL,
  name VARCHAR(20)  NOT NULL,
  color VARCHAR(12)  NOT NULL 
) ENGINE=InnoDB ;
DROP TABLE IF EXISTS wcf1_gman_wow_races;
CREATE TABLE wcf1_gman_wow_races (
  wraceID INT(10) NOT NULL PRIMARY KEY,
  mask INT(10) NOT NULL,
  side VARCHAR(20)  NOT NULL,
  sideID TINYINT(4) NOT NULL,
  name VARCHAR(50)  NOT NULL
) ENGINE=InnoDB ;
DROP TABLE IF EXISTS wcf1_gman_wow_acms;
CREATE TABLE wcf1_gman_wow_acms (
  acmID INT(10) NOT NULL PRIMARY KEY,
  bnetData TEXT NULL ,
  bnetUpdate INT(10) NOT NULL 
) ENGINE=InnoDB ;
DROP TABLE IF EXISTS wcf1_gman_wow_gacms;
  CREATE TABLE wcf1_gman_wow_gacms (
  gacmID INT(10) NOT NULL PRIMARY KEY,
  bnetData TEXT NULL,
  bnetUpdate INT(10) NOT NULL
) ENGINE=InnoDB ;
DROP TABLE IF EXISTS wcf1_gman_wow_items;
CREATE TABLE wcf1_gman_wow_items (
  itemID INT(10) NOT NULL PRIMARY KEY,
  bnetData TEXT  NULL,
  bnetUpdate INT(10) NOT NULL
) ENGINE=InnoDB ;
DROP TABLE IF EXISTS wcf1_gman_wow_itemclass;
CREATE TABLE wcf1_gman_wow_itemclass (
  itemclassID INT(10) NOT NULL PRIMARY KEY,
  bnetData TEXT NULL ,
  bnetUpdate INT(10) NOT NULL 
) ENGINE=InnoDB ;
DROP TABLE IF EXISTS wcf1_gman_guild_acm;
CREATE TABLE wcf1_gman_guild_acm (
  guildAcmID int(5) NOT NULL PRIMARY KEY,
  gacmID int(5) NOT NULL,
  gacmTime int(10) NOT NULL,
  articleID int(10) NULL
) ENGINE=InnoDB ;
DROP TABLE IF EXISTS wcf1_gman_character_equip;
CREATE TABLE wcf1_gman_character_equip (
  characterID INT(10) NOT NULL PRIMARY KEY,
  averageItemLevel smallint(5) NULL,
  averageItemLevelEquipped smallint(5) NULL,
  head TEXT NULL,
  neck TEXT NULL,
  shoulder TEXT NULL,
  back TEXT NULL,
  chest TEXT NULL,
  shirt TEXT NULL,
  wrist TEXT NULL,
  hands TEXT NULL,
  waist TEXT NULL,
  legs TEXT NULL,
  feet TEXT  NULL,
  finger1 TEXT NULL,
  finger2 TEXT NULL,
  trinket1 TEXT NULL,
  trinket2 TEXT NULL,
  mainHand TEXT NULL,
  offHand TEXT NULL,
  equipTime INT(10) NOT NULL,
  KEY (characterID, equipTime),
  UNIQUE KEY charEquip (characterID, equipTime)
) ENGINE=InnoDB ;
DROP TABLE IF EXISTS wcf1_gman_feedlist;
CREATE TABLE wcf1_gman_feedlist (
  characterID INT(10) NOT NULL PRIMARY KEY,
  type TINYINT(1) NOT NULL DEFAULT 0,
  itemID INT(10) NULL,
  acmID INT(10) NULL,
  quantity INT(10)  NOT NULL DEFAULT 0,
  bonusLists TEXT NULL,
  context TEXT NULL,
  criteria TEXT NULL,
  feedTime INT(10) NOT NULL,
  inGuild TINYINT(1) NOT NULL DEFAULT 0,
  KEY (characterID, type, feedTime),
  UNIQUE KEY charFeed (characterID, type, feedTime)
) ENGINE=InnoDB ;
DROP TABLE IF EXISTS wcf1_gman_acmlist;
CREATE TABLE wcf1_gman_acmlist (
  userID INT(10) NOT NULL PRIMARY KEY,
  acmID INT(10) NULL,
  feedTime INT(10) NOT NULL
  KEY (userID, acmID, feedTime),
  UNIQUE KEY charACM (userID, acmID, feedTime)
) ENGINE=InnoDB;

ALTER TABLE wcf1_gman_group ADD FOREIGN KEY (articleID) REFERENCES wcf1_article (articleID) ON DELETE SET NULL;
ALTER TABLE wcf1_gman_group ADD FOREIGN KEY (threadID) REFERENCES wbb1_thread (threadID) ON DELETE SET NULL;
ALTER TABLE wcf1_gman_group ADD FOREIGN KEY (boardID) REFERENCES wbb1_board (boardID) ON DELETE SET NULL;
ALTER TABLE wcf1_gman_group ADD FOREIGN KEY (imageID) REFERENCES wcf1_media (mediaID) ON DELETE SET NULL;
ALTER TABLE wcf1_gman_character ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE wcf1_gman_character ADD FOREIGN KEY (primaryGroup) REFERENCES wcf1_gman_group (groupID) ON DELETE SET NULL;
ALTER TABLE wcf1_gman_char_to_group ADD FOREIGN KEY (characterID) REFERENCES wcf1_gman_character (characterID) ON DELETE CASCADE;
ALTER TABLE wcf1_gman_char_to_group ADD FOREIGN KEY (groupID) REFERENCES wcf1_gman_group (groupID) ON DELETE CASCADE;
ALTER TABLE wcf1_gman_application ADD FOREIGN KEY (assignedOfficerID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE wcf1_gman_application ADD FOREIGN KEY (threadID) REFERENCES wbb1_thread (threadID) ON DELETE SET NULL;
ALTER TABLE wcf1_gman_guild ADD FOREIGN KEY (articleID) REFERENCES wcf1_article (articleID) ON DELETE SET NULL;
ALTER TABLE wcf1_gman_guild ADD FOREIGN KEY (pageID) REFERENCES wcf1_page (pageID) ON DELETE SET NULL;
ALTER TABLE wcf1_gman_pointtrans ADD FOREIGN KEY (issuerID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE wcf1_gman_pointtrans ADD FOREIGN KEY (eventID) REFERENCES calendar1_event (eventID) ON DELETE SET NULL;
ALTER TABLE wcf1_gman_pointtrans ADD FOREIGN KEY (characterID) REFERENCES wcf1_gman_character (characterID) ON DELETE CASCADE;
ALTER TABLE wcf1_gman_pointtrans ADD FOREIGN KEY (typeID) REFERENCES wcf1_gman_pointtype (typeID) ON DELETE RESTRICT;
ALTER TABLE wcf1_gman_character_equip ADD FOREIGN KEY (characterID) REFERENCES wcf1_gman_character (characterID) ON DELETE CASCADE;
ALTER TABLE wcf1_gman_feedlist ADD FOREIGN KEY (characterID) REFERENCES wcf1_gman_character (characterID) ON DELETE CASCADE;
ALTER TABLE wcf1_gman_acmlist ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;
ALTER TABLE wcf1_gman_character_statistics ADD FOREIGN KEY (characterID) REFERENCES wcf1_gman_character (characterID) ON DELETE CASCADE;
ALTER TABLE wcf1_gman_character_pets ADD FOREIGN KEY (characterID) REFERENCES wcf1_gman_character (characterID) ON DELETE CASCADE;
ALTER TABLE wcf1_gman_character_mounts ADD FOREIGN KEY (characterID) REFERENCES wcf1_gman_character (characterID) ON DELETE CASCADE;

INSERT INTO wcf1_gman_wow_classes (wclassID, mask, powerType, name, color) VALUES ('1', '1', 'rage', 'Krieger', '#C79C6E'), ('2', '2', 'mana', 'Paladin', '#F58CBA'), ('3', '4', 'focus', 'Jäger', '#ABD473'), ('4', '8', 'energy', 'Schurke', '#FFF569'), ('5', '16', 'mana', 'Priester', '#FFFFFF'), ('6', '32', 'runic-power', 'Todesritter', '#C41F3B'), ('7', '64', 'mana', 'Schamane', '#0070DE'), ('8', '128', 'mana', 'Magier', '#69CCF0'), ('9', '256', 'mana', 'Hexenmeister', '#9482C9'), ('10', '512', 'energy', 'Mönch', '#00FF96'), ('11', '1024', 'mana', 'Druide', '#FF7D0A'), ('12', '2048', 'fury', 'Dämonenjäger', '#A330C9');
INSERT INTO wcf1_gman_wow_races (wraceID, mask, side, sideID, name) VALUES ('1', '1', 'alliance', '0', 'Mensch'), ('2', '2', 'horde', '1', 'Orc'), ('3', '4', 'alliance', '0', 'Zwerg'), ('4', '8', 'alliance', '0', 'Nachtelf'), ('5', '16', 'horde', '1', 'Untoter'), ('6', '32', 'horde', '1', 'Tauren'), ('7', '64', 'alliance', '0', 'Gnom'), ('8', '128', 'horde', '1', 'Troll'), ('9', '256', 'horde', '1', 'Goblin'), ('10', '512', 'horde', '1', 'Blutelf'), ('11', '1024', 'alliance', '0', 'Draenei'), ('22', '2097152', 'alliance', '0', 'Worgen'), ('24', '8388608', 'neutral', '2', 'Pandaren'), ('25', '16777216', 'alliance', '0', 'Pandaren'), ('26', '33554432', 'horde', '1', 'Pandaren');
