{assign var="artifact" value=$viewChar->getEquip()->getItem('mainHand')}
{if $artifact->isArtifact()}
<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h2 class="contentTitle">Artefakt Talente</h2>
    </div>
</header>
<div class="section sectionContainerList">
    <ul class="inlineList">
        {foreach from=$artifact->getSockets() item=relic}
        <li class="artifactRelic">
            <span class="icon-frame socket-type-{$relic['typeID']}">
                {if $relic['gem']|isset}
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
                    {@$relic['gem']->getIcon()->getIconTag(56)}
                    <span class="frame"></span>
                </a>
                {/if}
            </span>
        </li>
        {/foreach}
    </ul>
</div>
    <div class="section sectionContainerList">
        {foreach from=$artifact->getOrderedTraits() item=artifactList key=artifactOrderNo}
        <ul class="artifactTrait{$artifactOrderNo} containerList">
            <li>
                <ul class="inlineList">
                    {foreach from=$artifactList item=artifact}
                    <li data-object-id="{$artifact->artifactID}" class="artifactTraitItem">
                        <div class="box24 boxMenuLink">
                            <a href="{link controller='ArmorySpell' object=$artifact}{/link}" title="{$artifact->spellName}">{@$artifact->getIcon()->getImageTag(36, false, true)} {$artifact->spellName} <b>Rang: {$artifact->rank}</b></a>
                        </div>
                    </li>
                    {/foreach}
                </ul>
            </li>
        </ul>
        {/foreach}
    </div>

{/if}



