<?php
require 'sbnc.class.php';
$sbnc = new sbnc();

if($sbnc->checkRequest()) {
    // form submitted and no bot spam detected!
    // here comes your own validation and you can process your form
    // you can add errors from your own validation like this: $sbnc->addError('custom error');
}

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<?php
    $sbnc->printHead();
?>
<title>sbnc - Spam Block No Captcha</title>
</head>
<body>
<?php
if($sbnc->submit() && !empty($sbnc->errors)) {
    echo '<ul>';
    foreach($sbnc->getErrorMessages() as $error) {
        echo "<li>$error</li>";
    }
    echo '</ul>';
}
?>
<form action="example.php" method="post"<?php $sbnc->novalidate(); ?>>
    <fieldset>
        <legend>Data:</legend>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $sbnc->filter('name'); ?>" required><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $sbnc->filter('email'); ?>" required><br>
    </fieldset>
    <fieldset>
        <legend>Message:</legend>
        <textarea id="message" name="message" required><?php echo $sbnc->filter('message'); ?></textarea><br>
    </fieldset>
    <?php
        $sbnc->printFields();
    ?>
    <input type="submit" id="send" value="Submit">
</form>
</body>
</html>
