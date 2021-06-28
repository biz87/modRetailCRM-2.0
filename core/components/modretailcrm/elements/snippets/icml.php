<?php

/**
 * @var modX $modx
 * @var array $scriptProperties
 */
header('Content-Type: application/xml; charset=utf-8');
//services
$pdo = $modx->getService('pdoTools');
$pdoFetch = $modx->getService('pdoFetch');

//common params
$date = date('Y-m-d H:i');
$shop = $modx->getOption('shop', $scriptProperties, $modx->getOption('site_name'));
$company = $modx->getOption('company', $scriptProperties, $modx->getOption('site_name'));
$parents = $modx->getOption('parents', $scriptProperties, 0);
$parentsArr = array_map('trim', explode(',', $parents));
$allow_msoptionsprice = $modx->getOption('modretailcrm_allow_msoptionsprice');

$mainTemplate = '@INLINE
<yml_catalog date="{$date}">
    <shop> 
        <name>{$name | replace : "&" : "AND"}</name> 
        <company>{$company | replace : "&" : "AND"}</company> 
        <categories>{$categories}</categories> 
        <offers>{$offers}</offers> 
    </shop>
</yml_catalog>
';
$outputWrapper = $modx->getOption('outputWrapper', $scriptProperties, $mainTemplate);


// categories params
$categoryTemplate = '@INLINE
<category id="{$id}" {if $parent?}parentId="{$parent}"{/if}>{$pagetitle | replace : "&" : "AND"}</category> 
';
$categoryTpl = $modx->getOption('categoryTpl', $scriptProperties, $categoryTemplate);

//product offers params
$offerTemplate = '@INLINE
{if $modifications?}
    {foreach $modifications as $modification}
        <offer 
            id="mod-{$id}-{$modification.id}" 
            productId="{$id}" 
            {if $modification.count?}
                quantity="{$modification.count}"
            {/if}
        > 
            <url>{$id | url : ["scheme" => "full"] }</url> 
            <price>
                {if $modification.price?}
                    {$modification.price}.00
                {else}
                    {$price}.00
                {/if}
            </price> 
            <categoryId>{$parent}</categoryId> 
            {if $image}
                <picture>{"site_url" | option | preg_replace : "#/$#" : ""}{$image}</picture>
            {/if}
            
            <name>{$pagetitle | replace : "&" : "AND"} - {$modification.name | replace : "&" : "AND"}</name> 
            {if $xmlId}
                <xmlId>{$xmlId}</xmlId>
            {/if}
            <productName>{$pagetitle | replace : "&" : "AND"}</productName> 
            
            {if $modification.article?}
                <param name="Артикул" code="article">{$modification.article | replace : "&" : "AND"}</param> 
            {/if}
            {foreach $modification.options as $key => $value}
                {switch $key}
                    {case "size"}
                    <param name="Размер" code="{$key}">{$value | replace : "&" : "AND"}</param>
                    {case "color"}
                    <param name="Цвет" code="{$key}">{$value | replace : "&" : "AND"}</param>
                    {case "weight"}
                    <param name="Вес" code="{$key}">{$value | replace : "&" : "AND"}</param>
                    {default}
                    <param name="{$key}" code="{$key}">{$value | replace : "&" : "AND"}</param>
                {/switch}                
            {/foreach}
            
            <vendor>{$_pls["vendor.name"] | replace : "&" : "AND"}</vendor> 
            {if $weight?}
                <param name="Вес" code="weight">{$weight | replace : "&" : "AND"}</param>
            {/if}
            {if $modification.weight?}
                <param name="Вес" code="weight">{$modification.weight | replace : "&" : "AND"}</param>
            {/if}
            <unit code="pcs" name="Штука" sym="шт." />
        </offer> 
    {/foreach}
    <offer id="{$id}" productId="{$id}"> 
        <url>{$id | url : ["scheme" => "full"] }</url> 
        <price>{$price}.00</price> 
        <categoryId>{$parent}</categoryId> 
        <picture>{"site_url" | option | preg_replace : "#/$#" : ""}{$image}</picture> 
        <name>{$pagetitle | replace : "&" : "AND"}</name> 
        {if $xmlId}
            <xmlId>{$xmlId}</xmlId>
        {/if}
        <productName>{$pagetitle | replace : "&" : "AND"}</productName> 
        {if $article?}
            <param name="Артикул" code="article">{$article | replace : "&" : "AND"}</param>
        {/if}
        <vendor>{$_pls["vendor.name"] | replace : "&" : "AND"}</vendor> 
        {if $weight?}
            <param name="Вес" code="weight">{$weight | replace : "&" : "AND"}</param>
        {/if}
        <unit code="pcs" name="Штука" sym="шт." />
    </offer>
{else}
    <offer id="{$id}" productId="{$id}"> 
        <url>{$id | url : ["scheme" => "full"] }</url> 
        <price>{$price}.00</price> 
        <categoryId>{$parent}</categoryId> 
        <picture>{"site_url" | option | preg_replace : "#/$#" : ""}{$image}</picture> 
        <name>{$pagetitle | replace : "&" : "AND"}</name> 
        {if $xmlId}
            <xmlId>{$xmlId}</xmlId>
        {/if}
        <productName>{$pagetitle | replace : "&" : "AND"}</productName> 
        {if $article?}
            <param name="Артикул" code="article">{$article | replace : "&" : "AND"}</param>
        {/if}
        <vendor>{$_pls["vendor.name"] | replace : "&" : "AND"}</vendor> 
        {if $weight?}
            <param name="Вес" code="weight">{$weight}</param>
        {/if}
        <unit code="pcs" name="Штука" sym="шт." />
    </offer>
{/if}
';

