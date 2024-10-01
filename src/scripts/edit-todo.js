const url = new URL(window.location.href);
const params = url.searchParams;
const tid = params.get('tid');
const ttl = document.getElementById("ttl");
const startAt = document.getElementById("startAt");
const limitAt = document.getElementById("limitAt");
const bgClr = document.getElementsByName("bgClr");
const fontClr = document.getElementsByName("fontClr");
const text = document.getElementById("text");
const groupId = document.getElementById("groupId");
const errTtl = document.getElementById("errTtl");
const errSpan = document.getElementById("errSpan");
const errClr = document.getElementById("errClr");
const errText = document.getElementById("errText");
const updateBtn = document.getElementById("updateBtn");
getPlanDatas();

function checkCompleteForm() {
  if( ttl.value === "" ) {
    updateBtn.disabled = true;
  } else {
    updateBtn.disabled = false;
  }
}

function getPlanDatas() {
  fetch('https://nk-apps.net/src/php/getToDoAllDatasFromToDoId.php', {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify(tid)
  })
    .then(response => response.json())
    .then(res => {
      switch( res[0] ) {
        case "notExistPlan":
        case "cannotGetPlan":
          alert("ToDO情報の取得中にエラーが発生しました。\nToDoに戻ります。");
          window.location.href = "https://nk-apps.net/todo/";
          break;
        case "success":
          ttl.value = res[1][3];
          startAt.value = res[1][8];
          limitAt.value = res[1][9];
          text.value = res[1][4];
          groupId.value = res[1][1];

          for( let i=0; i<bgClr.length; i++ ) {
            if( bgClr[i].value === res[1][6] ) {
              bgClr[i].checked = true;
            }
          }

          for( let i=0; i<fontClr.length; i++ ) {
            if( fontClr[i].value === res[1][7] ) {
              fontClr[i].checked = true;
            }
          }
    
          checkCompleteForm();
          break;
      }
    })
}

function pageBack() {
  if( confirm("ToDoに戻ります。\n編集中の内容は保存されません。") ) {
    window.location.href = "https://nk-apps.net/todo/";
  }
}

function updatePlan() {
  const startAtDate = new Date(startAt.value);
  const limitAtDate = new Date(limitAt.value);
  let selectBgClr = "";
  let selectFontClr = "";
  errTtl.textContent = "";
  errSpan.textContent = "";
  errClr.textContent = "";
  errText.textContent = "";

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
  if( startAtDate > limitAtDate && startAt.value != null && limitAt.value != null ) errSpan.textContent = "期限は開始時刻以降に設定してください。";
  if( selectFontClr === "" ) errClr.textContent = "文字色を選択してください。";
  if( selectBgClr === "" ) errClr.textContent = "背景色を選択してください。";
  if( errTtl.textContent === "" &&
      errSpan.textContent === "" &&
      errClr.textContent === "" &&
      errText.textContent === "" ) {
    const planDatas = [
      ttl.value,
      startAt.value,
      limitAt.value,
      selectBgClr,
      selectFontClr,
      text.value,
      tid,
      groupId.value
    ];
    fetch('https://nk-apps.net/src/php/updateToDo.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(planDatas)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "cannotEditPlan":
            alert("ToDo編集の権限がありません。\nToDoがグループで共有されている場合、作成者か管理者である必要があります。");
            window.location.href = "https://nk-apps.net/todo/";
            break;
          case "success":
            alert("ToDo情報を更新しました。");
            window.location.href = "https://nk-apps.net/todo/";
            break;
        }
      })
  }
}