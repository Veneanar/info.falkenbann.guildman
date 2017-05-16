<div class="wiki-tooltip">    
    <span class="icon-frame frame-56 float-right" style='background-image: url("{@$spell->getIcon()->getURL(56)}");'>
    </span>
    <h3>{$spell->getName()}</h3>
    <h4>{if $spell->rank > 0}{lang}wcf.page.gman.tooltip.rank{/lang} {$spell->rank}{/if}</h4>
    <div>
        {if $spell->cooldown|isset}<span class="float-right">{$spell->cooldown}</span>{/if}
        {if $spell->castTime|isset}{$spell->castTime}{/if}
        <span class="clear"><!-- --></span>
    </div>
    <div>
        {if $spell->powerCost|isset}<span class="float-right">{$spell->powerCost}</span>{/if}
        {if $spell->range|isset}{$spell->range}{/if}
        <span class="clear"><!-- --></span>
    </div>
    <div class="color-tooltip-yellow">
        {$spell->getDescription()}
    </div>
</div>


