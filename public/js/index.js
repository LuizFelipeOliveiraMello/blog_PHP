const socket = new WebSocket("ws:localhost:8080/chat");

socket.addEventListener("open", (e) => {
  console.log("Conectado com webscoket");
});

socket.addEventListener("message", (event) => {
  console.log(event.data);
  if (event.data == "reload") {
    location.reload();
  }
});

$("#logoutBtn").on("click", (e) => {
  $.post(
    "http://localhost:80/blog_PHP-main/src/auth/logoutAuth.php",
    function (data) {
      location.href = data;
    }
  );
});

$("#createArticleBtn").on("click", (e) => {
  e.preventDefault();

  let clone = $(".ql-editor").clone();
  quillContent = quill.getContents();

  let imageData = [];

  for (let i = 0; i < quillContent.ops.length; i++) {
    imageData.push(quillContent.ops[i].insert.image);
  }

  // if(quillContent.ops[0].insert.image)
  // {
  //   imageData = quillContent.ops[0].insert.image
  // }
  // else
  // {
  //   imageData = "NULL"
  // }

  $_HTMLCOD = quill.getSemanticHTML();

  $.post(
    "http://localhost:80/blog_PHP-main/src/createArticle.php",
    { html: $_HTMLCOD, image: imageData },
    (res) => {
      socket.send("new article");
      console.log("Mensagem Enviada");
    }
  );
});

$.get("http://localhost:80/blog_PHP-main/src/auth/getName.php", (res) => {
  $("#name").text(res);
});

$.get("http://localhost:80/blog_PHP-main/src/getArticlesPreview.php", (res) => {
  for (let test = 0; test < res.length; test++) {
    const articleContainer = document.createElement("div");
    const tittle_preview = document.createElement("div");
    const infos = document.createElement("div");

    const likes = document.createElement("p");
    const from = document.createElement("p");

    const searchArt = document.querySelector(".searchArticle");

    likes.innerHTML = res[test].likes;
    from.innerHTML = res[test].por;

    articleContainer.classList.add("article");
    tittle_preview.classList.add("title_preview");
    infos.classList.add("infos");

    const test_PARSE = $.parseHTML(res[test].html);

    for (let i = 0; i < test_PARSE.length; i++) {
      tittle_preview.appendChild(test_PARSE[i]);
    }

    console.log(tittle_preview.classList);
    tittle_preview.childNodes.forEach((e) => {
      console.log(e);
    });

    infos.appendChild(likes);
    infos.appendChild(from);

    $(".allArticles").append(articleContainer);
    articleContainer.appendChild(tittle_preview);
    articleContainer.appendChild(infos);

    articleContainer.addEventListener("click", (e) => {
      location.href =
        "http://localhost:80/blog_PHP-main/src/views/fullArticleView.php?id=" +
        res[test].id +
        "";
    });

    searchArt.addEventListener("input", (e) => {
      console.log(e.target.value);
      tittle_preview.childNodes.forEach((childs) => {
        if (childs.textContent.includes(e.target.value)) {
          console.log(childs.parentElement.parentElement);
          childs.parentElement.parentElement.style.display = "flex";
        } else {
          childs.parentElement.parentElement.style.display = "none";
        }
      });
    });
  }
});
