const hMenu = document.getElementById("hMenu");
const hMenuBtn = document.getElementById("hMenuBtn");
const grayBackHMenu = document.getElementById("grayBackHMenu");
const mouseCircle = document.getElementById("mouseCircle");
const grayBack = document.getElementById("grayBack");
const modal = document.getElementById("modal");
const modalTtl = document.getElementById("modalTtl");
const modalContent = document.getElementById("modalContent");
const noticeListContainer = document.getElementById("noticeListContainer");
const noticeBack = document.getElementById("noticeBack");

getNotices();

function moveHMenu() {
  if( hMenu.style.marginLeft === "100vw" || hMenu.style.marginLeft === "" ) {
    hMenu.style.animation = "open-h-menu 1.0s ease forwards";
    hMenu.style.marginLeft = "calc( 100vw - var(--h-menu-width) )";
    hMenuBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
    grayBackHMenu.style.display = "block";
  } else {
    hMenu.style.animation = "close-h-menu 1.0s ease forwards";
    hMenu.style.marginLeft = "100vw";
    hMenuBtn.innerHTML = '<i class="fa-solid fa-bars"></i>';
    grayBackHMenu.style.display = "none";
  }
}

function closeHMenu() {
  hMenu.style.animation = "close-h-menu 1.0s ease forwards";
  hMenu.style.marginLeft = "100vw";
  hMenuBtn.innerHTML = '<i class="fa-solid fa-bars"></i>';
  grayBackHMenu.style.display = "none";
}

document.addEventListener('mousemove', function (e) {
  mouseCircle.style.transform = 'translate(' + e.clientX + 'px, ' + e.clientY + 'px)';
});

async function getNotices() {
  const noticeCounter = document.getElementById("noticeCounter");
  const noticeList = document.getElementById("noticeList");
  const noticeListHMenu = document.getElementById("noticeListHMenu");
  let noticeNum = 0;
  await fetch('https://nk-apps.net/src/php/getNotices.php')
    .then(response => response.json())
    .then(res => {
      console.log(res);

      noticeList.innerHTML = '';
      noticeListHMenu.innerHTML = '';
      for( let i=0; i<res.length; i++ ) {

        switch( res[i][2] ) {
          case "notread":
            noticeList.innerHTML += '\
              <div class="notice notread" onclick="displayNoticeDetail(\'notice\', \'' + res[i][0] + '\')">\
                <div class="ttl">' + res[i][3] + '</div>\
                <div class="text">' + res[i][4].replace('\n', '<br>') + '</div>\
                <div class="date">' + res[i][5].slice(0, 16) + '</div>\
              </div>\
            ';
            noticeListHMenu.innerHTML += '\
              <div class="notice notread" onclick="displayNoticeDetail(\'notice\', \'' + res[i][0] + '\')">\
                <div class="ttl">' + res[i][3] + '</div>\
                <div class="date">' + res[i][5].slice(0, 16) + '</div>\
              </div>\
            ';
            noticeNum++;
            break;
          case "read":
            noticeList.innerHTML += '\
              <div class="notice" onclick="displayNoticeDetail(\'notice\', \'' + res[i][0] + '\')">\
                <div class="ttl">' + res[i][3] + '</div>\
                <div class="text">' + res[i][4].replace('\n', '<br>') + '</div>\
                <div class="date">' + res[i][5].slice(0, 16) + '</div>\
              </div>\
            ';
            noticeListHMenu.innerHTML += '\
              <div class="notice" onclick="displayNoticeDetail(\'notice\', \'' + res[i][0] + '\')">\
                <div class="ttl">' + res[i][3] + '</div>\
                <div class="date">' + res[i][5].slice(0, 16) + '</div>\
              </div>\
            ';
            break;
        }
      }

      if( noticeNum == 0 ) {
        noticeCounter.style.display = "none";
      } else {
        noticeCounter.style.display = "block";
        noticeCounter.textContent = noticeNum <= 99 ? noticeNum : "99+";
      }
    })
    .catch(error => {
      alert("お知らせの取得中に予期せぬエラーが発生しました。\nお手数ですが、時間をおいて再度お試しください。");
      console.log(error);
    })
}

async function displayNoticeDetail( mode, id ) {  
  grayBack.style.display = "block";
  modal.style.display = "block";
  modal.style.animation = "displayModal 0.5s ease forwards";
  modalContent.style.animation = "displayModalContent 1.0s ease forwards";
  grayBack.style.display = "displayGrayBack 0.5s ease forwards";

  closeNotice();
  closeHMenu();

  switch( mode ) {
    case "notice":
      await fetch('https://nk-apps.net/src/php/readNotice.php', {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(id)
      })
        .then(response => response.json())
        .then(res => {
          console.log(res);

          if( res[1][6] == null ) res[1][6] = "";
    
          switch( res[1][1] ) {
            case "test_notice":
            case "notice":
              modalTtl.textContent = "お知らせ";
              modalContent.innerHTML = '\
                <div class="notice">\
                  <div class="ttl">' + res[1][3] + '</div>\
                  <div class="date">' + res[1][5].slice(0, 16) + ' ～ ' + res[1][6].slice(0, 16) + '</div>\
                  <div class="text">' + res[1][4] + '</div>\
                </div>\
              ';
              break;
          }
        })
        .catch(error => {
          console.log(error);
          alert("お知らせの表示処理中の予期せぬエラーが発生しました。\nお手数ですが、時間をおいて再度お試しください。");
        })
      break;
  }
}

function closeModal() {
  modal.style.animation = "closeModal 1.0s ease forwards";
  modalContent.style.animation = "closeModalContent 0.5s ease forwards";
  grayBack.style.display = "closeGrayBack 0.5s ease forwards";
  grayBack.style.display = "none";
}

function displayNotice() {
  noticeListContainer.style.display = "block";
  noticeBack.style.display = "block";
}

function closeNotice() {
  console.log("test");
  noticeListContainer.style.display = "none";
  noticeBack.style.display = "none";
}