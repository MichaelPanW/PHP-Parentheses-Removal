# ParenthesesRemoval

Given a string containing an expression, return the expression with unnecessary parentheses removed.

# Example:
 - f("1*(2+(3*(4+5)))") ===> "1*(2+3*(4+5))"
 - f("2 + (3 / -5)") ===> "2 + 3 / -5"
 - f("x+(y+z)+(t+(v+w))") ===> "x+y+z+t+v+w"

## Introduction
Expression is a PHP class that removes unnecessary parentheses from mathematical expressions.

## Usage
### Running the Program
#### Using Docker

To run the program, use the following command:

```bash
COMMAND="php index.php" docker-compose up --build
```
This command will execute the index.php script with the specified command.

#### Using PHP Directly
If you already have PHP installed on your environment, you can execute the following commands:

```bash
composer install
php index.php
```
These commands will install dependencies using Composer and then run the index.php script directly.

### Running Tests
To run the PHPUnit tests, use the following command:

```bash
COMMAND="vendor/bin/phpunit" docker-compose up --build
```
This command will execute the PHPUnit tests located in the tests directory.