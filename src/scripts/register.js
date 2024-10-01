const bar = document.getElementById("bar");
const userId = document.getElementById("userId");
const userName = document.getElementById("userName");
const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirmPassword");
const errUserId = document.getElementById("errUserId");
const errUserName = document.getElementById("errUserName");
const errPassword = document.getElementById("errPassword");
const registerBtn = document.getElementById("registerBtn");
const userIdPattern = /^[a-zA-Z0-9-]{0,32}$/;
const formContainer = document.getElementById("formContainer");
const successContainer = document.getElementById("successContainer");

function checkCompleteForm() {
  if( userId.value !== "" &&
      userName.value !== "" &&
      password.value !== "" &&
      confirmPassword.value !== "" ) {
    registerBtn.disabled = false;
  }else{
    registerBtn.disabled = true;
  }
}

function register() {
  const url = new URL(window.location.href);
  const params = url.searchParams;

  errUserId.textContent = "";
  errUserName.textContent = "";
  errPassword.textContent = "";

  if( userId.value === "" ) errUserId.textContent = "ユーザIDを入力してください。";
  if( !userIdPattern.test(userId.value) ) errUserId.textContent = "ユーザIDは半角英数字とハイフン(-)のみ使用できます。";
  if( userId.value.length > 32 ) errUserId.textContent = "ユーザIDは32文字以内で入力してください。";
  if( userName.value === "" ) errUserName.textContent = "ユーザネームを入力してください。";
  if( userName.value.length > 32 ) errUserName.textContent = "ユーザネームは32文字以内で入力してください。";
  if( password.value !== confirmPassword.value ) errPassword.textContent = "パスワードが一致しません。";
  if( password.value === "" ) errPassword.textContent = "パスワードは両方とも入力してください。";
  if( confirmPassword.value === "" ) errPassword.textContent = "パスワードは両方とも入力してください。";
  if( errUserId.textContent === "" && errUserName.textContent === "" && errPassword.textContent === "" ) {
    const userDatas = [
      params.get('uid'),
      userId.value,
      userName.value,
      password.value
    ];
    fetch('../src/php/register.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(userDatas)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "existId":
            errUserId.textContent = "すでにこのユーザIDは登録されています。";
            break;
          case "success":
            formContainer.style.display = "none";
            successContainer.style.display = "block";
            bar.style.animation = "progress-fourth 2.0s ease forwards";
            break;
        }
      })
  }
}