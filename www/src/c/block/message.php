<?php

App::$i->lib( 'Db_Adapter' );
$pdo = Db_Adapter::getPDO( 'memory' );

echo "<pre>";
//#------------------- Q1 -------------------#//
$query = $pdo->exec('CREATE TABLE foo(bar TEXT, baz TEXT, num NUMERIC, empty TEXT)');

// echo $query->getSQL() . PHP_EOL;

//#------------------- Q2.1 -------------------#//
$query = $pdo->query("INSERT INTO foo VALUES (null, 'For baz', 14, 'finish')");

echo $query->getSQL() . PHP_EOL;

//#------------------- Q2.2 -------------------#//
$query = $pdo->prepare('SELECT * FROM foo WHERE bar = :bar AND baz = :baz');

// Test with passed in array
echo $query->getSQL(array(':bar' => 'fo\'o', ':baz' => 'bat')) . PHP_EOL;
//SELECT * FROM foo WHERE bar = 'fo''o' AND baz = 'bat'

//#------------------- Q3 -------------------#//
$query = $pdo->prepare('SELECT * FROM foo WHERE bar = :bar');

// Test with bound params and values
$bar = 'bar';
$baz = 'baz';
$num = 14;
$empty = 'empty!!';


// Bind Param
// $query->bindParam(':bar', $bar);

// Bind Value
// $query->bindValue(':baz', $baz);

// Bind With types
$query->bindParam(':num', $num, PDO::PARAM_INT);

$query->execute();

$query->bindParam(':empty', $empty, PDO::PARAM_NULL);

echo $query->getSQL() . PHP_EOL;
//SELECT * FROM foo WHERE bar = 'bar' AND baz = 'baz' AND num = '0' AND empty=NULL

//#------------------- Q3.1 -------------------#//
// Change the vars
$bar = 'foo';
$baz = 'bat';
$num = '2.6';
$empty = 'blah!';

echo $query->getSQL() . PHP_EOL;
//SELECT * FROM foo WHERE bar = 'foo' AND baz = 'baz' AND num = '2' AND empty=NULL 

//#------------------- Q3.2 -------------------#//
// Bind with length
$query->bindParam(':bar', $bar, PDO::PARAM_STR, 2);

echo $query->getSQL() . PHP_EOL;
//SELECT * FROM foo WHERE bar = 'fo' AND baz = 'baz' AND num = '2' AND empty=NULL 

echo "</pre>";
return void;





















































if ( ! count( $this->_parent )) {
	$this->message = 'I have no options yet!';
	return 'Please, send me some options.';
}

$this->message = "I took this options: '"
	. implode( "', '", $this->_parent ) . "'.";

return 'I have ' . count( $this->_parent ) . ' options. Thanks man!';