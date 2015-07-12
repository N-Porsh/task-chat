<div class="container">
    <?php if($authStatus != "true"): ?>
        <form action="<?php echo URL . 'user/login' ?>" method="post" id="login">
            <label for="username">User name:</label>
            <input type="text" name="username" id="username" placeholder="Your name" maxlength="20">
            <input type="submit" name="login" value="Login">
        </form>
    <?php else:?>
        <form action="<?php echo URL . 'user/logout' ?>" method="post" id="logout">
            <label><?php echo $_SESSION['user']['name'] ?></label>
            <input type="submit" name="logout" value="Logout">
        </form>
    <?php endif; ?>
</div>

<div class="container">
    <div id="users">
        <h3>Contact list:</h3>
        <ul id="users-list"></ul>
    </div>
    <div id="chat-box">
        <div id="chat-content"></div>
        <form id="chat-form">
            <textarea id="chat-msg-area" cols="73" rows="5" ></textarea>
            <input type="submit" value="Send" />
        </form>
    </div>
    <div class="clr"></div>
</div>