/*  会員登録時に資産カテゴリーと収支カテゴリーをあらかじめいくつか用意しておく
 *  モーダルから資産カテゴリーの金額を変更した場合fromかtoの片方をNullにしてdbに登録する。
 *  SP表示でも資産カテゴリーと収支カテゴリーを編集できるようにする。
 */

const selectGroup = document.getElementById("selectGroup");
const displayAtViewer = document.getElementById("displayAtViewer");
const days = ["日", "月", "火", "水", "木", "金", "土"];
let span = "month";
let group = "self";
let summaryMode = "all";
let listMode = "expense";
let visitingTabNum = 0;
let visitingModalTabNum = 0;
let displayAt = new Date();
let startAt = new Date( displayAt );
let endAt = new Date( displayAt );
let isChratResponsive = true;
let beforeIsChartResponsive;
let today = new Date();
startAt.setDate(1);
endAt.setMonth( endAt.getMonth() + 1, 0 );

document.getElementById("expenseDate").value = today.getFullYear() + "-" + ( today.getMonth() + 1 ).toString().padStart(2, '0') + "-" + today.getDate().toString().padStart(2, '0');
document.getElementById("incomeDate").value = today.getFullYear() + "-" + ( today.getMonth() + 1 ).toString().padStart(2, '0') + "-" + today.getDate().toString().padStart(2, '0');
document.getElementById("transferDate").value = today.getFullYear() + "-" + ( today.getMonth() + 1 ).toString().padStart(2, '0') + "-" + today.getDate().toString().padStart(2, '0');

getCats();
getGroupList();
changeDisplayAt(displaySpan);

