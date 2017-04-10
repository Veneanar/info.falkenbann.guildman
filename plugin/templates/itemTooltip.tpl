<div class="wiki-tooltip">    
{$item}
    <span class="icon-frame frame-56 float-right" style='background-image: url("{@$item->getIcon()->getURL(56)}");'>
    </span>
    <h3 class="color-q{$item->quality}">{$item->getName()}</h3>
    <ul class="item-specs" style="margin: 0">
        {@$item->getDescriptionTag()}
        <li class="color-tooltip-yellow">Gegenstandsstufe {$item->itemLevel}</li>
        {foreach from=$item->getItemHeader() item=itemheader}
        <li>{$itemheader}</li>
        {/foreach}
        <li>
            <!-- <span class="float-right">Verschiedenes</span> -->
            {$item->getInventoryType()}
        </li>

        {@$item->getStatsTag()}
        {if $item->itemSpells[0]|isset}
            <li class="color-q2 item-spec-group">
                {if $item->itemSpells[0][trigger]=="ON_EQUIP"}{lang}wcf.page.gman.tooltip.onequip{/lang}{else}{lang}wcf.page.gman.tooltip.onuse{/lang}{/if}
                {$item->itemSpells[0][spell][description]}
            </li>
        {/if}
        {if $item->hasSockets()}
            <li>
                <ul class="item-specs">
                    {@$item->getSocketTag()}
                </ul>        
            </li>
        {/if}
        {if $item->gemInfo['bonus']['name']|isset}
        <li class="color-q2 item-spec-group">
            {$item->gemInfo['bonus']['name']}
        </li>
        {/if}
        {if $item->isEnchanhtable()}
            <li class="color-q2 item-spec-group">
                {if $item->isEnchanted()}{@$item->getEnchantmentTag()}{else}<span class="color-red">{lang}wcf.page.gman.tooltip.noenchant{/lang}</span>{/if}
            </li>
        {/if}
        <li>
            <ul class="item-specs">
                {foreach from=$item->getRequierments() item=rq}
                <li>{$rq}</li>
                {/foreach}
                <li>
                    {lang}wcf.page.gman.tooltip.sellprice{/lang} {@$item->getPriceTag()}
                </li>
            </ul>
        </li>
    </ul>
    <span class="clear"><!-- --></span>
</div>
