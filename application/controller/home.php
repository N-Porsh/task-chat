<?php

class Home extends Controller
{

    public function index()
    {
        session_start();
        $authStatus = (isset($_SESSION['user_authorized']) && $_SESSION['user_authorized'] == "true") ? "true" : "false";


        // load views
        require APP . 'view/_templates/header.php';
        require APP . 'view/home/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function opensession()
    {
        session_start();
        $user1 = $_SESSION['user']['id'];
        $user2 = $_POST['userId'];

        $res = $this->model->checkExistingSession($user1, $user2);

        // check if there is no session room for both users
        if (!$res) {
            $this->model->newSession($user1, $user2);
        } else {
            $_SESSION['session_room_id'] = $res->room_id;
        }

    }

    public function showmessages()
    {
        session_start();
        $data = $this->model->getMessages($_SESSION['session_room_id']);
        echo json_encode($data);
    }

    public function newmessage()
    {
        session_start();
        $res = $this->model->newMessage($_POST['data'], $_SESSION['session_room_id'], $_SESSION['user']['id']);
    }

}
