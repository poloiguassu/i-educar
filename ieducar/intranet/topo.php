<?php
  require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/bootstrap.php';
  $entityName = $GLOBALS['coreExt']['Config']->app->entity->name;

  // Como já está amarrado lá em cima pelo $_SERVER['DOCUMENT_ROOT'], aproveitamos aqui.
  try {
    $release_file = $_SERVER['DOCUMENT_ROOT'] . '/version.txt';
    if (file_exists($release_file))
      $release_info = file_get_contents($release_file);
  } catch (Exception $e) {
    $release_info = False;
  }
?>

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel=stylesheet type='text/css' href='styles/reset.css' />
    <link rel=stylesheet type='text/css' href='styles/portabilis.css' />
  </head>

  <body>
    <div id="cabecalho" class="texto-normal">
      <div id="ccorpo">
        <p><a id="logo" href="javascript:updateFrame()"><span class="logoTJ">&nbsp;</span></a><span id="status"><span id="entidade"><?php echo $entityName; ?></span></span></p>
      </div>
    </div>
  </body>

	<script language="JavaScript">
		function updateFrame(){
			window.parent.frames[1].location="/intranet/index.php"
		}
	</script>

</html>
