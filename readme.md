# php-nano-template - Template Engine

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/9505345b0cca4307a96635413af8877e)](https://www.codacy.com/app/azettl/nanoPHP?utm_source=github.com&utm_medium=referral&utm_content=azettl/nanoPHP&utm_campaign=badger)

The php-nano-template class replaces placeholders in a string with values from an array.

## Installation

```cmd
composer require azettl/php-nano-template
```

## Usage

```php
$nano = new com\azettl\nano\template();
$nano->setTemplate(
  "<p>
    {user.greeting()} {user.function(2)} {user.function('test')} {user.first_name} {user.last name}! 
    Your account is <strong>{user.account.status}</strong> 
    {user.nonexistingnode}
  </p>"
);
$nano->setData($aData);
$nano->setShowEmpty(true);

echo $nano->render(); 
```

or

```php
$nano = new com\azettl\nano\template(
  "<p>
    {user.greeting()} {user.first_name} {user.last name}! 
    Your account is <strong>{user.account.status}</strong> 
    {user.nonexistingnode}
  </p>",
  [
    "user" => [
      "login" => "demo",
      "first_name" => "Anon",
      "last name" => "Ymous",
      "account" => [
        "status" => "active",
        "expires_at" => "2016-12-31"
      ],
      "greeting" => function(){
        return 'Hello';
      },
      "function" => function($param){
        return 'Test' . $param;
      }
    ]
  ]
);

echo $nano->render(); 
```

or

```php
$nano = new com\azettl\nano\template();
$nano->setTemplateFile(
  "tests/template.html"
);
$nano->setData($aData);

echo $nano->render(); 
```