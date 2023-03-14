/*Caroussel */

let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides((slideIndex += n));
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("slide");
  if (n > slides.length) {
    slideIndex = 1;
  }
  if (n < 1) {
    slideIndex = slides.length;
  }
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  slides[slideIndex - 1].style.display = "flex";
}

/*Menu Burger */

function toggleMenu() {
  const navbar = document.querySelector(".header");
  const burger = document.querySelector(".burger");

  burger.addEventListener("click", (e) => {
    navbar.classList.toggle("show-nav");
  });

  /* bonus */
  const navbarMenu = document.querySelectorAll(".header a");
  navbarMenu.forEach((link) => {
    link.addEventListener("click", (e) => {
      navbar.classList.toggle("show-nav");
    });
  });
}
toggleMenu();



/*Caroussel infoGlobale */


/*Card formules et garantie */


const card1 = document.querySelector('.card_1');
const card2 = document.querySelector('.card_2');
const text = document.getElementById('formule1');
const text2 = document.getElementById('formule2');

card1.addEventListener('click', function () {
  card1.style.backgroundColor = 'var(--color-blue-transparent)';
  text.style.color = 'var(--color-yellow)';
  card2.style.backgroundColor = "white";
  text2.style.color = "var(--color-blue)";
});

card2.addEventListener('click', function () {
  card1.style.backgroundColor = "white";
  text.style.color = "var(--color-blue)";
  card2.style.backgroundColor = 'var(--color-blue-transparent)';
  text2.style.color = 'var(--color-yellow)';
});



/* Overlay */

function openOverlay() {
  document.getElementById("overlay").style.visibility = "visible";
}


function openOverlayWarning() {
  document.getElementById("overlay_warning").style.visibility = "visible";
}
function openOverlayCross() {
  document.getElementById("overlay_cross").style.visibility = "visible";
}
function openOverlayGlobe() {
  document.getElementById("overlay_globe").style.visibility = "visible";
}
function openOverlayHand() {
  document.getElementById("overlay_hand").style.visibility = "visible";
}
function openOverlayResiliation() {
  document.getElementById("overlay_resiliation").style.visibility = "visible";
}
function openOverlayTime() {
  document.getElementById("overlay_time").style.visibility = "visible";
}
function openOverlayMoney() {
  document.getElementById("overlay_money").style.visibility = "visible";
}





/* Form with progressBar à modifier avec boucle */


var form_1 = document.querySelector(".form_1");
var form_2 = document.querySelector(".form_2");
var form_3 = document.querySelector(".form_3");
var form_4 = document.querySelector(".form_4");



var form_1_btns = document.querySelector(".form_1_btns");
var form_2_btns = document.querySelector(".form_2_btns");
var form_3_btns = document.querySelector(".form_3_btns");
var form_4_btns = document.querySelector(".form_4_btns");


var form_1_next_btn = document.querySelector(".form_1_btns .btn_next");
var form_2_back_btn = document.querySelector(".form_2_btns .btn_back");
var form_2_next_btn = document.querySelector(".form_2_btns .btn_next");
var form_3_back_btn = document.querySelector(".form_3_btns .btn_back");
var form_3_next_btn = document.querySelector(".form_3_btns .btn_next");
var form_4_back_btn = document.querySelector(".form_4_btns .btn_back");



var form_2_progessbar = document.querySelector(".form_2_progessbar");
var form_3_progessbar = document.querySelector(".form_3_progessbar");
var form_4_progessbar = document.querySelector(".form_4_progessbar");

var btn_done = document.querySelector(".btn_done");
var modal_wrapper = document.querySelector(".modal_wrapper");
var shadow = document.querySelector(".shadow");

form_1_next_btn.addEventListener("click", function () {
  form_1.style.display = "none";
  form_2.style.display = "block";

  form_1_btns.style.display = "none";
  form_2_btns.style.display = "flex";

  form_2_progessbar.classList.add("active");
});

form_2_back_btn.addEventListener("click", function () {
  form_1.style.display = "block";
  form_2.style.display = "none";

  form_1_btns.style.display = "flex";
  form_2_btns.style.display = "none";

  form_2_progessbar.classList.remove("active");
});

form_2_next_btn.addEventListener("click", function () {
  form_2.style.display = "none";
  form_3.style.display = "block";

  form_3_btns.style.display = "flex";
  form_2_btns.style.display = "none";

  form_3_progessbar.classList.add("active");
});

form_3_back_btn.addEventListener("click", function () {
  form_2.style.display = "block";
  form_3.style.display = "none";

  form_2_btns.style.display = "flex";
  form_3_btns.style.display = "none";

  form_3_progessbar.classList.remove("active");
});

form_3_next_btn.addEventListener("click", function () {
  form_3.style.display = "none";
  form_4.style.display = "block";

  form_4_btns.style.display = "flex";
  form_3_btns.style.display = "none";

  form_4_progessbar.classList.add("active");
});

form_4_back_btn.addEventListener("click", function () {
  form_3.style.display = "block";
  form_4.style.display = "none";

  form_4_btns.style.display = "none";
  form_3_btns.style.display = "flex";

  form_4_progessbar.classList.remove("active");
});


btn_done.addEventListener("click", function () {
  modal_wrapper.classList.add("active");
})

shadow.addEventListener("click", function () {
  modal_wrapper.classList.remove("active");
})













