<?php
/**
 *
 */

use Wepesi\Core\Bundles;
use Wepesi\Core\I18n;
use Wepesi\Core\Session;
use Wepesi\Core\Token;

$lang = Session::exists("lang") ? Session::get("lang") : "en";
$errors = Session::exists("errors") ? Session::flash("errors") : null;
$language = new I18n($lang);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $language->translate('Welcome') ?></title>

    <!-- Favicons -->
    <link href='assets/img/favicon.ico' rel='icon'>
    <link href='assets/img/logo.jpg' rel='wepesi-icon'>
    <?php
    Bundles::insertCSS('w3');
    ?>
</head>

<body>
<div class='w3-container w3-center'>
    <h2 class='w3-blue-gray'><?= $language->translate('Welcome to Wepesi') ?></h2>
    <p><?= $language->translate('A Simple Php MVC platform to develop quickly a php application') ?></p>
    <p class="w3-text-gray">
        <?= $language->translate('you can find the simple example here') ?>: <a class="w3-text-blue"
                                                                                href="https://github.com/bim-g/library-exemple">example</a>
    </p>
    <H2 class="w3-text-blue-gray w3-padding">Home Pages</H2>
    <?php if ($errors) { ?>
        <div class="w3-panel  w3-red">
            <?= $errors ?>
        </div>
    <?php } ?>
    <div class="w3-card w3-border w3-round-large " style="width: 300px;overflow: hidden">
        <h3 class="w3-text-blue-gray w3-padding"><?= $language->translate("Change the language") ?></h3>
        <form action="<?= WEB_ROOT . "changelang" ?>" method="post">
            <input type="hidden" name="token" value="<?= Token::generate() ?>">
            <select name="lang" id="lang_id" class="w3-select w3-center">
                <option value="" class="w3-center w3-large" disabled selected><?= $lang ?></option>
                <option value="fr">Francais</option>
                <option value="en">English</option>
            </select>
            <div class="w3-padding w3-light-gray">
                <input type="submit" class="w3-button w3-blue" value="<?= $language->translate("Translate") ?>">
            </div>
        </form>
    </div>
</div>
</body>
</html>