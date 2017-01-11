CREATE DATABASE IF NOT EXISTS twitter;
USE twitter;
# -----------------------------------------------------------------------------
#       TABLE : USER
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS USER
 (
   ID_USER INTEGER(11) NOT NULL AUTO_INCREMENT ,
   USERNAME CHAR(60) NOT NULL  ,
   NAME CHAR(60) NULL  ,
   EMAIL CHAR(60) NOT NULL  ,
   PASSWORD CHAR(60) NULL  ,
   DATESIGNUP DATETIME NULL  ,
   AVATARPATH TEXT NULL
   , PRIMARY KEY (ID_USER) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : TWEET
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS TWEET
 (
   ID_TWEET INTEGER(11) NOT NULL AUTO_INCREMENT ,
   ID_TWEET_ANSWERTO INTEGER(11) NULL  ,
   ID_USER INTEGER(11) NOT NULL  ,
   TEXT CHAR(140) NULL  ,
   DATEPUBLISHED DATETIME NULL  
   , PRIMARY KEY (ID_TWEET) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE TWEET
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_TWEET_TWEET
     ON TWEET (ID_TWEET_ANSWERTO ASC);

CREATE  INDEX I_FK_TWEET_USER
     ON TWEET (ID_USER ASC);

# -----------------------------------------------------------------------------
#       TABLE : TWEETLIKE
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS TWEETLIKE
 (
   ID_USER INTEGER(11) NOT NULL  ,
   ID_TWEET INTEGER(11) NOT NULL  ,
   NOTIFICATIONREADED BOOL NULL  ,
   DATELIKED DATE NULL  
   , PRIMARY KEY (ID_USER,ID_TWEET) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE TWEETLIKE
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_TWEETLIKE_USER
     ON TWEETLIKE (ID_USER ASC);

CREATE  INDEX I_FK_TWEETLIKE_TWEET
     ON TWEETLIKE (ID_TWEET ASC);

# -----------------------------------------------------------------------------
#       TABLE : CONTAINHASHTAG
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS CONTAINHASHTAG
 (
   ID_TWEET INTEGER(11) NOT NULL  ,
   HASHTAG_NAME CHAR(60) NOT NULL
   , PRIMARY KEY (ID_TWEET,HASHTAG_NAME) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE CONTAINHASHTAG
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_CONTAINHASHTAG_TWEET
     ON CONTAINHASHTAG (ID_TWEET ASC);

CREATE  INDEX I_FK_CONTAINHASHTAG_HASHTAG
     ON CONTAINHASHTAG (HASHTAG_NAME ASC);

# -----------------------------------------------------------------------------
#       TABLE : MENTIONED
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS MENTIONED
 (
   ID_USER INTEGER(11) NOT NULL  ,
   ID_TWEET INTEGER(11) NOT NULL  ,
   NOTIFICATIONREADED BOOL NULL  
   , PRIMARY KEY (ID_USER,ID_TWEET) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE MENTIONED
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_MENTIONED_USER
     ON MENTIONED (ID_USER ASC);

CREATE  INDEX I_FK_MENTIONED_TWEET
     ON MENTIONED (ID_TWEET ASC);

# -----------------------------------------------------------------------------
#       TABLE : FOLLOWS
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS FOLLOWS
 (
   ID_USER_FOLLOWER INTEGER(11) NOT NULL  ,
   ID_USER_FOLLOWED INTEGER(11) NOT NULL  ,
   NOTIFICATIONREADED BOOL NULL  ,
   DATEFOLLOW DATETIME NULL  
   , PRIMARY KEY (ID_USER_FOLLOWER,ID_USER_FOLLOWED) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE FOLLOWS
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_FOLLOWS_USER
     ON FOLLOWS (ID_USER_FOLLOWER ASC);

CREATE  INDEX I_FK_FOLLOWS_USER1
     ON FOLLOWS (ID_USER_FOLLOWED ASC);


# -----------------------------------------------------------------------------
#       CREATION DES REFERENCES DE TABLE
# -----------------------------------------------------------------------------

ALTER TABLE `TWEET` ENGINE=INNODB;
ALTER TABLE `CONTAINHASHTAG` ENGINE=INNODB;
ALTER TABLE `MENTIONED` ENGINE=INNODB;
ALTER TABLE `FOLLOWS` ENGINE=INNODB;
ALTER TABLE `USER` ENGINE=INNODB;
ALTER TABLE `TWEETLIKE` ENGINE=INNODB;

ALTER TABLE TWEET 
  ADD FOREIGN KEY FK_TWEET_TWEET (ID_TWEET_ANSWERTO)
      REFERENCES TWEET (ID_TWEET)
      ON DELETE CASCADE;

ALTER TABLE TWEET 
  ADD FOREIGN KEY FK_TWEET_USER (ID_USER)
      REFERENCES USER (ID_USER)
      ON DELETE CASCADE;


ALTER TABLE TWEETLIKE 
  ADD FOREIGN KEY FK_TWEETLIKE_USER (ID_USER)
      REFERENCES USER (ID_USER)
      ON DELETE CASCADE;


ALTER TABLE TWEETLIKE 
  ADD FOREIGN KEY FK_TWEETLIKE_TWEET (ID_TWEET)
      REFERENCES TWEET (ID_TWEET)
      ON DELETE CASCADE;


ALTER TABLE CONTAINHASHTAG 
  ADD FOREIGN KEY FK_CONTAINHASHTAG_TWEET (ID_TWEET)
      REFERENCES TWEET (ID_TWEET)
      ON DELETE CASCADE;


ALTER TABLE MENTIONED 
  ADD FOREIGN KEY FK_MENTIONED_USER (ID_USER)
      REFERENCES USER (ID_USER)
      ON DELETE CASCADE;


ALTER TABLE MENTIONED 
  ADD FOREIGN KEY FK_MENTIONED_TWEET (ID_TWEET)
      REFERENCES TWEET (ID_TWEET)
      ON DELETE CASCADE;


ALTER TABLE FOLLOWS 
  ADD FOREIGN KEY FK_FOLLOWS_USER (ID_USER_FOLLOWER)
      REFERENCES USER (ID_USER)
      ON DELETE CASCADE;


ALTER TABLE FOLLOWS 
  ADD FOREIGN KEY FK_FOLLOWS_USER1 (ID_USER_FOLLOWED)
      REFERENCES USER (ID_USER)
      ON DELETE CASCADE;

