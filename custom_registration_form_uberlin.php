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
if ( ! class_exists( 'WP_List_Table' ) )
{
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

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
	$personalData          = $_REQUEST['personalData'];
	$personalData1         = $_REQUEST['personalData1'];
	$personalData2         = $_REQUEST['personalData2'];

	$reg_error = registration_validation( $fiouser, $bdate, $bplace, $email, $phonenumber,
		$city, $passport, $passDate, $rovInfo, $address,
		$bik, $korrBank, $bankName, $poluchCode, $bankCard, $file, $personalData1, $personalData2, $personalData );

	if ( count( $reg_error ) == 0 )
	{
		$response = complete_registration( $fiouser, $bdate, $bplace, $email, $phonenumber,
			$city, $helpInfo, $passport, $passDate, $rovInfo, $address,
			$bik, $korrBank, $bankName, $poluchCode, $bankCard, $file, $comment );


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

        var new_reg_ajax_url = 'http://uberlin.ru/wp-admin/admin-ajax.php';
        // var new_reg_ajax_url = 'http://37.57.92.40/wp-3/wp-admin/admin-ajax.php';

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
                        console.log('success');
                        if (response.error) {
                            console.log('2 error');
                            jQuery('#modal-text').html('');
                            jQuery('#myModal').css('display', 'block');
                            if (response.error.fiouser) {
                                jQuery('#modal-text').append('<p>' + response.error.name + '<p>');
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
	$city, $passport, $passDate, $rovInfo, $address,
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
	$bik, $korrBank, $bankName, $poluchCode, $bankCard, $file, $comment
) {
	setlocale( LC_TIME, "ru_RU.UTF-8" );
//    $date = strftime("%d %B, %H:%M",strtotime($row['date']));

	$html = '<style>
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
</style>	<div style="text-align:center"><h5 >ДОГОВОР ПРИСОЕДИНЕНИЯ К ПУБЛИЧНОМУ АГЕНТСКОМУ ДОГОВОРУ</h5>	<h6>№ 5-63B от «' . date( 'Y.m.d' ) . '» г.</h6></div>		<table>		<tr><td>Настоящая анкета является частными условиями публичного агентского договора индивидуального предпринимателя Макухина Арсения Александровича.<td></tr>		<tr><td>Общие условия публичного агентского договора ИП Макухина Арсения Александровича размещены на сайте  (далее – <a href="www.uberlin.ru/dogovor">www.uberlin.ru/dogovor</a> «Оферта»).</td></tr>
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
	<div>Индивидуальный код: (4420) _______________________ / ' . $fiouser . '/</div>
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

	var_dump( insert_user_data(
		$fiouser, $bdate, $bplace, $email, $phonenumber,
		$city, $helpInfo, $passport, $passDate, $rovInfo, $address,
		$bik, $korrBank, $bankName, $poluchCode, $bankCard, $comment
	) );

	$subj = 'Договор';
	$text = 'Здравствуйте, ваши реквизиты получены, присоединение к агентскому договору сформировано';

	XMail( $email, $subj, $text, $path );
	XMail( 'dogovor@uberlin.ru', $subj, $text, $path );


	return [ 'error' => 'Успешно' ];
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

register_activation_hook( __FILE__, 'testplugin_install' );
function testplugin_install()
{
	global $wpdb;
	$table_name = $wpdb->prefix . "custom_registration_form_uberlin";
	// создание таблицы данных плагина в базе данных
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name )
	{
		$sql = "CREATE TABLE " . $table_name . " (
  id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
  fiouser TEXT NULL,
  bdate   TEXT NULL,
  bplace     TEXT NULL,
  phonenumber   TEXT NULL,
  email      TEXT NULL,
  city       TEXT NULL,  
  passport    TEXT NULL,
  rovInfo     TEXT NULL,
  passDate    TEXT NULL,
  address     TEXT NULL,
  bik         TEXT NULL,
  korrBank    TEXT NULL,
  bankName    TEXT NULL,
  poluchCode  TEXT NULL,
  bankCard    TEXT NULL,
  helpInfo    TEXT NULL,
  comment  TEXT NULL,
  UNIQUE KEY id (id)
 );";


		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );


	}
}

/** Step 2 (from text above). */
add_action( 'admin_menu', 'my_plugin_menu' );

/** Step 1. */
function my_plugin_menu()
{
	add_menu_page( 'Таблица пользователей', 'Custom Registration Form', 'manage_options', 'custom_reg_form', 'my_plugin_options' );
}

/** Step 3. */
function my_plugin_options()
{

	//Create an instance of our package class...
	$testListTable = new TT_Example_List_Table();
	//Fetch, prepare, sort, and filter our data...
	$testListTable->prepare_items();

	?>
    <div class="wrap">

        <div id="icon-users" class="icon32"><br/></div>
        <h2>Пользователи</h2>

        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="movies-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
            <!-- Now we can render the completed list table -->
			<?php $testListTable->display() ?>
        </form>

    </div>
	<?php
}

function get_all_user_data()
{
	global $wpdb;
	$table_name = $wpdb->get_blog_prefix() . 'custom_registration_form_uberlin';
	$users      = $wpdb->get_results( "SELECT id,fiouser,email,phonenumber,city,passport FROM {$table_name}", 'ARRAY_A' );

	return $users;
}


function insert_user_data(
	$fiouser, $bdate, $bplace, $email, $phonenumber,
	$city, $helpInfo, $passport, $passDate, $rovInfo, $address,
	$bik, $korrBank, $bankName, $poluchCode, $bankCard, $comment
) {

	global $wpdb;
	$table_name = $wpdb->prefix . 'custom_registration_form_uberlin';
	$wpdb->show_errors();

	return $wpdb->insert( $table_name, array(
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
		'bik'         => $bik,
		'korrBank'    => $korrBank,
		'bankName'    => $bankName,
		'poluchCode'  => $poluchCode,
		'bankCard'    => $bankCard,
		'comment'     => $comment
	) );

	return;

}


class TT_Example_List_Table extends WP_List_Table
{


	/** ************************************************************************
	 * REQUIRED. Set up a constructor that references the parent constructor. We
	 * use the parent reference to set some default configs.
	 ***************************************************************************/
	function __construct()
	{
		global $status, $page;

		//Set parent defaults
		parent::__construct( array(
			'singular' => 'Пользователи',     //singular name of the listed records
			'plural'   => 'пользователь',    //plural name of the listed records
			'ajax'     => false        //does this table support ajax?
		) );

	}


	/** ************************************************************************
	 * Recommended. This method is called when the parent class can't find a method
	 * specifically build for a given column. Generally, it's recommended to include
	 * one method for each column you want to render, keeping your package class
	 * neat and organized. For example, if the class needs to process a column
	 * named 'title', it would first see if a method named $this->column_title()
	 * exists - if it does, that method will be used. If it doesn't, this one will
	 * be used. Generally, you should try to use custom column methods as much as
	 * possible.
	 *
	 * Since we have defined a column_title() method later on, this method doesn't
	 * need to concern itself with any column with a name of 'title'. Instead, it
	 * needs to handle everything else.
	 *
	 * For more detailed insight into how columns are handled, take a look at
	 * WP_List_Table::single_row_columns()
	 *
	 * @param array $item        A singular item (one full row's worth of data)
	 * @param array $column_name The name/slug of the column to be processed
	 *
	 * @return string Text or HTML to be placed inside the column <td>
	 **************************************************************************/
	function column_default( $item, $column_name )
	{
		switch ( $column_name )
		{
			case 'id':
			case 'fiouser':
			case 'email':
			case 'phonenumber':
			case 'city':
			case 'passport':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}


	/** ************************************************************************
	 * Recommended. This is a custom column method and is responsible for what
	 * is rendered in any column with a name/slug of 'title'. Every time the class
	 * needs to render a column, it first looks for a method named
	 * column_{$column_title} - if it exists, that method is run. If it doesn't
	 * exist, column_default() is called instead.
	 *
	 * This example also illustrates how to implement rollover actions. Actions
	 * should be an associative array formatted as 'slug'=>'link html' - and you
	 * will need to generate the URLs yourself. You could even ensure the links
	 *
	 *
	 * @see WP_List_Table::::single_row_columns()
	 *
	 * @param array $item A singular item (one full row's worth of data)
	 *
	 * @return string Text to be placed inside the column <td> (movie title only)
	 **************************************************************************/
	function column_title( $item )
	{

		//Build row actions
		$actions = array(
			'edit'   => sprintf( '<a href="?page=%s&action=%s&movie=%s">Edit</a>', $_REQUEST['page'], 'edit', $item['id'] ),
			'delete' => sprintf( '<a href="?page=%s&action=%s&movie=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['id'] ),
		);

		//Return the title contents
		return sprintf( '%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
			/*$1%s*/
			$item['fiouser'],
			/*$2%s*/
			$item['id'],
			/*$3%s*/
			$this->row_actions( $actions )
		);
	}


	/** ************************************************************************
	 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	 * is given special treatment when columns are processed. It ALWAYS needs to
	 * have it's own method.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 *
	 * @param array $item A singular item (one full row's worth of data)
	 *
	 * @return string Text to be placed inside the column <td> (movie title only)
	 **************************************************************************/
	function column_cb( $item )
	{
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/
			$this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
			/*$2%s*/
			$item['id']                //The value of the checkbox should be the record's id
		);
	}


	/** ************************************************************************
	 * REQUIRED! This method dictates the table's columns and titles. This should
	 * return an array where the key is the column slug (and class) and the value
	 * is the column's title text. If you need a checkbox for bulk actions, refer
	 * to the $columns array below.
	 *
	 * The 'cb' column is treated differently than the rest. If including a checkbox
	 * column in your table you must create a column_cb() method. If you don't need
	 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
	 **************************************************************************/
	function get_columns()
	{
		$columns = array(
			'cb'          => '<input type="checkbox" />', //Render a checkbox instead of text
			'id'          => 'Id',
			'fiouser'     => 'ФИО',
			'email'       => 'Email',
			'phonenumber' => 'Номер телефона',
			'city'        => 'Город',
			'passport'    => 'Пасспорт',
		);

		return $columns;
	}


	/** ************************************************************************
	 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
	 * you will need to register it here. This should return an array where the
	 * key is the column that needs to be sortable, and the value is db column to
	 * sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 *
	 * This method merely defines which columns should be sortable and makes them
	 * clickable - it does not handle the actual sorting. You still need to detect
	 * the ORDERBY and ORDER querystring variables within prepare_items() and sort
	 * your data accordingly (usually by modifying your query).
	 *
	 * @return array An associative array containing all the columns that should be sortable:
	 *               'slugs'=>array('data_values',bool)
	 **************************************************************************/
	function get_sortable_columns()
	{
		$sortable_columns = array(
			'id'          => array( 'id', false ),
			'fiouser'     => array( 'fiouser', false ),
			'email'       => array( 'email', false ),
			'phonenumber' => array( 'phonenumber', false ),
			'city'        => array( 'city', false ),
			'passport'    => array( 'passport', false ),
		);

		return $sortable_columns;
	}


	/** ************************************************************************
	 * Optional. If you need to include bulk actions in your list table, this is
	 * the place to define them. Bulk actions are an associative array in the format
	 * 'slug'=>'Visible Title'
	 *
	 * If this method returns an empty value, no bulk action will be rendered. If
	 * you specify any bulk actions, the bulk actions box will be rendered with
	 * the table automatically on display().
	 *
	 * Also note that list tables are not automatically wrapped in <form> elements,
	 * so you will need to create those manually in order for bulk actions to function.
	 *
	 * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
	 **************************************************************************/
	function get_bulk_actions()
	{
		$actions = array(
			'delete' => 'Delete'
		);

		return $actions;
	}


	/** ************************************************************************
	 * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
	 * For this example package, we will handle it in the class to keep things
	 * clean and organized.
	 *
	 * @see $this->prepare_items()
	 **************************************************************************/
	function process_bulk_action()
	{

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() )
		{
			wp_die( 'Items deleted (or they would be if we had items to delete)!' );
		}

	}


	/** ************************************************************************
	 * REQUIRED! This is where you prepare your data for display. This method will
	 * usually be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args(), although the following properties and methods
	 * are frequently interacted with here...
	 *
	 * @global WPDB $wpdb
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 **************************************************************************/
	function prepare_items()
	{
		global $wpdb; //This is used only if making any database queries

		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = 10;


		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();


		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array( $columns, $hidden, $sortable );


		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();


		/**
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example
		 * package slightly different than one you might build on your own. In
		 * this example, we'll be using array manipulation to sort and paginate
		 * our data. In a real-world implementation, you will probably want to
		 * use sort and pagination data to build a custom query instead, as you'll
		 * be able to use your precisely-queried data immediately.
		 */
		$data = get_all_user_data();


		/**
		 * This checks for sorting input and sorts the data in our array accordingly.
		 *
		 * In a real-world situation involving a database, you would probably want
		 * to handle sorting by passing the 'orderby' and 'order' values directly
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary.
		 */
		function usort_reorder( $a, $b )
		{
			$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
			$order   = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
			$result  = strcmp( $a[ $orderby ], $b[ $orderby ] ); //Determine sort order

			return ( $order === 'asc' ) ? $result : - $result; //Send final sort direction to usort
		}

		usort( $data, 'usort_reorder' );


		/***********************************************************************
		 * ---------------------------------------------------------------------
		 * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
		 *
		 * In a real-world situation, this is where you would place your query.
		 *
		 * For information on making queries in WordPress, see this Codex entry:
		 * http://codex.wordpress.org/Class_Reference/wpdb
		 *
		 * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
		 * ---------------------------------------------------------------------
		 **********************************************************************/


		/**
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/**
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		$total_items = count( $data );


		/**
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to
		 */
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );


		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;


		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
		) );
	}


}

