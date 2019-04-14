<?php

namespace Drupal\math_parser\Services;

/**
 * Class MathParser
 *
 * @package Drupal\math_parser\Services
 */
class MathParser {

  /**
   * The token types and associated regex
   * Set in constructor
   *
   * @var array
   */
  protected $token_types;

  /**
   * Supported operators and precedence
   * Set in constructor
   *
   * @var array
   */
  protected $operators;

  /**
   * MathParser constructor.
   */
  public function __construct() {

    $this->operators = [
      '+' => [
        'precedence' => 2,
      ],
      '-' => [
        'precedence' => 2,
      ],
      '*' => [
        'precedence' => 3,
      ],
      '/' => [
        'precedence' => 3,
      ],
    ];

    $this->token_types = [
      "/^(\d+)/" => "T_DIGIT",
      "/^([\-\+\*\/])/" => "T_OPERATOR",
      "/^(\s+)/" => "T_WHITESPACE",
    ];

  }

  /**
   * Calculates a mathematical formula string
   *
   * @param $string
   *
   * @return float|int|mixed
   * @throws \Exception
   */
  public function calculate($string) {
    // Initialise result of calculation
    $result = 0;

    if (!$string) {
      // Gracefully fallback if empty string
      return $result;
    }

    // Grab tokenised array in postfix notation
    $output = $this->infixToPostfix($string);

    // Intialise array for use in evaluation
    $stack = [];

    // Loop through each token in postfix array
    foreach ($output as $token) {
      switch ($token['token']) {
        case 'T_DIGIT':
          // If digit, push straight to array
          array_push($stack, $token['match']);
          break;
        case 'T_OPERATOR':
          // Get our operands and remove from evaluation array
          $operand_2 = array_pop($stack);
          $operand_1 = array_pop($stack);
          // Evaluate sum based on operator type
          switch ($token['match']) {
            case '+':
              $result = $operand_1 + $operand_2;
              break;
            case '-':
              $result = $operand_1 - $operand_2;
              break;
            case '*':
              $result = $operand_1 * $operand_2;
              break;
            case '/':
              if ((int) $operand_2 === 0) {
                throw new \Exception("Division by zero: " . $operand_1 . " / " . $operand_2);
              }
              $result = $operand_1 / $operand_2;
              break;
          }
          // Push result to array
          array_push($stack, $result);
          break;
        case 'T_WHITESPACE':
          // Do nothing
          break;
      }

    }
    // Once finished, remainin array item is our result
    $result = array_pop($stack);
    return $result;
  }

  /**
   * @param $string
   *
   * @return array
   * @throws \Exception
   */
  public function tokenise($string) {
    // Initialise array to hold our tokens
    $tokens = [];
    // Begin at start of string
    $offset = 0;
    while ($offset < strlen($string)) {
      // Try get match on the start of our string
      // Based on $this->token_types
      $result = $this->match($string, $offset);
      // Throw exception if string contains illegal characters
      if ($result === FALSE) {
        throw new \Exception("Unable to parse string: " . $string);
      }
      // Add our digit, operator or whitespace token to array
      $tokens[] = $result;
      // Increment offset to look at next part of string
      $offset += strlen($result['match']);
    }
    return $tokens;
  }

  /**
   * @param $string
   * @param $offset
   *
   * @return array|bool
   */
  protected function match($string, $offset) {
    // Get part of string our offset dictates
    $string = substr($string, $offset);
    // Check if there's a tokenmatch at the start of string
    foreach ($this->token_types as $pattern => $name) {
      if (preg_match($pattern, $string, $matches)) {
        // If there is, return the matched value and
        // the token type
        return [
          'match' => $matches[1],
          'token' => $name,
        ];
      }
    }
    return FALSE;
  }

  /**
   * Converts infix string to postfix string OR tokenised array
   * @param $string
   * @param string $format
   *
   * @return array|string
   * @throws \Exception
   */
  public function infixToPostfix($string, $format = 'array') {
    // Tokenize
    $tokens = $this->tokenise($string);

    // Shunting yard algorithm to convert infix string to postfix string
    // E.g. 10 + 20 - 30 + 15 * 5 => 10 20 + 30 - 15 5 * +
    $output = [];
    $operator_stack = [];

    foreach ($tokens as $token) {
      switch ($token['token']) {
        case 'T_DIGIT':
          // If number, add token to output
          $output[] = $token;
          break;
        case 'T_OPERATOR':
          //Push token to stack if there is an operator at the top of the operator stack with greater precedence
          foreach ($operator_stack as $operator) {
            $last_precedence = $this->operators[end($operator_stack)['match']]['precedence'];
            $current_precedence = $this->operators[$token['match']]['precedence'];
            if ($last_precedence >= $current_precedence) {
              // pop operators from the operator stack, onto the output queue.
              $output[] = array_pop($operator_stack);
            }
          }
          // Push onto operator stack
          $operator_stack[] = $token;

          break;
        case 'T_WHITESPACE':
          // Do nothing
          break;
      }
    }
    // Add the rest of the operators
    $output = array_merge($output, array_reverse($operator_stack));

    // Allow this function to be use to just output postfix string
    // instead of array (default)
    if ($format == 'string') {
      $string_output = '';
      // Create concatenated string with postfix array
      array_walk($output, function ($token) use (&$string_output) {
        $string_output .= $token['match'];
      });
      $output = $string_output;
    }

    return $output;
  }
}