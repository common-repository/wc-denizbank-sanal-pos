<?php
    if (!defined('ABSPATH')) {
        exit;
    }

    class WC_DenizPos_Gateway extends WC_Payment_Gateway
    {

        /**
         * Class constructor, more about it in Step 3
         */
        public function __construct()
        {

            $this->id = 'denizpos'; // payment gateway plugin ID
            $this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
            $this->has_fields = true;
            $this->method_title = 'Denizbank Sanal Pos';
            $this->method_description = 'Woocommerce için Denizbank Sanal Pos'; // will be displayed on the options page
            $this->supports = array(
                'products'
            );
            $this->init_form_fields();

            $this->init_settings();
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->enabled = $this->get_option('enabled');
            $this->testmode = 'yes' === $this->get_option('testmode');
            $this->private_key = $this->testmode ? $this->get_option('test_private_key') : $this->get_option('private_key');
            $this->publishable_key = $this->testmode ? $this->get_option('test_publishable_key') : $this->get_option('publishable_key');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));
            add_action('woocommerce_receipt_denizpos', array($this, 'denizpos_payment_redirect'));
            add_action('woocommerce_api_denizpos', array($this, 'webhook'));

        }

        /**
         * Plugin options,
         */
        public function init_form_fields()
        {

            $this->form_fields = array(
                'enabled' => array(
                    'title' => esc_html__('Aktif/Pasif', 'denizpos'),
                    'label' => esc_html__('Etkinleştir', 'denizpos'),
                    'type' => 'checkbox',
                    'description' => '',
                    'default' => 'no'
                ),
                'api_type' => array(
                    'title' => esc_html__('Api Türü', 'denizpos'),
                    'type' => 'select',
                    'options' =>
                        array(
                            'https://test.inter-vpos.com.tr/' => esc_html__('Test Ortam', 'denizpos'),
                            'https://inter-vpos.com.tr/' => esc_html__('Canlı Ortam', 'denizpos')
                        ),
                ),
                'title' => array(
                    'title' => esc_html__('Ödeme Seçeneği Başlık', 'denizpos'),
                    'type' => 'text',
                    'description' => esc_html__('Bu mesaj ödeme sırasında kullanıcıya gösterecektir.', 'denizpos'),
                    'default' => 'Denizbank Sanal Pos',
                    'desc_tip' => true,
                ),
                'description' => array(
                    'title' => esc_html__('Açıklama', 'denizpos'),
                    'type' => 'text',
                    'description' => 'Ödeme sırasında kullanıcının gördüğü açıklama',
                    'default' => 'Kredi kartı veya banka kartı ile güvenli ödeme yapabilirsiniz.',
                    'desc_tip' => true,
                ),

                'magaza_numarasi' => array(
                    'title' => esc_html__('Mağaza Numarası', 'denizpos'),
                    'type' => 'text',
                    'description' => 'Denizbank mağaza numaranızı giriniz.',
                    'desc_tip' => true,
                ),
                'kullanici_kodu' => array(
                    'title' => esc_html__('Kullanıcı Kodu', 'denizpos'),
                    'type' => 'text',
                    'description' => 'Denizbank kullanıcı kodunuzu giriniz.',
                    'desc_tip' => true,
                ),
                'sifre' => array(
                    'title' => esc_html__('Şifre', 'denizpos'),
                    'type' => 'text',
                    'description' => 'Denizbank şifrenizi giriniz.',
                    'desc_tip' => true,
                ),
                '3d_anahtari' => array(
                    'title' => esc_html__('3d Anahtarı', 'denizpos'),
                    'type' => 'text',
                    'description' => 'Denizbank 3d anahtarını giriniz.',
                    'desc_tip' => true,
                ),
                'form_type' => array(
                    'title' => esc_html__('Form Türü', 'paynkolay'),
                    'type' => 'select',
                    'options' =>
                        array(
                            'payment-form-api' => esc_html__('Ödeme Formu', 'paynkolay'),
                            'payment-form-iframe' => esc_html__('Ortak Ödeme Sayfası', 'paynkolay')
                        ),
                ),
            );

        }


        /*
         * Custom CSS and JS
         */
        public function payment_scripts()
        {
            wp_enqueue_style('denizpos-style', plugins_url('../assets/css/style.css', __FILE__));
            wp_enqueue_style('denizpos-bootstrap', plugins_url('../assets/css/bootstrap-icons.css', __FILE__));
        }


        public function process_payment($order_id)
        {

            $order = wc_get_order($order_id);

            return array(
                'result' => 'success',
                'redirect' => $order->get_checkout_payment_url(true)
            );
        }


        public function denizpos_payment_redirect($order_id)
        {


            $order = wc_get_order($order_id);
            $options = get_option("woocommerce_denizpos_settings");

            switch (get_woocommerce_currency()) {
                case "TRY":
                    $Currency = 949;
                    break;
                case "USD":
                    $Currency = 840;
                    break;
                case "EUR":
                    $Currency = 978;
                    break;
                case "GBP":
                    $Currency = 826;
                    break;
                case "JPY":
                    $Currency = 392;
                    break;
                case "RUB":
                    $Currency = 810;
                    break;
            }

            $shopCode = esc_html($options['magaza_numarasi']);
            $purchaseAmount = $order->get_total();
            $okUrl = get_site_url() . "/wc-api/denizpos";
            $InstallmentCount = 1;
            $rnd = microtime();
            $failUrl = $order->get_checkout_payment_url(true);
            $txnType = "Auth";
            $MerchantPass = $options['3d_anahtari'];

            $hashstr = $shopCode . $order_id . $purchaseAmount . $okUrl . $failUrl . $txnType . $InstallmentCount . $rnd . $MerchantPass;
            $hash = base64_encode(pack('H*', sha1($hashstr)));

            if (isset($_REQUEST['ErrorMessage'])) {
                echo '<ul class="woocommerce-error" role="alert">
			    <li>' . $_REQUEST['ErrorMessage'] . '</li>
	        </ul>';
            }

            if ($options['form_type'] == "payment-form-api") {
                require_once pgfd_plugin_dir.("") . "view/payment-form-api.php";
            } else {
                require_once pgfd_plugin_dir. "view/payment-form-ortak-odeme.php";
            }
        }


        /*
         * 3d complete
         */
        public function webhook()
        {
            $options = get_option("woocommerce_denizpos_settings");

            $hashparams = sanitize_text_field($_POST["HASHPARAMS"]);
            $hashparamsval = sanitize_text_field($_POST["HASHPARAMSVAL"]);
            $hashparam = sanitize_text_field($_POST["HASH"]);
            $paramsval = "";
            $index1 = 0;
            $index2 = 0;

            while ($index1 < strlen($hashparams)) {
                $index2 = strpos($hashparams, ":", $index1);
                $vl = $_POST[substr($hashparams, $index1, $index2 - $index1)];
                if ($vl == null)
                    $vl = "";
                $paramsval = $paramsval . $vl;
                $index1 = $index2 + 1;
            }
            $merchantpass = esc_html($options['3d_anahtari']);
            $hashval = $paramsval . $merchantpass;

            $hash = base64_encode(pack('H*', sha1($hashval)));

            if ($paramsval != $hashparamsval || $hashparam != $hash)
                echo "<h4>Güvenlik Uyarisi. Sayisal Imza Geçerli Degil</h4>";


            $Status = sanitize_text_field($_POST["3DStatus"]);
            if ($Status == 1 || $Status == 2 || $Status == 3 || $Status == 4) {


                $data = [
                    'ShopCode' => sanitize_text_field($_POST['ShopCode']),
                    'PurchAmount' => sanitize_text_field($_POST["PurchAmount"]),
                    'Currency' => sanitize_text_field($_POST["Currency"]),
                    'OrderId' => sanitize_text_field($_POST["OrderId"]),
                    'TxnType' => 'Auth',
                    'UserCode' => esc_html($options['kullanici_kodu']),
                    'UserPass' => esc_html($options['sifre']),
                    'SecureType' => 'NonSecure',
                    'InstallmentCount' => '',
                    'MD' => sanitize_text_field($_POST["MD"]),
                    'Lang' => 'TR',
                    'PayerAuthenticationCode' => sanitize_text_field($_POST["PayerAuthenticationCode"]),
                    'Eci' => sanitize_text_field($_POST["Eci"]),
                    'PayerTxnId' => sanitize_text_field($_POST["PayerTxnId"]),
                    'MOTO' => '0',
                ];

                $array = [];
                $array['pan'] = sanitize_text_field($_REQUEST['Pan']);

                $response = wp_remote_post("https://inter-vpos.com.tr/mpi/Default.aspx", array(
                    'method'      => 'POST',
                    'body'        => $data,
                ));
                $result = wp_remote_retrieve_body($response);

                $resultValues = explode(";;", $result);


                foreach ($resultValues as $result) {
                    if (strpos($result, "ProcReturnCode") > -1) {
                        $response = substr($result, -2, 2);
                    }

                    if (!empty(explode('HolderName=', $result)[1])) {
                        $CardHolderName = explode('HolderName=', $result)[1];
                    }
                }

                // $array['CardHolderName'] = $CardHolderName;
                $array['odeme_saati'] = wp_date("d F Y, H:i:s");
                update_post_meta(sanitize_text_field($_POST['OrderId']), "odemeBilgileri", $array);


                $order = wc_get_order(sanitize_text_field($_POST["OrderId"]));

                if ($response == "00") {
                    global $woocommerce;
                    $order->payment_complete();
                    $woocommerce->cart->empty_cart();

                    header("Location: " . $order->get_checkout_order_received_url());

                } else {

                    foreach ($resultValues as $value) {
                        if (!empty(explode("Message=", $value)[1])) {
                            $hata = explode("Message=", $value)[1];
                        }
                    }

                    header("Location: " . $order->get_checkout_payment_url(true) . "&ErrorMessage=$hata");

                }
            } else {
                echo "3D islemi onay almadi";
            }


            exit;

        }


    }