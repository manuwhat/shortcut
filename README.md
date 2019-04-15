Class Instantiation Shortcut for PHP
=====================================


[![Build Status](https://travis-ci.org/manuwhat/shortcut.svg?branch=master)](https://travis-ci.org/manuwhat/shortcut)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/manuwhat/shortcut/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/manuwhat/shortcut/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/manuwhat/shortcut/badges/build.png?b=master)](https://scrutinizer-ci.com/g/manuwhat/shortcut/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/manuwhat/shortcut/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

Library which create custom Shortcut for classes Instantiation.


**Requires**: PHP 5.3+


### Why Create Shortcut for PHP classes Instantiation?


Typically you would create Shortcut for class instantiation if:

1. You need something more customized just like PHP  create arrays with the array() function .
2. You want to be more productive avoiding the "new" keyword usage but also too long class name repetitive usage .
3. Instead of a gain of productivity You just are  nostalgic of the times when you were using Python or the old fashion style of javaScript when you were not load yourself of the "new" keyword to instantiate a new object. 
4. Just for the fun to do it.
5. Because you are just crazy in love with functions -:).

### How to use it

Require the library by issuing this command:

```bash
composer require manuwhat/Shortcut
```

Add `require 'vendor/autoload.php';` to the top of your script.

Next, create a Shortcut, just like this:

```php
use ezama/Shortcut;

create_Shortcut('fullQualifiedclassname'[,'chosen_Shortcut_name']);
create_Shortcut('ArrayIterator');
```

The `Shortcut` is once and forever until cache suppression automatically created and the code included then you can use it in your script:

```php
$obj=chosen_Shortcut_name(); // ==> ### if no chosen name has been specified then you may use!
$obj=fullQualifiedclassname();// of course you must pass the arguments if the constructor require them at instantiation
$iterator=ArrayIterator([1,1,2,3,3]);

```
By default shortcuts are cached in a default directory but you can specify a custom directory path where shortcuts will be saved using
 
 ```php
use ezama/Shortcut;
Shortcut::setDir('your/path/here');
create_Shortcut('fullQualifiedclassname'[,'chosen_Shortcut_name']);
create_Shortcut('ArrayIterator','AI');
```
Keep in mind that only one custom Shortcut can be defined per class and that only instantiable classes can be used.


To run unit tests 
```bash
phpunit  ./tests
```
