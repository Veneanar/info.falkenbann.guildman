﻿{include file='header' pageTitle='wcf.acp.gman.character.list'}

<script data-relocate="true">
	$(function() {
		new WCF.Action.Delete('wcf\\data\\wow\\character\\CharacterAction', '.jsCharacterRow');
	});
	require(['WoltLabSuite/Core/Ui/User/Search/Input'], function(UiUserSearchInput) {
	    new UiUserSearchInput(elBySel('input[name="ownerName"]'));
	});

    require(['WoltLabSuite/GMan/Ui/Character/Search/Input'], function(UiCharacterSearchInput) {
        new UiCharacterSearchInput(elBySel('input[name="charName"]'));
    });

    require(['WoltLabSuite/GMan/Ui/GuildGroup/Search/Input'], function (UiGuildGroupSearchInput) {
        new UiGuildGroupSearchInput(elBySel('input[name="groupName"]'));
    });
    require(['WoltLabSuite/GMan/Character/Update'], function (UpdateData) {
        new UpdateData(elByClass('jsUpdateButton'));
    });
</script>
<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.gman.character.list{/lang}</h1>
        <p class="contentDescription">
        {if $items > 0}{$items} {lang}wcf.acp.gman.character.found{/lang}
        {else} {lang}wcf.page.search.error.noResults{/lang}
        {/if}
        </p>
    </div>

    {hascontent}
    <nav class="contentHeaderNavigation">
        <ul>
            {content}
            {if $__wcf->getSession()->getPermission('admin.gman.canAddGroups"')}
            <li><a href="{link controller='UserGroupAdd'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.acp.character.add{/lang}</span></a></li>
            {/if}
            {event name='contentHeaderNavigation'}
            {/content}
        </ul>
    </nav>
    {/hascontent}
</header>

<form method="post" action="{link controller='CharacterList'}{/link}">
    <section class="section">
        <h2 class="sectionTitle">{lang}wcf.global.filter{/lang}</h2>

        <div class="row rowColGap formGrid">
            <dl class="col-xs-12 col-md-4">
                <dt></dt>
                <dd>
                    <select name="classID" id="classID">
                        <option value="0">{lang}wcf.page.gman.wow.class{/lang}</option>
                        {foreach from=$classes item=$class}
                        <option value="{$class->wclassID}" {if $class->wclassID==$classID} selected{/if}>{$class->getName()}</option>
                        {/foreach}
                    </select>
                </dd>
            </dl>
            <dl class="col-xs-12 col-md-4">
                <dt></dt>
                <dd>
                    <select name="raceID" id="raceID">
                        <option value="0">{lang}wcf.page.gman.wow.race{/lang}</option>
                        {foreach from=$races item=$race}
                        <option value="{$race->wraceID}" {if $race->wraceID==$raceID} selected{/if}>{$race->getName()}</option>
                        {/foreach}

                    </select>
                </dd>
            </dl>
            <dl class="col-xs-12 col-md-4">
                <dt></dt>
                <dd>
                    <select name="rankID" id="rankID">
                        <option value="-1">{lang}wcf.page.gman.wow.rank{/lang}</option>
                        {foreach from=$guild->getRanks() item=$rank}
                        <option value="{$rank['rankID']}" {if $rank['rankID']==$rankID} selected{/if}>{$rank['rankName']}</option>
                        {/foreach}
                    </select>
                </dd>
            </dl>

            <dl class="col-xs-12 col-md-4">
                <dt>{lang}wcf.page.gman.wow.minlevel{/lang}</dt>
                <dd>
                    <input type="text" id="minLevel" name="minLevel" value="{$minLevel}" placeholder="{lang}wcf.page.gman.wow.minlevel{/lang}" class="long" pattern="[0-9]+">
                </dd>
            </dl>

            <dl class="col-xs-12 col-md-4">
                <dt>{lang}wcf.page.gman.wow.minAverageItemLevel{/lang}</dt>
                <dd>
                    <input type="text" id="minAVGILVL" name="minAVGILVL" value="{$minAVGILVL}" placeholder="{lang}wcf.page.gman.wow.minAverageItemLevel{/lang}" class="long" pattern="[0-9]+">
                </dd>
            </dl>

            <dl class="col-xs-12 col-md-4">
                <dt>{lang}wcf.page.gman.wow.username{/lang}</dt>
                <dd>
                    <input type="text" id="ownerName" name="ownerName" value="{$ownerName}" placeholder="{lang}wcf.page.gman.wow.username{/lang}" class="long">
                </dd>
            </dl>
            <dl class="col-xs-12 col-md-4">
                <dt>{lang}wcf.page.gman.wow.charname{/lang}</dt>
                <dd>
                    <input type="text" id="charName" name="charName" value="{$charName}" placeholder="{lang}wcf.page.gman.wow.charname{/lang}" class="long">
                </dd>
            </dl>
            <dl class="col-xs-12 col-md-4">
                <dt>{lang}wcf.page.gman.wow.groupname{/lang}</dt>
                <dd>
                    <input type="text" id="groupName" name="groupName" value="{$groupName}" placeholder="{lang}wcf.page.gman.wow.groupname{/lang}" class="long">
                </dd>
            </dl>



            {event name='filterFields'}
        </div>

        <div class="formSubmit">
            <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
            {@SECURITY_TOKEN_INPUT_TAG}
        </div>
    </section>
</form>

