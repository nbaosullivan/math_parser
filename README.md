# Math Parser Drupal 8 Module
This Drupal 8 module provides a mathematical string parser service, field type, and configurable field formatter.

## Getting started

### Prerequisites
You will need a working Drupal 8 instance with the module dependencies installed.

`composer require drupal/graphql_views` and then install within Drupal. This will also install GraphQL.

**Module Dependencies**
- [GraphQL Drupal Module](https://www.drupal.org/project/graphql) 
- [GraphQL Views Drupal Module](https://www.drupal.org/project/graphql_views)
### Installing
 
1. Include this module in the `/modules` folder of a Drupal 8 instance.
2. Navigate to `js/app` and run `npm install` and then `npm run build`.
3. Navigate to `/admin/modules` and install `Math Parser` and `Math Parser Example`, found under the Field Types category. 
This will create an example content type called `Maths quiz` and a view called `Math Parser Example` (used for GraphQL request from the React app).
4. Create a piece of Maths quiz content, demonstrated below. 

![](https://media.giphy.com/media/Swmfqn0IZPuXggmfX3/giphy.gif)

## Overview
### Field formatter widget options

- `React` displays the formulas as an interactive React quiz. This only works when using the Math Parser field type.
- `Twig` will render the field without loading the React javascript and display it in the following format `{{formula}} = {{result}}`. This formatter widget option can also work on Text (plain) field types. 
- `Answer only` will render just the answer. This is used within the GraphQL view to return only the answer when checking if the user input is correct. This formatter widget option can also work on Text (plain) field types.

By default, the field formatter will display the formulas in an interactive quiz as seen below. Put in your answer and hit the enter key.

![](http://g.recordit.co/eq5QPdqEXe.gif)

### Drupal Service

This module provides the MathParser service, found in `src/Services/MathParser.php`.
You can access this like any other Drupal service - see the example below.

```
      $string = '2+2';  
      $math_parser = \Drupal::Service('math_parser.math_parser');
      $parsed_value = $math_parser->calculate($string);
```

### React
When using the `React` widget option for the field formatter, a progressively decoupled React app using Webpack is included within the Math Parser field template. 
This uses GraphQL to retrieve the formula solution when a user enters their answer, and after three attempts or a correct answer, the user is given the next formula. 

*Please note that currently the React requests using GraphQL won't work without the `Math Parser Example` module enabled as the GraphQL query depends on a specific view that references fields that are also generated upon installation of the example module.* 
 
## Running the tests
You may need to run `composer run-script drupal-phpunit-upgrade` if PHPUnit testing framework is out of date.

Module unit tests can be found in `tests/src/Unit` and run using PHPUnit.
 
**Recommended:** create phpunit.xml.dist (using config similar to the below example in root of project and run `./vendor/bin/phpunit`
```
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="core/tests/bootstrap.php" colors="true">
  <testsuites>
    <testsuite name="unit">
      <directory>modules/custom/math_parser/tests</directory>
    </testsuite>
  </testsuites>
</phpunit>
```