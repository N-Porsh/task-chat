<?php

class User extends Controller
{
    public function login()
    {
        $userName = trim($_POST['username']);

        if (!empty($userName)) {

            session_start();
            $user = $this->model->findUser($userName);

            if(!$user) {
                $this->model->newUser($userName);
                $_SESSION['user']['id'] = $this->db->lastInsertId();
            } else {
                $_SESSION['user']['id']  = $user->id;
            }

            $_SESSION['user_authorized'] = "true";
            $_SESSION['user']['name']    = $userName;

            $this->model->setActivityDate($_SESSION['user']['id']);

            header("location:" . URL . "home/index");
        } else {
            echo "Error in login information";
        }
    }

    public function logout()
    {
        session_start();
        $this->model->setActivityDate($_SESSION['user']['id']);
        session_destroy();
        header("location:" . URL . "home/index");
        exit();
    }

    public function online()
    {
        session_start();
        $onlineUsers = $this->model->getOnlineUsers($_SESSION['user']['name']);

        echo json_encode($onlineUsers);
    }
}