$(document).ready(function() {
    var login_form = $("#"+user_login_form);
    var login_button = login_form.find("button[type=submit]");
    var button_text_sign_in = login_form.find("button[type=submit]").text();
    var wait_time = 0;
    var button_update_text_interval = 0;
    
    function set_login_button_text()
    {
        if (wait_time > 0) {
            login_button.text(user_login_button_text_wait.replace("{0}", wait_time--));
        } else {
            // stop the interval
            clearInterval(button_update_text_interval);
            // restore old sign in text
            login_button.text(button_text_sign_in);
            // enable the button:
            login_button.prop("disabled", false);
        }
    }

    $("#login-form").on("ajaxComplete", function (event, jqXHR, textStatus) {
        // wait_time
        if (typeof jqXHR["responseJSON"] != "undefined"
            && typeof jqXHR["responseJSON"]["login-form-password"] != "undefined"
            && typeof jqXHR["responseJSON"]["login-form-password"][1] != "undefined") {
            wait_time = jqXHR["responseJSON"]["login-form-password"][1];

            if (wait_time > 0) {
                // disable the sign in button:
                login_button.prop("disabled", true);
                // set wait text
                set_login_button_text()
                // update the text every second
                button_update_text_interval = setInterval(function() { 
                    set_login_button_text()
                }, 1000);
            }
        }
    });
});
