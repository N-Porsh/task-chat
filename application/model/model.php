<?php

class Model
{
    /**
     * @param object $db A PDO database connection
     */
    function __construct($db)
    {
        try {
            $this->db = $db;
        } catch (PDOException $e) {
            exit('Database connection could not be established.');
        }
    }

    public function getOnlineUsers($this_user)
    {
        $sql = "SELECT id, username
                FROM users
                WHERE username != '$this_user'
                AND last_activity > SUBTIME(NOW(),'0:10:0')";

        $query = $this->db->prepare($sql);
        $query->execute();


        return $query->fetchAll();
    }

    public function newMessage($text, $roomId, $userId)
    {

        $sql = "INSERT INTO messages (user_id, room_id, message)
                VALUES (:userId, :room_id, :message)";

        $query = $this->db->prepare($sql);
        $parameters = array(':userId' => $userId, ':room_id' => $roomId, ':message' => $text);

        $query->execute($parameters);
    }

    public function getMessages($roomId)
    {
        $sql = "SELECT messages.user_id, messages.message, users.username
                FROM messages
                JOIN users
                ON messages.user_id = users.id
                WHERE room_id = $roomId
                ORDER BY date asc";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function checkExistingSession($userId1, $userId2)
    {

        $sql = "SELECT DISTINCT u1.room_id AS room_id
                FROM user_session u1, user_session u2
                WHERE u1.user_id = :user_id1 AND u2.user_id = :user_id2
                AND u1.room_id = u2.room_id";

        $query = $this->db->prepare($sql);
        $parameters = array(':user_id1' => $userId1, ':user_id2' => $userId2);

        $query->execute($parameters);


        return $query->fetch();
    }

    public function newSession($user1, $user2)
    {

        // user1 & user2 instead of normal salt
        $newRoom = md5(time() . $user1 . $user2);

        //create new session room
        $sql = "INSERT INTO rooms (session_room) VALUES ('$newRoom')";
        $this->db->exec($sql);

        $roomId = $this->db->lastInsertId();

        // associate two users
        $sql = "INSERT INTO user_session (user_id, room_id) VALUES (:user, :room_id)";
        $query = $this->db->prepare($sql);
        $parameters = array(':user' => $user1, ':room_id' => $roomId);

        $query->execute($parameters);

        $parameters = array(':user' => $user2, ':room_id' => $roomId);
        $query->execute($parameters);

        $_SESSION['session_room_id'] = $roomId;
    }

    public function findUser($username)
    {
        $sql = "SELECT id FROM users WHERE username = :username LIMIT 1";
        $query = $this->db->prepare($sql);
        $parameters = array(':username' => $username);

        $query->execute($parameters);
        return $query->fetch();
    }

    public function setActivityDate($userId)
    {
        $sql = "UPDATE users SET last_activity = NOW() WHERE id = $userId";
        $this->db->exec($sql);
    }

    public function newUser($username)
    {
        $sql = "INSERT INTO users (username) VALUES (:username)";
        $query = $this->db->prepare($sql);
        $parameters = array(':username' => $username);

        $query->execute($parameters);
    }

}
