users_dir = 'http://'+window.location.hostname+'/users/';

function checkLogin(){
    var httpRequest;
    httpRequest = new XMLHttpRequest();

    if (!httpRequest) {
        console.log('Cannot create an XMLHTTP instance');
        return false;
    }else{
        httpRequest.onreadystatechange = alertContents;
        httpRequest.open("POST", users_dir+'login.php');
        httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        httpRequest.send('action=checkLogin');
    }
    function alertContents() {
        if (httpRequest.readyState === XMLHttpRequest.DONE) {
            if (httpRequest.status === 200) {
                console.log(httpRequest.responseText);
                var data = JSON.parse(httpRequest.responseText);
                console.log(data);
                if(data == 'logged in'){
                    document.getElementById('loginForms').innerHTML = '<input type="button" value="Log out" onclick="logOut()" />';
                }else{
                    document.getElementById('loginDiv').innerHTML = '<form id="loginForm"><input type="email" name="email" id="email" placeholder="email" /><br/><input type="password" name="password" id="password" placeholder="password" /><br/><input type="button" id="login" value="Login" onclick="loginEmail()" /></form>';
                }
                
            }else{
                console.log('There was a problem with the request.');
            }
        }
    }
}
checkLogin();
function logOut(){
    var httpRequest;
    httpRequest = new XMLHttpRequest();

    if (!httpRequest) {
        console.log('Cannot create an XMLHTTP instance');
        return false;
    }else{
        httpRequest.onreadystatechange = alertContents;
        httpRequest.open("POST", users_dir+'login.php');
        httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        httpRequest.send('action=logout');
    }
    function alertContents() {
        if (httpRequest.readyState === XMLHttpRequest.DONE) {
            if (httpRequest.status === 200) {
                console.log(httpRequest.responseText);
                var data = JSON.parse(httpRequest.responseText);
                console.log(data);
                if(data == 'logged out'){
                    FB.getLoginStatus(function(response) {
                        if( response.status === 'connected'){
                            FB.logout(function(response) {
                                console.log(response)
                            });
                        }
                    });
                    window.location = users_dir+'login.html';
                }
                
            }else{
                console.log('There was a problem with the request.');
            }
        }
    }
}

function loginEmail(){
    var formElement = document.getElementById('loginForm');
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;

    var httpRequest;
    httpRequest = new XMLHttpRequest();

    if (!httpRequest) {
        console.log('Cannot create an XMLHTTP instance');
        return false;
    }else{
        httpRequest.onreadystatechange = alertContents;
        httpRequest.open("POST", users_dir+'login.php');
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        httpRequest.send('email='+email+'&password='+password);
    }
    function alertContents() {
        if (httpRequest.readyState === XMLHttpRequest.DONE) {
            if (httpRequest.status === 200) {
                console.log(httpRequest.responseText);
                var data = JSON.parse(httpRequest.responseText);
                console.log(data);
                if(data == 'logged in'){
                    document.getElementById('loginDiv').innerHTML = "Logged In!";
                    //window.location = 'view.php';
                }else{
                    document.getElementById('status').innerHTML = data;
                    document.getElementById('email').value = email;
                }
            }else{
                console.log('There was a problem with the request.');
            }
        }
    }
    checkLogin();
}