async function displayModal( mode, id ) {
  grayBack.style.display = "block";
  modal.style.display = "block";
  modal.style.animation = "displayModal 0.5s ease forwards";
  modalContent.style.animation = "displayModalContent 1.0s ease forwards";
  grayBack.style.display = "displayGrayBack 0.5s ease forwards";

  switch( mode ) {
    case "asset_cat":
      let assetUniqIds = [];

      modalTtl.textContent = "資産カテゴリー";

      modalContent.innerHTML = '\
        <details class="simple">\
          <summary>資産カテゴリーとは？</summary>\
          <div class="explanation">所有する資産の種類を表します。</div>\
        </details>\
        <div class="add-form">\
          <input type="text" id="assetName" placeholder="資産カテゴリー名">\
          <select id="assetGroup"></select>\
          <button class="m secondary" onclick="addAssetCat()">追加</button>\
        </div>\
        <div id="assetList">\
        </div>\
      ';

      const assetList = document.getElementById("assetList");
      await fetch('https://nk-apps.net/src/php/getAssetCatsFromLoginToken.php', {
        method: "POST"
      })
        .then(response => response.json())
        .then(res => {
          console.log(res);
          if( res.length == 0 ) {
            assetList.innerHTML = '<div class="explanation no-data">資産カテゴリーが登録されていません。</div>';
          } else {
            for( let i=0; i<res.length; i++ ) {
              assetUniqIds.push(res[i][0]);

              assetList.innerHTML += '\
                <div class="row">\
                  <div class="view-mode" id="viewMode' + res[i][0] + '">\
                    <div class="left-block">\
                      <div class="ttl">' + res[i][3] + '</div>\
                    <div class="group">' + res[i][7] + '</div>\
                    </div>\
                    <div class="amount">' + res[i][5] + '円</div>\
                    <button class="m secondary" id="editBtn' + res[i][0] + '" onclick="editMode(\'' + res[i][0] + '\')">編集</button>\
                  </div>\
                  <div class="edit-mode" id="editMode' + res[i][0] + '">\
                    <button class="m unstyled change-mode-btn" onclick="viewMode(\'' + res[i][0] + '\')"><i class="fa-solid fa-xmark"></i></button>\
                    <input type="text" class="asset-name" id="assetName' + res[i][0] + '" value="' + res[i][3] + '" placeholder="資産カテゴリー名">\
                    <div class="amount-container">\
                      <input type="number" class="asset-amount" id="assetAmount' + res[i][0] + '" value="' + res[i][5] + '" placeholder="金額">\
                      <div class="text">円</div>\
                    </div>\
                    <button class="m secondary" id="updateBtn' + res[i][0] + '" onclick="updateAssetCat(\'' + res[i][0] + '\')">更新</button>\
                    <button class="m danger-secondary delete-btn" id="deleteBtn' + res[i][0] + '" onclick="deleteAssetCat(\'' + res[i][0] + '\', \'' + res[i][3] + '\')"><i class="fa-solid fa-trash"></i></button>\
                  </div>\
                </div>\
              ';

              if( res[i][8] == "member" ) {
                document.getElementById("editBtn" + res[i][0]).disabled = true;
                document.getElementById("updateBtn" + res[i][0]).disabled = true;
                document.getElementById("deleteBtn" + res[i][0]).disabled = true;
              }
            }
          }
        })
        .catch(error => {
          console.error(error);
        })
        
        // Get group list
        const assetGroup = document.getElementById("assetGroup");
        await fetch('https://nk-apps.net/src/php/getGroupListByLoginToken.php', {
          method: "POST"
        })
          .then(response => response.json())
          .then(res => {
            assetGroup.innerHTML = '<option value="self">自分のみ</option>';
            for( let i=0; i<res.length; i++ ) {
              assetGroup.innerHTML += '<option value="' + res[i][0] + '">' + res[i][2] + '</option>';
            }
          })
      break;      
    case "bop_cat":
      let bopUniqIds = [];

      modalTtl.textContent = "収支カテゴリー";

      modalContent.innerHTML = '\
        <details class="simple">\
          <summary>収支カテゴリーとは？</summary>\
          <div class="explanation">収入源や費用の種類を表します。</div>\
        </details>\
        <div class="add-form">\
          <input type="text" id="bopName" placeholder="収支カテゴリー名">\
          <select id="bopGroup"></select>\
          <button class="m secondary" onclick="addBopCat()">追加</button>\
        </div>\
        <div id="bopList">\
        </div>\
      ';

      const bopList = document.getElementById("bopList");
      await fetch('https://nk-apps.net/src/php/getBopCatsFromLoginToken.php', {
        method: "POST"
      })
        .then(response => response.json())
        .then(res => {
          console.log(res);
          if( res.length == 0 ) {
            bopList.innerHTML = '<div class="explanation no-data">収支カテゴリーが登録されていません。</div>';
          } else {
            for( let i=0; i<res.length; i++ ) {
              bopUniqIds.push(res[i][0]);

              bopList.innerHTML += '\
                <div class="row">\
                  <div class="view-mode" id="viewMode' + res[i][0] + '">\
                    <div class="left-block">\
                      <div class="ttl">' + res[i][3] + '</div>\
                    <div class="group">' + res[i][6] + '</div>\
                    </div>\
                    <button class="m secondary" id="editBtn' + res[i][0] + '" onclick="editMode(\'' + res[i][0] + '\')">編集</button>\
                  </div>\
                  <div class="edit-mode" id="editMode' + res[i][0] + '">\
                    <button class="m unstyled change-mode-btn" onclick="viewMode(\'' + res[i][0] + '\')"><i class="fa-solid fa-xmark"></i></button>\
                    <input type="text" class="bop-name" id="bopName' + res[i][0] + '" value="' + res[i][3] + '" placeholder="収支カテゴリー名">\
                    <button class="m secondary" id="updateBtn' + res[i][0] + '" onclick="updateBopCat(\'' + res[i][0] + '\')">更新</button>\
                    <button class="m danger-secondary delete-btn" id="deleteBtn' + res[i][0] + '" onclick="deleteBopCat(\'' + res[i][0] + '\', \'' + res[i][3] + '\')"><i class="fa-solid fa-trash"></i></button>\
                  </div>\
                </div>\
              ';

              if( res[i][7] == "member" ) {
                document.getElementById("editBtn" + res[i][0]).disabled = true;
                document.getElementById("updateBtn" + res[i][0]).disabled = true;
                document.getElementById("deleteBtn" + res[i][0]).disabled = true;
              }
            }
          }
        })
        .catch(error => {
          console.error(error);
        })
        
        // Get group list
        const bopGroup = document.getElementById("bopGroup");
        await fetch('https://nk-apps.net/src/php/getGroupListByLoginToken.php', {
          method: "POST"
        })
          .then(response => response.json())
          .then(res => {
            bopGroup.innerHTML = '<option value="self">自分のみ</option>';
            for( let i=0; i<res.length; i++ ) {
              bopGroup.innerHTML += '<option value="' + res[i][0] + '">' + res[i][2] + '</option>';
            }
          })
      break;
    case "bop":
      modalTtl.textContent = "取引履歴";
      
      await fetch('https://nk-apps.net/src/php/getBopAllDatasByBopId.php', {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(id)
      })
        .then(response => response.json())
        .then(res => {
          console.log(res);
          modalContent.innerHTML = '\
            <div class="top">\
              <div class="basic-info">\
                <div class="ttl">' + res[1][13] + '</div>\
                <div class="amount">' + res[1][5] + '円</div>\
                <div class="date">' + res[1][7].slice(0, 16) + '</div>\
                <div class="asset-cat">' + res[1][12] + '</div>\
                <div class="text">' + res[1][6] + '</div>\
              </div>\
              <div class="btn-container">\
                <a href="https://nk-apps.net/edit-bop/?bid=' + res[1][0] + '">\
                  <button class="m secondary" id="editBtn" style="margin-bottom: var(--margin-xxs);">取引情報を編集</button>\
                </a>\
                <button class="m danger-secondary" id="deleteBtn" onclick="deleteBop(\'' + res[1][0] + '\',\'' + res[1][13] + '\')">取引を削除</button>\
              </div>\
            </div>\
            \
            <div class="normal-tab-container">\
              <div class="btns" id="bopDetailTabBtns">\
                <button class="btn visiting" onclick="changeTabModal(0)">詳細</button>\
                <button class="btn" onclick="changeTabModal(1)">メンバー</button>\
              </div>\
              <div class="contents" id="bopDetailTabContents">\
                <div class="content visiting">\
                  <table class="detail-table">\
                    <tr>\
                      <th>収支カテゴリー</th>\
                      <td>' + res[1][13] + '</td>\
                    </tr>\
                    <tr>\
                      <th>資産カテゴリー</th>\
                      <td>' + res[1][12] + '</td>\
                    </tr>\
                    <tr>\
                      <th>金額</th>\
                      <td>' + res[1][5] + '円</td>\
                    </tr>\
                    <tr>\
                      <th>日時</th>\
                      <td>' + res[1][7].slice(0, 10) + '</td>\
                    </tr>\
                    <tr>\
                      <th>グループ</th>\
                      <td>' + res[1][14] + '</td>\
                    </tr>\
                    <tr>\
                      <th>作成日時</th>\
                      <td>' + res[1][8].slice(0, 16) + '</td>\
                    </tr>\
                    <tr>\
                      <th>作成者</th>\
                      <td>' + res[1][16] + '</td>\
                    </tr>\
                    <tr>\
                      <th>最終更新日時</th>\
                      <td>' + res[1][9].slice(0, 16) + '</td>\
                    </tr>\
                    <tr>\
                      <th>最終更新者</th>\
                      <td>' + res[1][17] + '</td>\
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
          visitingModalTabNum = 0;

          const userTable = document.getElementById("userTable");
          userTable.innerHTML = "";
          if( res[1][3] === "self" ) {
            userTable.innerHTML = '<div class="no-data">この取引は他のユーザには公開されません。</div>';
          } else {
            for( let i=0; i<res[2].length; i++ ) {
              userTable.innerHTML += '\
                <tr>\
                  <td>\
                    <div class="icon">\
                      <img src="https://nk-apps.net/src/images/user-icons/' + res[2][i][0] + '/' + res[2][i][3] + '" alt="ユーザアイコン" width="40px" height="40px" onerror="this.src=\'https://nk-apps.net/src/images/no_user_images.svg\'" />\
                    </div>\
                    <div class="name">' + res[2][i][2] + '</div>\
                    <div class="id">ID: ' + res[2][i][1] + '</div>\
                  </td>\
                </tr>\
              ';
            }
          }

          if( res[1][15] == "member" ) {
            document.getElementById("editBtn").disabled = true;
            document.getElementById("deleteBtn").disabled = true;
          }
        })
        .catch(error => {
          console.error(error);
        })
      break;
    case "transfer":
      modalTtl.textContent = "移動履歴";
      
      await fetch('https://nk-apps.net/src/php/getTransferAllDatasByTransferId.php', {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(id)
      })
        .then(response => response.json())
        .then(res => {
          console.log(res);
          modalContent.innerHTML = '\
            <div class="top">\
              <div class="basic-info">\
                <div class="ttl">移動前: ' + res[1][12] + '</div>\
                <div class="ttl">移動後: ' + res[1][13] + '</div>\
                <div class="amount">' + res[1][5] + '円</div>\
                <div class="date">' + res[1][7].slice(0, 16) + '</div>\
                <div class="asset-cat">' + res[1][12] + '</div>\
                <div class="text">' + res[1][6] + '</div>\
              </div>\
              <div class="btn-container">\
                <a href="https://nk-apps.net/edit-transfer/?trid=' + res[1][0] + '">\
                  <button class="m secondary" id="editBtn" style="margin-bottom: var(--margin-xxs);">移動履歴を編集</button>\
                </a>\
                <button class="m danger-secondary" id="deleteBtn" onclick="deleteTransfer(\'' + res[1][0] + '\', \'' + res[1][13] + '\', \'' + res[1][5] + '\')">移動履歴を削除</button>\
              </div>\
            </div>\
            \
            <div class="normal-tab-container">\
              <div class="btns" id="bopDetailTabBtns">\
                <button class="btn visiting" onclick="changeTabModal(0)">詳細</button>\
                <button class="btn" onclick="changeTabModal(1)">メンバー</button>\
              </div>\
              <div class="contents" id="bopDetailTabContents">\
                <div class="content visiting">\
                  <table class="detail-table">\
                    <tr>\
                      <th>移動前</th>\
                      <td>' + res[1][12] + '</td>\
                    </tr>\
                    <tr>\
                      <th>移動後</th>\
                      <td>' + res[1][13] + '</td>\
                    </tr>\
                    <tr>\
                      <th>金額</th>\
                      <td>' + res[1][5] + '円</td>\
                    </tr>\
                    <tr>\
                      <th>日時</th>\
                      <td>' + res[1][7].slice(0, 10) + '</td>\
                    </tr>\
                    <tr>\
                      <th>グループ</th>\
                      <td>' + res[1][14] + '</td>\
                    </tr>\
                    <tr>\
                      <th>作成日時</th>\
                      <td>' + res[1][8].slice(0, 16) + '</td>\
                    </tr>\
                    <tr>\
                      <th>作成者</th>\
                      <td>' + res[1][16] + '</td>\
                    </tr>\
                    <tr>\
                      <th>最終更新日時</th>\
                      <td>' + res[1][9].slice(0, 16) + '</td>\
                    </tr>\
                    <tr>\
                      <th>最終更新者</th>\
                      <td>' + res[1][17] + '</td>\
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
          visitingModalTabNum = 0;

          const userTable = document.getElementById("userTable");
          userTable.innerHTML = "";
          if( res[1][3] === "self" ) {
            userTable.innerHTML = '<div class="no-data">この移動履歴は他のユーザには公開されません。</div>';
          } else {
            for( let i=0; i<res[2].length; i++ ) {
              userTable.innerHTML += '\
                <tr>\
                  <td>\
                    <div class="icon">\
                      <img src="https://nk-apps.net/src/images/user-icons/' + res[2][i][0] + '/' + res[2][i][3] + '" alt="ユーザアイコン" width="40px" height="40px" onerror="this.src=\'https://nk-apps.net/src/images/no_user_images.svg\'" />\
                    </div>\
                    <div class="name">' + res[2][i][2] + '</div>\
                    <div class="id">ID: ' + res[2][i][1] + '</div>\
                  </td>\
                </tr>\
              ';
            }
          }

          if( res[1][15] == "member" ) {
            document.getElementById("editBtn").disabled = true;
            document.getElementById("deleteBtn").disabled = true;
          }
        })
        .catch(error => {
          console.error(error);
        })
      break;
  }
}

