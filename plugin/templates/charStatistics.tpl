<section class="box">
{foreach from=$viewChar->getCharacterStatistics()->subCategories item=category}
    <h2 class="boxTitle">{$category['name']}</h2>
    <nav class="boxContent">
        <ol class="boxMenu">
            {foreach from=$category['statistics']->subCategories item=statistics}
            <li data-id="{$statistics['id']}" class="">
                <span class="name">{$statistics['name']}</span>
                <span class="value">{$statistics['quantity']}</span>
                <span class="clear"><!-- --></span>
            </li>
            {/foreach}
        </ol>
    </nav>
{/foreach}
</section>

