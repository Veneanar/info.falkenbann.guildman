<section class="box">
    {assign var="statlist" value=$viewChar->getCharacterStatistics()->getMaincategories()}
    {foreach from=$statlist item=maincategory}
    {if $maincategory[name]|isset}
    <header class="contentHeader">
        <div class="contentHeaderTitle">
            <h2 class="contentTitle boxTitle">{$maincategory['name']}</h2>
            {foreach from=$maincategory['statistics'] item=statistics}
            <p data-id="{$statistics['id']}" class="summary-stats-column boxContent">
                <span class="name">{$statistics['name']}</span>
                <span class="value">{$statistics['quantity']}</span>
                <span class="clear"><!-- --></span>
            </p>
            {/foreach}
        </div>
    </header>
    {if $maincategory[subCategories]|isset}
    {foreach from=$maincategory['subCategories'] item=subcategory}
    <h2 class="boxTitle">{$subcategory['name']}</h2>
    <nav class="boxContent">
        <ol class="boxMenu">
            {foreach from=$subcategory['statistics'] item=subStatistics}
            <li data-id="{$statistics['id']}" class="summary-stats-column">
                <span class="name">{$subStatistics['name']}</span>
                <span class="value">{$subStatistics['quantity']}</span>
                <span class="clear"><!-- --></span>
            </li>
            {/foreach}
        </ol>
    </nav>
    {/foreach}
    {/if}
    {/if}
    {/foreach}
</section>  