async function addAssetCat() {
  const assetName = document.getElementById("assetName");
  const assetGroup = document.getElementById("assetGroup");

  if( assetName.value == "" ) {
    alert("資産カテゴリー名を入力してください。");
  } else {
    const assetDatas = [
      assetName.value,
      assetGroup.value
    ];
    await fetch('https://nk-apps.net/src/php/addAssetCat.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(assetDatas)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "success":
            assetName.value = "";

            displayModal("asset_cat", "");
            break;
        }
      })
      .catch(error => {
        console.error(error);
      })
  }
}

function editMode(id) {
  document.getElementById("viewMode" + id).style.display = "none";
  document.getElementById("editMode" + id).style.display = "flex";
}

function viewMode(id) {
  document.getElementById("viewMode" + id).style.display = "flex";
  document.getElementById("editMode" + id).style.display = "none";
}

async function deleteAssetCat( id, name ) {
  if( confirm("[" + name + "] を削除します。\nよろしいですか？") ) {
    await fetch('https://nk-apps.net/src/php/deleteAssetCat.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(id)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "notExistAssetCat":
            alert("対象の資産カテゴリーが存在しません。");
            break;
          case "cannotDeleteAssetCat":
            alert("資産カテゴリー削除の権限がありません。\n資産カテゴリーがグループで共有されている場合、管理者である必要があります。");
            break;
          case "success":
            alert("削除しました。");
            displayModal("asset_cat", "");
            break;
        }
      })
      .catch(error => {
        console.error(error);
      })
  }
}

async function updateAssetCat(id) {
  const assetName = document.getElementById("assetName" + id);
  const assetAmount = document.getElementById("assetAmount" + id);
  let errFlag = true;

  if( assetName.value === "" ) {
    alert("資産カテゴリー名を入力してください。");
    errFlag = false;
  }
  if( assetAmount.value < 0 ) {
    alert("金額は0円以上で入力してください。");
    errFlag = false;
  }
  if( assetAmount.value === "" ) {
    alert("金額を入力してください。");
    errFlag = false;
  }
  if( errFlag === true ) {
    const assetCatDatas = [
      id,
      assetName.value,
      assetAmount.value
    ];
    console.log(assetCatDatas);
    await fetch('https://nk-apps.net/src/php/updateAssetCat.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(assetCatDatas)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "notExistAssetCat":
            alert("対象の資産カテゴリーが存在しません。");
            break;
          case "cannotDeleteAssetCat":
            alert("資産カテゴリー編集の権限がありません。\n資産カテゴリーがグループで共有されている場合、管理者である必要があります。");
            break;
          case "success":
            alert("更新しました。");
            displayModal("asset_cat", "");
            break;
        }
      })
      .catch(error => {
        console.error(error);
      })
  }
}

function closeModal() {
  modal.style.animation = "closeModal 1.0s ease forwards";
  modalContent.style.animation = "closeModalContent 0.5s ease forwards";
  grayBack.style.display = "closeGrayBack 0.5s ease forwards";
  grayBack.style.display = "none";
  modalContent.innerHTML = '';
}

async function addBopCat() {
  const bopName = document.getElementById("bopName");
  const bopGroup = document.getElementById("bopGroup");

  if( bopName.value == "" ) {
    alert("収支カテゴリー名を入力してください。");
  } else {
    const bopDatas = [
      bopName.value,
      bopGroup.value
    ];
    await fetch('https://nk-apps.net/src/php/addBopCat.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(bopDatas)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "success":
            bopName.value = "";

            displayModal("bop_cat", "");
            break;
        }
      })
      .catch(error => {
        console.error(error);
      })
  }
}

