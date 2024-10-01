const url = new URL(window.location.href);
const params = url.searchParams;
const trid = params.get('trid');
const type = document.getElementById("type");
const amount = document.getElementById("amount");
const date = document.getElementById("date");
const assetCatFrom = document.getElementById("assetCatFrom");
const assetCatTo = document.getElementById("assetCatTo");
const bopCat = document.getElementById("bopCat");
const text = document.getElementById("text");
const groupId = document.getElementById("groupId");
const errAmount = document.getElementById("errAmount");
const errDate = document.getElementById("errDate");
const errAssetCatFrom = document.getElementById("errAssetCatFrom");
const errAssetCatTo = document.getElementById("errAssetCatTo");
const errText = document.getElementById("errText");
let beforeAmount = 0;
getTransferDatas();

async function getCats( assetCatFromId, assetCatToId ) {
  await fetch('https://nk-apps.net/src/php/getCatsByLoginToken.php', {
    method: "POST"
  })
    .then(response => response.json())
    .then(res => {
      // res[0] => asset cat list
      // res[1] => bop cat list
      assetCatFrom.innerHTML = "";
      assetCatTo.innerHTML = "";

      for( let i=0; i<res[0].length; i++ ) {
        if( res[0][i][0] == assetCatFromId ) {
          assetCatFrom.innerHTML += '<option value="' + res[0][i][0] + '" selected>' + res[0][i][3] + ' (' + res[0][i][7] + ')</option>';
        } else {
          assetCatFrom.innerHTML += '<option value="' + res[0][i][0] + '">' + res[0][i][3] + ' (' + res[0][i][7] + ')</option>';
        }
        
        if( res[0][i][0] == assetCatToId ) {
          assetCatTo.innerHTML += '<option value="' + res[0][i][0] + '" selected>' + res[0][i][3] + ' (' + res[0][i][7] + ')</option>';
        } else {
          assetCatTo.innerHTML += '<option value="' + res[0][i][0] + '">' + res[0][i][3] + ' (' + res[0][i][7] + ')</option>';
        }
      }
    })
    .catch(error => {
      console.error(error);
    })
}

async function getTransferDatas() {
  await fetch('https://nk-apps.net/src/php/getTransferAllDatasByTransferId.php', {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify(trid)
  })
    .then(response => response.json())
    .then(res => {
      console.log(res);
      amount.value = res[1][5];
      beforeAmount = res[1][5];
      date.value = res[1][7];
      text.value = res[1][6];
      groupId.value = res[1][3];

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
      assetCatFrom.value === "" ||
      assetCatTo.value === "") {
    updateBtn.disabled = true;
  } else {
    updateBtn.disabled = false;
  }
}

function updateTransfer() {
  errAmount.textContent = "";
  errDate.textContent = "";
  errAssetCatFrom.textContent = "";
  errAssetCatTo.textContent = "";
  errText.textContent = "";

  if( amount.value <= 0 ) errAmount.textContent = "金額は0円以上で入力してください。";
  if( amount.value === "" ) errAmount.textContent = "金額を入力してください。";
  if( date.value === "" ) errDate.textContent = "日付を選択してください。";
  if( errAssetCatFrom.value === "" ) errAssetCatFrom.textContent = "移動前の資産カテゴリーを選択してください。";
  if( errAssetCatTo.value === "" ) errAssetCatTo.textContent = "移動後の資産カテゴリーを選択してください。";
  if( errAmount.textContent === "" &&
      errDate.textContent === "" &&
      errAssetCatFrom.textContent === "" &&
      errAssetCatTo.textContent === "" ) {
    const transferDatas = [
      amount.value,
      date.value,
      assetCatFrom.value,
      assetCatTo.value,
      text.value,
      trid,
      groupId.value,
      beforeAmount
    ];
    console.log(transferDatas);
    fetch('https://nk-apps.net/src/php/updateTransfer.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(transferDatas)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "cannotEditTransfer":
            alert("取引編集の権限がありません。\n取引がグループで共有されている場合、作成者か管理者である必要があります。");
            window.location.href = "https://nk-apps.net/todo/";
            break;
          case "notMatchGroup":
            errAssetCatFrom.textContent = "グループが一致しません。";
            errAssetCatTo.textContent = "グループが一致しません。";
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