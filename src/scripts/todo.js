// calendarとの連動

const ttl = document.getElementById("ttl");
const group = document.getElementById("group");
const startAt = document.getElementById("startAt");
const limitAt = document.getElementById("limitAt");
const beforeToDo = document.getElementById("beforeToDo");
const bgClr = document.getElementsByName("bgClr");
const fontClr = document.getElementsByName("fontClr");
const text = document.getElementById("text");
const errTtl = document.getElementById("errTtl");
const errGroup = document.getElementById("errGroup");
const errSpan = document.getElementById("errSpan");
const errBeforeToDo = document.getElementById("errBeforeToDo");
const errClr = document.getElementById("errClr");
const errText = document.getElementById("errText");
const addBtn = document.getElementById("addBtn");
const selectGroups = document.getElementsByName("selectGroups");
const displayGroup = document.getElementById("displayGroup");
const displayView = document.getElementById("displayView");
const todoList = document.getElementById("todoList");
const today = new Date();
let isDisplaySelectGroup = false;
let view = "unchecked";
let groupList = ["self"];
let visitingTabNum = 0;

today.setHours( today.getHours() + 9 );
startAt.value = today.toISOString().slice(0, 16);
limitAt.value = today.toISOString().slice(0, 16);

getGroupList();
displayToDo();

function checkCompleteForm() {
  if( ttl.value !== "" ) {
    addBtn.disabled = false;
  } else {
    addBtn.disabled = true;
  }
}

function getGroupList() {
  group.innerHTML = '<option value="self">自分のみ</option>';
  displayGroup.innerHTML = '\
    <input type="checkbox" id="self" name="selectGroups" value="self" onchange="changeDisplayGroup()" checked>\
    <label for="self">\
      <div class="ttl">自分のみ</div>\
    </label>\
  ';
  groupList = ["self"];
  fetch('https://nk-apps.net/src/php/getGroupListByLoginToken.php')
    .then(response => response.json())
    .then(res => {
      for( let i=0; i<res.length; i++ ) {
        group.innerHTML += '<option value="' + res[i][0] + '">' + res[i][2] + '</option>';
        displayGroup.innerHTML += '\
          <input type="checkbox" id="displayGroup' + res[i][0] + '" name="selectGroups" value="' + res[i][0] + '" onchange="changeDisplayGroup()" checked>\
          <label for="displayGroup' + res[i][0] + '">\
            <div class="ttl">' + res[i][2] + '</div>\
          </label>\
        ';
        groupList.push( res[i][0] );
      }
    })
}

function addToDo() {
  let startAtCheck = "";
  let limitAtCheck = "";
  let selectBgClr = "";
  let selectFontClr = "";
  errTtl.textContent = "";
  errGroup.textContent = "";
  errSpan.textContent = "";
  errBeforeToDo.textContent = "";
  errClr.textContent = "";
  errText.textContent = "";

  if( startAt.value !== "" ) {
    startAtCheck = new Date( startAt.value );
  }
  if( limitAt.value !== "" ) {
    limitAtCheck = new Date( limitAt.value );
  }

  for( let i=0; i<bgClr.length; i++ ) {
    if( bgClr[i].checked === true ) {
      selectBgClr = bgClr[i].value;
    }
  }
  
  for( let i=0; i<fontClr.length; i++ ) {
    if( fontClr[i].checked === true ) {
      selectFontClr = fontClr[i].value;
    }
  }

  if( ttl.value === "" ) errTtl.textContent = "タイトルを入力してください。";
  if( group.value === "" ) errGroup.textContent = "グループを選択してください。";
  if( startAt.value !== "" && limitAt.value !== "" && startAtCheck >= limitAtCheck ) errSpan.textContent = "期限は開始日時よりも後に設定してください。";
  if( beforeToDo.value === "" ) errBeforeToDo.textContent = "順番ToDoを選択してください。";
  if( selectFontClr === "" ) errClr.textContent = "文字色を選択してください。";
  if( selectBgClr === "" ) errClr.textContent = "背景色を選択してください。";
  if( errTtl.textContent === "" && errGroup.textContent === "" && errSpan.textContent === "" && errBeforeToDo.textContent === "" && errClr.textContent === "" ) {
    const todoDatas = [
      ttl.value,
      group.value,
      startAt.value,
      limitAt.value,
      beforeToDo.value,
      selectBgClr,
      selectFontClr,
      text.value
    ];

    fetch('https://nk-apps.net/src/php/addToDo.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(todoDatas)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "notExistGroup":
            alert("グループ情報取得中に予期せぬエラーが発生しました。\nお手数ですが、時間をおいて再度お試しください。");
            break;
          case "success":
            ttl.value = "";
            text.value = "";
            startAt.value = "";
            limitAt.value = "";
            
            displayToDo();
            break;
        }
      })
      .catch(error => {
        console.log(error);
      })
  }
}

