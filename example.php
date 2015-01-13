<?php
error_reporting(E_ALL); // remove this line in production
ini_set('display_errors', 'On'); // remove this line in production

require 'Sbnc.php';

$sbnc = new Sbnc\Sbnc();

$my_action = function($sbnc) {
    // add your own actions here
    //
    // you can use any public sbnc method at this point!
    //
    // it's also a good place to use $sbnc->add_error('My error message');
    // if you add some logic on your own
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

<p>
<?php
if ($sbnc->is_valid()) {
    echo '<h4 style="color:green">The form is valid.</h4>';
    // you may send an email here if the form was submitted without errors
} elseif($sbnc->is_invalid()) {
    echo '<h4 style="color:red">Errors occured</h4>';
}
?>
</p>

<p>
    Display All Errors:
    <?php $sbnc->print_errors(); ?>
    <?php if ($sbnc->num_errors() < 1) echo 'No errors'; ?>

</p>

<p>
    Display Single Error:
    <br><br>
    <?php $sbnc->print_error(); ?>
    <?php if ($sbnc->num_errors() < 1) echo 'No errors'; ?>
</p>

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

<pre>
<?php
print_r($_SESSION);
?>
</pre>
</body>
</html>
