const userId = document.getElementById("userId");
const password = document.getElementById("password");
const errUserId = document.getElementById("errUserId");
const errPassword = document.getElementById("errPassword");
const loginBtn = document.getElementById("loginBtn");

function checkCompleteForm() {
  if( userId.value !== "" && password.value !== "" ) {
    loginBtn.disabled = false;
  } else {
    loginBtn.disabled = true;
  }
}

function login() {
  errUserId.textContent = "";
  errPassword.textContent = "";

  if( userId.value === "" ) errUserId.textContent = "ユーザIDまたはメールアドレスを入力してください。";
  if( password.value === "" ) errPassword.textContent = "パスワードを入力してください。";
  if( errUserId.textContent === "" && errPassword.textContent === "" ) {
    const userDatas = [
      userId.value,
      password.value
    ]
    fetch('../src/php/login.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(userDatas)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "incorrectData":
            errUserId.textContent = "ユーザIDまたはメールアドレスまたはパスワードが異なります。";
            errPassword.textContent = "ユーザIDまたはメールアドレスまたはパスワードが異なります。";
            break;
          case "success":
            window.location.href = "../portal/";
            break;
        }
      })
  }
}