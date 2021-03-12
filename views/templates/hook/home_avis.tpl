{* 

 * Created on Tue Feb 23 2021 10:05:45 AM

 *

 * Copyright (c) 2021 MSB

 *}





{if ($avies_display_choice eq "1")}



    <script type="application/ld+json">

        {

            "@context": "http://schema.org/",

            "@type": "Organization",

            "name": "{Configuration::get('PS_SHOP_NAME')}",

            "aggregateRating": {

                "@type": "AggregateRating",

                "ratingValue": "{$avis[1]}", 

                "reviewCount": "{$avis[0]}",

                "worstRating": "1",

                "bestRating": "5"

            }

        }

    </script>



{else}



    <div id="home_avis" class="avis_verifies" itemprop="review" itemscope itemid="{Configuration::get('PS_SHOP_URL')}"

        itemtype="http://schema.org/Review" {if $hide_avis} style="display:none;" {/if}>



        <div itemprop="itemReviewed" itemscope itemtype="http://schema.org/Organization">

            <meta itemprop="name" content="{Configuration::get('PS_SHOP_NAME')}" />

        </div>



        <meta itemprop="author" content="{Configuration::get('PS_SHOP_NAME')}" />

        <meta itemprop="datePublished" content="{'Y'|date}-{'m'|date}-{'d'|date}">



        <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">

            <meta itemprop="worstRating" content="1" />

            <meta itemprop="ratingValue" content="{$avis[1]}" />

            <meta itemprop="bestRating" content="5" />





            <a href="//www.avis-verifies.com/avis-clients/france-effect.com" target="_blank">

                <img ng-src="//cl.avis-verifies.com/fr/widget4/iframe/ribbonstars5.png"

                    src="//cl.avis-verifies.com/fr/widget4/iframe/ribbonstars5.png">

                <span class="average-title ng-binding">{$avis[1]}</span>

                <div class="ng-binding">Excellent </div>

            </a>

            <div class="header-counter">

                <a href="//www.avis-verifies.com/avis-clients/france-effect.com" target="_blank">

                    <div class="header-teaser ng-binding" ng-hide="hideCount">

                        Basé sur <b class="ng-binding">{$avis[0]}</b> avis

                    </div>

                </a>

                <div class="header-logo">

                    <a href="//www.avis-verifies.com/avis-clients/france-effect.com" target="_blank">

                        <img class="company-logo" ng-src="//cl.avis-verifies.com/fr/widget4/iframe/logo_170.png"

                            src="//cl.avis-verifies.com/fr/widget4/iframe/logo_170.png">

                    </a>

                </div>

            </div>

        </div>





    </div>



    <div itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">

        <meta itemprop="reviewCount" content="{$avis[0]}" />

        <meta itemprop="ratingValue" content="{$avis[1]}" />

        <div itemprop="itemReviewed" itemscope itemtype="http://schema.org/Organization">

            <meta itemprop="name" content="{Configuration::get('PS_SHOP_NAME')}" />

        </div>

    </div>





{/if}