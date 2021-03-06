<?php
/*
  Plugin Name: Custom Registration Form
  Plugin URI: https://www.facebook.com/bodunjo
  Description: Custom Registration Form for http://uberlin.ru/
  Version: 1.0
  Author: Stepanchuk Evgeniy
  Author URI: https://www.facebook.com/bodunjo
 */

include( 'MPDF57/mpdf.php' );
include 'form_menu.php';

global $wpdb;

///////////////////////////// Submit Registration  /////////////////////////////////////////////
add_action( 'wp_ajax_custom_registration_submit_ajax', 'custom_registration_submit_ajax' );
add_action( 'wp_ajax_nopriv_custom_registration_submit_ajax', 'custom_registration_submit_ajax' );
function custom_registration_submit_ajax()
{


	$fiouser               = $_REQUEST['fiouser'];
	$bdate                 = $_REQUEST['bdate'];
	$bplace                = $_REQUEST['bplace'];
	$phonenumber           = $_REQUEST['phonenumber'];
	$email                 = $_REQUEST['email'];
	$city                  = $_REQUEST['city'];
	$helpInfo              = $_REQUEST['helpInfo'];
	$passport              = $_REQUEST['passport'];
	$rovInfo               = $_REQUEST['rovInfo'];
	$passDate              = $_REQUEST['passDate'];
	$address               = $_REQUEST['address'];
	$file['passFrontPage'] = $_FILES['passFrontPage'];
	$bik                   = $_REQUEST['bik'];
	$korrBank              = $_REQUEST['korrBank'];
	$bankName              = $_REQUEST['bankName'];
	$poluchCode            = $_REQUEST['poluchCode'];
	$bankCard              = $_REQUEST['bankCard'];
	$comment               = $_REQUEST['comment'];
	$FIOPoluch             = $_REQUEST['FIOPoluch'];
	$personalData          = $_REQUEST['personalData'];
	$personalData1         = $_REQUEST['personalData1'];
	$personalData2         = $_REQUEST['personalData2'];

	$reg_error = registration_validation( $fiouser, $bdate, $bplace, $email, $phonenumber,
		$city, $passport, $passDate, $rovInfo, $address, $FIOPoluch,
		$bik, $korrBank, $bankName, $poluchCode, $bankCard, $file, $personalData1, $personalData2, $personalData );

	if ( count( $reg_error ) == 0 )
	{
		$response = complete_registration( $fiouser, $bdate, $bplace, $email, $phonenumber,
			$city, $helpInfo, $passport, $passDate, $rovInfo, $address,
			$bik, $korrBank, $bankName, $poluchCode, $bankCard, $file, $comment, $FIOPoluch );


		if ( $response['success'] )
		{
			$response = [
				'success' => 'Спасибо, Ваши реквизиты получены, копия ваших реквизитов и договор отправлены вам на почту, это подтверждает, то что мы уже получили ваши реквизиты. 

Для действующих водителей:
Пожалуйста, отпишитесь нам после заполнения реквизитов что передали реквизиты по вотсап/вайбер/смс на номер 8-964-559-55-51

Для новых водителей:
Надеемся, что вы четко следуете нашей инструкции шаг за шагом, следующий Шаг № 3 – Вам нужно пройти видео обучение и установить самостоятельно приложение Uber Driver. 
Ссылка на видео и инструкции https://uberlin.ru/edu/'
			];
		} else
		{
			$response = [ 'complete' => 'Ошибка завершения регистрации!' ];
		}


	} else
	{
		$response = [ 'error' => $reg_error ];
	}

	echo json_encode( $response );
	die();
}

////////////////////////////////////////////////////////////////////////////////////////////////


