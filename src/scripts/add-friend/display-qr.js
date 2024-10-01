// Create QR
fetch('https://nk-apps.net/src/php/getUserIdByLoginToken.php')
  .then(response => response.json())
  .then(res => {
    $(function(){
      var qrtext = res;
      var utf8qrtext = unescape(encodeURIComponent(qrtext));
      $("#myQr").html("");
      $("#myQr").qrcode({width:256,height:256,text:utf8qrtext}); 
    });
  })