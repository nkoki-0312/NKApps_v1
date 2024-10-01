const url = new URL(window.location.href);
const params = url.searchParams;
const gid = params.get('gid');
const member = document.getElementsByName("member");
const addBtn = document.getElementById("addBtn");
getFriendList();

function getFriendList() {
  members.innerHTML = "";

  fetch("https://nk-apps.net/src/php/getFriendListWhoNotJoinGroupByLoginTokenAndGroupId.php", {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify(gid)
  })
    .then(response => response.json())
    .then(res => {
      for( let i=0; i<res.length; i++ ){
        members.innerHTML += '\
          <input type="checkbox" name ="member" id="checkbox + ' + res[i][0] + '" value="' + res[i][0] + '" onchange="checkCompleteForm()">\
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
      
      if( res.length == 0 ) {
        members.innerHTML = '<div class="no-data">フレンドがいないか、すでにすべてのフレンドがグループに参加しています。</div>';
      }
    })
}

function addMembers() {
  let addMembers = [];
  for( let i=0; i<member.length; i++ ) {
    if( member[i].checked === true ) {
      addMembers.push(member[i].value);
    }
  }

  const datas = [
    addMembers,
    gid
  ];
  console.log(datas);

  fetch('https://nk-apps.net/src/php/addMembersByMemberListAndGroupId.php', {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify(datas)
  })
    .then(response => response.json())
    .then(res => {
      switch( res ) {
        case "success":
          alert(datas[0].length + "人のユーザを追加しました。");
          window.location.href = "https://nk-apps.net/group/?gid=" + gid;
          break;
      }
    })
}

function checkCompleteForm() {
  let countMembers = 0;
  console.log(member);

  for( let i=0; i<member.length; i++ ) {
    if( member[i].checked === true ) {
      countMembers++;
    }
  }

  if( countMembers >= 1 ) {
    addBtn.disabled = false;
  } else {
    addBtn.disabled = true;
  }
}