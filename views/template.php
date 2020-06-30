<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        // indert css librarie and files using bundles class methodes
        Bundles::insertCSS('w3');
        Bundles::insertCSS('index');
    ?>
    <title>Document</title>
</head>
<body>
    
    <?php echo $content;
    
    //insert js using php class methode
    Bundles::insertJS('w3');
    Bundles::insertJS('index');

    //include alert messages
        if(Session::get('danger') && !empty(Session::get('danger'))){
            include "./alert/danger.php";
        }
        if(Session::get('question') && !empty(Session::get('question'))){
            include "./alert/question.php";
        }
        if(Session::get('success') && !empty(Session::get('success'))){
            include "./alert/success.php";
        }
    ?>
</body>

</html>