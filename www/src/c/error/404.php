<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Упс, произошла ошибка</title>
</head>
<body>
	<h1>Ошибка 404: Страница не найдена</h1>
	<p>Знаете, обычно когда человек попадает на такую страницу, он думает:</p>
	<blockquote>Тупые программисты! Зачем делать сайт который не работает?</blockquote>
	<p>Но на самом деле, 404-я ошибка - вовсе и не ошибка. Просто по тому адресу, по которому Вы перешли, у нас нет никаких данных. Почему? Да всякое бывает. Может быть модератор удалил страницу, или Ваш друг, отправляя Вам ссылку, случайно написал точку в конце ссылки.</p>
	<p>Эта страница нужна специально для того, чтобы как можно комфортнее оповестить Вас о сложившейся ситуации. Ведь согласитесь, если бы здесь вылетели системные ошибки, куски кода и прочая программисткая мудрость - эта страница была бы куда менее приятной.</p>
	<p>Поэтому не злитесь на нас пожалуйста, попробуйте перейти на <a href="<?php lnk(); ?>">главную страницу</a> и найти что Вам нужно, а если Вы все же считаете что здесь что-то должно быть - напишите нам: <a href="mailto:admin@framework.lh" title="Техническая поддержка">admin@framework.lh</a>.</p>
	<?php $this->runModule('search'); ?>
</body>
</html>