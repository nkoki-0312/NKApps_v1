const hMenu = document.getElementById("hMenu");
const hMenuBtn = document.getElementById("hMenuBtn");
const grayBackHMenu = document.getElementById("grayBackHMenu");
const mouseCircle = document.getElementById("mouseCircle");

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

document.addEventListener('mousemove', function (e) {
  mouseCircle.style.transform = 'translate(' + e.clientX + 'px, ' + e.clientY + 'px)';
});
