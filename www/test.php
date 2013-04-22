<pre><?php

error_reporting(E_ALL);
ini_set('display_errors',1);

function testMemoryUsage($item, $key) {
	$t = call_user_func($item);
	// $t -= 32; // Столько места занимает одна переменная внутри функции?
	echo "#" . $key . ":\t" . $t . "\n";
}

function localVars() {
	$functions = array(
		'a_null'=>
		function(){//\\//\\//\\//\\//\\//\\//\\//
			$s = NULL;
			$a = NULL;
			$s = memory_get_usage();

			$a = NULL;

			return memory_get_usage()-$s;
		},

		'a_empty'=>
		function(){//\\//\\//\\//\\//\\//\\//\\//
			$s = NULL;
			$a = NULL;
			$s = memory_get_usage();

			$a = '';

			return memory_get_usage()-$s; // Предположительно: +8 - на определение формата и +8 - за каждый чанк
		},

		'a_empty_to_null'=>
		function(){//\\//\\//\\//\\//\\//\\//\\//
			$s = NULL;
			$a = '';
			$s = memory_get_usage();

			$a = NULL;

			return memory_get_usage()-$s;
		},

		'unset_a_null'=>
		function(){//\\//\\//\\//\\//\\//\\//\\//
			$s = NULL;
			$a = NULL;
			$s = memory_get_usage();

			unset($a);

			return memory_get_usage()-$s; // Наверное NULL - не занимает места в памяти
		},

		'unset_a_empty'=>
		function(){//\\//\\//\\//\\//\\//\\//\\//
			$s = NULL;
			$a = ''; // 48
			$s = memory_get_usage();

			unset($a);

			return memory_get_usage()-$s; // Вероятно что переменная занимает 32 байта
		},

		'aaabbbbbbbbccccccccd_null'=>
		function(){//\\//\\//\\//\\//\\//\\//\\//
			$s = NULL;
			$aaabbbbbbbbccccccccd = NULL;
			$s = memory_get_usage();

			$aaabbbbbbbbccccccccd = NULL;

			return memory_get_usage()-$s;
		},

		'a_len_1'=>
		function(){//\\//\\//\\//\\//\\//\\//\\//
			$s = NULL;
			$a = NULL;
			$s = memory_get_usage();

			$a = 'a';

			return memory_get_usage()-$s; // Забавно что переменная с одним символом занимает столько же памяти сколько и переменная с пустой строкой - 16 байт
		},

		'a_len_7'=>
		function(){//\\//\\//\\//\\//\\//\\//\\//
			$s = NULL;
			$a = NULL;
			$s = memory_get_usage();

			$a = str_repeat('a',7);

			return memory_get_usage()-$s; // И переменная с семью символами тоже будет занимать 16 байт
		},

		'a_len_8'=>
		function(){//\\//\\//\\//\\//\\//\\//\\//
			$s = NULL;
			$a = NULL;
			$s = memory_get_usage();

			$a = str_repeat('a',8); // А вот когда в строке появится восьмой символ переменная уже будет занимать 24 байта

			return memory_get_usage()-$s;
		},

		'a_len_8x9'=>
		function(){//\\//\\//\\//\\//\\//\\//\\//
			$s = NULL;
			$a = NULL;
			$s = memory_get_usage();

			$a = str_repeat('a',8*9); // Для каждого восьмого символа в строке будет выделяться дополнительный чанк в строке
			$a = str_repeat('a',8*9+7); // Значения обеих строк - одинаково, так как размер чанка - 8 байт

			return memory_get_usage()-$s;
		},

		'a_len_8x9'=>
		function(){//\\//\\//\\//\\//\\//\\//\\//
			$s = NULL;
			$a = NULL;
			$s = memory_get_usage();

			$a = str_repeat('a',8*1240+7); // Формулы для подсчета занимаемой памяти:
			// echo 16+floor((strlen($a))/8)*8 . "\n";
			// echo 16+(strlen($a))-(strlen($a))%8 . "\n";

			return memory_get_usage()-$s;
		},
	);

	echo "\n__ Local vars:\n";
	array_walk($functions, 'testMemoryUsage');
}

localVars();
$globalVars=true;

if($globalVars){
	echo "\n__ Global vars:\n";

	$diff = 0; // Стабилизатор. На всякий случай...
	$s = NULL; // Эта переменная будет считаться в memory_get_usage если ее не создать предварительно

	//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//
	unset($a);
	$s = memory_get_usage();

	$a = NULL; // На создание переменной уйдет 80 байт

	$t = memory_get_usage()-$s+$diff;
	echo "#create_a_null:\t" . $t . "\n";

	//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//
	$a = NULL; // Здесь переменная занимает 80 байт
	$s = memory_get_usage();

	$a = NULL; // Ничего не меняется, соответственно - ноль

	$t = memory_get_usage()-$s+$diff;
	echo "#set_null_a_null:\t" . $t . "\n";

	//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//
	$a = NULL;
	$s = memory_get_usage();

	unset($a); // Оп, минус 80 за пропавшую переменную

	$t = memory_get_usage()-$s+$diff;
	echo "#unset_a_null:\t" . $t . "\n";

	//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//
	$a = '';
	$s = memory_get_usage();

	unset($a); // Оп, минус 80 за пропавшую переменную

	$t = memory_get_usage()-$s+$diff;
	echo "#unset_a_empty:\t" . $t . "\n"; // -80 за убитую переменню и минус 16 за строку с одним чанком

	//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//
	unset($aaab);
	$s = memory_get_usage();

	$aaab = NULL; // На создание переменной, имя которой длиннее 3х символов - уйдет 88 байт

	$t = memory_get_usage()-$s+$diff;
	echo "#create_aaab_null:\t" . $t . "\n";

	//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//
	unset($aaabbbbbbbbccccccccd);
	$s = memory_get_usage();

	$aaabbbbbbbbccccccccd = NULL; // На создание переменной, имя которой длиннее 3х символов - уйдет 88 байт

	$t = memory_get_usage()-$s+$diff;
	echo "#create_aaabbbbbbbbccccccccd_null:\t" . $t . "\n";

	//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//
	unset($a);
	$s = memory_get_usage();

	$a = str_repeat('a',1);

	$t = memory_get_usage()-$s+$diff;
	echo "#a_len_1:\t" . $t . "\n";

	//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//
	unset($a);
	$s = memory_get_usage();

	$a = str_repeat('a',8);

	$t = memory_get_usage()-$s+$diff;
	echo "#a_len_8:\t" . $t . "\n";
}