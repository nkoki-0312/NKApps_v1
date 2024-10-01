const id = document.getElementById("id");
const name = document.getElementById("name");
const email = document.getElementById("email");
const password = document.getElementById("password");
const newPassword = document.getElementById("newPassword");
const confirmNewPassword = document.getElementById("confirmNewPassword");
const errId = document.getElementById("errId");
const errName = document.getElementById("errName");
const errEmail = document.getElementById("errEmail");
const errPassword = document.getElementById("errPassword");
const idPattern = /^[a-zA-Z0-9-]{0,32}$/;
const namePattern = /^[a-zA-Z0-9-]{0,32}$/;
const updateBtn = document.getElementById("updateBtn");

function pageBack() {
  if( confirm("マイページに戻ります。\n編集中の内容は保存されません。") ) {
    window.location.href = "../mypage/";
  }
}

function checkCompleteForm() {
  if( id.value !== "" &&
      name.value !== "" &&
      email.value !== "" &&
      (
        (
          password.value === "" &&
          newPassword.value === "" &&
          confirmNewPassword.value === ""
        ) ||
        (
          password.value !== "" &&
          newPassword.value !== "" &&
          confirmNewPassword.value !== ""
        )
      )
   ) {
    updateBtn.disabled = false;
   } else {
    updateBtn.disabled = true;
   }
}

function updateUserInfo() {
  errId.textContent = "";
  errName.textContent = "";
  errEmail.textContent = "";
  errPassword.textContent = "";

  if( id.value === "" ) errId.textContent = "ユーザIDを入力してください。";
  if( !idPattern.test(id.value) ) errId.textContent = "ユーザIDは半角英数字とハイフン(-)のみ使用できます。";
  if( id.value.length > 32 ) errId.textContent = "ユーザIDは32文字以内で入力してください。";
  if( name.value === "" ) errName.textContent = "ユーザネームを入力してください。";
  if( name.value.length > 32 ) errName.textContent = "ユーザネームは32文字以内で入力してください。";
  if( email.value === "" ) errEmail.textContent = "メールアドレスを入力してください。";
  if( newPassword.value !== confirmNewPassword.value ) errPassword.textContent = "新しいパスワードが一致しません。";
  if( 
    ( password.value === "" && newPassword.value !== "" ) ||
    ( newPassword.value === "" && confirmNewPassword.value !== "" ) ||
    ( confirmNewPassword.value === "" && password.value !== "" )
  ) errPassword.textContent = "パスワードを変更する場合は、3項目とも入力してください。";

  if( errId.textContent === "" && errName.textContent === "" && errEmail.textContent === "" && errPassword.textContent === "" ) {
    const userDatas = [
      id.value,
      name.value,
      email.value,
      password.value,
      newPassword.value
    ];
    console.log(userDatas);

    fetch("../src/php/updateUserInfo.php", {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(userDatas)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "existId":
            errId.textContent = "すでにこのユーザIDが登録されています。";
            break;
          case "existEmail":
            errEmail.textContent = "すでにこのメールアドレスが登録されています。";
            break;
          case "incorrectPassword":
            errPassword.textContent = "現在のパスワードが異なります。";
            break;
          case "success":
            alert("更新しました。");
            window.location.href = "../mypage/";
        }
      })
  }
}