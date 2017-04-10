{foreach from=$socketList item=socket}
<li>
    <span class="icon-socket socket-type-{$socket['typeID']}">
        {if $socket['gem']|isset}
            <a href="/wow/de/item/130218" class="gem">
                {@$socket['gem']->getIcon()->getIconTag(18)}
                <span class="frame"></span>
            </a>
        {/if}
    </span>
        {if $socket['gem']|isset}
            {@$socket['gem']->getNameTag()}
        {else}
            {lang}wcf.page.gman.tooltip.nogem{/lang}
         {/if}
        <span class="clear">
            <!-- -->
        </span>
</li>
{/foreach}