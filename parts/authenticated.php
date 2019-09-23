<?php
?>
<form action="<?php menu_page_url(\App\Models\Config::MENU_PAGE_SLUG); ?>"
      method="POST">
    <p class="submit">
        <?php

        submit_button(
            'Sincronização completa',
            'secondary',
            'synchronizeAll',
            false
        );

        $tagplus->buttonToRevokeAccess();

        ?>
    </p>
</form>
<?php

if (isset($_POST['synchronizeAll'])) {
    (new \App\Tagplus\ProductSynchronize())->synchronizeAll();
}

$tagplus = (new \App\Tagplus\Tagplus());

if (!$tagplus->accessTokenIsValid()) {
    return;
}

