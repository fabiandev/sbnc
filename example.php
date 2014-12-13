<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require 'Sbnc.php';
$sbnc = new Sbnc\Sbnc();
$sbnc->check();
?>
<!doctype html>
<html lang="en">
<head>
    <title>sbnc example</title>
</head>
<body>
<h3>v0.2</h3>
<form action="example.php" method="post">
    <fieldset>
        <legend>Data:</legend>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php $sbnc->filter('name'); ?>" required><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php $sbnc->filter('email'); ?>" required><br>
    </fieldset>
    <fieldset>
        <legend>Message:</legend>
        <textarea id="message" name="message" required><?php $sbnc->filter('message'); ?></textarea><br>
    </fieldset>
    <?php $sbnc->print_fields(); ?>
    <input type="submit" id="submit" value="Submit">
</form>
<?php $sbnc->print_js(); ?>
</body>
</html>
