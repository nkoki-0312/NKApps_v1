const url = new URL(window.location.href);
const params = url.searchParams;
const pid = params.get('pid');
const ttl = document.getElementById("ttl");
const startAt = document.getElementById("startAt");
const endAt = document.getElementById("endAt");
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
  if( ttl.value === "" ||
      startAt.value === "" ||
      endAt.value === "" ) {
    updateBtn.disabled = true;
  } else {
    updateBtn.disabled = false;
  }
}

function getPlanDatas() {
  fetch('https://nk-apps.net/src/php/getPlanAllDatasFromPlanId.php', {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify(pid)
  })
    .then(response => response.json())
    .then(res => {
      switch( res[0] ) {
        case "notExistPlan":
        case "cannotGetPlan":
          alert("イベント情報の取得中にエラーが発生しました。\nカレンダーに戻ります。");
          window.location.href = "https://nk-apps.net/calendar/";
          break;
        case "success":
          ttl.value = res[1][3];
          startAt.value = res[1][6];
          endAt.value = res[1][7];
          text.value = res[1][8];
          groupId.value = res[1][2];

          for( let i=0; i<bgClr.length; i++ ) {
            if( bgClr[i].value === res[1][4] ) {
              bgClr[i].checked = true;
            }
          }

          for( let i=0; i<fontClr.length; i++ ) {
            if( fontClr[i].value === res[1][5] ) {
              fontClr[i].checked = true;
            }
          }
    
          checkCompleteForm();
          break;
      }
    })
}

function pageBack() {
  if( confirm("カレンダーに戻ります。\n編集中の内容は保存されません。") ) {
    window.location.href = "https://nk-apps.net/calendar/";
  }
}

function updatePlan() {
  const startAtDate = new Date(startAt.value);
  const endAtDate = new Date(endAt.value);
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
  if( startAtDate > endAtDate ) errSpan.textContent = "終了時刻は開始時刻以降に設定してください。";
  if( endAt.value === "" ) errSpan.textContent = "終了日時を入力してください。";
  if( startAt.value === "" ) errSpan.textContent = "開始日時を入力してください。";
  if( selectFontClr === "" ) errClr.textContent = "文字色を選択してください。";
  if( selectBgClr === "" ) errClr.textContent = "背景色を選択してください。";
  if( errTtl.textContent === "" &&
      errSpan.textContent === "" &&
      errClr.textContent === "" &&
      errText.textContent === "" ) {
    const planDatas = [
      ttl.value,
      startAt.value,
      endAt.value,
      selectBgClr,
      selectFontClr,
      text.value,
      pid,
      groupId.value
    ];
    fetch('https://nk-apps.net/src/php/updatePlan.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(planDatas)
    })
      .then(response => response.json())
      .then(res => {
        let displayMonth = startAt.value.slice(0, 10);
        switch( res ) {
          case "cannotEditPlan":
            alert("予定編集の権限がありません。\n予定がグループで共有されている場合、作成者か管理者である必要があります。");
            window.location.href = "https://nk-apps.net/calendar/?d=" + displayMonth;
            break;
          case "success":
            alert("予定情報を更新しました。");
            window.location.href = "https://nk-apps.net/calendar/?d=" + displayMonth;
            break;
        }
      })
  }
}