function displaySelectGroupForm() {
  if( isDisplaySelectGroup ) {
    displayGroup.style.display = "none";
    unstyledBack.style.display = "none";
    isDisplaySelectGroup = false;
  } else {
    displayGroup.style.display = "block";
    unstyledBack.style.display = "block";
    isDisplaySelectGroup = true;
  }
}

function changeView() {
  view = displayView.value;

  displayToDo();
}

function changeDisplayGroup() {
  groupList = [];

  for( let i=0; i<selectGroups.length; i++ ) {
    if( selectGroups[i].checked === true ) {
      groupList.push( selectGroups[i].value );
    }
  }

  displayToDo();
}

async function displayToDo() {
  console.log(view);
  console.log(groupList);
  let todoDatas = "";
  let isCheck = false;
  let viewList = [];
  const today = new Date();
  let todoDate;

  beforeToDo.innerHTML = '<option value="none">指定しない</option>';

  await fetch('https://nk-apps.net/src/php/getTodos.php', {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify()
  })
    .then(response => response.json())
    .then(res => {
      switch( res[0] ) {
        case "notExistGroup":
          alert("選択したグループが存在しないか、グループに参加していません。");
          break;
        case "success":
          todoDatas = res[1];
          break;
      }
    })
    .catch(error => {
      console.log(error);
    })

  if( todoDatas != "" ) {
    console.log(todoDatas);
    todoList.innerHTML = "";

    for( let i=0; i<todoDatas.length; i++ ) {
      let span = "";
      isCheck = "";
      if( todoDatas[i][8] === null ) todoDatas[i][8] = "";
      if( todoDatas[i][9] === null ) todoDatas[i][9] = "";
      if( todoDatas[i][8] === "" && todoDatas[i][9] === "" ) {
        span = "無期限"
      } else {
        span = todoDatas[i][8].slice(0, 16) + '～' + todoDatas[i][9].slice(0, 16);
      }

      if( todoDatas[i][5] == 1 ) {
        isCheck = "checked";
      }

      switch( view ) {
        case "unchecked":
          viewList = [0];
          break;
        case "checked":
          viewList = [1];
          break;
        case "all":
          viewList = [0, 1];
          break;
      }

      if( groupList.includes( todoDatas[i][1] ) && viewList.includes( Number(todoDatas[i][5]) ) && ( ( todoDatas[i][10] == 1 && view == "unchecked" ) || ( view != "unchecked" ) ) ) {
        todoList.innerHTML += '\
          <div class="todo" style="color: ' + todoDatas[i][7] + ';background: ' + todoDatas[i][6] + ';">\
            <div class="label" id="label' + todoDatas[i][0] + '"></div>\
            <div class="checkmark-container">\
              <label class="checkmark-outer">\
                <input type="checkbox" ' + isCheck + ' id="check' + todoDatas[i][0] + '" style="display: none;" onchange="changeCheckMark(\'' + todoDatas[i][0] + '\')">\
                <span class="checkmark"></span>\
              </label>\
            </div>\
            <div class="text-container" onclick="displayModal(\'todo-detail\', \'' + todoDatas[i][0] +'\')">\
              <div class="text-container-left">\
                <div class="ttl">' + todoDatas[i][3] + '</div>\
                <div class="date">' + span + '</div>\
              </div>\
              <div class="text-container-right">\
                <div class="group">' + todoDatas[i][11] + '</div>\
              </div>\
            </div>\
          </div>\
        ';

        // If this todo is set limit_at and not checked, check date
        if( todoDatas[i][9] != "" && todoDatas[i][5] == 0 ) {
          todoDatas[i][9][11] = "T";
          todoDate = new Date( todoDatas[i][9] );
          if( today > todoDate ) {
            document.getElementById("label" + todoDatas[i][0]).style.background = "var(--nk-red-500)";
            document.getElementById("label" + todoDatas[i][0]).style.border = "1px solid var(--border-color)";
            document.getElementById("label" + todoDatas[i][0]).title = "期限が過ぎたToDoです。";
          }
        }
      }

      if( todoDatas[i][5] == 0 ) {
        beforeToDo.innerHTML += '<option value="' + todoDatas[i][0] + '">' + todoDatas[i][3] + '</option>';
      }
    }
  }
}

