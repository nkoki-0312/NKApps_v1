const id = document.getElementById("id");
const errId = document.getElementById("errId");
const addBtn = document.getElementById("addBtn");

function checkCompleteForm() {
  if( id.value === "" ) {
    addBtn.disabled = true;
  } else {
    addBtn.disabled = false;
  }
}

function addFriend() {
  errId.textContent = "";
  
  fetch('../../src/php/checkAddFriendByUserId.php', {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify(id.value)
  })
    .then(response => response.json())
    .then(resCheck => {
      switch( resCheck ) {
        case "notExistUser":
          errId.textContent = "このユーザIDまたはメールアドレスを持つユーザが存在しません。";
          break;
        case "myself":
          errId.textContent = "自分自身をフレンドに追加することはできません。";
          break;
        case "alreadyFriend":
          errId.textContent = "すでにフレンドに追加済みのユーザです。";
          break;
        default:
          if( confirm(resCheck[1] + "さんをフレンドに追加します。\nよろしいですか？") ) {
            fetch('../../src/php/addFriend.php', {
              method: "POST",
              headers: {"Content-Type": "application/json"},
              body: JSON.stringify(resCheck[0])
            }) 
              .then(response => response.json())
              .then(res => {
                switch( res ) {
                  case "success":
                    alert(resCheck[1] + "さんをフレンドに追加しました。");
                    window.location.href = "https://nk-apps.net/mypage/";
                    break;
                  case "alreadyFriend":
                    errId.textContent = "すでにフレンドに追加済みのユーザです。";
                    break;
                  default:
                    errId.textContent = "フレンド登録処理中に予期せぬエラーが発生しました。\n時間をおいて再度お試しください。";
                    break;
                }
              })
          }
          break;
      }
    })
}