$.fn.postAjaxData = function(output) {
    $('body').prepend('<div id="Login_screen"><img src="'+base_url+'img/loader1.gif"></div>');
    var d = this;
    exe = output;
    this.bind('submit',function(e) {
        form = d;
        console.log(form.attr('id'));
        e.preventDefault();
        var method = form.attr("method");
        var action = form.attr("action");
        var data = form.serialize();
        var f = form.valid();
        console.log(f);
        if(f)
        {
        $("#Login_screen").fadeIn('fast');
        $.ajax({
            url : action,
            method : method,
            data : data,
            error: function(jqXHR, exception) {
                $("#Login_screen").fadeOut(100);
                //Remove Loader
                if (jqXHR.status === 0) {
                    alert('Not connect.\n Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found. [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (exception === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (exception === 'timeout') {
                    alert('Time out error.');
                } else if (exception === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error.\n' + jqXHR.responseText);
                }
            },
            success : function(result)
            {
                $("#Login_screen").fadeOut(100);
                var results = JSON.parse(result);
                if(typeof results === 'string' && results.indexOf('>') > -1)
                {   
                    var split = results.split('>');
                    var func = split[0];
                    split[0] = ''; //Removes Func Name
                    var args = split;
                    switch(func)
                    {  
                        case 'Redirect':
                            window.location.href = args[1];
                        break;
                        default :
                            exe(results);
                        break;
                    }
                }
                else {
                    exe(results);
                }
            }
        });
        }
        else
        {
           // $("#Login_screen").fadeOut('fast');
        }
    });
};