<!DOCTYPE html>
<html>
<head>
    <title>KiwiTaxis Admin Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/css/user.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="/js/user.js" type="text/javascript"></script>
</head>
<body>

    <div id='loginTitle'>
        <span >KiwiTaxis Admin System</span>
    </div>
    <div id="loginForm">
        <form action="/user/login" method="POST" onsubmit="window.checkInput()">
            <div class="form-group">
                <input type="text" class="form-control loginInput" id="username" name="username" placeholder="Enter User Name">
            </div>
            <div class="form-group">
                <input type="password" class="form-control loginInput" id="passwd" placeholder="Enter User Password">
                <input type="hidden" id="passwd_md5" name="password">
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>

</body>

<script type="text/javascript">

</script>

</html>