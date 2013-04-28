<?php

/* ____ MOCK ____ */

define( 'URL', 'site' );

global $_query;
global $_runStack;
$_runStack = array( '/' );

function parsePath( $path, $current )
{
	$result = array(
		'/^/a' => '/',
		'/^/a/b' => '/',
		'/c^/a' => '/c',
		'c^/a/b' => '/a/c',
		'c^/a/b' => '/a/c',
		'c/d^/a/b' => '/a/c/d',
		'.^/a/b' => '/a/b',
		'.^/a/b' => '/a/b',
		'^/a/b' => '/a/b',
		'^/a/b' => '/a/b',
		'a^/' => '/a',
		'..^/a/b/c' => '/a/b',
		'../d^/a/b/c' => '/a/d',
		'../../e^/a/b/c/d' => '/a/e',
		);
	return $result[ $path . '^' . $current ];
}

/* ____ END MOCKS ____ */

include 'app.php';

class GetLinkTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider easyProvider
	 */
	public function testEasy( $result, $link = '/', $request, $get = array() )
	{
		global $_query;
		$_query = $request;
		$this->assertEquals( URL . $result, getLnk( $link, $get ));
	}

	public function easyProvider()
	{
		return array(
			array( '/', '/', '/a' ),
			array( '/', '/', '/a/b' ),
			array( '/c', '/c', '/a' ),
			array( '/a/c', 'c', '/a/b' ),
			array( '/a/c', 'c', '/a/b' ),
			array( '/a/c/d', 'c/d', '/a/b' ),
			array( '/a/b', '.', '/a/b' ),
			array( '/a/b', '.', '/a/b' ),
			array( '/a/b', '', '/a/b' ),
			array( '/a/b', '', '/a/b' ),
			array( '/a', 'a', '/' ),
			array( '/a/b', '..', '/a/b/c' ),
			array( '/a/d', '../d', '/a/b/c' ),
			array( '/a/e', '../../e', '/a/b/c/d' ),
			);
	}

	/**
	 * @dataProvider hardProvider
	 */
	public function testHard( $result, $link = '/', $stack, $get = array() )
	{
		global $_runStack;
		array_push( $_runStack, $stack );
		$this->assertEquals( URL . $result, getLnk( $link, $get ));
	}

	public function hardProvider()
	{
		return array(
			array( '/a/c', '~/c', '/a/b' ),
			array( '/a/c', '~/c', '/a/b' ),
			array( '/a/c/d', '~/c/d', '/a/b' ),
			array( '/a/b', '~', '/a/b' ),
			array( '/a/b', '~', '/a/b' ),
			array( '/a', '~/a', '/' ),
			array( '/a/b', '~/..', '/a/b/c' ),
			array( '/a/d', '~/../d', '/a/b/c' ),
			array( '/a/e', '~/../../e', '/a/b/c/d' ),
			);
	}
}