<?php
  require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/bootstrap.php';
  $entityName = $GLOBALS['coreExt']['Config']->app->entity->name;
  $logoFileName = $GLOBALS['coreExt']['Config']->report->logo_file_name;
  $logoUrl = '/modules/Reports/ReportLogos/' . $logoFileName;

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
	<link rel=stylesheet type='text/css' href='styles/header.css' />
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro' rel='stylesheet' type='text/css'>
  </head>

  <body>
	<div class="header">
	    <a class="logo" href="/">Trilha Jovem Iguassu</a>
	    <span class="entity">
	    	<img src="<?php echo $logoUrl; ?>" alt="brasao" />
			<?php echo $entityName; ?>
		</span>
	</div>
  </body>

	<script language="JavaScript">
		function updateFrame(){
			window.parent.frames[1].location="/intranet/index.php"
		}
	</script>

</html>