async function deleteBopCat( id, name ) {
  if( confirm("[" + name + "] を削除します。\nよろしいですか？") ) {
    await fetch('https://nk-apps.net/src/php/deleteBopCat.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(id)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "notExistBopCat":
            alert("対象の収支カテゴリーが存在しません。");
            break;
          case "cannotDeleteBopCat":
            alert("収支カテゴリー削除の権限がありません。\n収支カテゴリーがグループで共有されている場合、管理者である必要があります。");
            break;
          case "success":
            alert("削除しました。");
            displayModal("bop_cat", "");
            break;
        }
      })
      .catch(error => {
        console.error(error);
      })
  }
}

async function updateBopCat(id) {
  const bopName = document.getElementById("bopName" + id);
  let errFlag = true;

  if( bopName.value === "" ) {
    alert("収支カテゴリー名を入力してください。");
    errFlag = false;
  }
  if( errFlag === true ) {
    const bopCatDatas = [
      id,
      bopName.value
    ];
    await fetch('https://nk-apps.net/src/php/updateBopCat.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(bopCatDatas)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "notExistBopCat":
            alert("対象の収支カテゴリーが存在しません。");
            break;
          case "cannotDeleteBopCat":
            alert("収支カテゴリー編集の権限がありません。\n収支カテゴリーがグループで共有されている場合、管理者である必要があります。");
            break;
          case "success":
            alert("更新しました。");
            displayModal("bop_cat", "");
            break;
        }
      })
      .catch(error => {
        console.error(error);
      })
  }
}

async function getCats() {
  const expenseAssetCat = document.getElementById("expenseAssetCat");
  const expenseBopCat = document.getElementById("expenseBopCat");
  const incomeAssetCat = document.getElementById("incomeAssetCat");
  const incomeBopCat = document.getElementById("incomeBopCat");
  const transferAssetCatFrom = document.getElementById("transferAssetCatFrom");
  const transferAssetCatTo = document.getElementById("transferAssetCatTo");

  await fetch('https://nk-apps.net/src/php/getCatsByLoginToken.php', {
    method: "POST"
  })
    .then(response => response.json())
    .then(res => {
      // res[0] => asset cat list
      // res[1] => bop cat list

      for( let i=0; i<res[0].length; i++ ) {
        expenseAssetCat.innerHTML += '<option value="' + res[0][i][0] + '">' + res[0][i][3] + ' (' + res[0][i][7] + ')</option>';
        incomeAssetCat.innerHTML += '<option value="' + res[0][i][0] + '">' + res[0][i][3] + ' (' + res[0][i][7] + ')</option>';
        transferAssetCatFrom.innerHTML += '<option value="' + res[0][i][0] + '">' + res[0][i][3] + ' (' + res[0][i][7] + ')</option>';
        transferAssetCatTo.innerHTML += '<option value="' + res[0][i][0] + '">' + res[0][i][3] + ' (' + res[0][i][7] + ')</option>';
      }

      for( let i=0; i<res[1].length; i++ ) {
        expenseBopCat.innerHTML += '<option value="' + res[1][i][0] + '">' + res[1][i][3] + ' (' + res[1][i][6] + ')</option>';
        incomeBopCat.innerHTML += '<option value="' + res[1][i][0] + '">' + res[1][i][3] + ' (' + res[1][i][6] + ')</option>';
      }
    })
    .catch(error => {
      console.error(error);
    })
}

function checkCompleteForm( type ) {
  switch( type ) {
    case "expense":
      const expenseAmount = document.getElementById("expenseAmount");
      const expenseDate = document.getElementById("expenseDate");
      const expenseAssetCat = document.getElementById("expenseAssetCat");
      const expenseBopCat = document.getElementById("expenseBopCat");
      const expenseText = document.getElementById("expenseText");
      const addExpenseBtn = document.getElementById("addExpenseBtn");

      if( expenseAmount.value != "" &&
          expenseDate.value != "" &&
          expenseAssetCat.value != "" &&
          expenseBopCat.value != "" ) {
        addExpenseBtn.disabled = false;
      } else {
        addExpenseBtn.disabled = true;
      }
      break;
    case "income":
      const incomeAmount = document.getElementById("incomeAmount");
      const incomeDate = document.getElementById("incomeDate");
      const incomeAssetCat = document.getElementById("incomeAssetCat");
      const incomeBopCat = document.getElementById("incomeBopCat");
      const incomeText = document.getElementById("incomeText");
      const addIncomeBtn = document.getElementById("addIncomeBtn");

      if( incomeAmount.value != "" &&
          incomeDate.value != "" &&
          incomeAssetCat.value != "" &&
          incomeBopCat.value != "" ) {
        addIncomeBtn.disabled = false;
      } else {
        addIncomeBtn.disabled = true;
      }
      break;
    case "transfer":
      const transferAmount = document.getElementById("transferAmount");
      const transferDate = document.getElementById("transferDate");
      const transferAssetCatFrom = document.getElementById("transferAssetCatFrom");
      const transferAssetCatTo = document.getElementById("transferAssetCatTo");
      const transferText = document.getElementById("transferText");
      const addTransferBtn = document.getElementById("addTransferBtn");

      if( transferAmount.value != "" &&
          transferDate.value != "" &&
          transferAssetCatFrom.value != "" &&
          transferAssetCatTo.value != "" ) {
            addTransferBtn.disabled = false;
      } else {
        addTransferBtn.disabled = true;
      }
      break;
  }
}

function changeTab( tabNum ) {
  const appFormTabContents = document.getElementById("appFormTabContents");
  const appFormTabBtns = document.getElementById("appFormTabBtns");

  appFormTabBtns.children[visitingTabNum].classList.remove("visiting");
  appFormTabBtns.children[tabNum].classList.add("visiting");
  appFormTabContents.children[visitingTabNum].classList.remove("visiting");
  appFormTabContents.children[tabNum].classList.add("visiting");

  visitingTabNum = tabNum;
}

function changeTabModal( modalTabNum ) {
  const bopDetailTabContents = document.getElementById("bopDetailTabContents");
  const bopDetailTabBtns = document.getElementById("bopDetailTabBtns");

  bopDetailTabBtns.children[visitingModalTabNum].classList.remove("visiting");
  bopDetailTabBtns.children[modalTabNum].classList.add("visiting");
  bopDetailTabContents.children[visitingModalTabNum].classList.remove("visiting");
  bopDetailTabContents.children[modalTabNum].classList.add("visiting");

  visitingModalTabNum = modalTabNum;
}

