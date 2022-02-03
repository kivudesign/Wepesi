<?php

use Wepesi\Core\I18n;

$trans = new I18n();

?>
<div class="w3-container w3-center">
    <h2 class="w3-blue-gray"><?=$trans->translate("Welcom to Wepesi")?></h2>
    <nav class="w3-bar w3-border w3-round-large">
        <a href="<?=WEB_ROOT?>" class="w3-bar-item w3-btn <?=$home?>"><?=$trans->translate("Home")?></a>
        <a href="<?=WEB_ROOT."contact"?>" class="w3-bar-item w3-btn  <?=$contact?>"><?=$trans->translate("contact")?></a>
    </nav>
    <p><?=$trans->translate("A Simple Php MVC platform to develop quickly a php application")?></p>
    <p class="w3-text-gray"><?=$trans->translate("you can find the simple example here")?>: <a class="w3-text-blue" href="https://github.com/bim-g/library-exemple">example</a> </p>
</div>