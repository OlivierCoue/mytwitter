<?php
namespace Model\Notification;
use \Db;
use \PDOException;
/**
 * Notification model
 *
 * This file contains every db action regarding the notifications
 */

/**
 * Get a liked notification in db
 * @param uid the id of the user in db
 * @return a list of objects for each like notification
 * @warning the post attribute is a post object
 * @warning the liked_by attribute is a user object
 * @warning the date attribute is a DateTime object
 * @warning the reading_date attribute is either a DateTime object or null (if it hasn't been read)
 */
function get_liked_notifications($uid) {
    $db = Db::dbc();
    try{
        $notifications = [];
        $sql = 'SELECT tweetlike.* FROM tweetlike INNER JOIN tweet USING(id_tweet) WHERE tweet.id_user = :uid';
        $sth = $db->prepare($sql);
        $sth->execute(array(':uid'=>$uid));
        while ($row = $sth->fetch()) {
            $date =  \DateTime::createFromFormat('Y-m-j H:i:s', $row['DATE_CREATED']);
            $date_readed =  ($row['DATE_READED']) ? \DateTime::createFromFormat('Y-m-j H:i:s', $row['DATE_READED']) : null;
            array_push($notifications, (object) array(
                "type" => "liked",
                "post" => \Model\Post\get($row['ID_TWEET']),
                "liked_by" => \Model\User\get($row['ID_USER']),
                "date" => $date,
                "reading_date" => $date_readed
            ));
        }        
        return $notifications;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}

/**
 * Mark a like notification as read (with date of reading)
 * @param pid the post id that has been liked
 * @param uid the user id that has liked the post
 * @return true if everything went ok, false else
 */
function liked_notification_seen($pid, $uid) {
    $db = Db::dbc();
    try{
        $sql = 'UPDATE tweetlike SET date_readed = now() WHERE id_user = :uid AND id_tweet = :pid';
        $sth = $db->prepare($sql);
        $sth->execute(array(':uid'=>$uid, ':pid'=>$pid));
        return true;
    }catch(\PDOException $e){
        print $e->getMessage();
        return false;
    }
}

/**
 * Get a mentioned notification in db
 * @param uid the id of the user in db
 * @return a list of objects for each like notification
 * @warning the post attribute is a post object
 * @warning the mentioned_by attribute is a user object
 * @warning the reading_date object is either a DateTime object or null (if it hasn't been read)
 */
function get_mentioned_notifications($uid) {
    $db = Db::dbc();
    try{
        $notifications = [];
        $sql = 'SELECT * FROM mentioned WHERE id_user = :uid';
        $sth = $db->prepare($sql);
        $sth->execute(array(':uid'=>$uid));
        while ($row = $sth->fetch()) {
            if(isset($row['ID_TWEET'])){
                $post = \Model\Post\get($row['ID_TWEET']);
                if(is_object($post)){
                    $date =  \DateTime::createFromFormat('Y-m-j H:i:s', $row['DATE_CREATED']);
                    $date_readed =  ($row['DATE_READED']) ? \DateTime::createFromFormat('Y-m-j H:i:s', $row['DATE_READED']) : null;    
                    array_push($notifications, (object) array(
                        "type" => "mentioned",
                        "post" => $post,
                        "mentioned_by" => $post->author,
                        "date" => $date,
                        "reading_date" => $date_readed
                    ));
                }
            }
        }        
        return $notifications;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}

/**
 * Mark a mentioned notification as read (with date of reading)
 * @param uid the user that has been mentioned
 * @param pid the post where the user was mentioned
 * @return true if everything went ok, false else
 */
function mentioned_notification_seen($uid, $pid) {
    $db = Db::dbc();
    try{
        $sql = 'UPDATE mentioned SET date_readed = now() WHERE id_user = :uid AND id_tweet = :pid';
        $sth = $db->prepare($sql);
        $sth->execute(array(':uid'=>$uid, ':pid'=>$pid));
        return true;
    }catch(\PDOException $e){
        print $e->getMessage();
        return false;
    }
}

/**
 * Get a followed notification in db
 * @param uid the id of the user in db
 * @return a list of objects for each like notification
 * @warning the user attribute is a user object which corresponds to the user following.
 * @warning the reading_date object is either a DateTime object or null (if it hasn't been read)
 */
function get_followed_notifications($uid) {
    $db = Db::dbc();
    try{
        $notifications = [];
        $sql = 'SELECT * FROM follows WHERE id_user_followed = :uid';
        $sth = $db->prepare($sql);
        $sth->execute(array(':uid'=>$uid));
        while ($row = $sth->fetch()) {      
            $date =  \DateTime::createFromFormat('Y-m-j H:i:s', $row['DATE_CREATED']);
            $date_readed =  ($row['DATE_READED']) ? \DateTime::createFromFormat('Y-m-j H:i:s', $row['DATE_READED']) : null;
            array_push($notifications, (object) array(
                "type" => "followed",                
                "user" => \Model\User\get($row['ID_USER_FOLLOWER']),
                "date" => $date,
                "reading_date" => $date_readed
            ));
        }        
        return $notifications;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}

/**
 * Mark a followed notification as read (with date of reading)
 * @param followed_id the user id which has been followed
 * @param follower_id the user id that is following
 * @return true if everything went ok, false else
 */
function followed_notification_seen($followed_id, $follower_id) {
    $db = Db::dbc();
    try{
        $sql = 'UPDATE follows SET date_readed = now() WHERE id_user_follower = :uid1 AND id_user_followed = :uid2';
        $sth = $db->prepare($sql);
        $sth->execute(array(':uid1'=>$follower_id, ':uid2'=>$followed_id));
        return true;
    }catch(\PDOException $e){
        print $e->getMessage();
        return false;
    }
}

/**
 * Get all the notifications sorted by time (descending order)
 * @param uid the user id
 * @return a sorted list of every notifications objects
 */
function list_all_notifications($uid) {
    $ary = array_merge(get_liked_notifications($uid), get_followed_notifications($uid), get_mentioned_notifications($uid));    
    usort(
        $ary,
        function($a, $b) {
            return $a->date->format('Y-m-j H:i:s') - $b->date->format('Y-m-j H:i:s');
        }
    );    
    return $ary;
}

/**
 * Mark a notification as read (with date of reading)
 * @param uid the user to whom modify the notifications
 * @param notification the notification object to mark as seen
 * @return true if everything went ok, false else
 */
function notification_seen($uid, $notification) {
    switch($notification->type) {
        case "liked":
            return liked_notification_seen($notification->post->id, $notification->liked_by->id);
        break;
        case "mentioned":
            return mentioned_notification_seen($uid, $notification->post->id);
        break;
        case "followed":
            return followed_notification_seen($uid, $notification->user->id);
        break;
    }
    return false;
}

