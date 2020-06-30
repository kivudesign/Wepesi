<?php
    ob_start();
    ?>

    <div class="w3-content w3-margin w3-padding">
        <div class="w3-bar w3-blue">
            <H3>Hello Libraries</H3>
        </div>
        <div class="w3-padding">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Ducimus, mollitia minus quia nobis id ab praesentium vel, labore blanditiis error incidunt corporis neque facere architecto rem repellat, eligendi iste suscipit.
        </div>
    </div>

    <?php
    $content=ob_get_clean();
    include "../views/template.php";

?>