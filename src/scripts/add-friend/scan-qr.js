// Webカメラの起動
const video = document.getElementById('video');
let contentWidth;
let contentHeight;
const windowWidth = document.body.clientWidth;
let videoSize = 0;
if( windowWidth < 960 ) {
  videoSize = windowWidth - 64 - 4;
} else {
  videoSize = 596;
}
const media = navigator.mediaDevices.getUserMedia({ audio: false, video: {width:videoSize, height:videoSize} })
  .then((stream) => {
    video.srcObject = stream;
    video.onloadeddata = () => {
      video.play();
      contentWidth = video.clientWidth;
      contentHeight = video.clientHeight;
      canvasUpdate();
      checkImage();
    }
  }).catch((e) => {
    console.log(e);
  });

// カメラ映像のキャンバス表示
const cvs = document.getElementById('camera-canvas');
const ctx = cvs.getContext('2d');
const canvasUpdate = () => {
  cvs.width = contentWidth;
  cvs.height = contentHeight;
  ctx.drawImage(video, 0, 0, contentWidth, contentHeight);
  requestAnimationFrame(canvasUpdate);
}

// QRコードの検出
const rectCvs = document.getElementById('rect-canvas');
const rectCtx =  rectCvs.getContext('2d');
const checkImage = () => {
  // imageDataを作る
  const imageData = ctx.getImageData(0, 0, contentWidth, contentHeight);
  // jsQRに渡す
  const code = jsQR(imageData.data, contentWidth, contentHeight);

  // 検出結果に合わせて処理を実施
  if (code) {
    drawRect(code.location);
    
    fetch('../../src/php/checkAddFriendByUserId.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(code.data)
    })
      .then(response => response.json())
      .then(resCheck => {
        switch( resCheck ) {
          case "notExistUser":
            alert("ユーザ情報を取得できませんでした。\nもう一度スキャンしてください。");
            break;
          case "myself":
            alert("自分自身をフレンドに追加することはできません。");
            break;
          case "alreadyFriend":
            alert("すでにフレンドに追加済みのユーザです。");
            break;
          default:
            if( confirm(resCheck[1] + "さんをフレンドに追加します。\nよろしいですか？") ) {
              fetch('../../src/php/addFriend.php', {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify(resCheck[0])
              }) 
                .then(response => response.json())
                .then(res => {
                  switch( res ) {
                    case "success":
                      alert(resCheck[1] + "さんをフレンドに追加しました。");
                      window.location.href = "https://nk-apps.net/mypage/";
                      break;
                    case "alreadyFriend":
                      alert("すでにフレンドに追加済みのユーザです。");
                      break;
                    default:
                      alert("フレンド登録処理中に予期せぬエラーが発生しました。\n時間をおいて再度お試しください。");
                      break;
                  }
                })
            }
            break;
        }
      })
  } else {
    rectCtx.clearRect(0, 0, contentWidth, contentHeight);
  }
  setTimeout(()=>{ checkImage() }, 500);
}

// 四辺形の描画
const drawRect = (location) => {
  rectCvs.width = contentWidth;
  rectCvs.height = contentHeight;
  drawLine(location.topLeftCorner, location.topRightCorner);
  drawLine(location.topRightCorner, location.bottomRightCorner);
  drawLine(location.bottomRightCorner, location.bottomLeftCorner);
  drawLine(location.bottomLeftCorner, location.topLeftCorner)
}

// 線の描画
const drawLine = (begin, end) => {
  rectCtx.lineWidth = 4;
  rectCtx.strokeStyle = "#F00";
  rectCtx.beginPath();
  rectCtx.moveTo(begin.x, begin.y);
  rectCtx.lineTo(end.x, end.y);
  rectCtx.stroke();
}