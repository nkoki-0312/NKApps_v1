const icon = document.getElementById("icon");
const icon_label = document.getElementById("icon_label");
icon.addEventListener('change', viewFileName);

function viewFileName(){
  icon_label.innerHTML = icon.value;
}

function pageBack() {
  if( confirm("マイページに戻ります。\n編集中の内容は保存されません。") ) {
    window.location.href = "../mypage/";
  }
}