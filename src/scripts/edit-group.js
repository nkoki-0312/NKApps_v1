const url = new URL(window.location.href);
const params = url.searchParams;
const gid = params.get('gid');
const name = document.getElementById("name");
const id = document.getElementById("id");
const usePassword = document.getElementById("usePassword");
const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirmPassword");
const text = document.getElementById("text");
const nerrNameame = document.getElementById("errName");
const errId = document.getElementById("errId");
const errPassword = document.getElementById("errPassword");
const errText = document.getElementById("errText");
const updateBtn = document.getElementById("updateBtn");
const groupIdPattern = /^[a-zA-Z0-9-]{0,32}$/;
getGroupDatas();

function checkCompleteForm() {
  console.log(usePassword.value);
  console.log(name.value === "" || ( usePassword.checked && password.value === "" ) || ( usePassword.checked && confirmPassword.value === "" ) );
  if( name.value === "" ) {
    updateBtn.disabled = true;
  } else {
    updateBtn.disabled = false;
  }
}

function changeUsePassword() {
  if( usePassword.checked ) {
    password.disabled = false;
    confirmPassword.disabled = false;
  } else {
    password.value = "";
    confirmPassword.value = "";
    password.disabled = true;
    confirmPassword.disabled = true;
  }
  checkCompleteForm();
}

function getGroupDatas() {
  fetch('https://nk-apps.net/src/php/getGroupDatasByGroupId.php', {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify(gid)
  })
    .then(response => response.json())
    .then(res => {
      switch( res[0] ) {
        case "notExistGroup":
          alert("グループ情報の取得中に予期せぬエラーが発生しました。\nマイページに戻ります。");
          window.location.href = "https://nk-apps.net/mypage/";
          break;
        case "success":
          name.value = res[1][3];
          id.value = res[1][2];
          text.value = res[1][5];
          if( res[1][4] !== "" ) {
            usePassword.checked = true;
          }
          break;
      }

      changeUsePassword();
    })
}

function update() {
  errName.textContent = "";
  errId.textContent = "";
  errPassword.textContent = "";
  errText.textContent = "";

  if( name.value === "" ) errName.textContent = "グループ名を入力してください。";
  if( name.value.length > 32 ) errName.textContent = "グループ名は32文字以内で入力してください。";
  if( !groupIdPattern.test(id.value) ) errId.textContent = "グループIDは半角で入力してください。";
  if( id.value.length > 32 ) errId.textContent = "グループIDは32文字以内で入力してください。";
  if( password.value !== confirmPassword.value ) errPassword.textContent = "新しいパスワードが異なります。";
  if( errName.textContent === "" && errId.textContent === "" && errPassword.textContent === "" ) {
    const datas = [
      gid,
      name.value,
      id.value,
      password.value,
      text.value,
      usePassword.checked
    ];
    fetch('https://nk-apps.net/src/php/editGroup.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(datas)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "cannotEditGroup":
            alert("権限がないため、グループの編集ができません。\nマイページに戻ります。");
            window.location.href = "https://nk-apps.net/mypage/";
            break;
          case "success":
            alert("グループ情報を更新しました。");
            window.location.href = "https://nk-apps.net//group/?gid=" + gid;
            break;
        }
      })
  }
}