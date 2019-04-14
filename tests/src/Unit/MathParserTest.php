<?php

namespace Drupal\Tests\math_parser\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\math_parser\Services\MathParser;

/**
 * MathParser unit tests.
 *
 * @group math_parser
 * @group math_parser
 */
class MathParserTest extends UnitTestCase {

  protected $math_parser;

  /**
   * Create new Math parser object so accessible to all test methods
   */
  public function setUp() {
    $this->math_parser = new MathParser();
  }

  /**
   * Data provider for testCalculate().
   * @return array
   */
  public function provideTestCalculate() {
    return [
      [4, '2+2'],
      [75, '10 + 20 - 30 + 15 * 5'],
      [23, '20 + 3 - 0/23'],
      [0, '']
    ];
  }
  /**
   * @covers \Drupal\math_parser\Services\MathParser::calculate
   * @dataProvider provideTestCalculate
   */
  public function testCalculate($expected, $math_string) {

    $this->assertEquals($expected, $this->math_parser->calculate($math_string));
  }
  /**
   * Data provider for testCalculateException().
   * @return array
   */
  public function provideTestCalculateException() {
    return [
      ['2-ASD'],
      ['?!@#'],
      ['qwerty'],
      ['2+3*FOUR'],
      ['20 + 3 - 23 / 0']
    ];
  }
  /**
   * @covers \Drupal\math_parser\Services\MathParser::calculate
   * @dataProvider provideTestCalculateException
   */
  public function testCalculateException($math_string) {

    $this->expectException(\Exception::class);
    $this->math_parser->calculate($math_string);

  }

  /**
   * Data provider for testTokenise().
   */
  public function provideTestTokenise() {
    return [
      [[
        [
          'match' => '34',
          'token' => 'T_DIGIT',
        ],
        [
          'match' => '+',
          'token' => 'T_OPERATOR',
        ],
        [
          'match' => '5',
          'token' => 'T_DIGIT',
        ],
        [
          'match' => '/',
          'token' => 'T_OPERATOR',
        ],
        [
          'match' => '3',
          'token' => 'T_DIGIT',
        ],
        [
          'match' => '+',
          'token' => 'T_OPERATOR',
        ],
        [
          'match' => '456',
          'token' => 'T_DIGIT',
        ],
      ], '34+5/3+456']
    ];
  }
  /**
   * @covers \Drupal\math_parser\Services\MathParser::tokenise
   * @dataProvider provideTestTokenise
   */
  public function testTokenise($expected, $math_string) {
    $this->assertArrayEquals($expected, $this->math_parser->tokenise($math_string));
  }

  /**
   * Data provider for testMatch().
   */
  public function provideTestMatch() {
    return [
      [[
        'match' => '433',
        'token' => 'T_DIGIT',
      ], '433+12', 0],
    ];
  }

  /**
   * @covers \Drupal\math_parser\Services\MathParser::match
   * @dataProvider provideTestMatch
   */
  public function testMatch($expected, $math_string, $offset) {

    // Use reflection to make match() public.
    $ref_match = new \ReflectionMethod($this->math_parser, 'match');
    $ref_match->setAccessible(TRUE);
    $this->assertArrayEquals($expected, $ref_match->invokeArgs($this->math_parser,[$math_string, $offset]));
  }

  /**
   * Data provider for testInfixToPostfix().
   */
  public function provideTestInfixToPostfix() {
    return [
     ['1020+30-155*+', '10 + 20 - 30 + 15 * 5'],
     ['1465*-184/6*+1+', '14 - 6 * 5 + 18 / 4 * 6 + 1']
    ];
  }
  /**
   * @covers \Drupal\math_parser\Services\MathParser::infixToPostfix
   * @dataProvider provideTestInfixToPostfix
   */
  public function testInfixToPostfix($expected, $math_string) {
    $this->assertEquals($expected, $this->math_parser->infixToPostfix($math_string, 'string'));
  }

  /**
   * Unset the $math_parser object after tests have been completed.
   */
  public function tearDown() {
    unset($this->math_parser);
  }
}