async function addExpense() {
  const expenseAmount = document.getElementById("expenseAmount");
  const expenseDate = document.getElementById("expenseDate");
  const expenseAssetCat = document.getElementById("expenseAssetCat");
  const expenseBopCat = document.getElementById("expenseBopCat");
  const expenseText = document.getElementById("expenseText");
  const errExpenseAmount = document.getElementById("errExpenseAmount");
  const errExpenseDate = document.getElementById("errExpenseDate");
  const errExpenseAssetCat = document.getElementById("errExpenseAssetCat");
  const errExpenseBopCat = document.getElementById("errExpenseBopCat");
  const errExpenseText = document.getElementById("errExpenseText");

  errExpenseAmount.textContent = "";
  errExpenseDate.textContent = "";
  errExpenseAssetCat.textContent = "";
  errExpenseBopCat.textContent = "";
  errExpenseText.textContent = "";

  if( expenseAmount.value <= 0 ) errExpenseAmount.textContent = "金額は0円以上で入力してください。";
  if( expenseAmount.value == "" ) errExpenseAmount.textContent = "金額を入力してください。";
  if( expenseDate.value == "" ) errExpenseDate.textContent = "日付を選択してください。";
  if( expenseAssetCat.value == "" ) errExpenseAssetCat.textContent = "資産カテゴリーを選択してください。";
  if( expenseBopCat.value == "" ) errExpenseBopCat.textContent = "収支カテゴリーを選択してください。";
  if( errExpenseAmount.textContent == "" &&
      errExpenseDate.textContent == "" &&
      errExpenseAssetCat.textContent == "" &&
      errExpenseBopCat.textContent == "" ) {
    const expenseDatas = [
      expenseAmount.value,
      expenseDate.value,
      expenseAssetCat.value,
      expenseBopCat.value,
      expenseText.value
    ];
    console.log(expenseDatas);
    await fetch('https://nk-apps.net/src/php/addExpense.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(expenseDatas)
    })
      .then(response => response.json())
      .then(res => {
        console.log(res);
        switch( res ) {
          case "notEnoughAmount":
            errExpenseAmount.textContent = "金額が足りません。";
            break;
          case "notMatchGroup":
            errExpenseAssetCat.textContent = "グループが一致しません。";
            errExpenseBopCat.textContent = "グループが一致しません。";
            break;
          case "success":
            alert("登録しました。");

            expenseAmount.value = "";
            displayCharts();
            break;
        }
      })
      .catch(error => {
        console.error(error);
      })
  }
}

async function addIncome() {
  const incomeAmount = document.getElementById("incomeAmount");
  const incomeDate = document.getElementById("incomeDate");
  const incomeAssetCat = document.getElementById("incomeAssetCat");
  const incomeBopCat = document.getElementById("incomeBopCat");
  const incomeText = document.getElementById("incomeText");
  const errIncomeAmount = document.getElementById("errIncomeAmount");
  const errIncomeDate = document.getElementById("errIncomeDate");
  const errIncomeAssetCat = document.getElementById("errIncomeAssetCat");
  const errIncomeBopCat = document.getElementById("errIncomeBopCat");
  const errIncomeText = document.getElementById("errIncomeText");

  errIncomeAmount.textContent = "";
  errIncomeDate.textContent = "";
  errIncomeAssetCat.textContent = "";
  errIncomeBopCat.textContent = "";
  errIncomeText.textContent = "";

  if( incomeAmount.value <= 0 ) errIncomeAmount.textContent = "金額は0円以上で入力してください。";
  if( incomeAmount.value == "" ) errIncomeAmount.textContent = "金額を入力してください。";
  if( incomeDate.value == "" ) errIncomeDate.textContent = "日付を選択してください。";
  if( incomeAssetCat.value == "" ) errIncomeAssetCat.textContent = "資産カテゴリーを選択してください。";
  if( incomeBopCat.value == "" ) errIncomeBopCat.textContent = "収支カテゴリーを選択してください。";
  if( errIncomeAmount.textContent == "" &&
      errIncomeDate.textContent == "" &&
      errIncomeAssetCat.textContent == "" &&
      errIncomeBopCat.textContent == "" ) {
    const incomeDatas = [
      incomeAmount.value,
      incomeDate.value,
      incomeAssetCat.value,
      incomeBopCat.value,
      incomeText.value
    ];
    console.log(incomeDatas);
    await fetch('https://nk-apps.net/src/php/addIncome.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(incomeDatas)
    })
      .then(response => response.json())
      .then(res => {
        console.log(res);
        switch( res ) {
          case "notMatchGroup":
            errIncomeAssetCat.textContent = "グループが一致しません。";
            errIncomeBopCat.textContent = "グループが一致しません。";
            break;
          case "success":
            alert("登録しました。");

            incomeAmount.value = "";
            displayCharts();
            break;
        }
      })
      .catch(error => {
        console.error(error);
      })
  }
}

async function addTransfer() {
  const transferAmount = document.getElementById("transferAmount");
  const transferDate = document.getElementById("transferDate");
  const transferAssetCatFrom = document.getElementById("transferAssetCatFrom");
  const transferAssetCatTo = document.getElementById("transferAssetCatTo");
  const transferText = document.getElementById("transferText");
  const errTransferAmount = document.getElementById("errTransferAmount");
  const errTransferDate = document.getElementById("errTransferDate");
  const errTransferAssetCat = document.getElementById("errTransferAssetCat");
  const errTransferText = document.getElementById("errTransferText");

  errTransferAmount.textContent = "";
  errTransferDate.textContent = "";
  errTransferAssetCat.textContent = "";
  errTransferText.textContent = "";

  if( transferAmount.value <= 0 ) errTransferAmount.textContent = "金額は0円以上で入力してください。";
  if( transferAmount.value == "" ) errTransferAmount.textContent = "金額を入力してください。";
  if( transferDate.value == "" ) errTransferDate.textContent = "日付を選択してください。";
  if( transferAssetCatFrom.value === transferAssetCatTo.value ) errTransferAssetCat.textcontent = "異なる資産カテゴリーを選択してください。";
  if( transferAssetCatFrom.value == "" ) errTransferAssetCatFrom.textContent = "資産カテゴリーを選択してください。";
  if( transferAssetCatTo.value == "" ) errTransferAssetCatTo.textContent = "資産カテゴリーを選択してください。";
  if( errTransferAmount.textContent == "" &&
      errTransferDate.textContent == "" &&
      errTransferAssetCat.textContent == "" ) {
    const transferDatas = [
      transferAmount.value,
      transferDate.value,
      transferAssetCatFrom.value,
      transferAssetCatTo.value,
      transferText.value
    ];
    console.log(transferDatas);
    await fetch('https://nk-apps.net/src/php/addTransfer.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(transferDatas)
    })
      .then(response => response.json())
      .then(res => {
        console.log(res);
        switch( res ) {
          case "notEnoughAmount":
            errTransferAmount.textContent = "金額が足りません。";
            break;
          case "notMatchGroup":
            errTransferAssetCat.textContent = "グループが一致しません。";
            break;
          case "success":
            alert("登録しました。");

            transferAmount.value = "";
            displayCharts();
            break;
        }
      })
      .catch(error => {
        console.error(error);
      })
  }
}

