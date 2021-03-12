{* 
 * Created on Tue Feb 23 2021 10:05:45 AM
 *
 * Copyright (c) 2021 MSB
 *}


{if ($avies_display_choice eq "1")}

    <script type="application/ld+json">
        {
            "@context": "http://schema.org/",
            "@type": "Product",
            "category": "{$category|escape:'htmlall':'UTF-8'}",
            "name": "{$category|escape:'htmlall':'UTF-8'}",
            "description": "{$category_description|strip_tags|escape:'htmlall':'UTF-8'}",

            "offers": [{
                "@type": "AggregateOffer",
                "highPrice": "{$high_price|escape:'htmlall':'UTF-8'}",
                "lowPrice": "{$low_price|escape:'htmlall':'UTF-8'}",
                "priceCurrency": "EUR", 
                "offerCount": "{$category_discounted_count|escape:'htmlall':'UTF-8'}"
            }]

            {if ($category_rating_count && $category_rating_value) || $category_image || $category_url || $brand || $sku || $mpn || $gtin_ean || $gtin_upc},{/if}
                
            {if $brand}
                "brand": "{$brand|escape:'htmlall':'UTF-8'}"{if ($category_rating_count && $category_rating_value) || $sku || $mpn || $gtin_ean || $gtin_upc},{/if}
            {/if}

            {if $category_image} 
                "image": "{$category_image|escape:'htmlall':'UTF-8'}"{if ($category_rating_count && $category_rating_value) || $category_url || $brand || $sku || $mpn || $gtin_ean || $gtin_upc},{/if}
            {/if}

            {if $category_url} 
                "url": "{$category_url|escape:'htmlall':'UTF-8'}"{if ($category_rating_count && $category_rating_value) || $brand || $sku || $mpn || $gtin_ean || $gtin_upc},{/if}
            {/if}
            
            {if $sku} 
                "sku": "{$sku|escape:'htmlall':'UTF-8'}"{if ($category_rating_count && $average_rate) || $mpn || $gtin_ean || $gtin_upc},{/if}
            {/if}

            {if $mpn} 
                "mpn": "{$mpn|escape:'htmlall':'UTF-8'}"{if ($category_rating_count && $average_rate) || $gtin_ean || $gtin_upc},{/if}
            {/if}

            {if $gtin_ean} 
                "gtin8": "{$gtin_ean|escape:'htmlall':'UTF-8'}"{if ($category_rating_count && $average_rate) || $gtin_upc},{/if}
            {/if}

            {if $gtin_upc} 
                "gtin12": "{$gtin_upc|escape:'htmlall':'UTF-8'}"{if ($category_rating_count && $average_rate)},{/if}
            {/if} 

            {if $category_rating_count && $category_rating_value}
                "aggregateRating": {
                    "@type": "AggregateRating",
                    "ratingValue": "{$category_rating_value|escape:'htmlall':'UTF-8'}", 
                    "reviewCount": "{$category_rating_count|escape:'htmlall':'UTF-8'}",
                    "worstRating": "1",
                    "bestRating": "5"
                } 
            {/if}
        }
    </script>

{else}

    <div itemscope itemtype="http://schema.org/Product" id="av_snippets_block" itemid="{$category_url|escape:'htmlall':'UTF-8'}">

        <span itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer">
            <meta itemprop="priceCurrency" content="EUR" />
            <meta itemprop="highPrice" content="{$high_price|escape:'htmlall':'UTF-8'}" />
            <meta itemprop="lowPrice" content="{$low_price|escape:'htmlall':'UTF-8'}" />  
            <meta itemprop="offerCount" content="{$category_discounted_count|escape:'htmlall':'UTF-8'}" /> 
            <meta itemprop="url" content="{$category_url|escape:'htmlall':'UTF-8'}" />  
        </span>

        <meta itemprop="category" content="{$category|escape:'htmlall':'UTF-8'}" />
        <meta itemprop="name" content="{$category|escape:'htmlall':'UTF-8'}" />
        <meta itemprop="description" content="{$category_description|strip_tags|escape:'htmlall':'UTF-8'}" /> 

        {if $category_image}
            <meta itemprop="image" content="{$category_image|escape:'htmlall':'UTF-8'}" />
        {/if}

        {if $brand}
            <span itemprop="brand" itemscope itemtype="http://schema.org/Thing">
                <meta itemprop="name" content="{$brand|escape:'htmlall':'UTF-8'}" />
            </span>
        {/if}

        {if $category_url}
            <meta itemprop="url" content="{$category_url|escape:'htmlall':'UTF-8'}" />
        {/if}
        
        {if $sku}
            <meta itemprop="sku" content="{$sku|escape:'htmlall':'UTF-8'}" />
        {/if}

        {if $mpn}
            <meta itemprop="mpn" content="{$mpn|escape:'htmlall':'UTF-8'}" />
        {/if}

        {if $gtin_ean}
            <meta itemprop="gtin8" content="{$gtin_ean|escape:'htmlall':'UTF-8'}" />
        {/if}

        {if $gtin_upc}
            <meta itemprop="gtin12" content="{$gtin_upc|escape:'htmlall':'UTF-8'}" />
        {/if}

        <div itemprop="review" itemscope itemtype="http://schema.org/Review">
            <div itemprop="itemReviewed" itemscope itemtype="http://schema.org/Organization">
                <meta itemprop="name" content="{Configuration::get('PS_SHOP_NAME')}" />
            </div>
        
            <meta itemprop="author" content="{Configuration::get('PS_SHOP_NAME')}" />
            <meta itemprop="datePublished" content="{'Y'|date}-{'m'|date}-{'d'|date}">
        
            <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
                <meta itemprop="worstRating" content="1" />
                <meta itemprop="ratingValue" content="{$category_rating_value|escape:'htmlall':'UTF-8'}" />
                <meta itemprop="bestRating" content="5" />
            </div>
        </div>

        {if $category_rating_count && $category_rating_value}
                <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                    <meta itemprop="ratingValue" content="{$category_rating_value|escape:'htmlall':'UTF-8'}" />
                    <meta itemprop="bestRating"  content="5" />
                    <meta itemprop="worstRating" content="1" />
                    <meta itemprop="reviewCount" content="{$category_rating_count|escape:'htmlall':'UTF-8'}" />
                </div>
            {/if}

    </div>

{/if}