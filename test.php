<?php
error_reporting(E_ALL); // remove this line in production
ini_set('display_errors', 'On'); // remove

require 'Sbnc.php';
use sbnc\Sbnc;
Sbnc::start();
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
    if (Sbnc::is_valid()) {
        echo '<h4 style="color:green">The form is valid.</h4>';
        // you may send an email here if the form was submitted without errors
    } elseif(Sbnc::is_invalid()) {
        echo '<h4 style="color:red">Errors occured</h4>';
    }
    ?>
</p>

<p>
    Display All Errors:
    <?php Sbnc::print_errors(); ?>
    <?php if (Sbnc::num_errors() < 1) echo 'No errors'; ?>

</p>

<p>
    Display Single Error:
    <br><br>
    <?php Sbnc::print_error(); ?>
    <?php if (Sbnc::num_errors() < 1) echo 'No errors'; ?>
</p>

<form action="test.php" method="post">
    <fieldset>
        <legend>Data:</legend>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php Sbnc::print_value('name'); ?>" required><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php Sbnc::print_value('email'); ?>" required><br>
    </fieldset>
    <fieldset>
        <legend>Message:</legend>
        <textarea id="message" name="message" required><?php Sbnc::print_value('message'); ?></textarea><br>
    </fieldset>
    <?php Sbnc::print_fields(); ?>
    <input type="submit" id="submit" value="Submit">
</form>

<?php Sbnc::print_js(); ?>

<pre>
<?php
print_r($_SESSION);
//unset($_SESSION['sbnc_honeypot']);
?>
</body>
</html>
