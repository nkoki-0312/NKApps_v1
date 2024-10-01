const contents = document.getElementById("contents");
const btns = document.getElementById("btns");
let visitingTabNum = 0;
getFriendList();
getGroupList();

function changeTab( tabNum ) {
  btns.children[visitingTabNum].classList.remove("visiting");
  btns.children[tabNum].classList.add("visiting");
  contents.children[visitingTabNum].classList.remove("visiting");
  contents.children[tabNum].classList.add("visiting");

  visitingTabNum = tabNum;
}

function getFriendList() {
  const friendTable = document.getElementById("friendTable");

  fetch('https://nk-apps.net/src/php/getFriendListByLoginToken.php')
    .then(response => response.json())
    .then(res => {
      if( res.length !== 0 ) friendTable.innerHTML = "";
      for( let i=0; i<res.length; i++ ){
        friendTable.innerHTML += '\
          <tr>\
            <td>\
              <div class="icon">\
                <img src="https://nk-apps.net/src/images/user-icons/' + res[i][0] + '/' + res[i][3] + '" alt="ユーザアイコン" width="40px" height="40px" onerror="this.src=\'https://nk-apps.net/src/images/no_user_images.svg\'" />\
              </div>\
              <div class="name">' + res[i][2] + '</div>\
              <div class="id">ID: ' + res[i][1] + '</div>\
              <button class="delete-btn" onclick="deleteFriend(\'' + res[i][0] + '\')"><i class="fa-solid fa-trash"></i></button>\
            </td>\
          </tr>\
        ';
      }
    })
}

function deleteFriend( friendId ) {
  fetch('https://nk-apps.net/src/php/getUserNameByUniqId.php', {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify(friendId)
  })
    .then(response => response.json())
    .then(resFriendName => {
      if( confirm(resFriendName + "さんをフレンドから削除します。\n削除後も「フレンドを追加」ボタンから再度追加できます。") ) {
        fetch('https://nk-apps.net/src/php/deleteFriend.php', {
          method: "POST",
          headers: {"Content-Type": "application/json"},
          body: JSON.stringify(friendId)
        })
          .then(response => response.json())
          .then(res => {
            alert(res + "さんをフレンドから削除しました。");
            getFriendList();
          })
      }
    })
}

function getGroupList() {
  const groups = document.getElementById("groups");

  fetch('https://nk-apps.net/src/php/getGroupListByLoginToken.php')
    .then(response => response.json())
    .then(res => {
      if( res.length !== 0 ) groups.innerHTML = "";
      for( let i=0; i<res.length; i++ ) {
        if( res[i][1] === "" ) res[i][1] = "未登録";
        groups.innerHTML += '\
          <tr onclick="linkTo(\'' + res[i][0] + '\')">\
            <td>\
              <div class="name">' + res[i][2] + '</div>\
              <div class="id">ID: ' + res[i][1] + '</div>\
              <div class="triangle"><i class="fa-solid fa-caret-right"></i></div>\
            </td>\
          </tr>\
        ';
      }
    })
}

function linkTo( gid ) {
  window.location.href = "https://nk-apps.net/group/?gid=" + gid;
}