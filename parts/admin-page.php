<?php
?>
<div>
    <h1>Integração WooCommerce e TagPlus</h1>
    <?php

    $tagplus = (new \App\Tagplus\Tagplus());

    if (isset($_POST['Auth'])
        || (isset($_GET['state']) && isset($_GET['code']) && $_GET['code'])
    ) {
        $tagplus->getAuthorization();
        wp_redirect((new \App\Models\Config)->menuPageUrl);
        exit;
    }

    if (isset($_POST['RevokeAccess'])) {
        $tagplus->revokeAccess();
    }

    if ($tagplus->accessTokenIsValid()) {
        include BRV_PLUGIN_PATH . '/parts/authenticated.php';
    } else {
        $tagplus->buttonToAuth();
    }

    ?>
</div>
