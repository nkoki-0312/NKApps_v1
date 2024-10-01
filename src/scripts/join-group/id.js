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

function joinGroup() {
  let password = "";

  errId.textContent = "";
  
  fetch('../../src/php/checkJoinGroupByGroupId.php', {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify(id.value)
  })
    .then(response => response.json())
    .then(resCheck => {
      switch( resCheck ) {
        case "notExistGroupId":
          errId.textContent = "このグループIDを持つグループは存在しません。";
          break;
        case "alreadyJoin":
          errId.textContent = "すでに参加済みのグループです。";
          break;
        default:
          if( confirm(resCheck[2] + " に参加します。\nよろしいですか？") ) {
            if( resCheck[3] != "" ) {
              password = prompt("パスワードを入力してください。");
            }
            const datas = [
              resCheck[1],
              password
            ];
            fetch('../../src/php/joinGroup.php', {
              method: "POST",
              headers: {"Content-Type": "application/json"},
              body: JSON.stringify(datas)
            }) 
              .then(response => response.json())
              .then(res => {
                switch( res ) {
                  case "success":
                    alert(resCheck[2] + " に参加しました。");
                    window.location.href = "https://nk-apps.net/group/?gid=" + resCheck[1];
                    break;
                  case "alreadyFriend":
                    errId.textContent = "すでにフレンドに追加済みのユーザです。";
                    break;
                  case "unMatchPassword":
                    alert("パスワードが一致しません。");
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