$offerTpl = $modx->getOption('offerTpl', $scriptProperties, $offerTemplate);


//queries
$mainchildIds = array();
foreach ($parentsArr as $parent) {
    $childIds = $modx->getChildIds($parent, 10, array('context' => 'web'));
    $mainchildIds = array_merge($mainchildIds, $childIds);
}

//get categories
$where = array(
    'class_key' => 'msCategory',
    'deleted' => 0,
    'published' => 1,
);
if (count($mainchildIds) > 0) {
    $where['id:IN'] = array_merge($mainchildIds, $parentsArr);
}
$categories = $pdoFetch->getCollection(
    'msCategory',
    $where,
    [
        'select' => 'id,pagetitle,parent',
    ]
);
$categoriesXML = '';
$parentsIds = $parentsArr;

if (!empty($categories)) {
    foreach ($categories as $category) {
        $parentsIds[] = $category['id'];
    }

    foreach ($categories as $category) {
        if (in_array($category['parent'], $parentsArr)) {
            unset($category['parent']);
        }

        if (!in_array($category['parent'], $parentsIds)) {
            unset($category['parent']);
        }

        if (in_array($category['id'], $parentsArr)) {
            unset($category['parent']);
        }
        $categoriesXML .= $pdo->getChunk($categoryTpl, $category);
    }
}

//get products
$where = array(
    'class_key' => 'msProduct',
    'deleted' => 0,
    'published' => 1,
);
if (count($mainchildIds) > 0) {
    $where['id:IN'] = $mainchildIds;
}
$products = $pdoFetch->getCollection(
    'msProduct',
    $where
);
$productsXML = '';
if (!empty($products)) {
    foreach ($products as $product) {
        if (!in_array($product['parent'], $parentsIds)) {
            continue;
        }
        if ($allow_msoptionsprice) {
            $modifications = $pdoFetch->getCollection(
                'msopModification',
                ['rid' => $product['id']]
            );
            if (!empty($modifications)) {
                foreach ($modifications as $modification) {
                    $product['modifications'][] = $modification;
                }
            }
        }

        $productsXML .= $pdo->getChunk($offerTpl, $product);
    }
}


return $pdo->getChunk($outputWrapper, array(
    'date' => $date,
    'name' => $shop,
    'company' => $company,
    'categories' => $categoriesXML,
    'offers' => $productsXML,
));