function castom_reg_form_my_custom_js_footer()
{
	echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/core.js"></script>';
	echo '<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>';
	echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.12/jquery.mask.js"></script>';    //echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>';
	?>
    <script>

        // var new_reg_ajax_url = 'http://uberlin.ru/wp-admin/admin-ajax.php';
        var new_reg_ajax_url = 'http://37.57.92.40/wp-3/wp-admin/admin-ajax.php';

        jQuery(document).ready(function () {

            jQuery("#phonenumber").mask("7 (000) 000-0000");
            jQuery("#bdate").mask("00.00.0000");
            jQuery("#passDate").mask("00.00.0000");
            //jQuery("#poluchCode").mask("408 17 810 000000000000");
            //jQuery("#korrBank").mask("301 00000000000000000");
            jQuery("#rovInfo").mask("000-000");
            jQuery("#passport").mask("00 00 000000");
            // jQuery("#bankCard").mask("0000 0000 0000 0000");

            jQuery('.close').click(function () {
                jQuery('#myModal').css('display', 'none')
            });

            jQuery(document).click(function (e) {
                if (e.target == jQuery('#myModal')) {
                    jQuery('#myModal').css('display', 'none')
                }
            });

            jQuery('#bik').keydown(removeSpaces);
            jQuery('#korrBank').keydown(removeSpaces);
            jQuery('#poluchCode').keydown(removeSpaces);

            function removeSpaces(event) {
                if (event.keyCode == 32) {
                    return;
                }
                this.value = this.value.replace(/\s/g, "");
            }

            jQuery('#buttonSubmit').click(function () {
                jQuery('#buttonSubmit').attr('disabled', true);
                jQuery('#buttonSubmit').val('Загрузка..');

                var passFrontPage = jQuery('#passFrontPage')[0].files[0];
                var form_data = new FormData();
                form_data.append("passFrontPage", passFrontPage);
                form_data.append("action", 'custom_registration_submit_ajax');
                form_data.append("fiouser", jQuery('#fiouser').val());
                form_data.append("email", jQuery('#email').val());
                form_data.append("phonenumber", jQuery('#phonenumber').val());
                form_data.append("city", jQuery('#city').val());
                form_data.append("bankCard", jQuery('#bankCard').val());
                form_data.append("bdate", jQuery('#bdate').val());
                form_data.append("bplace", jQuery('#bplace').val());
                form_data.append("helpInfo", jQuery('#helpInfo').val());
                form_data.append("passport", jQuery('#passport').val());
                form_data.append("rovInfo", jQuery('#rovInfo').val());
                form_data.append("FIOPoluch", jQuery('#FIOPoluch').val());
                form_data.append("passDate", jQuery('#passDate').val());
                form_data.append("address", jQuery('#address').val());
                form_data.append("bik", jQuery('#bik').val());
                form_data.append("korrBank", jQuery('#korrBank').val());
                form_data.append("bankName", jQuery('#bankName').val());
                form_data.append("poluchCode", jQuery('#poluchCode').val());
                form_data.append("comment", jQuery('#comment').val());
                form_data.append("personalData", jQuery('#personalData').is(':checked'));
                form_data.append("personalData1", jQuery('#personalData1').is(':checked'));
                form_data.append("personalData2", jQuery('#personalData2').is(':checked'));
                jQuery.ajax({
                    type: "post",
                    url: new_reg_ajax_url,
                    data: form_data,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        jQuery('#buttonSubmit').removeAttr("disabled");
                        jQuery('#buttonSubmit').val('Отправить');
                        console.log('success');
                        if (response.error) {
                            console.log('2 error');
                            jQuery('#modal-text').html('');
                            jQuery('#myModal').css('display', 'block');
                            if (response.error.fiouser) {
                                jQuery('#modal-text').append('<p>' + response.error.fiouser + '<p>');
                            }
                            if (response.error.email) {
                                jQuery('#modal-text').append('<p>' + response.error.email + '<p>');
                            }
                            if (response.error.phonenumber) {
                                jQuery('#modal-text').append('<p>' + response.error.phonenumber + '<p>');
                            }
                            if (response.error.city) {
                                jQuery('#modal-text').append('<p>' + response.error.city + '<p>');
                            }
                            if (response.error.bankCard) {
                                jQuery('#modal-text').append('<p>' + response.error.bankCard + '<p>');
                            }
                            if (response.error.bdate) {
                                jQuery('#modal-text').append('<p>' + response.error.bdate + '<p>');
                            }
                            if (response.error.FIOPoluch) {
                                jQuery('#modal-text').append('<p>' + response.error.FIOPoluch + '<p>');
                            }
                            if (response.error.bplace) {
                                jQuery('#modal-text').append('<p>' + response.error.bplace + '<p>');
                            }
                            if (response.error.helpInfo) {
                                jQuery('#modal-text').append('<p>' + response.error.helpInfo + '<p>');
                            }
                            if (response.error.passport) {
                                jQuery('#modal-text').append('<p>' + response.error.passport + '<p>');
                            }
                            if (response.error.rovInfo) {
                                jQuery('#modal-text').append('<p>' + response.error.rovInfo + '<p>');
                            }
                            if (response.error.passDate) {
                                jQuery('#modal-text').append('<p>' + response.error.passDate + '<p>');
                            }
                            if (response.error.address) {
                                jQuery('#modal-text').append('<p>' + response.error.address + '<p>');
                            }
                            if (response.error.bik) {
                                jQuery('#modal-text').append('<p>' + response.error.bik + '<p>');
                            }
                            if (response.error.korrBank) {
                                jQuery('#modal-text').append('<p>' + response.error.korrBank + '<p>');
                            }
                            if (response.error.bankName) {
                                jQuery('#modal-text').append('<p>' + response.error.bankName + '<p>');
                            }
                            if (response.error.poluchCode) {
                                jQuery('#modal-text').append('<p>' + response.error.poluchCode + '<p>');
                            }
                            if (response.error.complete) {
                                jQuery('#modal-text').append('<p>' + response.error.complete + '<p>');
                            }
                            if (response.error.personalData) {
                                jQuery('#modal-text').append('<p>' + response.error.personalData + '<p>');
                            }
                            if (response.error.personalData1) {
                                jQuery('#modal-text').append('<p>' + response.error.personalData1 + '<p>');
                            }
                            if (response.error.personalData2) {
                                jQuery('#modal-text').append('<p>' + response.error.personalData2 + '<p>');
                            }
                            if (response.error.passFrontPage) {
                                jQuery('#modal-text').append('<p>' + response.error.passFrontPage + '<p>');
                            }
                        }
                        if (response.success) {
                            console.log('2 success');
                            jQuery('#modal-text').text(response.success);
                            jQuery('#myModal').css('display', 'block');
                            window.location.href = "https://uberlin.ru/rekok";
                        }
                    }
                });
            });
        });

    </script>
    <style>        /*@import url('https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css');*/
        /* The Modal (background) */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 200px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.04);
        }

        /* Modal Content */
        .modal-content {
            font-size: 17px;
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 36%;
        }

        /* The Close Button */
        .close {
            color: #aaaaaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        .textwidget {
            column-count: 1;
            font-family: Helvetica, sans-serif;
            color: black;
        }

        .textwidget div {
            margin: 8px 0;
            page-break-inside: avoid;
        }

        .textwidget .mains br {
            display: none;
        }

        .textwidget input[type="text"], .textwidget input[type="email"], .textwidget .select, .textwidget #select1, .textwidget #select2, .textwidget select, .textwidget textarea {
            padding: 3px;
            margin-left: 0;
            border: 1px solid #ccc;
            font-size: 1em;
            line-height: 1.071em;
            -moz-box-shadow: 0 1px 2px #eee inset;
            -webkit-box-shadow: 0 1px 2px #eee inset;
            box-shadow: 0 1px 2px #eee inset;
            width: 400px;
            font-family: Helvetica, sans-serif;
            background: none;
            padding: 4px 3px 2px 3px;
        / / color: #666;
            color: black;
            max-width: auto;
            min-width: auto;
            font-size: 14px;
            resize: none;
        }

        .textwidget #select1, .textwidget #select2 {
            -webkit-appearance: menulist-button;
        }

        .textwidget .fileupload {
            position: relative;
            width: 200px;
            cursor: pointer;
        }

        .textwidget .modal-content {
            display: block;
            margin: auto;
        }

        .textwidget #myModal {
            background: rgba(0, 0, 0, .4);
        }

        .textwidget .fileupload span {
            border: 1px solid #ccc;
            padding: 6px 12px;
            color: #666;
            text-shadow: 0 1px #fff;
            cursor: pointer;
            -moz-border-radius: 3px 3px;
            -webkit-border-radius: 3px 3px;
            border-radius: 3px 3px;
            -moz-box-shadow: 0 1px #fff inset, 0 1px #ddd;
            -webkit-box-shadow: 0 1px #fff inset, 0 1px #ddd;
            box-shadow: 0 1px #fff inset, 0 1px #ddd;
            background: #f5f5f5;
            background: -moz-linear-gradient(top, #f5f5f5 0%, #eeeeee 100%);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #f5f5f5), color-stop(100%, #eeeeee));
            background: -webkit-linear-gradient(top, #f5f5f5 0%, #eeeeee 100%);
            width: 100%;
            display: inline-block;
            background: rgba(240, 240, 240, 0.9);
            text-align: center;
            color: rgb(102, 102, 102);
            border: 1px solid #c5c5c5;
            padding: 9px 15px;
            text-transform: uppercase;
            border-radius: 5px 5px;
            cursor: pointer;
        }

        .textwidget .fileupload input {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0;
            cursor: pointer;
        }

        .textwidget label {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .textwidget input[type="button"] {
            cursor: pointer;
            -moz-border-radius: .3em;
            -webkit-border-radius: .3em;
            border-radius: .3em;
            padding: 6px 18px;
            color: #ffffff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            font-weight: bold;
            text-shadow: 0 1px 0px #1e549d;
            border: 1px solid #3d7fb1;
            -moz-box-shadow: inset 0 1px 0 0 rgba(255, 255, 255, 0.30), 0 1px 2px 0 rgba(0, 0, 0, 0.40);
            -webkit-box-shadow: inset 0 1px 0 0 rgba(255, 255, 255, 0.30), 0 1px 2px 0 rgba(0, 0, 0, 0.40);
            box-shadow: inset 0 1px 0 0 rgba(255, 255, 255, 0.30), 0 1px 2px 0 rgba(0, 0, 0, 0.40);
            background: #57a9eb;
            background: -moz-linear-gradient(top, #57a9eb 0%, #3871c0 100%);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #57a9eb), color-stop(100%, #3871c0));
            background: linear-gradient(top, #57a9eb 0%, #3871c0 100%);
            padding: 9px 15px;
            font-size: 14px;
            font-weight: normal;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#57a9eb', endColorstr='#3871c0', GradientType=0);
        }

        .textwidget input[type="button"]:hover {
            color: #ffffff !important;
            border: 1px solid #3d7fb1 !important;
            background: #78c3ff;
            background: -moz-linear-gradient(top, #78c3ff 0%, #4c85d3 100%);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #78c3ff), color-stop(100%, #4c85d3));
            background: linear-gradient(top, #78c3ff 0%, #4c85d3 100%);
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#78c3ff', endColorstr='#4c85d3', GradientType=0);
        }

        .textwidget div.mains label {
            letter-spacing: 0;
            font-weight: normal;
        }

        @media screen and (max-width: 600px) {
            .textwidget {
                column-count: 1;
            }

            .textwidget input[type="text"], .textwidget input[type="email"], .textwidget .select, .textwidget #select1, .textwidget #select2, .textwidget select, .textwidget textarea {
                width: 100%;
            }
        }
    </style>
	<?php
}

add_action( 'wp_footer', 'castom_reg_form_my_custom_js_footer' );


function custom_registration_function()
{

	registration_form( local() );
}

function local()
{
	$local = get_locale();
	switch ( $local )
	{
		case "ru":
			return [
				'fiouser'       => 'ФИО',
				'bdate'         => 'Дата рождения',
				'bplace'        => 'Место рождения',
				'email'         => 'Укажите вашу действующую почту',
				'phonenumber'   => 'Номер телефона',
				'city'          => 'Город',
				'helpInfo'      => 'Откуда узнали',
				'passport'      => 'Серия и номер паспорта',
				'rovInfo'       => 'Код подразделения, который выдал паспорт',
				'passDate'      => 'Дата выдачи паспорта',
				'address'       => 'Адрес по прописке',
				'passFrontPage' => 'Загрузить разворот паспорта',
				'FIOPoluch'     => 'ФИО получателя средств',
				'bik'           => 'БИК банка',
				'korrBank'      => 'Корр. счет банка',
				'bankName'      => 'Наименование банка',
				'poluchCode'    => 'Счет получателя',
				'bankCard'      => 'Номер карты',
				'comment'       => 'Коментарий',
				'personalData'  => 'Соглашение на обработку персональных данных ',
			];
			break;
		default:
			return [
				'fiouser'       => 'ФИО',
				'bdate'         => 'Дата рождения',
				'bplace'        => 'Место рождения',
				'email'         => 'Email',
				'phonenumber'   => 'Номер телефона',
				'city'          => 'Город',
				'helpInfo'      => 'Откуда узнали',
				'passport'      => 'Серия и номер паспорта',
				'rovInfo'       => 'Код подразделения, который выдал паспорт',
				'passDate'      => 'Дата выдачи паспорта',
				'address'       => 'Адрес по прописке',
				'passFrontPage' => 'Загрузить разворот паспорта',
				'FIOPoluch'     => 'ФИО получателя средств',
				'bik'           => 'БИК банка',
				'korrBank'      => 'Корр. счет банка',
				'bankName'      => 'Наименование банка',
				'poluchCode'    => 'Счет получателя',
				'bankCard'      => 'Номер карты',
				'comment'       => 'Коментарий',
				'personalData'  => 'Соглашение на обработку персональных данных ',
			];
			break;
	}

}

function registration_form( $local )
{
	$form = '
    <div>
        <label class="" for="fiouser">' . $local['fiouser'] . '</label>
        <input id="fiouser" type="text" required name="fiouser" placeholder="Укажите ФИО полностью" value=""/>
    </div>

    <div>
        <label class="" for="bdate">' . $local['bdate'] . '</label>
        <input id="bdate" type="text" required name="bdate" placeholder="ДД.ММ.ГГГГ" value=""/>
    </div>

    <div>
        <label class="" for="bplace">' . $local['bplace'] . '</label>
        <input id="bplace" type="text" name="bplace" placeholder="Как в паспорте" value=""/>
    </div>

    <div>
        <label class="" for="phonenumber">' . $local['phonenumber'] . '</label>
        <input id="phonenumber" type="text" required name="phone" placeholder="7(999) 999-9999" value="" />
    </div>

    <div>
        <label class="" for="email">' . $local['email'] . '</label>
        <input id="email" type="text" name="email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" placeholder="' . $local['email'] . '" value=""/>
    </div>

    <div>
        <label class="" for="city">' . $local['city'] . '</label>
        <select class="select" name="city" id="city">
            <option value="">' . $local['city'] . '</option>
            <option value="Воронеж">Воронеж</option>
            <option value="Екатеринбург">Екатеринбург</option>
            <option value="Казань">Казань</option>
            <option value="Краснодар">Краснодар</option>
            <option value="Красноярск">Красноярск</option>
            <option value="Москва">Москва</option>
            <option value="Нижний Новгород">Нижний Новгород</option>
            <option value="Новосибирск">Новосибирск</option>
            <option value="Омск">Омск</option>
            <option value="Пермь">Пермь</option>
            <option value="Ростов">Ростов</option>
            <option value="Самара">Самара</option>
            <option value="Санкт Петербург">Санкт Петербург</option>
            <option value="Сочи">Сочи</option>
            <option value="Уфа">Уфа</option>
            <option value="Челябинск">Челябинск</option>
            <option value="Тольятти">Тольятти</option>
        </select>
    </div>

    <div>
        <label class="" for="helpInfo">' . $local['helpInfo'] . '</label>
        <select class="select" name="helpInfo" id="helpInfo">
            <option value="">' . $local['helpInfo'] . '</option>
            <option value="Авито">Авито</option>
            <option value="Яндекс">Яндекс</option>
            <option value="Гугл">Гугл</option>
            <option value="Сайт вакансий">Сайт вакансий</option>
            <option value="Рекомендация друзей/знакомых">Рекомендация друзей/знакомых</option>
        </select>
    </div>

    <div>
        <label class="" for="passport">' . $local['passport'] . '</label>
        <input id="passport" type="text" name="passport" placeholder="12 34 567890" value=""/>
    </div>

    <div>
        <label class="" for="rovInfo">' . $local['rovInfo'] . '</label>
        <input id="rovInfo" type="text" name="rovInfo" placeholder="123-456" value=""/>
    </div>

    <div>
        <label class="" for="passDate">' . $local['passDate'] . '</label>
        <input id="passDate" type="text" name="passDate" placeholder="ДД.ММ.ГГГГ" value=""/>
    </div>

    <div>
        <label class="" for="address">' . $local['address'] . '</label>
        <input id="address" type="text" name="address" placeholder="Полный адрес прописки, как в паспорте" value=""/>
    </div>

    <div>
        <label class="" for="passFrontPage">' . $local['passFrontPage'] . '</label>				<div class="fileupload ">			<span>Загрузить</span>			<input type="file" id="passFrontPage" name="passFrontPage" accept="image/*">		</div>
    </div>

    <div>
        <label class="" for="FIOPoluch">' . $local['FIOPoluch'] . '</label>
        <input id="FIOPoluch" type="text" name="FIOPoluch" placeholder="ФИО получателя полностью" value=""/>
    </div>

	<div>
        <label class="" for="bankName">' . $local['bankName'] . '</label>
        <input id="bankName" type="text" name="bankName" placeholder="' . $local['bankName'] . '" value="" maxlength="18"/>
    </div>
	
	<div>
        <label class="" for="bankName">Номер счета</label>
        <input id="poluchCode" type="text" name="poluchCode" maxlength="20" placeholder="Начинается с 40817810" value=""/>
    </div>
	
    <div>
        <label class="" for="bik">' . $local['bik'] . '</label>
        <input id="bik" type="text" name="bik" maxlength="9" placeholder="Начинаются с 04" value=""/>
    </div>
    <div>
        <label class="" for="korrBank">' . $local['korrBank'] . '</label>
        <input id="korrBank" type="text" name="korrBank" maxlength="20" placeholder="Начинается с 301" value=""/>
    </div>
    
    
    <div>
        <label for="bankCard">' . $local['bankCard'] . '<strong style="color: red">*</strong></label>
        <input id="bankCard" type="text" name="bankCard" maxlength="18" placeholder="' . $local['bankCard'] . '" value=""/>
    </div>
    <div>
        <label for="comment">' . $local['comment'] . '</label>
        <textarea id="comment" rows="4" name="comment" placeholder="' . $local['comment'] . '"></textarea>
    </div>
	<div class="mains"><!-- style="width: 10% !important;"-->
        <input type="checkbox" id="personalData" name="personalData" value=""/>
        <label for="personalData" style="font-size: 13px;">Нажимая кнопку "Отправить", Я подтверждаю, что ознакомлен и принимаю условия офреты, подтверждаю акцепт и заключение оферты, также придаю юридическую силу документам и информации переданной по электронной почте по адресам указанным в реквизитам настоящего Договора и/или посредством заключения и акцент Оферты, размещенного на сайте <a href="www.uberlin.ru/dogovor">www.uberlin.ru/dogovor</a></label>
    </div>
	<div class="mains"><!-- style="width: 10% !important;"-->
        <input type="checkbox" id="personalData1" name="personalData1" value=""/>
        <label for="personalData1" style="font-size: 13px;">Я прочитал и принимаю условия Пользовательского соглашения и политики конфиденциальности в полном объеме</label>
    </div>
    <div class="mains"><!-- style="width: 10% !important;"-->
        <input type="checkbox" id="personalData2" name="personalData2" value=""/>
        <label for="personalData2" style="font-size: 13px;">Я подтверждаю, что действую сознательно, своей волей и в своих интересах, мои действия не контролируются иными третьими лицами, не представляют интересы третьих лиц (выгодоприобретателей)</label>
    </div>
    <input class="acf-button button button-primary button-large" type="button" name="buttonSubmit" id="buttonSubmit" value="Отправить"/>

    <!-- The Modal -->
    <div id="myModal" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modal-text">
            </div>
        </div>

    </div>

';
	echo $form;
}

function check_card_number( $str )
{
	$str = strrev( preg_replace( '/[^0-9]/', '', $str ) );
	$chk = 0;
	for ( $i = 0; $i < strlen( $str ); $i ++ )
	{
		$tmp = intval( $str[ $i ] ) * ( 1 + $i % 2 );
		$chk += $tmp - ( $tmp > 9 ? 9 : 0 );
	}

	return ! ( $chk % 10 );
}

function registration_validation(
	$fiouser, $bdate, $bplace, $email, $phonenumber,
	$city, $passport, $passDate, $rovInfo, $address, $FIOPoluch,
	$bik, $korrBank, $bankName, $poluchCode, $bankCard, $file, $personalData1, $personalData2, $personalData
) {

	$reg_errors = [];
	if ( strlen( $fiouser ) < 2 || strlen( $fiouser ) > 100 )
	{
		$reg_errors['fiouser'] = 'Ошибка ввода ФИО';
	}
	if ( strlen( $bdate ) < 2 || strlen( $bdate ) > 100 )
	{
		$reg_errors['bdate'] = 'Ошибка ввода даты рождения';
	}
	if ( strlen( $bplace ) < 2 || strlen( $bplace ) > 100 )
	{
		$reg_errors['bplace'] = 'Ошибка ввода места рождения';
	}
    if ( strlen( $FIOPoluch ) < 2 || strlen( $FIOPoluch ) > 100 )
	{
		$reg_errors['FIOPoluch'] = 'Ошибка имени получателя';
	}
	if ( strlen( $email ) < 2 || strlen( $email ) > 100 || ! is_email( $email ) )
	{
		$reg_errors['email'] = 'Ошибка ввода Email';
	}
	if ( strlen( $city ) < 2 || strlen( $city ) > 100 )
	{
		$reg_errors['city'] = 'Ошибка ввода города проживания';
	}
	if ( strlen( $passport ) < 2 || strlen( $passport ) > 100 )
	{
		$reg_errors['passport'] = 'Ошибка ввода паспорта';
	}
	if ( $personalData == 'false' )
	{
		$reg_errors['personalData'] = 'Подтвердите условия офреты';
	}
	if ( $personalData1 == 'false' )
	{
		$reg_errors['personalData'] = 'Подтвердите Пользовательского соглашения';
	}
	if ( $personalData2 == 'false' )
	{
		$reg_errors['personalData'] = 'Подтвердите действий сознательно';
	}
	if ( strlen( $rovInfo ) < 2 || strlen( $rovInfo ) > 100 )
	{
		$reg_errors['rovInfo'] = 'Ошибка ввода места выдачи паспорта';
	}
	if ( strlen( $address ) < 2 || strlen( $address ) > 100 )
	{
		$reg_errors['address'] = 'Ошибка ввода адреса выдачи паспорта';
	}
	if ( strlen( $bik ) < 2 || strlen( $bik ) > 100 )
	{
		$reg_errors['bik'] = 'Ошибка ввода БИК банка';
	}
	if ( strlen( $korrBank ) < 2 || strlen( $korrBank ) > 100 )
	{
		$reg_errors['korrBank'] = 'Ошибка ввода корр. счет банка';
	}
	if ( strlen( $bankName ) < 2 || strlen( $bankName ) > 100 )
	{
		$reg_errors['bankName'] = 'Ошибка ввода названия банка';
	}
	if ( strlen( $poluchCode ) < 2 || strlen( $poluchCode ) > 100 )
	{
		$reg_errors['poluchCode'] = 'Ошибка ввода кода получателя';
	}
	if ( empty( $file['passFrontPage'] ) )
	{
		$reg_errors['passFrontPage'] = 'Ошибка файла с фото с паспорта';
	}
	if ( ! isset( $phonenumber ) )
	{
		$reg_errors['phonenumber'] = 'Ошибка ввода номера';
	}
	if ( ! isset( $bankCard ) )
	{
		$reg_errors['bankCard'] = 'Ошибка ввода банковской карты';
	}
// 	if ( ! preg_match( "/[0-9]$/i", $phonenumber ) || strlen( $phonenumber ) != 11 )
// 	{
// 		$reg_errors['phonenumber'] = 'Ошибка ввода номера';
// 	}
//	if ( ! check_card_number( $bankCard ) )
//	{
//		$reg_errors['bankCard'] = 'Ошибка ввода банковской карты';
//	}


	return $reg_errors;

}

function generateRandomString()
{
	$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen( $characters );
	$randomString     = '';
	for ( $i = 0; $i < 10; $i ++ )
	{
		$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
	}

	return $randomString;
}

function complete_registration(
	$fiouser, $bdate, $bplace, $email, $phonenumber,
	$city, $helpInfo, $passport, $passDate, $rovInfo, $address,
	$bik, $korrBank, $bankName, $poluchCode, $bankCard, $file, $comment, $FIOPoluch
) {
	setlocale( LC_TIME, "ru_RU.UTF-8" );
//    $date = strftime("%d %B, %H:%M",strtotime($row['date']));

	$lastid = insert_user_data(
		$fiouser, $bdate, $bplace, $email, $phonenumber,
		$city, $helpInfo, $passport, $passDate, $rovInfo, $address,
		$bik, $korrBank, $bankName, $poluchCode, $bankCard, $file, $comment, $FIOPoluch
	);
	$html   = '<style>
body {font-family: sans-serif;
    font-size: 10pt;
}
td { vertical-align: top; 
    /*border-left: 0.6mm solid #000000;
    border-right: 0.6mm solid #000000;*/
	align: center;
}
table thead td { background-color: #EEEEEE;
    text-align: center;
    border: 0.6mm solid #000000;
}
td.lastrow {
    background-color: #FFFFFF;
    border: 0mm none #000000;
    border-bottom: 0.6mm solid #000000;
    border-left: 0.6mm solid #000000;
	border-right: 0.6mm solid #000000;
}
h5{	text-transform: uppercase;}
</style>	<div style="text-align:center"><h5 >ДОГОВОР ПРИСОЕДИНЕНИЯ К ПУБЛИЧНОМУ АГЕНТСКОМУ ДОГОВОРУ</h5>	<h6>№ ' . $lastid . ' от «' . date( 'Y.m.d' ) . '» г. </h6></div>		<table>		<tr><td>Настоящая анкета является частными условиями публичного агентского договора индивидуального предпринимателя Макухина Арсения Александровича.<td></tr>		<tr><td>Общие условия публичного агентского договора ИП Макухина Арсения Александровича размещены на сайте  (далее – <a href="www.uberlin.ru/dogovor">www.uberlin.ru/dogovor</a> «Оферта»).</td></tr>
</table>
<div style="text-align: center;"><h5>Реквизиты Принципала:</h5></div>
    <table border="0" align="center">
        <tr>			
            <td>1. ФИО:</td>
            <td>' . $fiouser . '</td>
        </tr>
        <tr>
            <td>2. Адрес электронной почты:</td>
            <td>' . $email . '</td>
        </tr>	
		<tr>
            <td>3. Номер телефона:</td>
            <td>' . $phonenumber . '</td>
        </tr>                  
        <tr>
            <td>4. Дата рождения:</td>
            <td>' . $bdate . '</td>
        </tr>
        <tr>
            <td>5. Место рождения:</td>
            <td>' . $bplace . '</td>
        </tr>
		<tr>
            <td>6. Серия и номер паспорта:</td>
            <td>' . $passport . '</td>
        </tr>
		<tr>
            <td>7. Номер отдела выдачи паспорта:</td>
            <td>' . $rovInfo . '</td>
        </tr>
        <tr>
            <td>8. Дата выдачи паспорта:</td>
            <td>' . $passDate . '</td>
        </tr>     
		<tr>
            <td>9. Адрес по прописке:</td>
            <td>' . $address . '</td>
        </tr>
		<tr>
            <td>10. БИК Банка:</td>
            <td>' . $bik . '</td>
        </tr>
		<tr>
            <td>11. Наименование банка:</td>
            <td>' . $bankName . '</td>
        </tr>
		<tr>
            <td>12. Счет получателя:</td>
            <td>' . $poluchCode . '</td>
        </tr>	       
        <tr>
            <td>13. Корр. счет банка:</td>
            <td>' . $korrBank . '</td>
        </tr>
        <tr>
            <td>14. Номер карты:</td>
            <td>' . $bankCard . '</td>
        </tr>
       </table>
	<table>
		<tr>
			<td>&#9746; Я подтверждаю, что ознакомлен и принимаю условия оферты, подтверждаю акцепт и заключение оферты, также придаю юридическую силу документам и информации переданной по электронной почте по адресам указанным в реквизитам настоящего Договора и/или посредством заключения и акцента Оферты, размещенного на сайте <a href="www.uberlin.ru/dogovor">www.uberlin.ru/dogovor</a></td>
</tr>
		<tr>
			<td>&#9746; Я прочитал и принимаю условия Пользовательского соглашения и политики конфиденциальности в полном объеме</td>
		</tr>
		<tr>
			<td>&#9746; Я подтверждаю, что действую сознательно, своей волей и в своих интересах, мои действия не контролируются иными третьими лицами, не представляют интересы третьих лиц (выгодоприобретателей)
			</td>
		</tr>
	</table>
	<div>Индивидуальный код: (' . $lastid . ') _______________________ / ' . $fiouser . '/</div>
	<table>
		<tr>
			<td>Индивидуальный предприниматель <br>
Макухин Арсений Александрович</td>
			<td><img style="max-width: 200px;" src="' . WP_CONTENT_DIR . '/plugins/custom_reg_form/uberlin.png"></td>
		</tr>
	</table>
	';

	$upload_dir = wp_upload_dir();
	$name       = date( 'Y-m-d_h:i:s' ) . '-' . generateRandomString() . '.pdf';

	$path = $upload_dir['basedir'] . '/user_pdf/' . $name;

	$mpdf = new mPDF();
	$mpdf->WriteHTML( $html );
	$mpdf->SetDisplayMode( 'fullpage' );
	$mpdf->charset_in = 'windows-1252';

	$mpdf->Output( $path );


	$subj = 'Договор';
	$text = 'Здравствуйте, ваши реквизиты получены, присоединение к агентскому договору сформировано';

	XMail( $email, $subj, $text, $path );
	XMail( 'dogovor@uberlin.ru', $subj, $text, $path );


	return [ 'success' => 'Успешно' ];
}

function XMail( $to, $subj, $text, $filename )
{
	$f    = fopen( $filename, "rb" );
	$un   = strtoupper( uniqid( time() ) );
	$head = "From: dogovor@uberlin.ru\n";
//	$head .= "To: $to\n";
//	$head .= "Subject: $subj\n";
	$head .= "X-Mailer: PHPMail Tool\n";
	$head .= "Reply-To: dogovor@uberlin.ru\n";
	$head .= "Mime-Version: 1.0\n";
	$head .= "Content-Type:multipart/mixed;";
	$head .= "boundary=\"----------" . $un . "\"\n\n";
	$zag  = "------------" . $un . "\nContent-Type:text/html;\n";
	$zag  .= "Content-Transfer-Encoding: 8bit\n\n$text\n\n";
	$zag  .= "------------" . $un . "\n";
	$zag  .= "Content-Type: application/octet-stream;";
	$zag  .= "name=\"" . basename( $filename ) . "\"\n";
	$zag  .= "Content-Transfer-Encoding:base64\n";
	$zag  .= "Content-Disposition:attachment;";
	$zag  .= "filename=\"" . basename( $filename ) . "\"\n\n";
	$zag  .= chunk_split( base64_encode( fread( $f, filesize( $filename ) ) ) ) . "\n";

	return @mail( "$to", "$subj", $zag, $head );
}

// Register a new shortcode: [cr_custom_registration]
add_shortcode( 'cr_custom_registration', 'custom_registration_shortcode' );


// The callback function that will replace [book]
function custom_registration_shortcode()
{
	ob_start();
	custom_registration_function();

	return ob_get_clean();
}

function insert_user_data(
	$fiouser, $bdate, $bplace, $email, $phonenumber,
	$city, $helpInfo, $passport, $passDate, $rovInfo, $address,
	$bik, $korrBank, $bankName, $poluchCode, $bankCard, $file, $comment, $FIOPoluch
) {

	global $wpdb;
	$table_name = $wpdb->prefix . 'custom_registration_form_uberlin';
	$wpdb->show_errors();

	$wpdb->insert( $table_name, array(
		'fiouser'     => $fiouser,
		'bdate'       => $bdate,
		'bplace'      => $bplace,
		'email'       => $email,
		'phonenumber' => $phonenumber,
		'city'        => $city,
		'helpInfo'    => $helpInfo,
		'passport'    => $passport,
		'passDate'    => $passDate,
		'rovInfo'     => $rovInfo,
		'address'     => $address,
		'FIOPoluch'   => $FIOPoluch,
		'bik'         => $bik,
		'korrBank'    => $korrBank,
		'bankName'    => $bankName,
		'poluchCode'  => $poluchCode,
		'bankCard'    => $bankCard,
		'comment'     => $comment
	) );

	return $wpdb->insert_id;

}




