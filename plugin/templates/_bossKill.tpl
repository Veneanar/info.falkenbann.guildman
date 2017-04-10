<section class="box">
{foreach from=$zoneList item=zone}
    <h2 class="boxTitle">{$zone['name']}</h2>
    <nav class="boxContent" data-zone-id="{$zone['id']}">
        <ol class="boxMenu ">
            {foreach from=$zone['bosses'] item=bossmain}
            {assign var="boss" value=$bossmain['boss']}
            {assign var="modes" value=$bossmain['modes']}
            <li>
                <ul class="">
                    <li class="summary-stats-column">
                        <div class="jsTooltip name boss-container containerHeadline" title="{$boss->getSimpleTooltip()}">
                            {@$boss->getIcon()->getImageTag()}
                            <h3>{$boss->getName()}</h3>
                        </div>
                         <span class="value">
                            {foreach from=$modes item=mode}
                            <img src="{$mode['icon']}" style="width: 18px; height: 18px;" alt="{$mode['difficulty']}" class="jsTooltip" title="{$mode['difficulty']}" />
                            <span class="jsTootlTip" title="{$mode['killDate']|time}">{$mode['quantity']}</span>
                            {/foreach}
                        </span>
                        <span class="clear"><!-- --></span>
                    </li>
                </ul>
            </li>
            {/foreach}
        </ol>
    </nav>
{/foreach}
</section>