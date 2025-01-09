
## Download required with composer = composer require cboden/ratchet

### Stack Aleatória: LAMP (Linux, Apache, MySQL, PHP)

## Desafio: Sistema de Blog

### Descrição

Crie um sistema de blog onde os usuários possam criar uma conta, fazer login e publicar artigos. Cada usuário deve ser capaz de criar, editar e excluir seus próprios artigos. O sistema deve ter uma interface amigável e responsiva.

### Requisitos

### Front-end

- **Tecnologias**: HTML, CSS, JavaScript (com alguma biblioteca de front-end se desejar, como jQuery)
- **Funcionalidades**:
    - Tela de registro de usuário
    - Tela de login
    - Tela principal com a lista de artigos publicados
    - Funcionalidades para adicionar, editar e excluir artigos
    - Interface responsiva

### Back-end

- **Tecnologias**: PHP com Apache
- **Funcionalidades**:
    - Rotas para registro e autenticação de usuários
    - Rotas CRUD (Create, Read, Update, Delete) para os artigos
    - Validação de entrada de dados
    - Proteção das rotas com autenticação (por exemplo, usando sessões)

### Banco de Dados

- **Tecnologias**: MySQL
- **Funcionalidades**:
    - Tabela de usuários com campos para nome, email e senha (criptografada)
    - Tabela de artigos com campos para título, conteúdo, data de publicação e referência ao usuário dono do artigo

### Estrutura de Diretórios

**Front-end (HTML, CSS, JavaScript)**

```
/public
  /css
    - styles.css
  /js
    - scripts.js
  - index.html
  - login.html
  - register.html
  - dashboard.html
  - article.html

```

**Back-end (PHP com Apache)**

```
/src
  /controllers
    - authController.php
    - articleController.php
  /models
    - User.php
    - Article.php
  /views
    - auth
      - loginView.php
      - registerView.php
    - articles
      - articleListView.php
      - articleFormView.php
  - routes.php
  - config.php
  - database.php
  - index.php

```

### Dicas

- Para autenticação, utilize sessões em PHP.
- Para criptografar senhas, use a função `password_hash` do PHP.
- Utilize `AJAX` para requisições assíncronas no front-end.
- Utilize bibliotecas como `PHPMailer` para funcionalidades adicionais, como recuperação de senha.
- No MySQL, utilize consultas preparadas para proteger contra injeção de SQL.
- Ratchet baixado com composer, usando webscoket

-------------------------------------------------------------------------------------------------------
