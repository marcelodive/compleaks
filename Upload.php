<?php
  error_reporting(0);
  header('Content-Type: text/html; charset=utf-8');

  // Nome das variáveis POST
  $disciplina = $_POST['disciplina'];
  $ano = $_POST['ano'];
  $semestre = $_POST['semestre'];
  $tipologia = $_POST['tipologia'];
  $professor = $_POST['professor'];
  $informacoes = $_POST['informacoes'];
  
  //Variáveis para se conectar ao servidor:
  $servername = "";
  $username = "";
  $password = "";
  $dbname = "";
  $tbname = "";
  
  //Criando conexão:
  $conn = mysql_connect($servername, $username, $password, $dbname);
  if (!$conn) {
      die("Connection failed: " . mysql_connect_error());
  } /* else echo "Conectado ao banco de dados {$dbname}<br>"; */
  mysql_select_db($dbname);
  
  //Escolhe a codificação a ser utilizada pelo banco de dados:
  mysql_query("SET NAMES 'utf8'");
  mysql_query('SET character_set_connection=utf8');
  mysql_query('SET character_set_client=utf8');
  mysql_query('SET character_set_results=utf8');

  //Inclui as bibliotecas que serão utilizadas:
  include '..library/config.php';
  include '..library/opendb.php';
  include '..library/closedb.php';
  include 'Funções.php';
  
  //Certifica que o arquivo não esta errado:
  $arquivoErrado = 0;
  //Arquivos que serão compactados:
  $file_to_zip[count($_FILES['userfile']['tmp_name'])];
  //Diretório a ser utilização para o upload dos arquivos:
  $uploaddir = 'uploads/';
  //Tamanho total do arquivo:
  $fileSize = 0;
  //Array com o nome dos arquivos:
  $nomeArquivo = array();
  
  //Corrige o bug do firefox de trocar aquivos PDF por binários:
  for($f=0; $f<count($_FILES['userfile']['tmp_name']); $f++) {
    if (($_FILES['userfile']['type'][$f] == 'binary/octet-stream' ||
    $_FILES['userfile']['type'][$f] == 'application/octet-stream')
          && substr_compare($fileName,'.pdf',-4,4,true)==0) {
              $fileType = 'application/pdf';
        $_FILES['userfile']['type'][$f] = 'application/pdf';
      }
    
    //Variáveis relativas ao arquivo:
    $path = $_FILES['userfile']['name'][$f];
    $tmpName  = $_FILES['userfile']['tmp_name'][$f];
    $file_to_zip[$f] = $tmpName;  
    $fileType = $_FILES['userfile']['type'][$f];
    $fileSize = $fileSize + $_FILES['userfile']['size'][$f];
    $nomeArquivo[$f] = basename($_FILES['userfile']['name'][$f]);

    //Certificasse de os arquivos estão no formato correto:
    if (($fileType != "image/gif")
    && ($fileType != "image/jpeg")
    && ($fileType != "image/jpg")
    && ($fileType != "image/png")
    && ($fileType != "application/pdf")
    && ($arquivoErrado == 0)){
      echo '
        <head>
          <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
          <title>Compleaks 2.0 - Procurar Material</title>
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <meta name="author" content="Marcelo Pereira Rodrigues" />
          <!-- Bootstratp --> 
          <link rel="stylesheet" href="bootstrap-3.2.0-dist/css/bootstrap.min.css">
          <link rel="stylesheet" href="bootstrap-3.2.0-dist/css/bootstrap-theme.min.css">
          <link rel="stylesheet" href="Standards/standards.css">
        </head>
        <html>
          <p align=center><br>Apenas PDF ou imagens menores que 10mb são permitidos<br><br>
          <button class="btn" onclick="history.go(-1);">Voltar</button> </p>
        </html>';
      $arquivoErrado = 1;
    }
  }

  $date = date("Y_m_d G_i_s");
  
  //Se os arquivos estiverem no formato correto, cria-se o zip
  //e faz-se o upload dos arquivos:
  if ($arquivoErrado != 1) {      
    //Nome do Arquivo:
    if(isset($_POST['upload']) && $_FILES['userfile']['size'] > 1 && $arquivoErrado == 0){
      $fileName = $_FILES['userfile']['name'][$f];
      $error = $_FILES['userfile']['error'][$f];
      $uploadfile = $uploaddir.$disciplina." - ".$tipologia." - ".$ano." - ".$date.".zip";      
      create_zip($file_to_zip, $uploadfile, "", $nomeArquivo);
    }
    
    //Coloca no banco de dados os dados:
    $query = "
      INSERT INTO `tbcompleaks` (
        `id`, `disciplina`, `ano`, `semestre`, `tipologia`, `professor`, `informacoes`, 
        `tipo`, `tamanho`, `conteudo`, `horario`
      )
      VALUES (
        NULL, '$disciplina', '$ano', $semestre, '$tipologia', '$professor', 
        '$informacoes', '$fileType', '$fileSize', '$uploadfile', CURRENT_TIMESTAMP
      )
    ";
    
    mysql_query($query) or die('Erro, falha na requisição.'); 

    //Imprime o agradecimento se o upload ocorrer com sucesso:
    if ($f > (count($_FILES['userfile']['tmp_name'])-2)){ ?>
      <html xmlns="http://www.w3.org/1999/xhtml" lang="en">
        <head>
          <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <meta name="author" content="Marcelo Pereira Rodrigues" />
          <!-- Bootstratp --> 
          <link rel="stylesheet" href="bootstrap-3.2.0-dist/css/bootstrap.min.css">
          <link rel="stylesheet" href="bootstrap-3.2.0-dist/css/bootstrap-theme.min.css">
          <link rel="stylesheet" href="Standards/standards.css">
        </head>
        
        <body>
          <!-- Cabeçalho -->
          <div class="container">
            <!-- <h1 style="margin-top: 10px; text-align: center;">Compleaks</h1> -->
          </div>
          
          <div style="margin-top: 10px; text-align: center;" class="container bg-success">
            <strong><p style="margin-top: 10px;">Obrigado pela contribuição.<br>
              O upload do(s) arquivo(s) foi realizado com sucesso!<br></strong>
              
              <?php
                /* echo "$tipologia de $disciplina de $ano/$semestre<br>
                    Professor(a): $professor<br>" */
              ?>
              
            </p>
          </div>
          
          <div style="text-align: center; margin-top: 25px;">
            <a href="index.html" type="button" class="btn btn-primary"> Voltar </a>
          </div>
          
          <!-- Bootstrap -->
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
            <script src="bootstrap-3.2.0-dist/js/bootstrap.min.js"></script>
            <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
          <script src="bootstrap-3.2.0-dist/js/bootstrap.min.js"></script> 
        </body>
      </html>
    <?php
    }
  }
  mysql_close($conn);
?>












