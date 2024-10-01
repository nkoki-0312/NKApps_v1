const url = new URL(window.location.href);
const params = url.searchParams;
const gid = params.get('gid');
const groupList = document.getElementById("groupList");
const groupListSp = document.getElementById("groupListSp");
const groupListPosition = document.getElementById("groupListPosition");
const groupInfo = document.getElementById("groupInfo");
const memberTop = document.getElementById("memberTop");
const btns = document.getElementById("btns");
const contents = document.getElementById("contents");
const members = document.getElementById("members");
const userIcon = document.getElementById("userIcon");
const userName = document.getElementById("userName");
const subTtl = document.getElementById("subTtl");
const addBtn = document.getElementById("addBtn");
let activeUserId = "";
let activeGroupId = gid;
let visitingGroupId = "";

getGroupList();

if( document.body.clientWidth < 960 ) {
  groupListPosition.textContent = "上(↑)";
} else {
  groupListPosition.textContent = "左(←)";
}

window.addEventListener("resize", () => {
  if( document.body.clientWidth < 960 ) {
    groupListPosition.textContent = "上(↑)";
  } else {
    groupListPosition.textContent = "左(←)";
  }
});

function getGroupList() {
  groupList.innerHTML = '';
  groupListSp.innerHTMl = '<option value="">グループを選択してください</option>';

  fetch('https://nk-apps.net/src/php/getGroupListByLoginToken.php')
    .then(response => response.json())
    .then(res => {
      for( let i=0; i<res.length; i++ ) {
        if( res[i][1] === "" ) res[i][1] = "未登録";
        groupList.innerHTML += '\
          <button class="group-btn" id="btn' + res[i][0] + '" onclick="changeGroup(\'' + res[i][0] + '\')">\
            <div class="ttl">\
              <div class="name">' + res[i][2] + '</div>\
              <div class="id">ID: ' + res[i][1] + '</div>\
            </div>\
            <div class="triangle"><i class="fa-solid fa-caret-right"></i></div>\
          </button>\
        ';

        groupListSp.innerHTML += '\
          <option value="' + res[i][0] + '">' + res[i][2] + '</option>\
        ';
      }
      
      if( gid ) {
        document.getElementById("btn" + gid).classList.add("visiting");
        groupListSp.value = gid;
        displayGroupDetails(gid);
      } else {
        subTtl.style.display = "none";
        addBtn.style.display = "none";
      }
    })
}

function displayGroupDetails( groupId ) {
  let isDisplayInfo = false;
  btns.children[0].classList.add("visiting");
  contents.children[0].classList.add("visiting");

  if( groupId === "sp-mode" ) {
    groupId = groupListSp.value;
  }

  console.log(groupId);

  // Get group all datas
  fetch('https://nk-apps.net/src/php/getGroupAllDatasByGroupId.php', {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify( groupId )
  })
    .then(response => response.json())
    .then(res => {
      console.log(res);
      if( res[0][1] === "" ) res[0][1] = "未登録";
      if( res[0][3] === "" ) usePassword = '参加する際にパスワードを使用しない';
      groupInfo.innerHTML = '\
        <div class="info">\
          <div class="name">' + res[0][2] + '</div>\
          <div class="id">ID: ' + res[0][1] + '</div>\
          <div class="text">' + res[0][4].replace(/\n/g, '<br>') + '</div>\
        </div>\
        <div class="btn-container">\
          <button class="m secondary" id="editGroupBtn" onclick="editGroup(\'' + groupId + '\')">グループ情報を編集</button>\
          <button class="m danger-secondary" onclick="disjoinGroup(\'' + groupId + '\')">グループから退会</button>\
        </div>\
      ';
      memberTop.innerHTML = '\
        <div class="sub-ttl" id="subTtl">メンバー</div>\
        <div class="container-right">\
          <button class="m primary" id="addBtn" style="margin-bottom: var(--margin-s);" onclick="addMember()">メンバーを追加</button>\
        </div>\
      ';

      members.innerHTML = "";
      for( let i=0; i<res[1].length; i++ ) {
        if( res[1][i][4] === "admin" ) {
          members.innerHTML += '\
            <tr>\
              <td id="td' + res[1][i][0] + '" onclick="displayModal(\'' + res[1][i][0] + '\')">\
                <div class="icon">\
                  <img src="https://nk-apps.net/src/images/user-icons/' + res[1][i][0] + '/' + res[1][i][3] + '" alt="ユーザアイコン" width="40px" height="40px" onerror="this.src=\'https://nk-apps.net/src/images/no_user_images.svg\'" />\
                </div>\
                <div class="name"><span class="admin" title="管理者"><i class="fa-solid fa-star"></i></span>&nbsp;' + res[1][i][2] + '</div>\
                <div class="id">ID: ' + res[1][i][1] + '</div>\
                <div class="triangle" id="triangle' + res[1][i][0] + '"><i class="fa-solid fa-caret-right"></i></div>\
              </td>\
            </tr>\
          ';
        } else {
          members.innerHTML += '\
            <tr>\
              <td id="td' + res[1][i][0] + '" onclick="displayModal(\'' + res[1][i][0] + '\')">\
                <div class="icon">\
                  <img src="https://nk-apps.net/src/images/user-icons/' + res[1][i][0] + '/' + res[1][i][3] + '" alt="ユーザアイコン" width="40px" height="40px" onerror="this.src=\'https://nk-apps.net/src/images/no_user_images.svg\'" />\
                </div>\
                <div class="name">' + res[1][i][2] + '</div>\
                <div class="id">ID: ' + res[1][i][1] + '</div>\
                <div class="triangle" id="triangle' + res[1][i][0] + '"><i class="fa-solid fa-caret-right"></i></div>\
              </td>\
            </tr>\
          ';
        }
        if( res[2] == "member" ) {
          document.getElementById("td" + res[1][i][0]).classList.add("cannotClick");
          document.getElementById("triangle" + res[1][i][0]).style.display = "none";
        }
      }

      if( res[2] === "member" ) {
        document.getElementById("editGroupBtn").disabled = true;
      }

      isDisplayInfo = true;
    })
  
  if( !isDisplayInfo ) {
    subTtl.style.display = "none";
    addBtn.style.display = "none";
  }
  visitingGroupId = groupId;
}

