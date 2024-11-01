<?php
    if (!defined('ABSPATH')) {
        exit;
    }


?>
<style>
    .input {
        width: auto !important;
    }
</style>
<main class="container form-signin mt-0 pt-0 shadow p-4 p-md-3">
    <div id="index">
        <form action="<?php echo esc_url(get_option("woocommerce_denizpos_settings")['api_type']) ?>mpi/Default.aspx"
              id="perForm" method="post">

            <input type="hidden" name="ShopCode" value="<?php esc_html_e($shopCode) ?>"/>
            <input type="hidden" name="PurchAmount" value="<?php esc_html_e($purchaseAmount); ?>"/>
            <input type="hidden" name="Currency" value="<?php esc_html_e($Currency) ?>"/>
            <input type="hidden" name="OrderId" value="<?php esc_html_e($order_id); ?>"/>
            <input type="hidden" name="OkUrl" value="<?php echo esc_url($okUrl) ?>"/>
            <input type="hidden" name="FailUrl" value="<?php echo esc_url($failUrl) ?>"/>
            <input type="hidden" name="Rnd" value="<?php esc_html_e($rnd); ?>"/>
            <input type="hidden" name="Hash" value="<?php esc_html_e($hash); ?>"/>
            <input type="hidden" name="TxnType" value="<?php esc_html_e($txnType) ?>"/>
            <input type="hidden" name="SecureType" value="3DModel"/>
            <input type="hidden" name="Version3D" value="2.0"/>
            <input type="hidden" name="Lang" value="tr"/>
            <input type="hidden" name="InstallmentCount" value="1"/>
            <input type="hidden" name="Expiry" value=""/>

            <div class="input-group mb-3">
            <span class="input-group-text text-primary" style="min-height: 50px !important;" id="basic-addon12">
                <i class="bi bi-person fs-4"></i>
            </span>
                <input type="text" class="form-control input" placeholder="Kart Üzerindeki Ad Soyad" id="name"
                       name="cardHolderName"
                       autocomplete="cc-name" aria-describedby="basic-addon12">
            </div>
            <span id="validationwarningname" class="text-danger input-group"></span>
            <div class="input-group mb-3">
            <span class="input-group-text text-primary" style="min-height: 50px !important;" id="basic-addon1">
                <i class="bi bi-credit-card-2-front fs-4"></i>
            </span>
                <input type="text" inputmode="numeric" class="form-control input" keyup="formatCard(this)"
                       placeholder="Kart Numarası" aria-label="number" id="number" name="Pan"
                       autocomplete="cc-number" pattern="[0-9\s]{4} [0-9\s]{4} [0-9\s]{4} [0-9\s]{4}" maxlength="19"
                       aria-describedby="basic-addon1">
            </div>
            <span id="validationwarningcard" class="text-danger input-group"></span>
            <div class="input-group g-1">
                <div class="col-8">
                    <div class="input-group mb-3">
                    <span class="input-group-text text-primary" style="min-height: 50px !important;" id="basic-addon13">
                        <i class="bi bi-calendar3 fs-4"></i>
                    </span>
                        <input type="text" inputmode="numeric" class="form-control input"
                               style="max-width: 60px !important;"
                               maxlength="2" autocomplete="cc-exp-month" placeholder="Ay" name="ay" id="ay"
                               aria-label="Ay">
                        <span class="input-group-text">/</span>
                        <input type="text" inputmode="numeric" class="form-control input" placeholder="Yıl"
                               maxlength="2"
                               aria-label="Yıl" autocomplete="cc-exp-year" name="yil" id="yil" aria-invalid="false"
                               style="max-width: 80px !important;">
                    </div>
                </div>


                <div class="col-4">
                    <div class="input-group mb-3">
                    <span class="input-group-text text-primary" style="min-height: 50px !important;" id="basic-addon14">
                        <i class="bi bi-credit-card-2-back fs-4"></i>
                    </span>
                        <input type="text" inputmode="numeric" class="form-control" placeholder="CVV" id="cvv"
                               name="Cvv2" autocomplete="cc-csc" aria-label="cvv" maxlength="4"
                               aria-describedby="basic-addon14" spellcheck="false" mask="000"
                               style="width: 50px!important;">
                    </div>
                </div>
                <span id="validationwarningcvv" class="text-danger input-group"></span>
                <div class="row g-2 mb-2 text-center" id="cardProgram">
                    <div class="col-4">
                        <img src="<?php echo pgfd_plugin_url ?>images/mastercard.png" class="img-fluid"
                             style="max-height:40px;">
                    </div>
                    <div class="col-4">
                        <img src="<?php echo pgfd_plugin_url ?>images/visa-yeni2.png" class="img-fluid"
                             style="max-height:40px;">
                    </div>
                    <div class="col-4">
                        <img src="<?php echo pgfd_plugin_url ?>images/troy.png" class="img-fluid"
                             style="max-height:40px;">
                    </div>
                </div>
            </div>
            <div class="row" id="taksit">
            </div>
            <div id="indexbutton">
                <button class="w-100 btn btn-lg btn-primary" type="button" onclick="validateForm();"
                        name="paybuttontext" id="paybuttontext"
                        title="Taksit Seçenekleri"> <?php echo esc_html__('Ödeme', 'denizpos') ?>
                </button>
            </div>
        </form>
    </div>
