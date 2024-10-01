const url = new URL(window.location.href);
const params = url.searchParams;
const gid = params.get('gid');
fetch('https://nk-apps.net/src/php/getGroupIdFromUniqId.php', {
  method: "POST",
  headers: {"Content-Type": "application/json"},
  body: JSON.stringify(gid)
})
  .then(response => response.json())
  .then(res => {
    if( res == "" ) {
      alert("二次元コードからメンバーを追加するにはグループIDを登録してください。");
      window.location.href = "https://nk-apps.net/group/?gid=" + gid;
    } else {
      var qrtext = res;
      var utf8qrtext = unescape(encodeURIComponent(qrtext));
      $("#myQr").html("");
      $("#myQr").qrcode({width:256,height:256,text:utf8qrtext}); 
    }
  })