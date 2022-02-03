<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php

    use Wepesi\App\Core\Bundles;

    Bundles::insertCSS('w3');
    $home="w3-green";
    $contact="";
    ?>
    <title>Welcom</title>
</head>

<body>

    <?php
        include ("views/header.php");

        if (isset($result)) {?>
        <div class="w3-padding w3-margin w3-blue">        
        <?=print_r($result);?>        
        </div>
    <?php }?>
</body>

</html>