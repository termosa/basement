# Добро пожаловать в Basement - скелет для PHP проектов

## Концепция

Я всегда не понимал для чего в фреймворках нужно столько проверок? Проверка на правильность переданных параметров, проверка на существование файлов, проверка на то, что объект не будет создан второй раз (Singleton). Зачем все это? Неужели программист будет стараться взломать собственный код? Хорошо если есть средства защиты от инъекции - это возможность очень часто используется программистами. Но скажите, зачем нужна проверка на существование файла подключаемой библиотеки? Как часто Вы работаете с динамической файловой структурой? Возможно у кого-то и встанет такая задача, но это же очень редкие случаи, зачем остальным 99% проектов носить за собой этот груз?

Я постоянно думаю об этом разрабатывая собственный фреймворк. Как результат - в нем не реализована защита от ошибок программиста. Я буду стараться делать его максимально быстрым, в первую очередь, для сервера, а уж потом для разработки программистом. Я не хочу приписывать этому фреймворку правила, стандарты, общепринятые паттерны и концепции. Далее, чтобы уменьшить количество негативных отзывов, скажу:

> Этот движок разрабатывается мною и для меня же. Мне интересно чтобы мне было удобно им пользоваться. Для остальных (на случай если кому-то придется работать с моим кодом) - движок очень прост и на его понимание уйдет совсем не много времени. К тому же я пишу довольно подробные комментарии в коде и обязательно напишу хорошей гайд. А пока, вот Вам readme.

Данный фреймворк никого не обязывает сохранять его структуру и исходные файлы в целостности. Если Вы считаете что стоит что-то изменить - ну кто Вам запретит это делать? Если в этом вопросе Вас беспокоит то, что при обновлении фреймворка Вам придется серьезно повозиться, то забудьте об этой проблеме! Каждый релиз отдельного модуля или фреймворка (обычно, любого) - это законченное приложение готовое для работы. Если речь идет об исправлении багов, то с учитывая масштаб данного фреймворка, этот баг будет крайне легко исправить по инструкции, которую я обязательно сделаю при возникновении таких проблем. Если же Вы переживаете что пришедший после Вас программист не сможет разобраться с измененным кодом:

1. Попробуйте разбить код на куски поменьше;
2. Тщательно комментируйте свой код;
3. Грамотно используйте VCS;
4. Напишите подробный readme с информацией о том что Вы изменили и зачем, а еще лучше: опишите так весь код;
5. Не помогло? Тогда используйте нормальные фреймворки типа Zend или Yii или напишите свой, как это сделал я;
6. Новый программист все еще не понимает код? Дайте ему по роже и скажите чтобы он перестал называть себя программистом.

## Структура

Я не могу сказать что этот код реализует шаблон проектирования HMVC, но считаю важным отметить что этот шаблон проектирования мне понравился и я реализовал иерархическое подключение, но триады Вы здесь не найдете - модель вывалилась. Я не понял как она должна туда вписываться и сказать по правде даже не интересно было в этом разбираться.

В какой-то момент я начал задумываться о том, чтобы убрать модели и вовсе и перенести их логику в контроллеры. Связано это было с тем, что их иерархический вызов позволял использовать контроллеры как функции, не затрагивая вид. Я пока не планирую добавлять скаффолдинг, отдав предпочтение стандартным возможностям PDO, поэтому такая схема была довольно легко реализуемой. Но я все же решил использовать более привычный подход отделив работу с данными.

Я рекомендую использовать файлы в формате ```ini``` для хранения настроек. За счет своего простого синтаксиса они обрабатываются быстрее (я хоть и проверял скорость работы, но все еще сам не до конца поверил) чем php-код и их удобнее редактировать. На случай если на эту страницу заглянет КЭП, жаждущий высказать свое недовольство моими советами, добавлю: ```ini``` файлы менее функциональны, поэтому в некоторых ситуациях они не подойдут.

В корне сайта требуется файл ```index.php``` определяющий несколько констант и запускающий фреймворк. Вместе с ним, по умолчанию, есть папка ```i```, предназначенная для подключаемых стилей, скриптов, шрифтов, изображений, флешек и чего сами еще придумаете, и ```src```, которая хранит в себе библиотеки, контроллеры, виды и пока больше ничего.

-

Далее будет описаны папки и файлы опубликованного фреймворка. Этот раздел не является правилом и необходим лишь для того чтобы программист смог быстрее понять что, где и зачем там лежит.

### Папки

* ```src``` - Защищенная папка хранящая в себе исполняемый код.
 * ```m``` - Папка для моделей. Путь хранится в константе ```M_FOLDER```.
 * ```v``` - Папка для видов. Путь хранится в константе ```V_FOLDER```.
  * ```template``` - Папка для шаблонов. Путь хранится в константе ```T_FOLDER```.
 * ```c``` - Папка для контроллеров. Путь хранится в константе ```C_FOLDER```.
 * ```lib``` - Папка для библиотек. Путь хранится в константе ```LIB_FOLDER```.
* ```i``` - Папка для подключаемых (англ. **I**nclude) файлов верстки.

### Файлы

* ```index.php``` - Точка входа в приложение. Настраивает и запускает фреймворк.
* ```src/App.php``` - По сути, это и есть фреймворк. Здесь определен класс внутри которого имеется свойство с настройками по умолчанию, свойство реализующее переменную ```instance``` из паттерна Singleton и методы для подключения библиотек и инициализации ```instance``` и запуска приложения.
* ```lib/Route/Router.php``` - Занимается распознаванием запросов и подключением модулей. Помещен в дополнительную папку лишь для демонстрации.
* ```lib/Module.php``` - Содержит класс на основе которого создаются модули.
* ```lib/Debugger.php``` - Добавляет панель отладки.

## Что еще нужно написать

* Именование классов внутри библиотек (подобно Zend-у)
* Инициализация приложения
* Методы приложения и базовых классов
* Использование иерархического подключения модулей
* Описание жизненного цикла приложения
* Константы