async function changeCheckMark( id )  {
  const checkmark = document.getElementById("check" + id);
  const isCheck = checkmark.checked;

  const todoDatas = [
    id,
    isCheck ? 1 : 0
  ];

  console.log(todoDatas);
  await fetch('https://nk-apps.net/src/php/updateToDoCheckMark.php', {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify(todoDatas)
  })
    .then(response => response.json())
    .then(res => {
      switch( res ) {
        case "notExistTodo":
          alert("対象のToDoが存在しないか、編集権限がありません。");
          checkmark.checked = isCheck ? false : true;
          break;
        case "success":
          displayToDo();
          break;
      }
    })
    
}

async function displayModal( mode, id ) {
  let todoDatas = "";
  let span = "";
  let startAt = "";
  let limitAt = "";

  grayBack.style.display = "block";
  modal.style.display = "block";
  modal.style.animation = "displayModal 0.5s ease forwards";
  modalContent.style.animation = "displayModalContent 1.0s ease forwards";
  grayBack.style.display = "displayGrayBack 0.5s ease forwards";

  switch( mode ) {
    case "todo-detail":
      await fetch('https://nk-apps.net/src/php/getToDoAllDatasFromToDoId.php', {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify( id )
      })
        .then(response => response.json())
        .then(res => {
          todoDatas = res;
        })
        .catch(error => {
          console.log(error);
        })
      
      modalTtl.textContent = "ToDo詳細";

      if( todoDatas[1][8] === null ){
        todoDatas[1][8] = "";
        startAt = "未設定";
      } else {
        startAt = todoDatas[1][8].slice(0, 16);
      }
      if( todoDatas[1][9] === null ){
        todoDatas[1][9] = "";
        limitAt = "未設定";
      } else {
        limitAt = todoDatas[1][9].slice(0, 16);
      }
      if( todoDatas[1][8] === "" && todoDatas[1][9] === "" ) {
        span = "無期限";
      } else {
        span = todoDatas[1][8] + '～' + todoDatas[1][9];
      }

      modalContent.innerHTML = '\
        <div class="top">\
          <div class="basic-info">\
            <div class="ttl">' + todoDatas[1][3] + '</div>\
            <div class="span">' + span + '</div>\
            <div class="text">' + todoDatas[1][4] + '</div>\
          </div>\
          <div class="btn-container">\
            <a href="https://nk-apps.net/edit-todo/?tid=' + todoDatas[1][0] + '">\
              <button class="m secondary" id="editBtn" style="margin-bottom: var(--margin-xxs);">予定情報を編集</button>\
            </a>\
            <button class="m danger-secondary" id="deleteBtn" onclick="deleteToDo(\'' + todoDatas[1][0] + '\',\'' + todoDatas[1][3] + '\')">予定を削除</button>\
          </div>\
        </div>\
        \
        <div class="normal-tab-container">\
          <div class="btns" id="todoDetailTabBtns">\
            <button class="btn visiting" onclick="changeTab(0)">詳細</button>\
            <button class="btn" onclick="changeTab(1)">メンバー</button>\
          </div>\
          <div class="contents" id="todoDetailTabContents">\
            <div class="content visiting">\
              <table class="detail-table">\
                <tr>\
                  <th>タイトル</th>\
                  <td>' + todoDatas[1][3] + '</td>\
                </tr>\
                <tr>\
                  <th>開始日時</th>\
                  <td>' + startAt + '</td>\
                </tr>\
                <tr>\
                  <th>終了日時</th>\
                  <td>' + limitAt + '</td>\
                </tr>\
                <tr>\
                  <th>グループ</th>\
                  <td>' + todoDatas[1][16] + '</td>\
                </tr>\
                <tr>\
                  <th>作成日時</th>\
                  <td>' + todoDatas[1][10].slice(0, 16) + '</td>\
                </tr>\
                <tr>\
                  <th>作成者</th>\
                  <td>' + todoDatas[1][18] + '</td>\
                </tr>\
                <tr>\
                  <th>最終更新日時</th>\
                  <td>' + todoDatas[1][11].slice(0, 16) + '</td>\
                </tr>\
                <tr>\
                  <th>最終更新者</th>\
                  <td>' + todoDatas[1][19] + '</td>\
                </tr>\
                <tr>\
                  <th>完了日時</th>\
                  <td>' + todoDatas[1][12].slice(0, 16) + '</td>\
                </tr>\
                <tr>\
                  <th>完了者</th>\
                  <td>' + todoDatas[1][20] + '</td>\
                </tr>\
              </table>\
            </div>\
            <div class="content">\
              <table class="user-table" id="userTable">\
              </table>\
            </div>\
          </div>\
        </div>\
      ';

      const userTable = document.getElementById("userTable");
      userTable.innerHTML = "";
      if( todoDatas[1][1] === "self" ) {
        userTable.innerHTML = '<div class="no-data">このToDoは他のユーザには公開されません。</div>';
      } else {
        for( let i=0; i<todoDatas[2].length; i++ ) {
          userTable.innerHTML += '\
            <tr>\
              <td>\
                <div class="icon">\
                  <img src="https://nk-apps.net/src/images/user-icons/' + todoDatas[2][i][0] + '/' + todoDatas[2][i][3] + '" alt="ユーザアイコン" width="40px" height="40px" onerror="this.src=\'https://nk-apps.net/src/images/no_user_images.svg\'" />\
                </div>\
                <div class="name">' + todoDatas[2][i][2] + '</div>\
                <div class="id">ID: ' + todoDatas[2][i][1] + '</div>\
              </td>\
            </tr>\
          ';
        }
      }

      if( todoDatas[1][21] == "member" ) {
        document.getElementById("editBtn").disabled = true;
        document.getElementById("deleteBtn").disabled = true;
      }
      break;
  }
}

