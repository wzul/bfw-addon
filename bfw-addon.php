<?php

/**
 * Plugin Name: BFW - Addon
 * Plugin URI: https://github.com/billplz
 * Description: Later
 * Author: Billplz Sdn. Bhd.
 * Version: 1.0.0
 * License: GPLv3
 * Text Domain: bfw-addon
 */

/* Load Bank Name List */
require 'bfw-bank-name.php';

class BFW_Addon
{
    public function __construct()
    {
        add_filter('bfw_has_fields', array($this, 'has_fields'));
        add_action('bfw_payment_fileds', array($this, 'payment_fields'), 10, 3);
        add_filter('bfw_reference_1_label', array($this, 'reference_1_label'));
        add_filter('bfw_reference_1', array($this, 'reference_1'));
        add_filter('bfw_url', array($this, 'url'));
    }

    public function has_fields($has_fields)
    {
        return true;
    }

    public function payment_fields($bfw)
    {
        $connnect = new BillplzWooCommerceWPConnect($bfw->api_key);
        $connnect->detectMode();
        $billplz = new BillplzWooCommerceAPI($connnect);
        list($rheader, $rbody) = $billplz->toArray($billplz->getFpxBanks());
            
        $bank_name = BillplzBankName::get();

        ?>
        <p class="form-row validate-required">
            <label><?php echo 'Choose Bank'; ?> <span class="required">*</span></label>
            <select name="billplz_bank" >
            <?php
            foreach ($rbody['banks'] as $bank) {
                if ($bank['active']) {
                    ?><option value="<?php echo $bank['name']; ?>"><?php echo $bank_name[$bank['name']] ? $bank_name[$bank['name']] : $bank['name']; ?></option><?php
                }
            }
            ?>
            </select>
        </p> 
        <?php
    }

    public function reference_1_label($reference_1_label)
    {
        return 'Bank Code';
    }

    public function reference_1($reference_1)
    {
        $bank_name = BillplzBankName::get();
        if (isset($bank_name[$_POST['billplz_bank']])) {
            return $_POST['billplz_bank'];
        }
        return '';
    }

    public function url($url)
    {
        return $url . '?auto_submit=true';
    }
}

new BFW_Addon();
