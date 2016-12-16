<?php
namespace Model\User;
use \Db;
use \PDOException;
/**
 * User model
 *
 * This file contains every db action regarding the users
 */

/**
 * Get a user in db
 * @param id the id of the user in db
 * @return an object containing the attributes of the user or null if error or the user doesn't exist
 */
function get($id) {
<<<<<<< HEAD
    $db = Db::dbc();
    try{
        $user = null;
        $sql = 'SELECT * FROM user WHERE id_user = :id';
        $sth = $db->prepare($sql);
        $sth->execute(array(':id'=>$id));
        while ($row = $sth->fetch()) {
            $user = rowToObject($row);
        }
        return $user;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
=======
    return (object) array(
        "id" => 1337,
        "username" => "yrlgtm",
        "name" => "User 1",
        "password" => "hashed",
        "email" => "yrlgtm@gmail.com",
        "avatar" => "" 
    );
>>>>>>> 88a3b0d9dfe40693d8379f65fc2f500da6b15375
}

/**
 * Create a user in db
 * @param username the user's username
 * @param name the user's name
 * @param password the user's password
 * @param email the user's email
 * @param avatar_path the temporary path to the user's avatar
 * @return the id which was assigned to the created user, null if an error occured
 * @warning this function doesn't check whether a user with a similar username exists
 * @warning this function hashes the password
 */
function create($username, $name, $password, $email, $avatar_path) {
<<<<<<< HEAD
    $db = Db::dbc();
    try{
        $sql = 'INSERT INTO user (username, name, password, email, avatarpath) VALUES(:username, :name, :password, :email, :avatar_path)';
        $sth = $db->prepare($sql);
        $sth->execute(array(':username'=>$username, ':name'=>$name, ':password'=>hash_password($password), ':email'=>$email, ':avatar_path'=>$avatar_path));
        return $db->lastInsertId();
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
=======
    return 1337;
>>>>>>> 88a3b0d9dfe40693d8379f65fc2f500da6b15375
}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param username the user's username
 * @param name the user's name
 * @param email the user's email
 * @return true if everything went fine, false else
 * @warning this function doesn't check whether a user with a similar username exists
 */
function modify($uid, $username, $name, $email) {
<<<<<<< HEAD
    $db = Db::dbc();
    try{
        $sql = 'UPDATE user SET username = :username, name = :name, email = :email WHERE id_user = :id';
        $sth = $db->prepare($sql);
        $sth->execute(array(':username'=>$username, ':name'=>$name, ':email'=>$email, ':id'=>$uid));
        return true;
    }catch(\PDOException $e){
        print $e->getMessage();
        return false;
    }
=======
    return false;
>>>>>>> 88a3b0d9dfe40693d8379f65fc2f500da6b15375
}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param new_password the new password
 * @return true if everything went fine, false else
 * @warning this function hashes the password
 */
function change_password($uid, $new_password) {
<<<<<<< HEAD
    $db = Db::dbc();
    try{
        $sql = 'UPDATE user SET password = :password WHERE id_user = :id';
        $sth = $db->prepare($sql);
        $sth->execute(array(':password'=>hash_password($new_password), ':id'=>$uid));
        return true;
    }catch(\PDOException $e){
        print $e->getMessage();
        return false;
    }
=======
    return false;
>>>>>>> 88a3b0d9dfe40693d8379f65fc2f500da6b15375
}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param avatar_path the temporary path to the user's avatar
 * @return true if everything went fine, false else
 */
function change_avatar($uid, $avatar_path) {
<<<<<<< HEAD
    $db = Db::dbc();
    try{
        $sql = 'UPDATE user SET avatarpath = :avatar_path WHERE id_user = :id';
        $sth = $db->prepare($sql);
        $sth->execute(array(':avatar_path'=>$avatar_path, ':id'=>$uid));
        return true;
    }catch(\PDOException $e){
        print $e->getMessage();
        return false;
    }
=======
    return false;
>>>>>>> 88a3b0d9dfe40693d8379f65fc2f500da6b15375
}

/**
 * Delete a user in db
 * @param id the id of the user to delete
 * @return true if the user has been correctly deleted, false else
 */
function destroy($id) {
<<<<<<< HEAD
    $db = Db::dbc();
    try{
        $sql = 'DELETE FROM user WHERE id_user = :id';
        $sth = $db->prepare($sql);
        $sth->execute(array(':id'=>$id));
        return true;
    }catch(\PDOException $e){
        print $e->getMessage();
        return false;
    }
=======
    return false;
>>>>>>> 88a3b0d9dfe40693d8379f65fc2f500da6b15375
}

/**
 * Hash a user password
 * @param password the clear password to hash
 * @return the hashed password
 */
function hash_password($password) {
<<<<<<< HEAD
    return md5($password);
=======
    return $password;
>>>>>>> 88a3b0d9dfe40693d8379f65fc2f500da6b15375
}

/**
 * Search a user
 * @param string the string to search in the name or username
 * @return an array of find objects
 */
function search($string) {
<<<<<<< HEAD
    $db = Db::dbc();
    try{
        $users = [];
        $sql = 'SELECT * FROM user WHERE username = :username OR name = :name';
        $sth = $db->prepare($sql);
        $sth->execute(array(':username'=>$string, ':name'=>$string));
        while ($row = $sth->fetch()) {
            array_push($users, rowToObject($row));
        }
        return $users;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
=======
    return [get(1)];
>>>>>>> 88a3b0d9dfe40693d8379f65fc2f500da6b15375
}

/**
 * List users
 * @return an array of the objects of every users
 */
function list_all() {
<<<<<<< HEAD
    $db = Db::dbc();
    try{
        $users = [];
        $sql = 'SELECT * FROM user';
        $sth = $db->prepare($sql);
        $sth->execute();
        while ($row = $sth->fetch()) {
            array_push($users, rowToObject($row));
        }
        return $users;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
=======
    return [get(1)];
>>>>>>> 88a3b0d9dfe40693d8379f65fc2f500da6b15375
}

/**
 * Get a user from its username
 * @param username the searched user's username
 * @return the user object or null if the user doesn't exist
 */
function get_by_username($username) {
<<<<<<< HEAD
    $db = Db::dbc();
    try{
        $user = null;
        $sql = 'SELECT * FROM user WHERE username = :username';
        $sth = $db->prepare($sql);
        $sth->execute(array(':username'=>$username));
        while ($row = $sth->fetch()) {
            $user = rowToObject($row);
        }
        return $user;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
=======
    return get(1);
>>>>>>> 88a3b0d9dfe40693d8379f65fc2f500da6b15375
}

/**
 * Get a user's followers
 * @param uid the user's id
 * @return a list of users objects
 */
function get_followers($uid) {
<<<<<<< HEAD
    $db = Db::dbc();
    try{
        $users = [];
        $sql = 'SELECT u.* FROM user u INNER JOIN follows f ON u.id_user = f.id_user_follower WHERE f.id_user_followed = :id';
        $sth = $db->prepare($sql);
        $sth->execute(array(':id'=>$uid));
        while ($row = $sth->fetch()) {
            array_push($users, rowToObject($row));
        }
        return $users;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
=======
    return [get(2)];
>>>>>>> 88a3b0d9dfe40693d8379f65fc2f500da6b15375
}

/**
 * Get the users our user is following
 * @param uid the user's id
 * @return a list of users objects
 */
function get_followings($uid) {
<<<<<<< HEAD
    $db = Db::dbc();
    try{
        $users = [];
        $sql = 'SELECT u.* FROM user u INNER JOIN follows f ON u.id_user = f.id_user_followed WHERE f.id_user_follower = :id';
        $sth = $db->prepare($sql);
        $sth->execute(array(':id'=>$uid));
        while ($row = $sth->fetch()) {
            array_push($users, rowToObject($row));
        }
        return $users;
    }catch(\PDOException $e){
        print $e->getMessage();
        return null;
    }
=======
    return [get(2)];
>>>>>>> 88a3b0d9dfe40693d8379f65fc2f500da6b15375
}

/**
 * Get a user's stats
 * @param uid the user's id
 * @return an object which describes the stats
 */
function get_stats($uid) {
    return (object) array(
        "nb_posts" => 10,
        "nb_followers" => 50,
        "nb_following" => 66
    );
}

/**
 * Verify the user authentification
 * @param username the user's username
 * @param password the user's password
 * @return the user object or null if authentification failed
<<<<<<< HEAD
 * @warning this function must perform the password hashing
 */
function check_auth($username, $password) {
    $user = get_by_username($username);
    if($user->password == md5($password))
        return $user;
    else
        return null;
=======
 * @warning this function must perform the password hashing   
 */
function check_auth($username, $password) {
    return null;
>>>>>>> 88a3b0d9dfe40693d8379f65fc2f500da6b15375
}

/**
 * Verify the user authentification based on id
 * @param id the user's id
 * @param password the user's password (already hashed)
 * @return the user object or null if authentification failed
 */
function check_auth_id($id, $password) {
<<<<<<< HEAD
    $user = get($id);
    if($user && $user->password == md5($password))
        return $user;
    else
        return null;
=======
    return null;
>>>>>>> 88a3b0d9dfe40693d8379f65fc2f500da6b15375
}

/**
 * Follow another user
 * @param id the current user's id
 * @param id_to_follow the user's id to follow
 * @return true if the user has been followed, false else
 */
function follow($id, $id_to_follow) {
<<<<<<< HEAD
    $db = Db::dbc();
    try{
        $sql = 'INSERT INTO follows (id_user_follower, id_user_followed) VALUES(:id, :id_to_follow)';
        $sth = $db->prepare($sql);
        $sth->execute(array(':id'=>$id, ':id_to_follow'=>$id_to_follow));
        return true;
    }catch(\PDOException $e){
        print $e->getMessage();
        return false;
    }
=======
    return false;
>>>>>>> 88a3b0d9dfe40693d8379f65fc2f500da6b15375
}

/**
 * Unfollow a user
 * @param id the current user's id
 * @param id_to_follow the user's id to unfollow
 * @return true if the user has been unfollowed, false else
 */
function unfollow($id, $id_to_unfollow) {
<<<<<<< HEAD
    $db = Db::dbc();
    try{
        $sql = 'DELETE FROM follows WHERE id_user_follower = :id AND id_user_followed = :id_to_unfollow';
        $sth = $db->prepare($sql);
        $sth->execute(array(':id'=>$id, ':id_to_unfollow'=>$id_to_unfollow));
        return true;
    }catch(\PDOException $e){
        print $e->getMessage();
        return false;
    }
}

function rowToObject($row){
    $user = (object) array(
        "id" => $row['ID_USER'],
        "username" => $row['USERNAME'],
        "name" => $row['NAME'],
        "password" => $row['PASSWORD'],
        "email" => $row['EMAIL'],
        "avatar" => $row['AVATARPATH']
    );
    return $user;
}
=======
    return false;
}

>>>>>>> 88a3b0d9dfe40693d8379f65fc2f500da6b15375
