<?php
include "db.php";
$error = "";

if(isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid Username or Password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | HINKAR TRADERS</title>
    <style>
        body { 
            font-family: 'Segoe UI', Roboto, sans-serif; 
            /* High-quality Spice Background */
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                        url('https://images.unsplash.com/photo-1506368249639-73a05d6f6488?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }

        .login-box { 
            background: rgba(255, 255, 255, 0.95); 
            padding: 40px; 
            width: 350px; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.4); 
            text-align: center;
            backdrop-filter: blur(5px); /* Modern glass effect */
            transition: transform 0.3s ease;
        }

        .login-box:hover {
            transform: translateY(-5px); /* Interactive lift on hover */
        }

        /* Your New Bold Heading */
        h1 { 
            color: #1b5e20; 
            margin-bottom: 5px; 
            font-size: 24px; 
            font-weight: 800; 
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        p.sub-heading {
            color: #444;
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 14px;
        }

        .err-msg { 
            color: #d32f2f; background: #ffebee; padding: 10px; 
            border-radius: 8px; font-size: 13px; margin-bottom: 20px; 
            border-left: 5px solid #d32f2f;
        }

        .input-container { position: relative; margin-bottom: 20px; }
        
        input { 
            width: 100%; 
            padding: 14px 15px; 
            border: 2px solid #eee; 
            border-radius: 10px; 
            box-sizing: border-box; 
            font-size: 15px;
            transition: 0.3s;
            background: #f9f9f9;
        }

        input:focus {
            border-color: #2e7d32;
            background: #fff;
            outline: none;
            box-shadow: 0 0 10px rgba(46,125,50,0.1);
        }

        .eye-icon {
            position: absolute; right: 15px; top: 50%;
            transform: translateY(-50%); cursor: pointer;
            font-size: 20px; user-select: none;
            transition: 0.2s;
        }
        
        .eye-icon:hover { opacity: 0.7; }

        button { 
            width: 100%; 
            padding: 15px; 
            background: #2e7d32; 
            color: white; 
            border: none; 
            border-radius: 10px; 
            font-weight: bold; 
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(46,125,50,0.3);
            transition: 0.3s;
        }

        button:hover { 
            background: #1b5e20; 
            box-shadow: 0 6px 20px rgba(46,125,50,0.4);
        }

        .footer { margin-top: 25px; font-size: 14px; color: #555; }
        .footer a { color: #2e7d32; text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>

    <div class="login-box">
        <h1>HINKAR TRADERS</h1>
        <p class="sub-heading">SPICES INVENTORY LOGIN</p>

        <?php if($error) echo "<div class='err-msg'>$error</div>"; ?>

        <form action="login.php" method="POST">
            <div class="input-container">
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-container">
                <input type="password" name="password" id="pass-field" placeholder="Password" required>
                <span class="eye-icon" id="eye-toggle" onclick="togglePass()">👁️</span>
            </div>

            <button type="submit" name="login">Sign In</button>
        </form>

        <div class="footer">
            New Staff? <a href="register.php">Create Account</a>
        </div>
    </div>

    <script>
        function togglePass() {
            var x = document.getElementById("pass-field");
            var icon = document.getElementById("eye-toggle");
            if (x.type === "password") {
                x.type = "text";
                icon.innerHTML = "🙈";
            } else {
                x.type = "password";
                icon.innerHTML = "👁️";
            }
        }
    </script>
</body>
</html>