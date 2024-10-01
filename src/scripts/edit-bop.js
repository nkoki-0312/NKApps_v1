const url = new URL(window.location.href);
const params = url.searchParams;
const bid = params.get('bid');
const type = document.getElementById("type");
const amount = document.getElementById("amount");
const date = document.getElementById("date");
const assetCat = document.getElementById("assetCat");
const bopCat = document.getElementById("bopCat");
const text = document.getElementById("text");
const groupId = document.getElementById("groupId");
const errAmount = document.getElementById("errAmount");
const errDate = document.getElementById("errDate");
const errAssetCat = document.getElementById("errAssetCat");
const errBopCat = document.getElementById("errBopCat");
const errText = document.getElementById("errText");
let beforeAmount = 0;
getBopDatas();

async function getCats( assetCatId, bopCatId ) {
  await fetch('https://nk-apps.net/src/php/getCatsByLoginToken.php', {
    method: "POST"
  })
    .then(response => response.json())
    .then(res => {
      // res[0] => asset cat list
      // res[1] => bop cat list
      assetCat.innerHTML = "";
      bopCat.innerHTML = "";

      for( let i=0; i<res[0].length; i++ ) {
        if( res[0][i][0] == assetCatId ) {
          assetCat.innerHTML += '<option value="' + res[0][i][0] + '" selected>' + res[0][i][3] + ' (' + res[0][i][7] + ')</option>';
        } else {
          assetCat.innerHTML += '<option value="' + res[0][i][0] + '">' + res[0][i][3] + ' (' + res[0][i][7] + ')</option>';
        }
      }

      for( let i=0; i<res[1].length; i++ ) {
        if( res[1][i][0] == bopCatId ) {
          bopCat.innerHTML += '<option value="' + res[1][i][0] + '" selected>' + res[1][i][3] + ' (' + res[1][i][6] + ')</option>';
        } else {
          bopCat.innerHTML += '<option value="' + res[1][i][0] + '">' + res[1][i][3] + ' (' + res[1][i][6] + ')</option>';
        }
      }
    })
    .catch(error => {
      console.error(error);
    })
}

async function getBopDatas() {
  await fetch('https://nk-apps.net/src/php/getBopAllDatasByBopId.php', {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify(bid)
  })
    .then(response => response.json())
    .then(res => {
      console.log(res);
      amount.value = res[1][5];
      beforeAmount = res[1][5];
      date.value = res[1][7];
      text.value = res[1][6];
      groupId.value = res[1][3];

      switch( res[1][4] ) {
        case "expense":
          type.value = "支出";
          break;
        case "income":
          type.value = "収入";
          break;
      } 

      getCats(res[1][1], res[1][2]);
    })
    .catch(error => {
      console.error(error);
    })
}

function pageBack() {
  if( confirm("家計簿に戻ります。\n編集中の内容は保存されません。") ) {
    window.location.href = "https://nk-apps.net/kakeibo/";
  }
}

function checkCompleteForm() {
  if( amount.value === "" ||
      date.value === "" ||
      assetCat.value === "" ||
      bopCat.value === "") {
    updateBtn.disabled = true;
  } else {
    updateBtn.disabled = false;
  }
}

function updateBop() {
  errAmount.textContent = "";
  errDate.textContent = "";
  errAssetCat.textContent = "";
  errBopCat.textContent = "";
  errText.textContent = "";

  if( amount.value <= 0 ) errAmount.textContent = "金額は0円以上で入力してください。";
  if( amount.value === "" ) errAmount.textContent = "金額を入力してください。";
  if( date.value === "" ) errDate.textContent = "日付を選択してください。";
  if( assetCat.value === "" ) errAssetCat.textContent = "資産カテゴリーを選択してください。";
  if( bopCat.value === "" ) errBopCat.textContent = "収支カテゴリーを選択してください。";
  if( errAmount.textContent === "" &&
      errDate.textContent === "" &&
      errAssetCat.textContent === "" &&
      errBopCat.textContent === "" ) {
    const bopDatas = [
      amount.value,
      date.value,
      assetCat.value,
      bopCat.value,
      text.value,
      bid,
      groupId.value,
      beforeAmount
    ];
    fetch('https://nk-apps.net/src/php/updateBop.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(bopDatas)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "cannotEditBop":
            alert("取引編集の権限がありません。\n取引がグループで共有されている場合、作成者か管理者である必要があります。");
            window.location.href = "https://nk-apps.net/todo/";
            break;
          case "notMatchGroup":
            errAssetCat.textContent = "グループが一致しません。";
            errBopCat.textContent = "グループが一致しません。";
            break;
          case "notEnoughAmount":
            errAmount.textContent = "金額が足りません。";
            break;
          case "success":
            alert("取引情報を更新しました。");
            window.location.href = "https://nk-apps.net/kakeibo/";
            break;
        }
      })
  }
}