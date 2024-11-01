<?php
    if (!defined('ABSPATH')) {
        exit;
    }
    ?>

<form action="<?php echo esc_url(get_option("woocommerce_denizpos_settings")['api_type']) ?>mpi/3DHost.aspx"
      id="perForm" method="post">

    <input type="hidden" name="ShopCode" value="<?php esc_html_e($shopCode); ?>"/>
    <input type="hidden" name="PurchAmount" value="<?php esc_html_e($purchaseAmount); ?>"/>
    <input type="hidden" name="Currency" value="<?php esc_html_e($Currency); ?>"/>
    <input type="hidden" name="OrderId" value="<?php esc_html_e($order_id); ?>"/>
    <input type="hidden" name="OkUrl" value="<?php echo esc_url($okUrl); ?>"/>
    <input type="hidden" name="FailUrl" value="<?php echo esc_url($failUrl) ?>"/>
    <input type="hidden" name="Rnd" value="<?php esc_html_e($rnd); ?>"/>
    <input type="hidden" name="Hash" value="<?php esc_html_e($hash); ?>"/>
    <input type="hidden" name="TxnType" value="<?php esc_html_e($txnType); ?>"/>
    <input type="hidden" name="SecureType" value="3DHost"/>
    <input type="hidden" name="Version3D" value="2.0"/>
    <input type="hidden" name="Lang" value="tr"/>
    <input type="hidden" name="IsBonus" value="0">
    <input type="submit" value="DEVAM" id="devam">
</form>

<style>
    body {
        display: none!important;
    }
</style>
<script>
    jQuery(document).ready(function ($) {
        $("#devam").click();
    });
</script>
