<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require 'sbnc.php';
$sbnc = new Sbnc\sbnc();
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
        <input type="text" id="name" name="name" value="" required><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="" required><br>
    </fieldset>
    <fieldset>
        <legend>Message:</legend>
        <textarea id="message" name="message" required></textarea><br>
    </fieldset>
    <?php $sbnc->print_fields(); ?>
    <input type="submit" id="send" value="Submit">
</form>
</body>
</html>