function changeGroup( groupId ) {
  window.location.href = "https://nk-apps.net/group/?gid=" + groupId;
}

function disjoinGroup( groupId ) {
  fetch('https://nk-apps.net/src/php/getGroupNameByGroupId.php', {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify(groupId)
  })
    .then(response => response.json())
    .then(resGroupName => {
      if( confirm(resGroupName + "から退会します。\nよろしいですか？") ) {
        fetch('https://nk-apps.net/src/php/disjoinGroup.php', {
          method: "POST",
          headers: {"Content-Type": "application/json()"},
          body: JSON.stringify(groupId)
        })
          .then(response => response.json())
          .then(res => {
            switch( res ) {
              case "notExistOtherAdmin":
                alert("グループには最低1人の管理者が必要です。\n他のメンバーを管理者に変更後に操作を行ってください。");
                break;
              case "success":
                alert(resGroupName + "から退会しました。");
                window.location.href = "../mypage/";
                break;
            }
          })
      }
    })
}

function displayModal( userId ) {
  const ids = [userId, activeGroupId];

  fetch('https://nk-apps.net/src/php/getUserGroupDatasByUserIdAndGroupId.php', {
    method: "POST",
    headers: {"Content-Type": "application.json"},
    body: JSON.stringify(ids)
  })
    .then(response => response.json())
    .then(res => {
      console.log(res)
      if( res[6] == "admin" ) {
        grayBack.style.display = "block";
        modal.style.display = "block";
        modal.style.animation = "displayModal 0.5s ease forwards";
        modalContent.style.animation = "displayModalContent 1.0s ease forwards";
        grayBack.style.display = "displayGrayBack 0.5s ease forwards";
        console.log(res);
        userIcon.innerHTML = '<img src="https://nk-apps.net/src/images/user-icons/' + res[0] + '/' + res[4] + '" alt="ユーザアイコン" width="40px" height="40px" onerror="this.src=\'https://nk-apps.net/src/images/no_user_images.svg\'" />';
        userName.textContent = res[2];
        document.getElementById(res[5]).checked = true;
        activeUserId = res[0];
      }
    })
}

function changeUserGroupState( newState ) {
  switch( newState ) {
    case "admin":
      if( confirm("ユーザ権限を「管理者」に変更します。\nよろしいですか？") ) {
        isContinue = true;
      }
      break;
    case "member":
      isContinue = true;
      break;
  }
  const datas = [
    activeUserId,
    activeGroupId,
    newState
  ];
  fetch('https://nk-apps.net/src/php/changeUserGroupState.php', {
    method: "POST",
    headers: {"Content-Type": "applicatino/json"},
    body: JSON.stringify(datas)
  })
    .then(response => response.json())
    .then(res => {
      switch( res ) {
        case "notExistAnotherAdminUser":
          alert("グループには最低1人の管理者が必要です。\n他のメンバーを管理者に変更後に操作を行ってください。");
          document.getElementById("admin").checked = true;
          document.getElementById("member").checked = false;
          break;
        case "cannotEdit":
          closeModal();
          alert("権限がないため、ユーザの編集ができません。");
          break;
        case "success":
          alert("権限を変更しました。");
          displayGroupDetails(activeGroupId);
          break;
      }
    })

  displayGroupDetails(activeGroupId);
}

function closeModal() {
  modal.style.animation = "closeModal 1.0s ease forwards";
  modalContent.style.animation = "closeModalContent 0.5s ease forwards";
  grayBack.style.display = "closeGrayBack 0.5s ease forwards";
  grayBack.style.display = "none";
}

function compulsionDisjoinGroup() {
  fetch('https://nk-apps.net/src/php/getUserNameByUniqId.php', {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify(activeUserId)
  })
    .then(response => response.json())
    .then(resUserName => {
      if( confirm(resUserName + "さんをグループから強制退会させます。\nよろしいですか？") ) {
        const ids = [
          activeUserId,
          activeGroupId
        ];
        fetch('https://nk-apps.net/src/php/disjoinGroupByUserIdAndGroupId.php', {
          method: "POST",
          headers: {"Content-Type": "application/json()"},
          body: JSON.stringify(ids)
        })
          .then(response => response.json())
          .then(res => {
            switch( res ) {
              case "notExistOtherAdmin":
                alert("グループには最低1人の管理者が必要です。\n他のメンバーを管理者に変更後に操作を行ってください。");
                break;
              case "success":
                alert(resUserName + "さんをグループから退会させました。");
                closeModal();
                displayGroupDetails( activeGroupId );
                break;
            }
          })
      }
    })
}

function addMember() {
  window.location.href = "https://nk-apps.net/add-member/?gid=" + activeGroupId;
}

function editGroup( groupId ) {
  window.location.href = "https://nk-apps.net/edit-group/?gid=" + groupId;
}