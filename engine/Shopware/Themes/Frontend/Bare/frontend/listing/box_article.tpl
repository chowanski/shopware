<li class="product--box panel{if $lastitem} is--last{/if}{if $firstitem} is--first{/if}"{if !{config name=disableArticleNavigation}} data-category-id="{$sCategoryCurrent}" data-ordernumber="{$sArticle.ordernumber}"{/if}>

	<div class="panel--body has--border">

		{* Product box badges - highlight, newcomer, ESD product and discount *}
		{block name='frontend_listing_box_article_badges'}
			{include file="frontend/listing/product-box/badges.tpl"}
		{/block}

		{* Customer rating for the product *}
		{block name='frontend_listing_box_article_rating'}
			{if $sArticle.sVoteAverange.averange}
                {include file='frontend/_includes/rating.tpl' points=$sArticle.sVoteAverange.averange type="aggregated" base="5" label=false}
			{/if}
		{/block}

		{* Product image *}
		{block name='frontend_listing_box_article_picture'}
			{include file="frontend/listing/product-box/image.tpl"}
		{/block}

		{* Product name *}
		{block name='frontend_listing_box_article_name'}
			<a href="{$sArticle.linkDetails|rewrite:$sArticle.articleName}" class="product--title"
			   title="{$sArticle.articleName|escape}">{$sArticle.articleName|truncate:47}</a>
		{/block}

		{* Product description *}
		{block name='frontend_listing_box_article_description'}
			{if $sTemplate eq 'listing-1col'}
				{$size=180}
			{else}
				{$size=60}
			{/if}

			{include file="frontend/listing/product-box/description.tpl" size=$size}
		{/block}

		{block name='frontend_listing_box_article_price_info'}
			<div class="product--price">

				{* Product price - Unit price *}
				{block name='frontend_listing_box_article_unit'}
					{include file="frontend/listing/product-box/unit-price.tpl"}
				{/block}

				{* Product price - Default and discount price *}
				{block name='frontend_listing_box_article_price'}
					{include file="frontend/listing/product-box/price.tpl"}
				{/block}
			</div>
		{/block}

		{* Product actions - Compare product, more information, buy now *}
		{block name='frontend_listing_box_article_actions'}
			{include file="frontend/listing/product-box/actions.tpl"}
		{/block}

	</div>
</li>