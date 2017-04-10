{foreach from=$socketList item=socket}
<li>
    <span class="icon-frame socket-type-{$socket['typeID']}">
        {if $socket['gem']|isset}
            <a href="/wow/de/item/130218" 
               data-itemid="{$socket['gem']->itemID}" 
               data-enchant="0" 
               data-gemlist="[]"
               data-transmog="0"
               data-setlist="[]"
               data-context="" 
               data-bonus="[]"  
               class="gem wowItemToolTip" title="{$socket['gem']->getName()}">
                {@$socket['gem']->getIcon()->getIconTag(18)}
                <span class="frame"></span>
            </a>
        {/if}
    </span>
</li>
{/foreach}