</main>


<script>


    function validateForm(s) {


        var yil = document.getElementsByName("yil")[0].value;
        var ay = document.getElementsByName("ay")[0].value;
        document.getElementsByName("Expiry")[0].value = ay + yil
        document.getElementById("perForm").submit();
        return !errorExist;
    }
    jQuery(document).ready(function ($) {
        if (document.getElementById('coverScreen') != undefined)
            document.getElementById('coverScreen').hidden = true;
        $('#cvv').keydown(function (event) {
            preventKeysForCVC(event);
        });
        $('#cvv').on('paste', function (event) {
            if (!hasJustNumbers(event.originalEvent.clipboardData.getData('Text'))) {
                event.preventDefault();
            }
        });
        $('#number').keydown(function (event) {
            if (keyEventIsNumber(event)) {
                $("#validationwarningcard").html("");
            }
        });
        $('#number').keyup(function () {
            var foo = $(this).val().split(" ").join("");
            if (foo.length > 0) {
                foo = foo.match(new RegExp('.{1,4}', 'g')).join(" ");
            }
            $(this).val(foo);
        });
        $("#yil").blur(function () {
            var year = $("#yil").val();
            var d = new Date();
            var y = "" + d.getFullYear();

        });


        function _onblur_number(amorjValue) {
            if ("NONE" == "PAY_BY_LINK" && false == true) {
                $("#validationwarninginputAmount").html("");
                if (IsNullOrEmty($("#inputAmount").val())) {
                    console.log("Tutar Giriniz.");
                    $("#validationwarninginputAmount").html("<h5>Tutar boş geçilemez</h5>");
                    return;
                }
                if ($("#inputAmount").val() == "0") {
                    console.log("Tutar 0 olamaz");
                    $("#validationwarninginputAmount").html("<h5>Tutar sıfırdan büyük olmalıdır</h5>");
                    return;
                }
            }
            var numberWithoutSpace = $("#number").val().replace(/\s/g, '');
            if (numberWithoutSpace.length > 12 && numberWithoutSpace.length < 20) {
                $("#validationwarningcard").html("");
                var errorExist = false;
                if ("nop" == "4" && (numberWithoutSpace.charAt(0) != "4")) {//visa
                    errorExist = true;
                    $("#validationwarningcard").html("<h5>Kartınız bir VISA kart olmalıdır.</h5>");
                }
                if ("nop" == "5" && (numberWithoutSpace.charAt(0) != "5")) {//mastercard
                    errorExist = true;
                    $("#validationwarningcard").html("<h5>Kartınız bir MASTERCARD olmalıdır.</h5>");
                }
                if (!("nop" == "nop")) {
                    var bins = "nop".split("|");
                    var binMatched = false;
                    for (var i = 0; i < bins.length; i++) {
                        if (numberWithoutSpace.substring(0, 6) == bins[i].trim()) {
                            binMatched = true;
                        }
                    }
                    if (!binMatched) {
                        errorExist = true;
                        $("#validationwarningcard").html("<h5>Kampanya kapsamındaki kredi – banka kartınız ile ödeme yapmanız gerekmektedir.</h5>");
                    }
                }

            } else {
                $("#validationwarningcard").html("<h5>Lütfen geçerli bir kart giriniz.</h5>");
            }
        }

        $("#number").blur(function () {
            var amorjValue = $("#amorj").val();
            _onblur_number(amorjValue);
        });

    });
