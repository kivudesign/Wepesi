<?php

$message = BoxMessage::warning($_SESSION['warning']);
$_SESSION['warning'] = null;
?>
<div id="warning" class="w3-modal w3-animate-opacity">
    <div class="w3-modal-content w3-card-4 w3-animate-zoom">
        <div class="w3-card-4">
            <header class="w3-container w3-amber">
                <h1>Warning ! <span class="w3-small"><?php echo $message['type']; ?></span></h1>
            </header>
            <div class="w3-container">
                <p><?php echo $message['warning']; ?></p>
            </div>
            <footer class="w3-container w3-center w3-padding w3-amber">
                <button type="reset" class="w3-btn w3-round w3-border" onclick="w3.hide('#warning')">Cancel</button>
            </footer>
        </div>
    </div>
</div>