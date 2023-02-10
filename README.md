<div align="center">

  <img src="https://github.com/MauricioRobertoDev/minizord-template/blob/main/minizord.png" alt="logo" width="150" height="auto" />
  <h1>Minizord - Template</h1>
  
  <p>
    Apenas o template para os componentes de Minizord! 
  </p>
  
  
<!-- Badges -->
<p>
  <a href="https://github.com/MauricioRobertoDev/minizord-http/graphs/contributors">
    <img src="https://img.shields.io/github/contributors/MauricioRobertoDev/minizord-http" alt="contributors" />
  </a>
  <a href="">
    <img src="https://img.shields.io/github/last-commit/MauricioRobertoDev/minizord-http" alt="last update" />
  </a>
  <a href="https://github.com/MauricioRobertoDev/minizord-http/network/members">
    <img src="https://img.shields.io/github/forks/MauricioRobertoDev/minizord-http" alt="forks" />
  </a>
  <a href="https://github.com/MauricioRobertoDev/minizord-http/stargazers">
    <img src="https://img.shields.io/github/stars/MauricioRobertoDev/minizord-http" alt="stars" />
  </a>
  <a href="https://github.com/MauricioRobertoDev/minizord-http/issues/">
    <img src="https://img.shields.io/github/issues/MauricioRobertoDev/minizord-http" alt="open issues" />
  </a>
  <a href="https://github.com/MauricioRobertoDev/minizord-http/blob/master/LICENSE">
    <img src="https://img.shields.io/github/license/MauricioRobertoDev/minizord-http.svg" alt="license" />
  </a>
</p>
   
<h4>
    <a href="https://github.com/MauricioRobertoDev/minizord-http#zap-como-usar">Exemplos</a>
  <span> · </span>
    <a href="https://github.com/MauricioRobertoDev/minizord-http">Documentação</a>
  <span> · </span>
    <a href="https://github.com/MauricioRobertoDev/minizord-http/issues/">Reporte Bugs</a>
  <span>
</div>

<br />

<!-- About the Project -->
## :star2: Sobre o projeto
O objetivo é aprender.

Esse pacote é uma implementação PSR-7 e PSR-17, uma série de classes que tem o papel de representar entidades básicas de uma requisição http, como por exemplo a Uri, a Stream e até mesmo a própria Request.

Esse pacote é quase como a espinha dorsal de qualquer framework php, por lhe proporcionar várias classes de fácil manipulação, podendo escalar sua aplicação fácilmente.

Veja mais nas minhas observações no final deste read-me.

<!-- Features -->
### :dart: Features

- Classe representando a uri (Uri)
- Classe representando um stream de recurso (Stream)
- Classe representando um arquivo upado (UploadedFile)
- Classe representando uma requisição (Request)
- Classe representando uma resposta (Response)
- Request abstrata podendo ser extendida e criar sua própria
- Factories para criação das classes
- Método para criar uma ServerRequest atráves das variáveis globais
- Método para popular sua classe através das variáveis globais
- Método para criar várias UploadedFiles através da variável global $_FILES


<br>

<!-- Usage -->
## :zap: Como usar

<!-- Prerequisites -->
### :bangbang: Pré-requisitos

É necessário que você tenha o composer instalado e php 8.1+.

<!-- Installation -->
### :gear: Instalação

Instale o pacote minizord/http com composer

```bash
  composer require minizord/http
```

<!-- Examples -->
### :rocket: Exemplos
Explicação de como usar, para saber mais detalhes veja a documentação.

```php
use Minizord\Http\Factory\RequestFactory;
use Minizord\Http\Factory\ResponseFactory;
use Minizord\Http\Factory\ServerRequestFactory;
use Minizord\Http\Factory\StreamFactory;
use Minizord\Http\Factory\UploadedFileFactory;
use Minizord\Http\Factory\UriFactory;

$uriFactory = new UriFactory();
$streamFactory = new StreamFactory();
$requestFactory = new RequestFactory();
$responseFactory = new ResponseFactory();
$uploadedFactory = new UploadedFileFactory();
$serverRequestFactory = new ServerRequestFactory();

$uri = $uriFactory->createUri('https://example.com.br/path/?arg=value');

$stream = $streamFactory->createStream('content');
$stream = $streamFactory->createStreamFromFile('path/to/file.txt', 'w+');
$stream = $streamFactory->createStreamFromResource(fopen('path/to/file.txt', 'w+'));

$request = $requestFactory->createRequest('POST', $uri);

$response = $responseFactory->createResponse(400, 'Bad Request');

$uploadedFile = $uploadedFactory->createUploadedFile($stream, 1024, UPLOAD_ERR_OK, 'file.txt', 'text/plain');
$uploadedFiles = $uploadedFactory->createUploadedFilesFromGlobal(); // $_FILES;

$serverRequest = $serverRequestFactory->createServerRequest('POST', $uri, []);
$serverRequest = $serverRequestFactory->createFromGlobals(); // $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
```