</script>
<!-- DCC İşlemleri -->
<script crossorigin="anonymous" type="text/javascript">
    var showcurrency = "";

    function getCurrencySetData(x) {
        $("#DISPENSE_CURRENCY").val(x.DISPENSE_CURRENCY);
        $("#CURRENCY_CODE_NUMERIC").val(x.CURRENCY_CODE_NUMERIC);
        $("#DISPENSE_AMOUNT").val(x.DISPENSE_AMOUNT);
        $("#EXCHANGE_RATE").val(x.EXCHANGE_RATE);
        $("#DCC_CURRENCY_PARITY").val(x.DCC_CURRENCY_PARITY);
        $("#SALE_AMOUNT").val(x.SALE_AMOUNT);
        $("#MARKUP_RATE").val(x.MARKUP_RATE);
        $("#CURRENCY_CODE_ALPHA").val(x.CURRENCY_CODE_ALPHA);
        $("#CURRENCY_PARITY").val(x.CURRENCY_PARITY);
        $("#ForeignPaymenAllowData").show();
    }

    function getCurrencyData(sel) {
        if (sel.value === "") {
            var x = JSON.parse('{"DISPENSE_CURRENCY":"","CURRENCY_CODE_NUMERIC":"","DISPENSE_AMOUNT":"","EXCHANGE_RATE":"","DCC_CURRENCY_PARITY":"","SALE_AMOUNT":"","MARKUP_RATE":"","CURRENCY_CODE_ALPHA":"","CURRENCY_PARITY":""}');
            getCurrencySetData(x);
            $("#ForeignPaymenAllowData").hide();
            return;
        }
        var x = JSON.parse(sel.value);
        if (!IsNullOrEmty(showcurrency))
            $(showcurrency).css("display", "none");
        showcurrency = "#detay_" + x.CURRENCY_CODE_NUMERIC;
        $(showcurrency).css("display", "block");
        getCurrencySetData(x);
        $("#ForeignPaymenAllowData").show();
        $("#paybuttontext").prop("disabled", false);
        $("#paybuttontext").text($("#CURRENCY_CODE_ALPHA").val() + " " + $("#SALE_AMOUNT").val() + " Öde");
        if ($("#cpaybuttontext") != undefined) {
            $("#cpaybuttontext").prop("disabled", false);
        }
        if ($("#registerbtn") != undefined) {
            $("#registerbtn").prop("disabled", false);
        }
    }
</script>
<script>
    var isCardStorageList = false;
    isCardStorageList = 'False' == 'True';
    jQuery(document).ready(function ($) {
        if (isCardStorageList) {
            console.log("refresh");
            $("#cardStoragelist").css("display", "");
            $("#index").css("display", "none");
        }

        $('#cardStoragecheck').change(function () {
            if ($(this).is(":checked")) {
                if (isCardStorageList) {
                    $("#cardStoragelist").css("display", "");
                    $("#index").css("display", "none");
                } else {
                    $("#cardStoragebutton").show();
                    $("#cardStorageHeader").show();
                    $("#accountAliasNameDiv").show();
                    $("#cardStoragechxdiv").hide();
                    $("#indexHeader").hide();
                    $("#indexbutton").hide();
                    $("#cardProgram").hide();
                    $("#CardStorageRegister").val("true");
                }
                return;
            }
            $("#cardStoragebutton").hide();
            $("#cardStorageHeader").hide();
            $("#cardStoragechxdiv").show();
            $("#indexHeader").show();
            $("#indexbutton").show();
            $("#accountAliasNameDiv").hide();
            $("#CardStorageRegister").val("false");
            $("#cardProgram").show();
        });
        $("#backIndexc").on("click", function () {
            if (isCardStorageList) {
                console.log("refresh 1");
                $("#cardStoragelist").css("display", "");
                $("#index").css("display", "none");
            } else {
                $("#cardStoragebutton").hide();
                $("#cardStorageHeader").hide();
                $("#accountAliasNameDiv").hide();
                $("#cardStoragechxdiv").show();
                $("#indexHeader").show();
                $("#indexbutton").show();
                $("#CardStorageRegister").val("false");
                $("#cardProgram").show();
                document.getElementById("cardStoragecheck").checked = false;
            }
        });

    });

</script>
</main>