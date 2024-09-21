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
    ?>
    <title>Pages not found</title>
</head>
<body>
<div class="w3-margin w3-center">
    <h1 class="w3-text-green ">404</h1>
    <p><a href="<?= url('/') ?>">Go home</a></p>
</div>
</body>
</html>