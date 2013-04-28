<?php

include 'app.php';

class RenderPathTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider provider
	 */
	public function test( $path, $current, $result )
	{
		$this->assertEquals( $result, parsePath( $path, $current ));
	}

	public function provider()
	{
		return array(
			array(	'/',	'/a',	'/' ),
			array(	'/',	'/a/b',	'/' ),
			array(	'/c',	'/a',	'/c'),
			array(	'c',	'/a/b',	'/a/c' ),
			array(	'c',	'/a/b',	'/a/c' ),
			array(	'c/d',	'/a/b',	'/a/c/d' ),
			array(	'.',	'/a/b',	'/a/b' ),
			array(	'.',	'/a/b',	'/a/b' ),
			array(	'',		'/a/b',	'/a/b' ),
			array(	'',		'/a/b',	'/a/b' ),
			array(	'a',	'/',	'/a' ),
			array(	'..',	'/a/b/c','/a/b' ),
			array(	'../d',	'/a/b/c',	'/a/d' ),
			array(	'../../e',	'/a/b/c/d',	'/a/e' )
			);
	}
}