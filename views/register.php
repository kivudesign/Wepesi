<?php
    ob_start();
    $title="Register";   
    if(isset($error)){
        var_dump($error);
    }
    else if(isset($result)){
        var_dump($result);
    }
?>
    <form action="<?php echo WEB_ROOT.'register' ?>" method="POST">
        name:<input type="text" name="name" id=""><br/>
        Email:<input type="text" name="email" id=""><br/>
        phone:<input type="text" name="phone" id=""><br/>
        Password:<input type="password" name="password" id=""><br/>
        <input type="hidden" name="token" value="<?php echo Token::generate();?>">
        <input type="submit" value="register">
    </form>

<?php
    $content=ob_get_clean();
    include 'view/template.php';
?>