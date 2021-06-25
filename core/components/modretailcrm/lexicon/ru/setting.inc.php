<?php

$_lang['area_modretailcrm_main'] = 'Основные';
$_lang['area_modretailcrm_auth'] = 'Авторизация';
$_lang['area_modretailcrm_classes'] = 'Классы';
$_lang['area_modretailcrm_components'] = 'Компоненты';

$_lang['setting_modretailcrm_apiKey'] = 'Ключ API';
$_lang['setting_modretailcrm_apiKey_desc'] =
    'Ключ API выданный вам в личном кабинете RetailCRM (Раздел Администрирование-Настройки)';

$_lang['setting_modretailcrm_siteCode'] = 'Код сайта';
$_lang['setting_modretailcrm_siteCode_desc'] =
    'Символьный код сайта, кодовое обозначение вашего сайта, 
    сохраненное в настройках RetailCRM (Раздел Администрирование-Магазины)';

$_lang['setting_modretailcrm_url'] = 'Адрес Вашей CRM';
$_lang['setting_modretailcrm_url_desc'] =
    'Уникальный адрес вашей CRM, сформированный при регистрации, например https://minishop2.retailcrm.ru';

$_lang['setting_modretailcrm_log'] = 'Включить логгирование';
$_lang['setting_modretailcrm_log_desc'] = 'Результат отправки данных в RetailCRM будут записаны в журнал ошибок MODX';

$_lang['setting_modretailcrm_sync_statuses'] = 'Перечень статусов заказов для синхронизации';
$_lang['setting_modretailcrm_sync_statuses_desc'] =
    "Через запятую id статусов заказов, которые нужно синхронизировать.
 <br> Важно каждому статусу нужно задать соответствующий символьный код в настройках minishop";

$_lang['setting_modretailcrm_allow_msoptionsprice'] = 'Используете msOptionsPrice2 ?';
$_lang['setting_modretailcrm_allow_msoptionsprice_desc'] =
    'Влияет на XML разметку для выгрузки товаров в RetailCRM, используется в передаче заказов';

$_lang['setting_modretailcrm_custom_orders_class'] = 'Собственный класс заказов';
$_lang['setting_modretailcrm_custom_orders_class_desc'] =
    'JSON массив данных о собственном классе заказа. Подробнее в документации';

$_lang['setting_modretailcrm_custom_customers_class'] = 'Собственный класс для покупателей';
$_lang['setting_modretailcrm_custom_customers_class_desc'] =
    'JSON массив данных о собственном классе информации о покупателях. Подробнее в документации';

$_lang['setting_modretailcrm_rewrite_num'] = 'Перезаписывать номер заказа из CRM';
$_lang['setting_modretailcrm_rewrite_num_desc'] = 'Номер заказа как в CRM. Вместо 2103/308 будет 100A';

$_lang['setting_modretailcrm_add_crm_number'] = 'Перезаписывать номер заказа в CRM';
$_lang['setting_modretailcrm_add_crm_number_desc'] = 'Номер заказа в CRM как на сайте. Вместо 100A будет 2103/308';