<br/>

<!-- Run Locally -->
##  :wrench: Desenvolvimento

Clone o projeto

```bash
  git clone https://github.com/MauricioRobertoDev/minizord-http
```

Entre na pasta do projeto

```bash
  cd minizord-http
```

Instale as dependências

```bash
  composer install
```

<!-- Running Tests -->
### :test_tube: Rodando os testes

Para rodar os testes use o comando baixo

```bash
  composer test
```

<br>

<!-- Contributing -->
## :wave: Contribuintes

<a href="https://github.com/MauricioRobertoDev/minizord-http/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=MauricioRobertoDev/minizord-http" />
</a>

Contribuições são sempre bem vindas!

<!-- See `contributing.md` for ways to get started. -->

<br>

<!-- License -->
## :lock: License

Licença MIT (MIT). Consulte o [arquivo de licença](https://github.com/MauricioRobertoDev/minizord-http/LICENSE) para obter mais informações.

<br>

<!-- Contact -->
## :handshake: Contato

Mauricio Roberto - mauricio.roberto.dev@gmail.com

Link do projeto: [https://github.com/MauricioRobertoDev/minizord-http](https://github.com/MauricioRobertoDev/minizord-http)

<br>

<!-- Comments -->
## :speech_balloon: Observações
Eu dei inicio ao projeto por este componente,  pois olhei as interfaces e pensei 'hummm.. Tem várias interfaces já definidas, é só implementar elas, deve ser fácil', não poderia estar mais enganado.

Já na classe URI fui introduzido ao mundo das RFC e a primeira reação ao abrir o txt não poderia ser outra a não ser 'o que diabos é isso?', letras e mais letras com uma barra lateral que parecia que jamais terminaria.

Mas claro a internet é maravilhosa e acabai encontrando sites pra facilitar a vida, só queria deixar registrado.

Nesta implementação e no mundo das RFCs, percebi que algumas RFC citadas já estavam obsoletas e pensei primeiramente que deveria então criar a implementação se baseando nas novas. 

Porem com pesquisa o que entendi é ser proposital, há várias RFC que ainda não foram abraçadas,  é o que acontece com a RFC 7230 que deve ser usada para  validar os headers http, ela já é obsoleta através da RFC 9112, mas aparentemente não foi muito aceita por restringir de mais os valores dos headers para apenas caracteres ASCII visíveis, enquanto a anterior permitia ASCII visíveis e mais alguns caracteres especiais. 
<br>

<!-- Acknowledgments -->
## :gem: Créditos/Reconhecimento
 - [Shields.io](https://shields.io/)
 - [Awesome Readme Template](https://github.com/Louis3797/awesome-readme-template)
 - [Emoji Cheat Sheet](https://github.com/ikatyang/emoji-cheat-sheet/blob/master/README.md#travel--places)
 - [Pest PHP](https://github.com/pestphp/pest)

<br>

<!-- References -->
## :microscope: Referências
 - [Regex101 - Testar regex](https://regex101.com/)
 - [Pinoy Code Streamer - Implementação básica](https://www.youtube.com/watch?v=6VAAyuVsDco)
 - [Mozilla - Visão geral do HTTP](https://developer.mozilla.org/pt-BR/docs/Web/HTTP/Overview)
 - [Mozilla - Evolução do HTTP](https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/Evolution_of_HTTP)
 - [Mozilla - Cabeçalhos HTTP](https://developer.mozilla.org/pt-BR/docs/Web/HTTP/Headers)
 - [IANA - Lista de códigos HTTP](http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml)
 - [PHP - Lista de erros ao upar um arquivo](https://www.php.net/manual/en/features.file-upload.errors.php)
 - [PHP - Diferença entre 1 arquivo e vários enviados](https://www.php.net/manual/en/reserved.variables.files)
 - [PHP - Dicas para tratar o $_FILES](https://www.php.net/manual/en/features.file-upload.php)
 - [PHP - Variáveis do $_SERVER](https://www.php.net/manual/en/reserved.variables.server)
 - [PHP - Dicas de como pegar todos os header](https://www.php.net/manual/en/function.getallheaders.php)
 - [Diego Brocanelli - Como realizar parse da query string](https://www.diegobrocanelli.com.br/php/realizar-parse-da-query-string-de-forma-simples/)
