$(".parent-wrapper").scroll(function() {
  if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
  } else {
  }
  console.log($(this).scrollTop(), $(this).innerHeight(), $(this)[0].scrollHeight);
});

$(document).ready(function () {
  $(".preloader").slideUp();
});
