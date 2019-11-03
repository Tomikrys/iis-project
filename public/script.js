function Confirm(title, msg, $true, $false, $link) { /*change*/
    var $neco = "<div class='dialog-ovelay'>" +
        "<div class='dialog'><header>" +
        " <h3> " + title + " </h3> " +
        "<i class='fa fa-close'></i>" +
        "</header>" +
        "<div class='dialog-msg'>" +
        " <p> " + msg + " </p> " +
        "</div>" +
        "<footer>" +
        "<div class='controls'>" +
        " <button class='button button-danger doAction'>" + $true + "</button> " +
        " <button class='button button-default cancelAction'>" + $false + "</button> " +
        "</div>" +
        "</footer>" +
        "</div>" +
        "</div>";
    var $content =
        "<div class='modal fade' id='addFormModal'>"+
        "<div class='modal-dialog'>"+
        "<div class='modal-content'>"+

        "<!-- Modal Header -->"+
        "<div class='modal-header'>"+
        "<h4 class='modal-title'>" + title + "</h4>"+
        "<button type='button' class='close' data-dismiss='modal'>&times;</button>"+
        "</div>"+

        "<!-- Modal body -->"+
        "<div class='modal-body'>"+ msg + "</div>"+
        "</div>"+
        "</div>"+
        "</div>";

    $('body').prepend($content);
    $('.doAction').click(function () {
        $(this).parents('.dialog-ovelay').fadeOut(500, function () {
            $(this).remove();
        });
        return true;
    });
    $('.cancelAction, .fa-close').click(function () {
        $(this).parents('.dialog-ovelay').fadeOut(500, function () {
            $(this).remove();
        });
        return false;
    });

}