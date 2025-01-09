<?php
include '../connectionArticles.php';
session_start();

if (!isset($_SESSION["user-id"])) {
    header('Location: http://localhost:80/blog_PHP-main/Fsrc/views/loginView.php');
    exit;
}

if (!isset($_GET['id'])) {
    echo 'Link Nao Existe';
    exit;
}

$_ID = $_GET['id'];

// Create a new instance of the Database class
$db = new Database('../mydatabase.db');

// Query to fetch article details with user name
$query = "SELECT articles.id, articles.titulo, articles.conteudo, articles.data_de_publicacao, users.nome AS por
          FROM articles
          LEFT JOIN users ON articles.usuario_id = users.id
          WHERE articles.id = :id";
$params = array('id' => $_ID);
$resultado = $db->select($query, $params);
$linha = $resultado->fetchArray(SQLITE3_ASSOC);

// Query to count likes for the article
$queryLikes = "SELECT COUNT(*) AS likes FROM likedby WHERE article_id = :id";
$paramsLikes = array('id' => $_ID);
$resultLikes = $db->select($queryLikes, $paramsLikes);
$likes = $resultLikes->fetchArray(SQLITE3_ASSOC)['likes'];

// Query to fetch comments with user names
$queryGetComments = "SELECT comments.id AS comment_id, comments.comentario AS commentValue, comments.data_comentario, users.nome AS por
                     FROM comments
                     LEFT JOIN users ON comments.usuario_id = users.id
                     WHERE comments.article_id = :id";
$paramsComments = array('id' => $_ID);
$getCommentsResult = $db->select($queryGetComments, $paramsComments);

if (!isset($linha['conteudo'])) {
    echo 'Pagina nao existe';
    header('Location: http://localhost:80/blog_PHP-main/src/views/articlesView.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Article View</title>
    <link rel="stylesheet" href="/blog_PHP-main/public/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <script src="../../public/js/jquery-3.7.1.min.js"></script>
    <style>
        /* Reset básico */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Corpo da página */
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            color: #333;
            padding: 20px;
        }

        /* Container principal */
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Título do artigo */
        .article-title {
            font-size: 2em;
            margin-bottom: 10px;
            color: #333;
        }

        /* Conteúdo do artigo */
        .article-content {
            margin-bottom: 20px;
        }

        /* Botões */
        button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Links */
        a {
            color: #007BFF;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <div class="container">
        <h1 class="article-title">Título do Artigo</h1>
        <div class="article-content">
            <!-- Conteúdo do artigo -->
        </div>
        <button id="editArticleBtn">Editar Artigo</button>
    </div>
    <div class="accountDetail">
        <div class="fotoIcon"></div>
        <div class="name">
            <h3 id="name">Por: <?php echo htmlspecialchars($linha['por']); ?></h3>
            <h4>Likes: <?php echo $likes; ?></h4>
            <button class="addLike">Dar like</button>
            <?php
            if ($linha['por'] == $_SESSION['user-name']) {
                echo '
                    <button class="articleEditBtn">Editar</button>
                    <button class="sendChangesBtn">Enviar</button>
                    <button class="deleteArticle">Deletar</button>
                ';
            }
            ?>
        </div>
    </div>

    <div class="article_comments">
        <div class="articles">
            <div id="editor"></div>
            <?php echo htmlspecialchars_decode($linha['conteudo']); ?>
        </div>
        <div class="comments">
            <input class="inputCommentValue" type="text" placeholder="Comentar">
            <button class="sendCommentBtn">Enviar Comentário</button>
            <?php
            while ($row = $getCommentsResult->fetchArray(SQLITE3_ASSOC)) {
                $content = $row['commentValue'];
                $commentBy = $row['por'];
                echo "
                    <div class='comment' id='{$row['comment_id']}'>
                        <h1>Por: $commentBy</h1>
                        <p>$content</p>
                    </div>
                ";
            }
            ?>
        </div>
    </div>

    <script>
        // Select DOM elements
const btnLike = document.querySelector('.addLike');
const inputComment = document.querySelector('.inputCommentValue');
const sendCommentBtn = document.querySelector('.sendCommentBtn');
const btnEdit = document.querySelector('.articleEditBtn');
const sendChangeBtn = document.querySelector('.sendChangesBtn');
const deleteArticleBtn = document.querySelector('.deleteArticle');
const editor = document.querySelector('#editor');

// Initialize Quill editor
const quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ header: [1, 2, false] }],
            ['bold', 'italic', 'underline'],
            ['image', 'code-block'],
        ],
    },
});