async function getGroupList() {
  selectGroup.innerHTML = '<option value="self">自分のみ</option>';
  await fetch('https://nk-apps.net/src/php/getGroupListByLoginToken.php')
    .then(response => response.json())
    .then(res => {
      for( let i=0; i<res.length; i++ ) {
        selectGroup.innerHTML += '<option value="' + res[i][0] + '">' + res[i][2] + '</option>';
      }
    })
}

function changeGroup() {
  group = document.getElementById("selectGroup").value;

  displayCharts();
}

function changeSpan() {
  span = document.getElementById("displaySpan").value;

  changeDisplayAt( null );
  displayCharts();
}

function changeSummaryMode() {
  summaryMode = document.getElementById("summaryMode").value;

  displayCharts();
}

function changeListMode() {
  listMode = document.getElementById("listMode").value;

  displayCharts();
}

async function displayCharts() {
  console.log("==============================");
  const summaryExpense = document.getElementById("summaryExpense");
  const summaryIncome = document.getElementById("summaryIncome");
  const summaryDiff = document.getElementById("summaryDiff");
  const summaryTotal = document.getElementById("summaryTotal");
  const summary = document.getElementById("summary");
  const summaryNoDatas = document.getElementById("summaryNoDatas");
  const pieChartExpense = document.getElementById("pieChartExpense");
  const pieChartIncome = document.getElementById("pieChartIncome");
  const pieChartNoExpenseDatas = document.getElementById("pieChartNoExpenseDatas");
  const pieChartNoIncomeDatas = document.getElementById("pieChartNoIncomeDatas");
  const bopList = document.getElementById("bopList");
  let sumExpense = 0;
  let sumIncome = 0;
  let summaryLabels = [];
  let summaryExpenseDatas = [];
  let summaryIncomeDatas = [];
  let summaryDatasets = [];
  let tmpPieChartExpenseUniqIds = [];
  let tmpPieChartIncomeUniqIds = [];
  let pieChartExpenseLabels = [];
  let pieChartIncomeLabels = [];
  let pieChartExpenseDatas = [];
  let pieChartIncomeDatas = [];
  let pieChartExpenseDatasets = [];

  if( span == "date" ) {
    startAt = new Date(displayAt.getFullYear() + "-" + ( displayAt.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayAt.getDate().toString().padStart(2, '0'));
    endAt = new Date(displayAt.getFullYear() + "-" + ( displayAt.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayAt.getDate().toString().padStart(2, '0'));
    startAt.setDate(1);
    endAt.setMonth( endAt.getMonth() + 1, 0 );
  }

  // Get bop datas
  const datas = [
    startAt.getFullYear() + "-" + ( startAt.getMonth() + 1 ) + "-" + startAt.getDate(),
    endAt.getFullYear() + "-" + ( endAt.getMonth() + 1 ) + "-" + endAt.getDate(),
    group
  ];
  console.log(datas);
  await fetch('https://nk-apps.net/src/php/getBopDatasBySpanAndGroup.php', {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify(datas)
  })
    .then(response => response.json())
    .then(res => {
      const notFoundStartSample = new Date("9999-12-31");
      const notFoundEndSample = new Date("1000-01-01");
      let allBopList = [];
      let tmpDate;
      let tmpExpenseStart = new Date("9999-12-31");
      let tmpExpenseEnd = new Date("1000-01-01");
      let tmpIncomeStart = new Date("9999-12-31");
      let tmpIncomeEnd = new Date("1000-01-01");

      if( res[3] === null ) res[3] = 0;

      console.log(res);

      // Set summary labels
      if( res[0].length != 0 ) {
        tmpExpenseStart = new Date( res[0][0][7] );
        tmpExpenseEnd = new Date( res[0][res[0].length - 1][7] );
      }
      if( res[1].length != 0 ) {
        tmpIncomeStart = new Date( res[1][0][7] );
        tmpIncomeEnd = new Date( res[1][res[1].length - 1][7] );
      }
      const summaryLabelStartAt = new Date( tmpExpenseStart < tmpIncomeStart ? tmpExpenseStart.getTime() : tmpIncomeStart.getTime() );
      const summaryLabelEndAt = new Date( tmpExpenseEnd > tmpIncomeEnd ? tmpExpenseEnd.getTime() : tmpIncomeEnd.getTime() );

      if( summaryLabelStartAt != notFoundStartSample ) {
        switch( span ) {
          case "all":
            tmpDate = new Date( summaryLabelStartAt.getTime() );
            summaryLabels.push(summaryLabelStartAt.getFullYear() + "年" );
            tmpDate.setFullYear( tmpDate.getFullYear() + 1 );
  
            while( tmpDate <= summaryLabelEndAt ) {
              summaryLabels.push(tmpDate.getFullYear() + "年" );
              tmpDate.setFullYear( tmpDate.getFullYear() + 1 );
            }
            break;
          case "year":
            tmpDate = new Date( summaryLabelStartAt.getTime() );
            tmpDate.setMonth( 0 );
            summaryLabels.push(tmpDate.getFullYear() + "年" + ( tmpDate.getMonth() + 1 ).toString().padStart(2, '0') + "月" );
            tmpDate.setMonth( tmpDate.getMonth() + 1 );
  
            for( let i=0; i<12; i++ ) {
              summaryLabels.push(tmpDate.getFullYear() + "年" + ( tmpDate.getMonth() + 1 ).toString().padStart(2, '0') + "月" );
              tmpDate.setMonth( tmpDate.getMonth() + 1 );
            }
            break;
          case "month":
          case "date":
            let lastDate = new Date( summaryLabelStartAt.getTime() );
            tmpDate = new Date( summaryLabelStartAt.getTime() );
            tmpDate.setDate(1);
            summaryLabels.push(tmpDate.getFullYear() + "年" + ( tmpDate.getMonth() + 1 ).toString().padStart(2, '0') + "月" + tmpDate.getDate().toString().padStart(2, '0') + "日" );
            tmpDate.setDate( tmpDate.getDate() + 1 );
            lastDate.setMonth( endAt.getMonth() + 1, 0 );
  
            for( let i=0; i<lastDate.getDate(); i++ ) {
              summaryLabels.push(tmpDate.getFullYear() + "年" + ( tmpDate.getMonth() + 1 ).toString().padStart(2, '0') + "月" + tmpDate.getDate().toString().padStart(2, '0') + "日" );
              tmpDate.setDate( tmpDate.getDate() + 1 );
            }
            break;
        }
      } else {
        summaryLabels = ["no-data"];
      }

      for( let i=0; i<summaryLabels.length; i++ ) {
        summaryExpenseDatas.push(0);
        summaryIncomeDatas.push(0);
      }

      // Calc expense datas
      for( let i=0; i<res[0].length; i++ ) {
        sumExpense += Number(res[0][i][5]);
        if( tmpPieChartExpenseUniqIds.indexOf(res[0][i][2]) != -1 ) {
          pieChartExpenseDatas[tmpPieChartExpenseUniqIds.indexOf(res[0][i][2])] += Number(res[0][i][5]);
        } else {
          tmpPieChartExpenseUniqIds.push(res[0][i][2]);
          pieChartExpenseDatas.push(Number(res[0][i][5]));
          pieChartExpenseLabels.push(res[0][i][9]);
        }

        switch( span ) {
          case "all":
            summaryExpenseDatas[summaryLabels.indexOf(res[0][i][7].slice(0, 4) + "年")] += Number(res[0][i][5]);
            break;
          case "year":
            summaryExpenseDatas[summaryLabels.indexOf(res[0][i][7].slice(0, 4) + "年" + res[0][i][7].slice(5, 7) + "月")] += Number(res[0][i][5]);
            break;
          case "month":
          case "date":
            summaryExpenseDatas[summaryLabels.indexOf(res[0][i][7].slice(0, 4) + "年" + res[0][i][7].slice(5, 7) + "月" + res[0][i][7].slice(8, 10) + "日")] += Number(res[0][i][5]);
            break;
        }
      }

      // Calc income datas
      for( let i=0; i<res[1].length; i++ ) {
        sumIncome += Number(res[1][i][5]);

        if( tmpPieChartIncomeUniqIds.indexOf(res[1][i][2]) != -1 ) {
          pieChartIncomeDatas[tmpPieChartIncomeUniqIds.indexOf(res[1][i][2])] += Number(res[1][i][5]);
        } else {
          tmpPieChartIncomeUniqIds.push(res[1][i][2]);
          pieChartIncomeDatas.push(Number(res[1][i][5]));
          pieChartIncomeLabels.push(res[1][i][9]);
        }

        switch( span ) {
          case "all":
            if( res[1].length == 0 ) {
              summaryIncomeDatas[summaryLabels.indexOf(res[1][i][7].slice(0, 4) + "年")] += Number(res[1][i][5]);
            } else {
              summaryIncomeDatas[summaryLabels.indexOf(res[1][i][7].slice(0, 4) + "年")] += Number(res[1][i][5]);
            }
            break;
          case "year":
            if( res[1].length == 0 ) {
              summaryIncomeDatas[summaryLabels.indexOf(res[1][i][7].slice(0, 4) + "年" + res[1][i][7].slice(5, 7) + "月")] += Number(res[1][i][5]);
            } else {
              summaryIncomeDatas[summaryLabels.indexOf(res[1][i][7].slice(0, 4) + "年" + res[1][i][7].slice(5, 7) + "月")] += Number(res[1][i][5]);
            }
            break;
          case "month":
          case "date":
            if( res[0].length == 0 ) {
              summaryIncomeDatas[summaryLabels.indexOf(res[1][i][7].slice(0, 4) + "年" + res[1][i][7].slice(5, 7) + "月" + res[1][i][7].slice(8, 10) + "日")] += Number(res[1][i][5]);
            } else {
              summaryIncomeDatas[summaryLabels.indexOf(res[1][i][7].slice(0, 4) + "年" + res[1][i][7].slice(5, 7) + "月" + res[1][i][7].slice(8, 10) + "日")] += Number(res[1][i][5]);
            }
            break;
        }
      }

      // Calc transfer datas
      for( let i=0; i<res[2].length; i++ ) {
        
      }

      summaryExpense.textContent = sumExpense + "円";
      summaryIncome.textContent = sumIncome + "円";
      summaryDiff.textContent = ( sumIncome - sumExpense ) + "円";
      summaryTotal.textContent = res[3] + "円";

      if( res[0].length == 0 && res[1].length == 0 ) {
        summary.style.display = "none";
        summaryNoDatas.style.display = "block";
        pieChartExpense.style.display = "none";
        pieChartIncome.style.display = "none";
        pieChartNoExpenseDatas.style.display = "block";
        pieChartNoIncomeDatas.style.display = "block";
        bopList.innerHTML = '<div class="no-datas">データが登録されていません。</div>';
      } else {
        summary.style.display = "block";
        summaryNoDatas.style.display = "none";
        pieChartExpense.style.display = "block";
        pieChartIncome.style.display = "block";
        pieChartNoExpenseDatas.style.display = "none";
        pieChartNoIncomeDatas.style.display = "none";
        bopList.innerHTML = "";

        // Display summary
        switch( summaryMode ) {
          case "all":
            summaryDatasets = [
              {
                label: "支出",
                data: summaryExpenseDatas,
                borderColor: "#dd2000",
                backgroundColor: "#ffffff00"
              },
              {
                label: "収入",
                data: summaryIncomeDatas,
                borderColor: "#0066ff",
                backgroundColor: "#ffffff00"
              }
            ];
            break;
          case "income":
            summaryDatasets = [
              {
                label: "収入",
                data: summaryIncomeDatas,
                borderColor: "#0066ff",
                backgroundColor: "#ffffff00"
              }
            ];
            break;
          case "expense":
            summaryDatasets = [
              {
                label: "支出",
                data: summaryExpenseDatas,
                borderColor: "#dd2000",
                backgroundColor: "#ffffff00"
              }
            ];
            break;
        }

        if (typeof summaryInstance !== 'undefined' && summaryInstance) {
          summaryInstance.destroy();
        }
        window.summaryInstance = new Chart(summary, {
          type: 'line',
          data: {
            labels: summaryLabels,
            datasets: summaryDatasets
          },
          options: {
            responsive: isChratResponsive,
            maintainAspectRatio: false,
          }
        })

        // Display pie chart
        if( res[0].length === 0 ) {
          pieChartExpense.style.display = "none";
          pieChartNoExpenseDatas.style.display = "block";
        } else {
          pieChartExpenseDatasets = [
            {
              data: pieChartExpenseDatas
            }     
          ];
          // Display pie chart
          if (typeof pieChartExpenseInstance !== 'undefined' && pieChartExpenseInstance) {
            pieChartExpenseInstance.destroy();
          }
          window.pieChartExpenseInstance = new Chart(pieChartExpense, {
            type: 'pie',
            data: {
              labels: pieChartExpenseLabels,
              datasets: pieChartExpenseDatasets
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                colorschemes: {
                    scheme: 'brewer.Paired12'
                }
              }
            }
          })
        }
        
        if( res[1].length === 0 ) {
          pieChartIncome.style.display = "none";
          pieChartNoIncomeDatas.style.display = "block";
        } else {
          pieChartIncomeDatasets = [
            {
              data: pieChartIncomeDatas
            }     
          ];
          if (typeof pieChartIncomeInstance !== 'undefined' && pieChartIncomeInstance) {
            pieChartIncomeInstance.destroy();
          }
          window.pieChartIncomeInstance = new Chart(pieChartIncome, {
            type: 'pie',
            data: {
              labels: pieChartIncomeLabels,
              datasets: pieChartIncomeDatasets
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                colorschemes: {
                    scheme: 'brewer.Paired12'
                }
              }
            }
          })
        }
      }

      // Display bop list
      bopList.innerHTML = "";
      switch( listMode ) {
        case "expense":
          if( res[0].length === 0 ) {
            bopList.innerHTML = '<div class="no-datas">データが登録されていません。</div>';
          } else {
            for( let i=0; i<res[0].length; i++ ) {
              bopList.innerHTML += '\
                <div class="row" onclick="displayModal(\'bop\', \'' + res[0][i][0] + '\')">\
                  <div class="left-block">\
                    <div class="bop-name">' + res[0][i][9] + '</div>\
                    <div class="date">' + res[0][i][7] + '</div>\
                  </div>\
                  <div class="amount">' + res[0][i][5] + '円</div>\
                </div>\
              ';
            }
          }
          break;
        case "income":
          if( res[1].length === 0 ) {
            bopList.innerHTML = '<div class="no-datas">データが登録されていません。</div>';
          } else {
            for( let i=0; i<res[1].length; i++ ) {
              bopList.innerHTML += '\
                <div class="row" onclick="displayModal(\'bop\', \'' + res[1][i][0] + '\')">\
                  <div class="left-block">\
                    <div class="bop-name">' + res[1][i][9] + '</div>\
                    <div class="date">' + res[1][i][7] + '</div>\
                  </div>\
                  <div class="amount">' + res[1][i][5] + '円</div>\
                </div>\
              ';
            }
          }
          break;
        case "transfer": 
          if( res[2].length === 0 ) {
            bopList.innerHTML = '<div class="no-datas">データが登録されていません。</div>';
          } else {
            for( let i=0; i<res[2].length; i++ ) {
              bopList.innerHTML += '\
                <div class="row" onclick="displayModal(\'transfer\', \'' + res[2][i][0] + '\')">\
                  <div class="left-block">\
                    <div class="bop-name">' + res[2][i][7] + '</div>\
                    <div class="date">' + res[2][i][6] + '</div>\
                  </div>\
                  <div class="amount">-' + res[2][i][4] + '円</div>\
                </div>\
              ';
              bopList.innerHTML += '\
                <div class="row" onclick="displayModal(\'transfer\', \'' + res[2][i][0] + '\')">\
                  <div class="left-block">\
                    <div class="bop-name">' + res[2][i][8] + '</div>\
                    <div class="date">' + res[2][i][6] + '</div>\
                  </div>\
                  <div class="amount">+' + res[2][i][4] + '円</div>\
                </div>\
              ';
            }
          }
          break;
      }
    })
    .catch(error => {
      console.error(error);
    })
}

