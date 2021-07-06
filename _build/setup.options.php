<?php

/**
 * Build the setup options form.
 * @var xPDOTransport $transport
 * @var array $options
 * @var modX $modx
 */
$exists = false;
$output = null;
$dialog = false;
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $dialog = true;
        break;

    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

if ($dialog) {
    $ru = (bool)($modx->getOption('manager_language') === 'ru');

    $intro = $ru
        ? 'В системные настройки будут внесены изменения, которые помогут подключиться к RetailCRM'
        : 'System settings will be modified to help connect to RetailCRM';

    $apikey = $ru ? 'Ключ API' : 'API KEY';
    $siteCode = $ru ? 'Символьный код сайта' : 'site code';
    $url = $ru ? 'Адрес Вашей CRM' : 'URL CRM';

    $output =
        '<style>
            #setup_form_wrapper {font: normal 12px Arial;line-height:18px;}
            #setup_form_wrapper a {color: #08C;}
            #setup_form_wrapper label {max-width: 150px; text-align: right;}
            #setup_form {width: 100%;}
            #setup_form_wrapper input {
                height: 25px; border: 1px solid #AAA; border-radius: 3px; padding: 3px; width: 100%;
            }
            #setup_form_wrapper table {margin-top:10px;}
            #setup_form_wrapper table td {padding: 5px 15px;}
            #setup_form_wrapper table tr td:first-child {width: 30%;}
            #setup_form_wrapper table tr td:last-child {width: 70%;}
            #setup_form_wrapper small {font-size: 10px; color:#555; font-style:italic;}
            #setup_form_wrapper .more_info {width: 100%;}
            #setup_form_wrapper .more_info a {line-height: 21px; display:inline-block;}
            #setup_form_wrapper .more_info img {border: none; display:inline-block;padding-top:10px;}
	    </style>
        <div id="setup_form_wrapper">
            <p>' . $intro . '</p>
            <table id="setup_form">
                <tr>
                    <td><label for="apikey">' . $apikey . ':</label></td>
                    <td><input type="text" name="apikey" value="" id="apikey" /></td>
                </tr>
                <tr>
                    <td><label for="code">' . $siteCode . '</label></td>
                    <td><input type="text" name="siteCode" value="" id="code" /></td>
                </tr>
                <tr>
                    <td><label for="url">' . $url . '</label></td>
                    <td><input type="text" name="url" value="" placeholder="https://site.retailcrm.ru/" id="url" /></td>
                </tr>
            </table>
        </div>
	';
}

return $output;
