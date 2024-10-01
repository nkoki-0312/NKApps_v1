const bar = document.getElementById("bar");
const email = document.getElementById("email");
const errEmail = document.getElementById("errEmail");
const checkEmail = document.getElementById("checkEmail");
const email_pattern = /^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]{1,}\.[A-Za-z0-9]{1,}$/;
const preregisterBtn = document.getElementById("preregisterBtn");
const formContainer = document.getElementById("formContainer");
const successContainer = document.getElementById("successContainer");

function checkCompleteForm() {
  if( email.value !== "" ) {
    preregisterBtn.disabled = false;
  } else {
    preregisterBtn.disabled = true;
  }
}

function preregister() {
  errEmail.textContent = "";

  if( email.value === "" ) errEmail.textContent = "メールアドレスを入力してください。";
  if( !email_pattern.test(email.value) ) errEmail.textContent = "メールアドレスの形式が異なります。";
  if( errEmail.textContent == "" ) {
    fetch("../src/php/preregister.php", {
      method: "POST",
      headers: {"Content-Type": "appliation/json"},
      body: JSON.stringify(email.value)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "existEmail":
            errEmail.textContent = "すでにこのメールアドレスは登録されています。";
            break;
          case "success":
            formContainer.style.display = "none";
            successContainer.style.display = "block";
            bar.style.animation = "progress-second 2.0s ease forwards";
            checkEmail.textContent = email.value;
            break;
        }
      })
  }
}