function changeDisplayAt( mode ) {
  // Calc date.
  switch( mode ) {
    case "before":
      switch ( span ) {
        case "date":
          displayAt.setDate( displayAt.getDate() - 1 );
          break;
        case "month":
          displayAt.setMonth( displayAt.getMonth() - 1 );
          break;
        case "year":
          displayAt.setFullYear( displayAt.getFullYear() - 1 );
          break;
      }
      break;
    case "after":
      switch ( span ) {
        case "date":
          displayAt.setDate( displayAt.getDate() + 1 );
          break;
        case "month":
          displayAt.setMonth( displayAt.getMonth() + 1 );
          break;
        case "year":
          displayAt.setFullYear( displayAt.getFullYear() + 1 );
          break;
      }
      break;
  }

  switch( span ) {
    case "date":
      displayAtViewer.textContent = displayAt.getFullYear() + "年" + ( displayAt.getMonth() + 1 ).toString().padStart(2, '0') + "月" + displayAt.getDate().toString().padStart(2, '0') + "日(" + days[displayAt.getDay()] + ")";
      startAt = new Date(displayAt.getFullYear() + "-" + ( displayAt.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayAt.getDate().toString().padStart(2, '0'));
      endAt = new Date(displayAt.getFullYear() + "-" + ( displayAt.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayAt.getDate().toString().padStart(2, '0'));
      break;
    case "month":
      displayAtViewer.textContent = displayAt.getFullYear() + "年" + ( displayAt.getMonth() + 1 ).toString().padStart(2, '0') + "月";
      startAt = new Date(displayAt.getFullYear() + "-" + ( displayAt.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayAt.getDate().toString().padStart(2, '0'));
      endAt = new Date(displayAt.getFullYear() + "-" + ( displayAt.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayAt.getDate().toString().padStart(2, '0'));
      startAt.setDate(1);
      endAt.setMonth( endAt.getMonth() + 1, 0 );
      break;
    case "year":
      displayAtViewer.textContent = displayAt.getFullYear() + "年";
      startAt = new Date(displayAt.getFullYear() + "-" + ( displayAt.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayAt.getDate().toString().padStart(2, '0'));
      endAt = new Date(displayAt.getFullYear() + "-" + ( displayAt.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayAt.getDate().toString().padStart(2, '0'));
      startAt.setMonth(0);
      startAt.setDate(1);
      endAt.setMonth(11);
      endAt.setDate(31);
      break;
    case "all":
      displayAtViewer.textContent = "すべて";
      startAt = new Date("1000-01-01T00:00:00");
      endAt = new Date("9999-12-31T23:59:59");
      break;
  }

  if( mode ) {
    displayCharts();
  }
}

async function deleteBop( id, bopName ) {
  if( confirm(bopName + "を削除します。\nよろしいですか？") ) {
    await fetch('https://nk-apps.net/src/php/deleteBop.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(id)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "notExistBop":
            alert("対象の取引が存在しません。");
            break;
          case "cannotDeleteBop":
            alert("取引削除の権限がありません。\n取引がグループで共有されている場合、管理者である必要があります。");
            break;
          case "success":
            alert("削除しました。");
            break;
        }

        closeModal();
        displayCharts();
      })
      .catch(error => {
        console.error(error);
      })
  }
}

async function deleteTransfer( id, assetCatNameTo, amount ) {
  if( confirm("この移動履歴を削除します。\nよろしいですか？") ) {
    await fetch('https://nk-apps.net/src/php/deleteTransfer.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(id)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "notExistTransfer":
            alert("対象の移動履歴が存在しません。");
            break;
          case "cannotDeleteTransfer":
            alert("移動履歴削除の権限がありません。\n移動履歴がグループで共有されている場合、管理者である必要があります。");
            break;
          case "notEnoughAmount":
            alert("金額が不足しています。\nこの処理を行うためには、" + assetCatNameTo + " の残高が" + amount + "円以上ある必要があります。");
            break;
          case "success":
            alert("削除しました。");
            break;
        }

        closeModal();
        displayCharts();
      })
      .catch(error => {
        console.error(error);
      })
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