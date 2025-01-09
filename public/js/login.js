$(".form").on("submit", (e) => {
  e.preventDefault();
  $.post(
    "http://localhost:80/blog_PHP-main/src/auth/loginAuth.php",
    $(".form").serialize(),
    function (data) {
      if (data == "Erro") {
        alert("senha ou email incorreto");
      } else {
        $(".pointer").text("Voce esta logado");
        location.href = data;
      }
    }
  );
});
