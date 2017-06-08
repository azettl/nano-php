# nanoPHP - Template Engine

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
