# nanoPHP - Template Engine

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/9505345b0cca4307a96635413af8877e)](https://www.codacy.com/app/azettl/nanoPHP?utm_source=github.com&utm_medium=referral&utm_content=azettl/nanoPHP&utm_campaign=badger)

The nanoPHP class replaces placeholders in a string with values from an array.

## Usage

```php
$nano = new nano();
$nano->setTemplate(
  "<p>
    {user.greeting()} {user.first_name} {user.last name}! 
    Your account is <strong>{user.account.status}</strong> 
    {user.nonexistingnode}
  </p>"
);
$nano->setData($aData);
$nano->setShowEmpty(true);

echo $nano->render(); 
```
