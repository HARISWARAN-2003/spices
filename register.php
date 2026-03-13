<?php
include('db.php');

$msg = "";
$msg_type = "";

if(isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    try {
        $sql = "INSERT INTO users (username, password) 
                VALUES ('$username', '$password')";
        if(mysqli_query($conn, $sql)) {
            $msg = "✅ Staff Registered Successfully!";
            $msg_type = "success";
        }
    } 
    catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $msg = "❌ Error: Username '$username' is already taken!";
            $msg_type = "error";
        } else {
            $msg = "❌ Error: " . $e->getMessage();
            $msg_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Registration | HINKAR TRADERS</title>
    <style>
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('https://images.unsplash.com/photo-1506368249639-73a05d6f6488?auto=format&fit=crop&w=1350&q=80');
            background-size: cover; background-position: center; background-attachment: fixed;
            display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; 
        }

        #notification {
            position: fixed; top: 20px; padding: 15px 25px; border-radius: 8px; color: white; 
            font-weight: bold; display: <?php echo $msg ? 'block' : 'none'; ?>; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.3); animation: slideDown 0.5s; z-index: 1000;
        }
        .success { background: #4CAF50; }
        .error { background: #f44336; }
        @keyframes slideDown { from { top: -50px; } to { top: 20px; } }

        .card { 
            background: rgba(255, 255, 255, 0.98); padding: 30px; width: 400px; 
            border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.5); text-align: center;
        }

        h1 { color: #1b5e20; margin: 0; font-size: 22px; font-weight: 800; text-transform: uppercase; }
        p.sub-heading { color: #555; margin-bottom: 20px; font-weight: 600; font-size: 13px; }

        .input-box { text-align: left; margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #444; font-size: 13px; }
        input { 
            width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; 
            box-sizing: border-box; background: #fff; font-size: 14px;
        }
        input:focus { border-color: #2e7d32; outline: none; box-shadow: 0 0 5px rgba(46,125,50,0.2); }

        .pwd-container { position: relative; }
        .eye-icon { position: absolute; right: 10px; top: 32px; cursor: pointer; color: #666; }

        button { 
            width: 100%; padding: 12px; background: #2e7d32; color: white; border: none; 
            border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 20px; 
            font-size: 16px; transition: 0.3s;
        }
        button:hover { background: #1b5e20; transform: translateY(-2px); }

        .link { margin-top: 15px; font-size: 13px; color: #555; }
        .link a { color: #2e7d32; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <?php if($msg): ?>
        <div id="notification" class="<?php echo $msg_type; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="card">
        <h1>HINKAR TRADERS</h1>
        <p class="sub-heading">STAFF REGISTRATION</p>

        <form method="POST" action="register.php">
            <div class="input-box pwd-container">
                <label>Username</label>
                <input type="text" name="username" placeholder="rajesh_hinkar" required>
            </div>
            
            <div class="input-box pwd-container">
                <label>Password</label>
                <input type="password" name="password" id="reg-pass" placeholder="••••••••" required>
                <span class="eye-icon" onclick="toggleRegPass()">👁️</span>
            </div>
            
            <button type="submit" name="register">Register Staff</button>
        </form>
        
        <div class="link">
            Already registered? <a href="login.php">Sign In</a>
        </div>
    </div>

    <script>
        setTimeout(() => {
            const notify = document.getElementById('notification');
            if(notify) notify.style.display = 'none';
        }, 4000);

        function toggleRegPass() {
            const passField = document.getElementById("reg-pass");
            const icon = event.target;
            if (passField.type === "password") {
                passField.type = "text";
                icon.textContent = "🙈";
            } else {
                passField.type = "password";
                icon.textContent = "👁️";
            }
        }
    </script>
</body>
</html>