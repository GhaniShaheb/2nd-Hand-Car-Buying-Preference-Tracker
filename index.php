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
    <title>Used Car Choice Advisor</title>
  </head>
  <body>
    <header>
      <nav>
        <div class="nav_logo">
          <h1><a href="index.php">Used Car Choice Advisor</a></h1>
        </div>
        <ul class="nav_link">
          <!-- <li><a href="profile.php">Profile</a></li>
          <li><a href="wishlist.php">Wishlist</a></li>
          <li><a href="show_students.php">Car Models</a></li>
          <li><a href="workshop.php">Workshops</a></li>
          <li><a href="fuel_pumps.php">Fuel Pumps</a></li>
          <li><a href="marketplace.php">Marketplace</a></li>
          <li><a href="register_user.php">Register User</a></li> -->
        </ul>
      </nav>
    </header>
    <main>
      <section class="login">
        <div class="login_box">
          <h1>Login</h1>
          <form class="login_form" action="login.php" method="post">
            <input
              type="text"
              id="email"
              name="email"
              placeholder="Email"
            />
            <input
              type="password"
              id="password"
              name="password"
              placeholder="password"
            />
            <input type="submit" value="Submit" />
          </form>
          <p>Don't have an account?</p>
          <a href="register_user.php">Register as a User</a> |

        </div>
      </section>
    </main>
  </body>
</html>
