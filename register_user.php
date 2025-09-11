<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap"
      rel="stylesheet"
    />
    <title>2nd Hand Car Advisor</title>
  </head>
  <body>
    <header>
      <nav>
        <div class="nav_logo">
          <h1><a href="index.php">2nd Hand Car Advisor</a></h1>
        </div>
        <ul class="nav_link">
         
          <li><a href="login.php">Login</a></li>
        </ul>
      </nav>
    </header>
    <main>
      <section class="add_userr">
        <div class="add_user_box">
          <h1>User Registration</h1>
          <form class="user_registration_form" action="insert_student.php" method="post" style="display:flex; flex-direction:column; gap:16px; max-width:350px; margin:auto;">
            <label for="new_user_name">Name:</label>
            <input type="text" id="new_user_name" name="new_user_name" required>
            <label for="new_user_password">Password:</label>
            <input type="password" id="new_user_password" name="new_user_password" required>
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>
            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" required>
            <input type="submit" value="Register User" style="margin-top:12px;" />
          </form>
        </div>
      </section>
    </main>
  </body>
</html>
