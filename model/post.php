<?php
namespace Model\Post;
use \Db;
use \PDOException;
/**
 * Post
 *
 * This file contains every db action regarding the posts
 */

/**
 * Get a post in db
 * @param id the id of the post in db
 * @return an object containing the attributes of the post or false if error
 * @warning the author attribute is a user object
 * @warning the date attribute is a DateTime object
 */
function get($id) {
    $db = Db::dbc();
    try{
        $post = null;
        $sql = 'SELECT * FROM tweet WHERE id_tweet = :id';
        $sth = $db->prepare($sql);
        $sth->execute(array(':id'=>$id));
        while ($row = $sth->fetch()) {
            $post = rowToObject($row);
        }
        return $post;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}

/**
 * Get a post with its likes, responses, the hashtags used and the post it was the response of
 * @param id the id of the post in db
 * @return an object containing the attributes of the post or false if error
 * @warning the author attribute is a user object
 * @warning the date attribute is a DateTime object
 * @warning the likes attribute is an array of users objects
 * @warning the hashtags attribute is an of hashtags objects
 * @warning the responds_to attribute is either null (if the post is not a response) or a post object
 */
function get_with_joins($id) {
    $db = Db::dbc();
    try{
        $post = null;
        $sql = 'SELECT * FROM tweet WHERE id_tweet = :id';
        $sth = $db->prepare($sql);
        $sth->execute(array(':id'=>$id));
        while ($row = $sth->fetch()) {
            $post = rowToObject($row);
            $post->responds_to = get($row['ID_TWEET_ANSWERTO']);
            $post->likes = get_likes($post->id);
            $post->hashtags = [];
        }
        return $post;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}

/**
 * Create a post in db
 * @param author_id the author user's id
 * @param text the message
 * @param mentioned_authors the array of ids of users who are mentioned in the post
 * @param response_to the id of the post which the creating post responds to
 * @return the id which was assigned to the created post, null if anything got wrong
 * @warning this function computes the date
 * @warning this function adds the mentions (after checking the users' existence)
 * @warning this function adds the hashtags
 * @warning this function takes care to rollback if one of the queries comes to fail.
 */
function create($author_id, $text, $response_to=null) {
    $db = Db::dbc();
    try{
        $sql = 'INSERT INTO tweet (text, datepublished, id_user, id_tweet_answerto) VALUES(:text, now(), :author_id, :response_to)';
        $sth = $db->prepare($sql);
        $sth->execute(array(':text'=>$text, ':author_id'=>$author_id, ':response_to'=>$response_to));

        $postId = $db->lastInsertId();

        $userMentionned = extract_mentions($text);              
        foreach ($userMentionned as $username) {            
            $user = \Model\User\get_by_username(ltrim($username, '@'));            
            if($user && $user->id)                    
                mention_user($postId,    $user->id);
        }

        $hashtags = extract_hashtags($text);        
        foreach ($hashtags as $hashtag) {            
            \Model\Hashtag\attach($postId, $hashtag);            
        }

        return $postId;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}

/**
 * Get the list of used hashtags in message
 * @param text the message
 * @return an array of hashtags
 */
function extract_hashtags($text) {
    return array_map(
        function($el) { return substr($el, 1); },
        array_filter(
            explode(" ", $text),
            function($c) {
                return $c !== "" && $c[0] == "#";
            }
        )
    );
}

/**
 * Get the list of mentioned users in message
 * @param text the message
 * @return an array of usernames
 */
function extract_mentions($text) {    
    return array_map(
        function($el) { return substr($el, 1); },
        array_filter(
            explode(" ", $text),
            function($c) {
                return $c !== "" && $c[0] == "@";
            }
        )
    );
}

/**
 * Mention a user in a post
 * @param pid the post id
 * @param uid the user id to mention
 * @return true if everything went ok, false else
 */
function mention_user($pid, $uid) {    
    $db = Db::dbc();
    try{
        $sql = 'INSERT INTO mentioned (id_user, id_tweet, date_created) VALUES(:uid, :pid, now())';
        $sth = $db->prepare($sql);
        $sth->execute(array(':pid'=>$pid, ':uid'=>$uid));
        return true;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}

/**
 * Get mentioned user in post
 * @param pid the post id
 * @return the array of user objects mentioned
 */
function get_mentioned($pid) {
    $db = Db::dbc();
    try{
        $users = [];
        $sql = 'SELECT * FROM mentioned INNER JOIN user ON user.id_user = mentioned.id_user WHERE mentioned.id_tweet = :pid';
        $sth = $db->prepare($sql);
        $sth->execute(array(':pid'=>$pid));
        while ($row = $sth->fetch()) {
            array_push($users, \Model\User\rowToObject($row));
        }
        return $users;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}

/**
 * Delete a post in db
 * @param id the id of the post to delete
 * @return true if the post has been correctly deleted, false else
 */
function destroy($id) {
    $db = Db::dbc();
    try{
        $sql = 'DELETE FROM tweet WHERE id_tweet = :id';
        $sth = $db->prepare($sql);
        $sth->execute(array(':id'=>$id));
        return true;
    }catch(\PDOException $e){
        print $e->getMessage();
        return false;
    }
}

/**
 * Search for posts
 * @param string the string to search in the text
 * @return an array of find objects
 */
function search($string) {
    $db = Db::dbc();
    try{
        $posts = [];
        $sql = "SELECT * FROM tweet WHERE text LIKE '%".$string."%'";
        $sth = $db->prepare($sql);
        $sth->execute(array());
        while ($row = $sth->fetch()) {
            array_push($posts, rowToObject($row));
        }
        return $posts;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}

/**
 * List posts
 * @param date_sorted the type of sorting on date (false if no sorting asked), "DESC" or "ASC" otherwise
 * @return an array of the objects of each post
 * @warning this function does not return the passwords
 */
function list_all($date_sorted=false) {    
    $db = Db::dbc();
    try{
        $posts = [];
        if($date_sorted == 'ASC')
            $sql = 'SELECT * FROM tweet ORDER BY datepublished ASC';
        else if($date_sorted == 'DESC')
            $sql = 'SELECT * FROM tweet ORDER BY datepublished DESC';
        else
            $sql = 'SELECT * FROM tweet ORDER BY datepublished';
        $sth = $db->prepare($sql);
        $sth->execute(array(':date_sorted' => $date_sorted));
        while ($row = $sth->fetch()) {
            array_push($posts, rowToObject($row));
        }
        return $posts;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}

/**
 * Get a user's posts
 * @param id the user's id
 * @param date_sorted the type of sorting on date (false if no sorting asked), "DESC" or "ASC" otherwise
 * @return the list of posts objects
 */
function list_user_posts($id, $date_sorted="DESC") {
    $db = Db::dbc();
    try{
        $posts = [];
        if($date_sorted == 'ASC')
            $sql = 'SELECT * FROM tweet WHERE id_user = :id ORDER BY datepublished ASC';
        else if($date_sorted == 'DESC')
            $sql = 'SELECT * FROM tweet WHERE id_user = :id ORDER BY datepublished DESC';
        else
            $sql = 'SELECT * FROM tweet WHERE id_user = :id ORDER BY datepublished';
        $sth = $db->prepare($sql);
        $sth->execute(array(':id'=>$id, ':date_sorted' => $date_sorted));
        while ($row = $sth->fetch()) {
            array_push($posts, rowToObject($row));
        }
        return $posts;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}

/**
 * Get a post's likes
 * @param pid the post's id
 * @return the users objects who liked the post
 */
function get_likes($pid) {
    $db = Db::dbc();
    try{
        $users = [];
        $sql = 'SELECT * FROM tweetlike NATURAL JOIN user WHERE tweetlike.id_tweet = :pid';
        $sth = $db->prepare($sql);
        $sth->execute(array(':pid'=>$pid));
        while ($row = $sth->fetch()) {
            array_push($users, \Model\User\rowToObject($row));
        }
        return $users;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}

/**
 * Get a post's responses
 * @param pid the post's id
 * @return the posts objects which are a response to the actual post
 */
function get_responses($pid) {
    $db = Db::dbc();
    try{
        $posts = [];
        $sql = 'SELECT * FROM tweet WHERE id_tweet_answerto = :id';
        $sth = $db->prepare($sql);
        $sth->execute(array(':id'=>$pid));
        while ($row = $sth->fetch()) {
            array_push($posts, rowToObject($row));
        }
        return $posts;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}

/**
 * Get stats from a post (number of responses and number of likes
 */
function get_stats($pid) {
    return (object) array(
        "nb_likes" => 10,
        "nb_responses" => 40
    );
}

/**
 * Like a post
 * @param uid the user's id to like the post
 * @param pid the post's id to be liked
 * @return true if the post has been liked, false else
 */
function like($uid, $pid) {
    $db = Db::dbc();
    try{
        $sql = 'INSERT INTO tweetlike (id_user, id_tweet, date_created) VALUES(:uiddd, :pid, now())';
        $sth = $db->prepare($sql);
        $sth->execute(array(':uiddd'=>$uid, ':pid'=>$pid));
        return true;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}

/**
 * Unlike a post
 * @param uid the user's id to unlike the post
 * @param pid the post's id to be unliked
 * @return true if the post has been unliked, false else
 */
function unlike($uid, $pid) {
    $db = Db::dbc();
    try{
        $sql = 'DELETE FROM tweetlike WHERE id_tweet = :pid AND id_user = :uid';
        $sth = $db->prepare($sql);
        $sth->execute(array(':pid'=>$pid, ':uid'=>$uid));
        return true;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
}

function rowToObject($row){
    $post = (object) array(
        "id" => $row['ID_TWEET'],
        "text" => $row['TEXT'],
        "date" => $row['DATEPUBLISHED'],
        "author" => \Model\User\get($row['ID_USER'])
    );
    return $post;
}

