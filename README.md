## About sbnc

SBNC (Spam Block No Captcha) does what the name already tells you. It blocks spam with no need for captchas.
[http://fabianweb.net/sbnc](http://fabianweb.net/sbnc)

## What's new

Everything. This version is not compatible with 0.1.x!

## Requirements

PHP 5 >= 5.4.0

## Progress

Nearly finished for a first developer release. v0.2 will replace the master soon and you will find v0.1 in a branch.

## Quickstart

Please note, that the quick start only covers the very basics of sbnc. There is much more to
know and a lot of possibilities. This readme will be updated within the next weeks with more
details.

#### Include the class

```php
require 'Sbnc.php';
use sbnc\Sbnc;

Sbnc::start();
```

Be sure to include and start sbnc before any other output has been done as it makes use of sessions and
header redirects!

#### Create a form

```php
<form action="form.php" method="post">
    <input type="text" id="name" name="name" value="<?php Sbnc::print_value('name'); ?>">
    // add any input fields you want
    <?php Sbnc::print_fields(); ?>
    <input type="submit" id="submit" value="Submit">
</form>
```

Create a form and let sbc pre-fill input fields if errors occurred with ```Sbnc::print_value('name');```
Don't forget to add fields required by sbnc with ```Sbnc::print_fields();```
Also make sure the form method is POST and the action points to the same file as the request comes from.
The action may point to another page if you adjust the settings.

#### Add JavaScript

```php
Sbnc::print_js();
```

Add JavaScript after the html form.

#### Check if submit was valid

```php
if (Sbnc::is_valid()) {
    // form was submitted and there were no errors
} elseif (Sbnc::is_invalid()) {
    // form was submitted, but errors occurred
}
```

Use this checks to know, if the form has been submitted in the previous request and if it's been valid.
By default sbnc uses flash messages and redirects, so reloading the page won't result in another submit.
