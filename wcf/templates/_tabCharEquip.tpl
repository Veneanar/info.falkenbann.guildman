<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.page.gman.arsenal.menu.equip{/lang}</h1>
        <p>{lang}wcf.page.gman.arsenal.menu.equip.description{/lang}</p>
    </div>
</header>
<div class="row section sectionContainerList">
    <div class="col-xs-12 col-md-8">
        <table class="smalltable">
            <thead>
                <tr>
                    <th class="columnTitle columnItemSlot">{lang}wcf.page.gman.armory.item.slot{/lang}</th>
                    <th class="columnIcon columnItemName" colspan="2">{lang}wcf.page.gman.armory.item.name{/lang}</th>
                    <th class="columnDigits columnItemLevel">{lang}wcf.page.gman.armory.item.ilvl{/lang}</th>
                    <th class="columnTitle columnItemEnchanted">{lang}wcf.page.gman.armory.item.enchanted{/lang}</th>
                    {event name='columnHeads'}
                </tr>
            </thead>

            <tbody data-benchmark="{$viewChar->getRuntime()}">
                {foreach from=$slotList item=slot}
                {assign var="item" value=$viewChar->getEquip()->getItem($slot->fieldName)}
                <tr class="jsItemRow" data-benchmark="{$viewChar->getRuntime()}" data-slot="{$slot->slotID}" data-menu-item="{$viewChar->getEquip()->getItem($slot->fieldName)->itemID}">
                    {if $item->itemID==0}
                    <td class="columnTitle columnItemSlot">{$slot->getName()}</td>
                    <td class="columnIcon"></td>
                    <td class="columnItemName"></td>
                    <td class="columnDigits columnItemLevel">0</td>
                    <td class="columnTitle columnItemEnchanted"></td>
                    {else}
                    <td class="columnTitle columnItemSlot">{$slot->getName()}</td>
                    <td class="columnIcon">
                        {@$item->getIcon()->getImageTag(36)}
                    </td>
                    <td class="columnItemName">
                        <a href="{link controller='ArmoryItem' object=$item}enchant={$item->enchantID}&gemlist={$item->getGemDataTag(1)}&transmog={$item->transmogID}&setlist={$item->getSetDataTag(1)}&context={$item->context}&bonus={$item->getBonusDataTag(1)}&itemlevel={$item->itemLevel}&isartifact={$item->isArtifact()}{/link}"
                            class="wowItemToolTip"
                            data-itemid="{$item->itemID}"
                            data-enchant="{$item->enchantID}"
                            data-gemlist="{$item->getGemDataTag()}"
                            data-transmog="{$item->transmogID}"
                            data-setlist="{$item->getSetDataTag()}"
                            data-context="{$item->context}"
                            data-bonus="{$item->getBonusDataTag()}"
                            data-itemlevel="{$item->itemLevel}"
                            data-isartifact="{$item->isArtifact()}"
                            name="{$item->itemID}Tooltip">
                            {@$item->getNameTag()}
                        </a>
                    </td>
                    <td class="columnDigits columnItemLevel">
                        {$item->itemLevel}
                    </td>
                    <td class="columnTitle columnItemEnchanted">
                        <ul class="inlineList">
                            {if $item->hasSockets()}
                            {@$item->getSmallSocketTag()}
                            {/if}
                            {if $item->isEnchanhtable()}
                            <li>
                                {if $item->isEnchanted()}
                                <span class="icon-frame enchant">
                                    <a href="{link controller='ArmoryItem' object=$item} enchant="{$item->enchantID}",gemlist="{$item->getGemDataTag()}",transmog="{$item->transmogID}",setlist="{$item->getSetDataTag()}",context="{$item->context}",bonus="{$item->getBonusDataTag()}",itemlevel="{$item->itemLevel}",isartifact="{$item->isArtifact()}"{/link}" data-itemid="{$item->itemID}" class="wowSpellToolTip enchant" title="{$item->getEnchant()->getName()}">
                                        {@$item->getEnchant()->getIcon()->getIconTag(18)}
                                        <span class="frame"></span>
                                    </a>
                                </span>
                                {else}
                                <span class="color-red">{lang}wcf.page.gman.tooltip.noenchant{/lang}</span>
                                {/if}
                            </li>
                            {/if}
                        </ul>
                    </td>
                    {event name='columns'}
                </tr>
                {/if}
                {/foreach}
            </tbody>
        </table>
    </div>
    <div class="col-xs-12 col-md-4">
        {include file='charAttribute'}
    </div>
</div>
<script>

</script>
