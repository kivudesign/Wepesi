<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php
        use Wepesi\Core\Bundles;
        Bundles::insertCSS('w3');
    $home="";
    $contact="w3-green";
    ?>
    <title>Contact</title>
</head>
<body>
<?php
    include ("views/header.php")
?>
    <div class="w3-margin w3-card" style="width: 450px;">
        <div class="w3-padding w3-black">Contact Us</div>
        <form class="w3-container w3-padding">

            <label>Email</label>
            <input class="w3-input" type="text">

            <label>content</label>
            <input class="w3-input" type="text"><br>

            <button class="w3-btn w3-blue">Send</button>
        </form>
    </div>
</body>
</html>