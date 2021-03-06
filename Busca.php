<?php
  error_reporting(0);
  header('Content-Type: text/html; charset=utf-8');
  
  include 'library/config.php';
  include 'library/opendb.php';

  //Conectando ao servidor:
  $servername = "";
  $username = "";
  $password = "";
  $dbname = "";
  $tbname = "";
  
  // Create connection
  $conn = mysql_connect($servername, $username, $password, $dbname);
  // Check connection
  if (!$conn) {
      die("Connection failed: " . mysql_connect_error());
  }
  /* else echo "Conectado ao banco de dados {$dbname}<br>"; */
  
  mysql_select_db($dbname);
  
  mysql_query("SET NAMES 'utf8'");
  mysql_query('SET character_set_connection=utf8');
  mysql_query('SET character_set_client=utf8');
  mysql_query('SET character_set_results=utf8');
    
  // Nome das variáveis POST:
  $disciplina = $_POST['disciplina'];
  $tipologia = $_POST['tipologia'];
  $professor = $_POST['professor'];
    
  //Troca os caracteres usados em html para seus esquivalentes:
  $disciplina = htmlspecialchars($disciplina);
  $tipologia = htmlspecialchars($tipologia);
  $professor = htmlspecialchars($professor);
     
  //Certifica-se de que ninguém fará uma injeção de SQL:
  $professor = mysql_real_escape_string($professor);
     
  //Trata os casos de busca: 
    if (strlen($professor) == 0 ){
      if($tipologia == "Todos"){
          $raw_results = mysql_query("SELECT * FROM `tbcompleaks`
            WHERE (`disciplina` LIKE '%".$disciplina."%') ORDER BY disciplina ASC, tipologia ASC, ano ASC") or die(mysql_error());
      }
      else $raw_results = mysql_query("SELECT * FROM `tbcompleaks`
            WHERE (`disciplina` LIKE '%".$disciplina."%') AND
            (`tipologia` LIKE '%".$tipologia."%') ORDER BY disciplina ASC, tipologia ASC, ano ASC") or die(mysql_error());
  } else {
      if($tipologia == "Todos"){
          $raw_results = mysql_query("SELECT * FROM `tbcompleaks`
            WHERE (`disciplina` LIKE '%".$disciplina."%') AND
            (`professor` LIKE '%".$professor."%') ORDER BY disciplina ASC, tipologia ASC, ano ASC") or die(mysql_error());
      }
      else $raw_results = mysql_query("SELECT * FROM `tbcompleaks`
            WHERE (`disciplina` LIKE '%".$disciplina."%') AND
            (`tipologia` LIKE '%".$tipologia."%') AND
            (`professor` LIKE '%".$professor."%') ORDER BY disciplina ASC, tipologia ASC, ano ASC") or die(mysql_error());
  }
  ?>
  
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <title>Compleaks 2.0 - Resultado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Marcelo Pereira Rodrigues" />
    <!-- Bootstratp --> 
    <link rel="stylesheet" href="bootstrap-3.2.0-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap-3.2.0-dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="Standards/standards.css">
  </head>
  
  <body style="text-align: center;">
    <!-- Cabeçalho -->
    <div class="container">
      <a style="color: inherit;" href="index.html"><h1 style="margin-top: 10px;">Compleaks 2.0<sup><sup><sub>beta</sub></sup></sup></h1></a>
    </div> <!-- /container -->
        
    <?php 
    //Monta a tabela com os resultados:
  if (strlen($professor)== 0) $professor = "Qualquer Um";
    if(mysql_num_rows($raw_results) > 0){   // if one or more rows are returned do following
  if ($disciplina == " ") $disciplina = "Todas";
      echo '<p style="margin-bottom: 15px;"> 
          <strong>Disciplina</strong>: '.$disciplina.'; 
          <strong>Material</strong>: '.$tipologia.';
          <strong>Professor</strong>: '.$professor.'.
          </p>';
    ?>
    <div class="container-fluid">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th style="text-align: center">Disciplina</th>
              <th style="text-align: center">Ano</th>
              <th style="text-align: center">Tipo</th>
              <th style="text-align: center">Professor</th>
              <th style="text-align: center">Observações</th>
              <th style="text-align: center">Download</th>
            </tr>
          </thead>
          <tbody>
        
  <?php
    //Entra em loop e imprime os resutados dentro do array results:
    if ($disciplina == "Todas") $disciplina = " ";
          while($results = mysql_fetch_array($raw_results)){
              ?>
                 <tr align="center">
                <td><?php echo $results['disciplina'] ?></td>
                <td><?php echo $results['ano']."/".$results['semestre'] ?></td>
                <td><?php echo $results['tipologia']?></td>
                <td><?php echo $results['professor']?></td>
                <td><?php echo $results['informacoes']?></td>
                <td><a href="<?php echo $results['conteudo']?>" class="btn btn-info">
                    <span class="glyphicon glyphicon-download-alt"></span>
                  </a>
                </td>
              </tr>          
              <?php
          }
      } else { // Se não tiver nenhum resultado:      
          echo '
          <strong>Disciplina</strong>: '.$disciplina.'; 
          <strong>Material</strong>: '.$tipologia.';
          <strong>Professor</strong>: '.$professor.'.
   
      <div style="margin-top: 10px; text-align: center;" class="container bg-warning">
        <strong><p style="margin-top: 10px;"><h3>Sem Resultados<br>
          :-/<br><h3></strong>
        </p>
      </div> 
          '; 
      }          
  ?>

        </tbody>
      </table>
      
      <div style="text-align: center; margin-top: 25px;">
      <a onclick="history.go(-1);" type="button" class="btn btn-primary"> Voltar </a>
    </div>
  </div>
    <!-- Bootstrap -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
      <script src="bootstrap-3.2.0-dist/js/bootstrap.min.js"></script>
      <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
    <script src="bootstrap-3.2.0-dist/js/bootstrap.min.js"></script> 
  </body>
  
