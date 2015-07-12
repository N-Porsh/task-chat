(function () {
    var app = {};

    app.contacts = function() {
        getOnlineUsers();
        setInterval (getOnlineUsers, 15000);
        $('#chat-form textarea, #chat-form input').attr("disabled", true);
    };

    app.startChat = function() {
        getLatestMessages();
        setInterval (getLatestMessages, 3000);
    };


    if(authStatus == "true") {
        $('#chat-content').text("Select some user from the left");
        app.contacts();
    } else {
        $('#chat-form textarea, #chat-form input').attr("disabled", true);
    }



    //**************************
    // events:


    $('#users-list').on('click', 'li', function() {
        $('#chat-content').html("New chat started");
        startChatSession(this.id);
        $('#chat-form textarea, #chat-form input').attr("disabled", false);
        app.startChat();
    });

    $('#chat-form input[type="submit"]').click(function(e) {
        e.preventDefault();
        sendMessage();
    });

    $("#chat-msg-area" ).keypress(function(e) {
        if (e.which == 13 ) {
            e.preventDefault();
            sendMessage();
        }
    });

    $('#login input[type="submit"]').click(function(e) {
        e.preventDefault();
        var username = $.trim($('#username').val());

        if(username != "" && username.length <= 20){
            $("#login").submit();
        } else {
            alert("Wrong user name! Should be not empty and less than 20 symbols");
        }
    });



    //***************************
    // private functions:

    function sendMessage () {
        var text = $.trim($('#chat-msg-area').val());
        if(text != ""){
            $.post(url + 'home/newmessage', {data: text}, function(data, textStatus, xhr) {
                getLatestMessages();
                $('#chat-msg-area').val("");
            });
        } else {
            alert("Write something in textarea");
        }

    }

    function startChatSession (userId) {
        $.post(url + 'home/opensession', {userId : userId}, function(data, textStatus, xhr) {
            console.log(data);
        });

    }

    function getOnlineUsers() {
        $('#users-list').html("");

        $.getJSON(url + 'user/online', function(data, textStatus) {
            $.each(data, function(index, val) {
                $('#users-list').append("<a><li id='"+ val.id +"'>"+ val.username +"</li></a>");
            });
        });
    };

    function getLatestMessages() {

        $('#chat-content').html("");

        $.getJSON(url + 'home/showmessages', function(data, textStatus) {
            var oldscrollHeight = $("#chat-content").prop("scrollHeight") - 20;

            $.each(data, function(index, val) {
                $("#chat-content").append(
                    '<div class="msg">'+
                        '<span>' + val.username+ ':</span>'+
                        '<p>' + val.message + '</p>'+
                    '</div>'
                );

                var newscrollHeight = $("#chat-content").prop("scrollHeight") - 20;
                if(newscrollHeight > oldscrollHeight){
                    $("#chat-content").animate({ scrollTop: newscrollHeight }); //Autoscroll to bottom of div
                }
            });

        });
    }


    return app;
})();