<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require 'Sbnc.php';
$sbnc = new Sbnc\Sbnc();
$my_action = function($sbnc) {
    // add your action, e.g. sending a mail
    // if you use flash messages.
    //
    // use any public sbnc method here, like add_error
    // with $sbnc->add_error('My error message');
};
$sbnc->start($my_action);
?>
<!doctype html>
<html lang="en">
<head>
    <title>sbnc example</title>
</head>
<body>
<h3><a href="http://fabianweb.net/sbnc">sbnc v0.2</a> [<a href="https://github.com/fabianweb/sbnc">github</a>]</h3>
<?php
$sbnc->print_errors();
// OR: $sbnc->print_error();
?>
<form action="example.php" method="post">
    <fieldset>
        <legend>Data:</legend>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php $sbnc->print_value('name'); ?>" required><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php $sbnc->print_value('email'); ?>" required><br>
    </fieldset>
    <fieldset>
        <legend>Message:</legend>
        <textarea id="message" name="message" required><?php $sbnc->print_value('message'); ?></textarea><br>
    </fieldset>
    <?php $sbnc->print_fields(); ?>
    <input type="submit" id="submit" value="Submit">
</form>
<?php $sbnc->print_js(); ?>
</body>
</html>
