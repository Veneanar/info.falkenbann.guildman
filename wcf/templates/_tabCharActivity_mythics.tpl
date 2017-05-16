<section class="header">
    <header class="contentHeader">
        <div class="contentHeaderTitle">
            <h2 class="contentTitle boxTitle">{lang}wcf.page.gman.arsenal.activity.mythicprogression{/lang}</h2>
        </div>
    </header>
</section>
    {assign var=m2pstart value=0}
    {assign var=m5pstart value=0}
    {assign var=m10pstart value=0}
    {assign var=m15pstart value=0}
    {assign var=showmax value=0}
    {foreach from=$mythicPlusTracking item=mythPlus key=mythPlusKey name=mythPlusLoop}
        {if $tpl.foreach.mythPlusLoop.first}
            {assign var=m2pstart value=$mythPlus['myth2']->dataIntegerValue}
            {assign var=m5pstart value=$mythPlus['myth5']->dataIntegerValue}
            {assign var=m10pstart value=$mythPlus['myth10']->dataIntegerValue}
            {assign var=m15pstart value=$mythPlus['myth15']->dataIntegerValue}
        {else}
            {assign var=maxcount value=$mythPlus['myth2']->dataIntegerValue - $m2pstart}
            {if $maxcount > 0}
            {if $showmax==1}</div></section>{lang}wcf.page.gman.arsenal.activity.highestmythic{/lang}: {$mythPlus['mythmax']->dataIntegerValue}{/if}
            <section class="box">
                <h2 class="boxTitle">{lang}wcf.page.gman.arsenal.activity.weekfrom{/lang} {@$mythPlusKey|date}</h2>
                <div class="boxContent charts charts--grouped">

                    <div class="jsTooltip charts__chart chart--p100 chart--lg chart--grey" data-maxcount={$maxcount} title="{lang}wcf.page.gman.arsenal.activity.mythic2higher{/lang}: {$mythPlus['myth2']->dataIntegerValue - $m2pstart}">
                        {if $mythPlus['myth5check']->dataIntegerValue > 0 && $mythPlus['myth5']->dataIntegerValue - $m5pstart > 0}
                        {assign var=m5p value=($mythPlus['myth5']->dataIntegerValue - $m5pstart) / $maxcount * 100}
                        <div class="jsTooltip charts__chart chart--p{$m5p|ceil} chart--green" title="{lang}wcf.page.gman.arsenal.activity.mythic5orhigher{/lang}: {$mythPlus['myth5']->dataIntegerValue - $m5pstart}">
                            {if $mythPlus['myth10check']->dataIntegerValue  > 0 && $mythPlus['myth10']->dataIntegerValue - $m10pstart > 0}
                            {assign var=m10p value=($mythPlus['myth10']->dataIntegerValue - $m10pstart)  / $maxcount * 100}
                            <div class="jsTooltip charts__chart chart--p{$m10p|ceil} chart--blue" title="{lang}wcf.page.gman.arsenal.activity.mythic10orhigher{/lang}: {$mythPlus['myth10']->dataIntegerValue - $m10pstart}">
                                {if $mythPlus['myth15check']->dataIntegerValue > 0 && $mythPlus['myth15']->dataIntegerValue - $m15pstart > 0}
                                {assign var=m15p value=($mythPlus['myth15']->dataIntegerValue - $m15pstart) / $maxcount * 100}
                                <div class="jsTooltip charts__chart chart--p{$m15p|ceil} chart--red" title="{lang}wcf.page.gman.arsenal.activity.mythic15orhigher{/lang}: {$mythPlus['myth15']->dataIntegerValue - $m15pstart}"></div>
                                {/if}
                            </div>
                            {/if}
                        </div>
                        {/if}
                    </div>
                
                {if $showmax==0}{assign var=showmax value=1}{/if}
                {if $tpl.foreach.mythPlusLoop.last}</div></section>{lang}wcf.page.gman.arsenal.activity.highestmythic{/lang} {lang}wcf.page.gman.arsenal.activity.unknown{/lang}{/if}
            {/if}
            {assign var=m2pstart value=$mythPlus['myth2']->dataIntegerValue}
            {assign var=m5pstart value=$mythPlus['myth5']->dataIntegerValue}
            {assign var=m10pstart value=$mythPlus['myth10']->dataIntegerValue}
            {assign var=m15pstart value=$mythPlus['myth15']->dataIntegerValue}
        {/if}
    {/foreach}
<section class="">
    <div class="chart--red legend"></div><span class="legendtext">{lang}wcf.page.gman.arsenal.activity.mythic15higher{/lang}</span>
    <div class="chart--blue legend" ></div><span class="legendtext">{lang}wcf.page.gman.arsenal.activity.mythic10higher{/lang}</span>
    <div class="chart--green legend"></div><span class="legendtext">{lang}wcf.page.gman.arsenal.activity.mythic5higher{/lang}</span>
    <div class="chart--grey legend"></div><span class="legendtext">{lang}wcf.page.gman.arsenal.activity.mythic2higher{/lang}</span>
    <div style="clear:both"></div>
    <small>{lang}wcf.page.gman.arsenal.activity.unknown.description{/lang}</small>
</section>