function closeModal() {
  modal.style.animation = "closeModal 1.0s ease forwards";
  modalContent.style.animation = "closeModalContent 0.5s ease forwards";
  grayBack.style.display = "closeGrayBack 0.5s ease forwards";
  grayBack.style.display = "none";
}

function changeTab( tabNum ) {
  const todoDetailTabContents = document.getElementById("todoDetailTabContents");
  const todoDetailTabBtns = document.getElementById("todoDetailTabBtns");

  todoDetailTabBtns.children[visitingTabNum].classList.remove("visiting");
  todoDetailTabBtns.children[tabNum].classList.add("visiting");
  todoDetailTabContents.children[visitingTabNum].classList.remove("visiting");
  todoDetailTabContents.children[tabNum].classList.add("visiting");

  visitingTabNum = tabNum;
}

function deleteToDo( ToDoId, ToDoTtl ) {
  if( confirm( ToDoTtl + "を削除します。\nよろしいですか？") ) {
    fetch('https://nk-apps.net/src/php/deleteToDo.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify( ToDoId )
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "notExistToDo":
            alert("ToDoの削除処理中に予期せぬエラーが発生しました。\nお手数ですが、時間をおいて再度お試しください。");
            break;
          case "cannotDeleteToDo":
            alert("ToDo削除の権限がありません。\nToDoがグループで共有されている場合、管理者である必要があります。");
            break;
          case "success":
            alert(ToDoTtl + "を削除しました。");
            break;
        }
      })
    
    closeModal();
    displayToDo();
  }
}

function displayAppForm() {
  grayBackAppForm.style.display = "block";
  appFormContainer.style.display = "block";
  appFormContainer.style.animation = "displayAppForm 0.5s ease forwards";
  grayBackAppForm.style.display = "displayGrayBack 0.5s ease forwards";
}

function closeAppForm() {
  appFormContainer.style.animation = "closeAppForm 1.0s ease forwards";
  grayBackAppForm.style.display = "closeGrayBack 0.5s ease forwards";
  grayBackAppForm.style.display = "none";
}