const name = document.getElementById("name");
const id = document.getElementById("id");
const password = document.getElementById("password");
const member = document.getElementsByName("member");
const text = document.getElementById("text");
const errName = document.getElementById("errName");
const errId = document.getElementById("errId");
const errPassword = document.getElementById("errPassword");
const errText = document.getElementById("errText");
const confirmPassword = document.getElementById("confirmPassword");
const createBtn = document.getElementById("createBtn");
const members = document.getElementById("members");
const groupIdPattern = /^[a-zA-Z0-9-]{0,32}$/;

text.value = text.value.replace(/\n/g,'<br>');

getFriendList();

function pageBack() {
  if( confirm("マイページに戻ります。\n編集中の内容は保存されません。") ) {
    window.location.href = "../mypage/";
  }
}

function checkConpleteForm() {
  if( name.value === "" ) {
    createBtn.disabled = true;
  } else {
    createBtn.disabled = false;
  }
}

function createGroup() {
  let addMembers = [];

  errName.textContet = "";
  errId.textContent = "";
  errPassword.textContent = "";
  errText.textContent = "";

  if( name.value === "" ) errName.textContent = "グループ名を入力してください。";
  if( name.value.length > 32 ) errName.textContent = "グループ名は32文字以内で入力してください。";
  if( !groupIdPattern.test(id.value) ) errId.textContent = "グループIDは半角で入力してください。";
  if( id.value.length > 32 ) errId.textContent = "グループIDは32文字以内で入力してください。";
  if( password.value !== confirmPassword.value ) errPassword.textContent = "パスワードが一致しません。";
  if( ( password.value === "" && confirmPassword.value !== "" ) || 
      ( password.value !== "" && confirmPassword.value === "" ) ) errPassword.textContent = "パスワードは2項目とも入力してください。";
  if( errName.textContent === "" && errId.textContent === "" && errPassword.textContent === "" ) {
    for( let i=0; i<member.length; i++ ) {
      if( member[i].checked === true ) {
        addMembers.push(member[i].value);
      }
    }
    
    const groupDatas = [
      name.value,
      id.value,
      addMembers,
      password.value,
      text.value
    ];
    console.log(groupDatas);
    fetch('https://nk-apps.net/src/php/createGroup.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(groupDatas)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "existGroupId":
            errId.textContent = "すでにこのグループIDは登録されています。";
            break;
          case "success":
            alert(name.value + "を作成しました。");
            window.location.href = "../mypage/";
            break;
        }
      })
  }
}

function getFriendList() {
  members.innerHTML = "";

  fetch("https://nk-apps.net/src/php/getFriendListByLoginToken.php")
    .then(response => response.json())
    .then(res => {
      for( let i=0; i<res.length; i++ ){
        members.innerHTML += '\
          <input type="checkbox" name ="member" id="checkbox + ' + res[i][0] + '" value="' + res[i][0] + '">\
          <label for="checkbox + ' + res[i][0] + '">\
            <div class="member-block">\
              <div class="icon">\
              <img src="https://nk-apps.net/src/images/user-icons/' + res[i][0] + '/' + res[i][3] + '" alt="ユーザアイコン" width="40px" height="40px" onerror="this.src=\'https://nk-apps.net/src/images/no_user_images.svg\'" />\
              </div>\
              <div class="name">' + res[i][2] + '</div>\
            </div>\
          </label>\
        ';
      }
      
    })
}