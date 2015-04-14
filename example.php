<?php
require 'Sbnc.php';
use sbnc\Sbnc;

$my_action = function () {
    // add your own actions here
    //
    // you can use any public sbnc method at this point!
    //
    // it's also a good place to use Sbnc::addError('My error message');
    // if you add some logic on your own
};
Sbnc::start($my_action); // or simply call Sbnc::start();
?>
<!doctype html>
<html lang="en">
<head>
    <title>sbnc example</title>
</head>
<body>

<h3><a href="http://fabianweb.net/sbnc">sbnc v0.2</a> [<a href="https://github.com/fabianweb/sbnc">github</a>]</h3>

<p>
    <?php
    if (Sbnc::passed()) {
        echo '<h4 style="color:green">The form is valid.</h4>';
        // you may send an email here if the form was submitted without errors
    }

    if (Sbnc::failed()) {
        echo '<h4 style="color:red">Errors occured</h4>';
    }
    ?>
</p>

<p>
    Display All Errors:
    <?php Sbnc::printErrors(); ?>
    <?php if (Sbnc::numErrors() < 1) echo 'No errors'; ?>

</p>

<p>
    Display Single Error:
    <br><br>
    <?php Sbnc::printError(); ?>
    <?php if (Sbnc::numErrors() < 1) echo 'No errors'; ?>
</p>

<form action="example.php" method="post" novalidate>
    <fieldset>
        <legend>Data:</legend>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php Sbnc::printValue('name'); ?>" required><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php Sbnc::printValue('email'); ?>" required><br>
    </fieldset>
    <fieldset>
        <legend>Message:</legend>
        <textarea id="message" name="message" required><?php Sbnc::printValue('message'); ?></textarea><br>
    </fieldset>
    <?php Sbnc::printFields(); ?>
    <input type="submit" id="submit" value="Submit">
</form>

<?php Sbnc::printJavascript(); ?>
</body>
</html>
