<div section>
    <header class="contentHeader">
        <div class="contentHeaderTitle">
            <h2 class="contentTitle boxTitle">{lang}wcf.page.gman.arsenal.activity.itemlevel{/lang}</h2>
        </div>
    </header>
    <h2 class="boxTitle"></h2>
    <div class="boxContent">
        <div class="charts charts--vertical">
            {assign var=highest value=1}
            {foreach from=$itemlevelTracking item=ilvlData}
                {if $ilvlData['ilvl']->dataIntegerValue >= $highest}
                    {assign var=highest value=$ilvlData['ilvl']->dataIntegerValue}
                {/if}
            {/foreach}
            {foreach from=$itemlevelTracking item=ilvlData key=ilvlKey name=ilvlLoop}
            {if !$tpl.foreach.ilvlLoop.first}
            {assign var=ilvlP value=$ilvlData['ilvl']->dataIntegerValue / $highest * 100}
            {assign var=ilvleqP value=$ilvlData['ilvleqipped']->dataIntegerValue / $highest * 100}
            {@$ilvlKey|date:"j. n"}
            <div class="charts__chart chart--p{$ilvlP|ceil} jsTooltip" title="{lang}wcf.page.gman.arsenal.activity.ilvl.total{/lang}: {$ilvlData['ilvl']->dataIntegerValue}">
                <span class="charts__percent">{$ilvlData['ilvl']->dataIntegerValue}</span>
            </div><!-- /.charts__chart -->
            <div class="charts__chart chart--p{$ilvleqP|ceil} chart--green chart--hover jsTooltip" title="{lang}wcf.page.gman.arsenal.activity.ilvl.equipped{/lang}: {$ilvlData['ilvleqipped']->dataIntegerValue}">
                <span class="charts__percent">{$ilvlData['ilvleqipped']->dataIntegerValue} </span>
            </div><!-- /.charts__chart -->
            {/if}
            {/foreach}
         </div>
    </div>
</div>