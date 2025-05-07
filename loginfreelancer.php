<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    />
    <link rel="stylesheet" href="style/workstyle.css" />
    <title>Login Page</title>
  </head>

  <body>
    <div class="container" id="container">
      <div class="form-container sign-in">
        <form action="backend/loginfreelancer.php" method="POST">
          <h1>Sign In</h1>
          <div class="social-icons">
            <a href="#" class="icon"
              ><i class="fa-brands fa-google-plus-g"></i
            ></a>
            <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
            <a href="#" class="icon"
              ><i class="fa-brands fa-linkedin-in"></i
            ></a>
          </div>
          <span>or use your phone number and id number</span>
          <input type="text" name="phone_number" placeholder="phone number" />
          <input type="password" name="id_number" placeholder="id number" />
          <button type="submit">Sign In</button>
        </form>
      </div>
      <div class="toggle-container">
        <div class="toggle">
          <div class="toggle-panel toggle-left">
            <h1>Welcome Back!</h1>
            <p>if you already have an account here</p>
            <button class="hidden" id="login">Sign In</button>
          </div>
          <div class="toggle-panel toggle-right">
            <h1><br>Start your</br>journey now</br>
                 as freelancer</h1>
            <p>
              if you don't have an account yet, join us and start your journey
            </p>
           
          </div>
        </div>
      </div>
    </div>

    <script>
      const container = document.getElementById("container");
      const registerBtn = document.getElementById("register");
      const loginBtn = document.getElementById("login");  

      registerBtn.addEventListener("click", () => {
      container.classList.add("active");
      });

      loginBtn.addEventListener("click", () => {
      container.classList.remove("active");
      });
    </script>
  </body>
</html>
