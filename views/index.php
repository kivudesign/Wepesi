<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    Bundles::insertCSS('w3');
    ?>
    <title>Welcom</title>
</head>

<body>
    <div class="w3-container w3-center">
        <h2 class="w3-green">Welcom to library</h2>
        <p>The Simple Php MVC platform to develop quickly a php application</p>
        <p class="w3-text-gray">you can find the simple example here: <a class="w3-text-blue" href="https://github.com/bim-g/library-exemple">example</a> </p>

    </div>
    <div class="w3-padding w3-margin w3-blue">
        <?php
        if (isset($result)) {
            echo $result;
        }
        ?>
    </div>
</body>

</html>