{assign var=maxAK value=50}
{assign var=maxLvl1 value=52}
{assign var=maxlvl2 value=101}
{assign var=maxAP1 value=170876633}
{assign var=maxAP2 value=66584264476633}
<div section>
    <header class="contentHeader">
        <div class="contentHeaderTitle">
            <h2 class="contentTitle boxTitle">{lang}wcf.page.gman.arsenal.activity.artifactprogression{/lang}</h2>
        </div>
    </header>
    {foreach from=$artifactTracking item=artifactData key=artifactKey name=artifactLoop}
    {if !$tpl.foreach.artifactLoop.first}
    <h2 class="boxTitle">{lang}wcf.page.gman.arsenal.activity.weekfrom{/lang} {@$artifactKey|date}</h2>
    <div class="boxContent">
        {assign var=chartcolor value='green'}
        {assign var=neededXP value=$viewChar->getEquip()->getItem('mainHand')->getTotalXPNeeded($artifactData['artl']->dataIntegerValue +1)}
        {assign var=artkP value=$artifactData['artk']->dataIntegerValue / $maxAK * 100}
        {assign var=artlP value=$artifactData['artl']->dataIntegerValue / $maxLvl1 * 100}
        {assign var=artpP value=$artifactData['artp']->dataIntegerValue / $neededXP * 100}
        {if $artifactData['artl']->dataIntegerValue > 52}
            {assign var=artlP value=$artifactData['artl']->dataIntegerValue / $maxlvl2 * 100}
            {assign var=artpP value=$artifactData['artp']->dataIntegerValue / $neededXP * 100}
            {assign var=chartcolor value='orange'}
        {/if}
        
        <!-- green -->
        <div class="clearfix">
            <div class="c100 p{$artlP|ceil} {$chartcolor}">
                <span>{$artifactData['artl']->dataIntegerValue}</span>
                <div class="slice">
                    <div class="bar"></div>
                    <div class="fill"></div>
                </div>
                <small>{lang}wcf.page.gman.arsenal.activity.artifactlevel{/lang}</small>
            </div>
            <div class="c100 p{$artpP|ceil} small {$chartcolor}">
                <span>{$artifactData['artp']->dataIntegerValue|shortUnit}</span>
                <div class="slice">
                    <div class="bar"></div>
                    <div class="fill"></div>
                </div>
                <small>{lang}wcf.page.gman.arsenal.activity.artifactpower{/lang}</small>
            </div>
            <div class="c100 p{$artkP|ceil} small {$chartcolor}">
                <span>{$artifactData['artk']->dataIntegerValue}</span>
                <div class="slice">
                    <div class="bar"></div>
                    <div class="fill"></div>
                </div>
                <small>{lang}wcf.page.gman.arsenal.activity.artifactknowlegde{/lang}</small>
            </div>
        </div>
        <!-- /green --> 
    </div>
    {/if}
    {/foreach}
</div>