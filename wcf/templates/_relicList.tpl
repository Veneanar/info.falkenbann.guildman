{foreach from=$socketList item=relic}
<li>
    <span class="icon-frame socket-type-{$relic['typeID']}">
        {if $socket['gem']|isset}
        <a href=""
            data-itemid="{$relic['gem']->itemID}"
            data-enchant="0"
            data-gemlist="[]"
            data-transmog="0"
            data-setlist="[]"
            data-context=""
            data-isartifact="0"
            data-bonus="{$relic['gem']->getBonusDataTag()}"
            class="gem wowItemToolTip" title="{$relic['gem']->getName()}">
            {@$relic['gem']->getIcon()->getIconTag($size)}
            <span class="frame"></span>
        </a>
        {/if}
    </span>
</li>
{/foreach}