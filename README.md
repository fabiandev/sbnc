## About sbnc

SBNC (Spam Block No Captcha) does what the name already tells you. It blocks spam with no need for captchas.
[http://fabianweb.net/sbnc](http://fabianweb.net/sbnc)

## How does it work?

	1. Check for mouse movement
	2. Check for keyboard usage
	3. Hidden field check
	4. Check for field manipulation
	5. Too fast form submit
	6. Too slow form submit
	7. HTML-Tags in inputs
	8. Optional email field check
	9. HTML5 compatible
	10. No-JavaScript mode available

## Quick start

#### Include SBNC class and create object
This will use the standard options of sbnc.
```php
require 'sbnc.class.php';
$sbnc = new sbnc();
```

#### Fetch form data if submitted
To make error handling easier you can add your errors to the sbnc error list.
Form fields with the name "mail" or "email" will be checked for a valid email address.
```php
if( $sbnc->checkRequest() ) {
    // here comes your own validation and you can process your form
    // you can add errors from your own validation: $sbnc->addError('custom error');
}
```

#### Add JavaScript
```php
<head>
<?php $sbnc->printHead(); ?>
</head>
```

#### Add stuff to your form
This adds sbnc form fields. The novalidate call is not required if you call ```$sbnc->printFields(false);``` (if no html5 doctype you may also remove ```$sbnc->novalidate();```)
```php
<form action="index.php" method="post"<?php $sbnc->novalidate(); ?>>
    // your form fields
    <?php $sbnc->printFields(); ?>
</form>
```

#### Error Handling
This fetches every error that occurred. Also errors you added with ```$sbnc->addError('custom error');```
```php
if( $sbnc->submit() && !empty( $sbnc->errors ) ) {
    foreach( $sbnc->getErrorMessages() as $error ) {
        // $error holds the error message
    }
}
```

#### One more thing...
Add this default value to your form fields, to refill them if errors occurred.
```php
<input type="text" name="name" value="<?php echo $sbnc->filter('name'); ?>">
```


## Options

You can set every option by calling ```$sbnc->option_<option_name> = $value;```

	- strict
		true = strict mode; javascript must be enabled (recommended)

	- html5
		false = not using html5 doctype

	- min
		integer = minimum seconds to wait until form can be submitted

	- max
		integer = maximum seconds after form can be submitted

	- gestmode (available with javascript mode enabled)
		"mouse" = only check for mouse movement (default)
		"keyboard" = only check for keyboard usage
		"both" = check for both

	- nojs
		false = don't use and don't include javascript (not recommended)

	- checkmail
		true, true   = check for valid email (fields with name "email" or "mail")
		true, false  = check fields with name "email" or "mail" if not empty
		false, true  = don't check email
		false, false = don't check email

## Public Functions:

	- printHead()
		prints javascript
	
	- printFields()
		adds form fields for sbnc checks
		
	- novalidate()
		disables browser auto-validation when using html5 (required!)
	
	- submit()
		checks if the request method is POST (form submitted)
		
	- getError($code)
		returns message (string) from error code
		
	- param($key[, $nl2br])
		refill form field after errors occurred

## Public Variables:

	- errors (array)
		error codes that occured during bot check; empty array if no errors
		
		
