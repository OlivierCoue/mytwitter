<?php
namespace Model\Hashtag;
use \Db;
use \PDOException;
/**
 * Hashtag model
 *
 * This file contains every db action regarding the hashtags
 */

/**
 * Attach a hashtag to a post
 * @param pid the post id to which attach the hashtag
 * @param hashtag_name the name of the hashtag to attach
 * @return true or false (if something went wrong)
 */
function attach($pid, $hashtag_name) {
    $db = Db::dbc();
    try{
        $sql = 'INSERT INTO containhashtag (id_tweet, hashtag_name) VALUES(:pid, :hashtag_name)';
        $sth = $db->prepare($sql);
        $sth->execute(array(':pid'=>$pid, ':hashtag_name'=>$hashtag_name));
        return true;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}

/**
 * List hashtags
 * @return a list of hashtags names
 */
function list_hashtags() {
    $db = Db::dbc();
    try{
        $hashtags = [];
        $sql = 'SELECT DISTINCT hashtag_name FROM containhashtag';
        $sth = $db->prepare($sql);
        $sth->execute();
        while ($row = $sth->fetch()) {        	
            array_push($hashtags, $row['hashtag_name']);
        }        
        return $hashtags;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}

/**
 * List hashtags sorted per popularity (number of posts using each)
 * @param length number of hashtags to get at most
 * @return a list of hashtags
 */
function list_popular_hashtags($length) {
    $db = Db::dbc();
    try{
        $hashtags = [];
        $sql = 'SELECT hashtag_name FROM containhashtag GROUP BY hashtag_name ORDER BY count(hashtag_name) DESC LIMIT '.$length;
        $sth = $db->prepare($sql);
        $sth->execute();
        while ($row = $sth->fetch()) {        	
            array_push($hashtags, $row['hashtag_name']);
        }        
        return $hashtags;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}

/**
 * Get posts for a hashtag
 * @param hashtag the hashtag name
 * @return a list of posts objects or null if the hashtag doesn't exist
 */
function get_posts($hashtag_name) {
	$db = Db::dbc();
    try{
        $posts = [];
        $sql = 'SELECT DISTINCT tweet.* FROM containhashtag NATURAL JOIN tweet WHERE hashtag_name = :hashtag_name';
        $sth = $db->prepare($sql);
        $sth->execute(array(':hashtag_name' => $hashtag_name));
        while ($row = $sth->fetch()) {        	
            array_push($posts, \Model\Post\rowToObject($row));
        }        
        return $posts;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
    return [\Model\Post\get(1)];
}

/** Get related hashtags
 * @param hashtag_name the hashtag name
 * @param length the size of the returned list at most
 * @return an array of hashtags names
 */
function get_related_hashtags($hashtag_name, $length) {
    $db = Db::dbc();
    try{
        $hashtags = [];
        $sql = 'SELECT hashtag_name FROM containhashtag ch1 WHERE EXISTS (SELECT * FROM containhashtag ch2 WHERE ch1.id_tweet = ch2.id_tweet AND ch2.hashtag_name = :hashtag_name) AND ch1.hashtag_name <> :hashtag_name LIMIT '.$length;
        $sth = $db->prepare($sql);
        $sth->execute(array(':hashtag_name' => $hashtag_name));
        while ($row = $sth->fetch()) {        	
            array_push($hashtags, $row['hashtag_name']);
        }        
        return $hashtags;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}
