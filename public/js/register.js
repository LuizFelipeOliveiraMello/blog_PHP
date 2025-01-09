$(".form").on("submit", (e) => {
  e.preventDefault();

  $.post(
    "http://localhost:80/blog_PHP-main/src/auth/registerAuth.php",
    $(".form").serialize(),
    function (data) {}
  );
});