{hascontent}
<div class="paginationTop">
    {content}
    {assign var='linkParameters' value=''}
    {if $raceID}{capture append=linkParameters}&raceID={@$raceID|rawurlencode}{/capture}{/if}
    {if $classID}{capture append=linkParameters}&classID={@$classID|rawurlencode}{/capture}{/if}
    {if $rankID != -1}{capture append=linkParameters}&rankID={@$rankID}{/capture}{/if}
    {if $minLevel}{capture append=linkParameters}&minLevel={@$minLevel}{/capture}{/if}
    {if $minAVGILVL}{capture append=linkParameters}&minAVGILVL={@$minAVGILVL|rawurlencode}{/capture}{/if}    
    {if $groupName}{capture append=linkParameters}&groupName={@$groupName|rawurlencode}{/capture}{/if}
    {pages print=true assign=pagesLinks controller="CharacterList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder$linkParameters"}
    
    {/content}
</div>
{/hascontent}

<div class="section tabularBox">
    <table class="table">
        <thead>
            <tr>
                <th class="columnID columnUserID{if $sortField == 'charID'} active {@$sortOrder}{/if}" colspan="3"><a href="{link controller='CharacterList'}pageNo={@$pageNo}&sortField=charID&sortOrder={if $sortField == 'charID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.global.name{/lang}</a></th>
                <th class="columnTitle columnCharRace{if $sortField == 'c_race'} active {@$sortOrder}{/if}"><a href="{link controller='CharacterList'}pageNo={@$pageNo}&sortField=c_race&sortOrder={if $sortField == 'c_race' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.page.gman.wow.race{/lang}</a></th>
                <th class="columnTitle columnCharClass{if $sortField == 'c_class'} active {@$sortOrder}{/if}"><a href="{link controller='CharacterList'}pageNo={@$pageNo}&sortField=c_class&sortOrder={if $sortField == 'c_class' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.page.gman.wow.class{/lang}</a></th>
                <th class="columnDigits columnCharLevel{if $sortField == 'c_level'} active {@$sortOrder}{/if}"><a href="{link controller='CharacterList'}pageNo={@$pageNo}&sortField=c_level&sortOrder={if $sortField == 'c_level' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.page.gman.wow.level{/lang}</a></th>
                <th class="columnDigits columnCharItemLevel{if $sortField == 'averageItemLevel'} active {@$sortOrder}{/if}"><a href="{link controller='CharacterList'}pageNo={@$pageNo}&sortField=averageItemLevel&sortOrder={if $sortField == 'averageItemLevel' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.page.gman.wow.averageItemLevel{/lang}</a></th>
                <th class="columnTitle columnCharRank{if $sortField == 'guildRank'} active {@$sortOrder}{/if}"><a href="{link controller='CharacterList'}pageNo={@$pageNo}&sortField=guildRank&sortOrder={if $sortField == 'guildRank' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.page.gman.wow.rank{/lang}</a></th>
                <th class="columnTitle columnCharGroups">{lang}wcf.page.gman.wow.groups{/lang}</th>

                {event name='columnHeads'}
            </tr>
        </thead>

        <tbody>
            {foreach from=$chars item=character}
            <tr id="groupContainer{@$character->charID}" class="jsCharacterRow">
                <td class="columnIcon">
                    {if $character->isEditable()}
                    <a href="{link controller='CharacterEdit' object=$character}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 fa-pencil"></span></a>
                    {else}
                    <span class="icon icon16 fa-pencil disabled" title="{lang}wcf.global.button.edit{/lang}"></span>
                    {/if}
                    <span class="icon icon16 fa-refresh jsUpdateButton jsTooltip pointer" title="{lang}wcf.global.button.refresh{/lang}" data-character-id="{$character->characterID}"></span>
                    {event name='rowButtons'}
                </td>
                <td class="columnIcon">{@$character->getAvatar()->getImageTag(24)}</td>
                <td class="columnID columnCharID">{@$character->name} <small>({$character->realm})</small></td>

                <td class="columnTitle columnCharRace">
                    {@$character->getRace()->getTag()}
                </td>
                <td class="columnDigits columnCharClass">
                    {@$character->getClass()->getTag()}                
                </td>
                <td class="columnDigits columnCharLevel">
                    {$character->c_level}
                </td>
                <td class="columnDigits columnCharItemLevel">

                    {$character->getEquip()->averageItemLevel}
                </td>
                <td class="columnTitle columnCharRank">
                    {$guild->getRankName($character->guildRank)}
                </td>
                <td class="columnTitle columnCharGroups">
                    <small>
                    {foreach from=$character->getGroups() item=group name=grouplist}
                        {$group->groupName}
                        {if !$tpl.foreach.grouplist.last}, {/if}
                    {/foreach}
                    </small>
                </td>

                {event name='columns'}
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
<footer class="contentFooter">
    {hascontent}
    <div class="paginationBottom">
        {content}{@$pagesLinks}{/content}
    </div>
    {/hascontent}
    {hascontent}
    <nav class="contentFooterNavigation">
        <ul>
            {content}
            {if $__wcf->getSession()->getPermission('admin.user.canAddGroup')}
            <li><a href="{link controller='CharacterAdd'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.acp.character.add{/lang}</span></a></li>
            {/if}

            {event name='contentFooterNavigation'}
            {/content}
        </ul>
    </nav>
    {/hascontent}
</footer>
{include file='footer'}