const socket = new WebSocket("wss://localhost:8080/chat");

socket.addEventListener("open", function(event) {
    console.log("Connected to WebSocket");
});

socket.addEventListener("message", function(event) {
    const message = event.data;
    if (message === "new comment") {
        fetchComments(<?php echo $_ID ?>);
    } else if (message === "new article") {
        location.reload();
    }
});

socket.addEventListener("error", function(event) {
    console.error("WebSocket error:", event);
});


// Function to add like
function addLike(articleId, userId) {
    $.post('http://localhost:80/blog_PHP-main/src/addlike.php', { articleId: articleId, likedBy: userId }, function(res) {
        if (res.status) {
            const likeCountElement = document.querySelector('.name h4');
            const currentLikes = parseInt(likeCountElement.textContent.split(':')[1].trim());
            likeCountElement.textContent = 'Likes: ' + (currentLikes + 1);
            btnLike.style.backgroundColor = "Red";
            socket.send('new article');
        } else {
            //alert('Failed to add like.');
        }
    });
}

// Function to add comment
function addComment(articleId, content) {
    $.post('http://localhost:80/blog_PHP-main/src/createComment.php', { content: content, articleId: articleId }, function(res) {
        if (res.status) {
            const commentsSection = document.querySelector('.comments');
            const newComment = document.createElement('div');
            newComment.className = 'comment';
            newComment.innerHTML = `
                <h1>Por: <?php echo $_SESSION['user-name'] ?></h1>
                <p>${content}</p>
            `;
            commentsSection.appendChild(newComment);
            socket.send('new comment');
        } else {
            //            extension_dir = "ext"
            extension=curl
            extension=gd
            extension=mbstring
            extension=mysqli
            extension=openssl
            extension=pdo_mysql
            extension=soap
            extension=sockets
            extension=xmlrpcalert('Failed to add comment.');
        }
    });
}

// Function to edit article
function editArticle(articleId, content) {
    $.post('http://localhost:80/blog_PHP-main/src/editArticle.php', { articleId: articleId, html: content }, function(res) {
        if (res.status == 'success') {
            const articleContent = document.querySelector('.articles p');
            articleContent.innerHTML = content;
            editor.style.display = 'none';
            socket.send('new article');
        } else {
            alert('Failed to edit article.');
        }
    });
}

// Function to delete article
function deleteArticle(articleId) {
    $.post('http://localhost:80/blog_PHP-main/src/deleteArticle.php', { articleId: articleId }, function(res) {
        if (res.status == 'success') {
            const articleSection = document.querySelector('.article_comments');
            articleSection.remove();
            socket.send('new article');
        } else {
            alert('Failed to delete article.');
        }
    });
}

// Event listeners
btnLike.addEventListener('click', function() {
    addLike(<?php echo $_ID ?>, <?php echo $_SESSION['user-id'] ?>);
});

sendCommentBtn.addEventListener('click', function() {
    const commentContent = inputComment.value;
    console.log(commentContent);
    if (commentContent.trim() === '') {
        alert('Please enter a comment.');
        return;
    }
    console.log(commentContent);
    addComment(<?php echo $_ID ?>, commentContent);
    inputComment.value = '';
});

btnEdit.addEventListener('click', function() {
    editor.style.display = 'block';
});

sendChangeBtn.addEventListener('click', function() {
    const editedContent = quill.getHTML();
    editArticle(<?php echo $_ID ?>, editedContent);
});

deleteArticleBtn.addEventListener('click', function() {
    if (confirm('Are you sure you want to delete this article?')) {
        deleteArticle(<?php echo $_ID ?>);
    }
});
    </script>
</body>

</html>
