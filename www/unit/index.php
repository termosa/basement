<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Unit tests</title>
	<style type="text/css">
		#content {border:5px ridge lightblue;margin-top:25px;padding:10px;}
		button {border:3px outset lightblue;padding:3px 5px;}
		button:hover {border-style:inset;}
	</style>
</head>
<body>

<form action="index.php" method="get">
	<div class="row">
		<button type="submit" name="test" value="test01">Метод <i>renderPath</i> - генерирует пути и ссылки</button>
	</div>
	<div class="row">
		<button type="submit" name="test" value="test02">Метод <i>getLnk</i> - возвращает ссылку пригодную для использования</button>
	</div>

	<input type="hidden" name="render" value="browser"/>
</form>

<div id="content">
	<?php
		if (isset($_GET['test']) && file_exists($_GET['test'])) {
			require_once 'PHPUnit/Autoload.php'; // Подключаем PHPUnit
			include $_GET['test'] . '/app.php'; // Запускаем тестирование
		} else {
			echo '<p>Ты мог бы что-нибудь запустить.</p>';
		}
	?>
</div>

</body>
</html>