﻿{include file='header' pageTitle='wcf.acp.gman.group.list'}

<script data-relocate="true">
	$(function() {
		new WCF.Action.Delete('wcf\\data\\guild\\group\\GuildGroupAction', '.jsUserGroupRow');
	});
</script>
<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.gman.group.list{/lang}</h1>
        <p class="contentDescription">{$guild->name}</p>
    </div>

    {hascontent}
    <nav class="contentHeaderNavigation">
        <ul>
            {content}
            {if $__wcf->getSession()->getPermission('admin.gman.canAddGroups"')}
            <li><a href="{link controller='UserGroupAdd'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.acp.group.add{/lang}</span></a></li>
            {/if}
            {event name='contentHeaderNavigation'}
            {/content}
        </ul>
    </nav>
    {/hascontent}
</header>
{hascontent}
<div class="paginationTop">
    {content}{pages print=true assign=pagesLinks controller="GuildGroupList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}{/content}
</div>
{/hascontent}

<div class="section tabularBox">
    <table class="table">
        <thead>
            <tr>
                <th class="columnID columnGroupID{if $sortField == 'groupID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='GuildGroupList'}pageNo={@$pageNo}&sortField=groupID&sortOrder={if $sortField == 'groupID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
                <th class="columnTitle columnGroupName{if $sortField == 'groupName'} active {@$sortOrder}{/if}"><a href="{link controller='GuildGroupList'}pageNo={@$pageNo}&sortField=groupName&sortOrder={if $sortField == 'groupName' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.name{/lang}</a></th>
                <th class="columnDigits columnMembers{if $sortField == 'members'} active {@$sortOrder}{/if}"><a href="{link controller='GuildGroupList'}pageNo={@$pageNo}&sortField=members&sortOrder={if $sortField == 'members' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.group.members{/lang}</a></th>
                <th class="columnDigits columnGameRank{if $sortField == 'gameRank'} active {@$sortOrder}{/if}"><a href="{link controller='GuildGroupList'}pageNo={@$pageNo}&sortField=gameRank&sortOrder={if $sortField == 'gameRank' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.gman.guild.rank{/lang}</a></th>

                {event name='columnHeads'}
            </tr>
        </thead>

        <tbody>
            {foreach from=$objects item=group}
            <tr id="groupContainer{@$group->groupID}" class="jsUserGroupRow">
                <td class="columnIcon">
                    {if $group->isEditable()}
                    <a href="{link controller='GuildGroupEdit' id=$group->groupID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 fa-pencil"></span></a>
                    {else}
                    <span class="icon icon16 fa-pencil disabled" title="{lang}wcf.global.button.edit{/lang}"></span>
                    {/if}
                    {if $group->isDeletable()}
                    <span class="icon icon16 fa-times jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$group->groupID}" data-confirm-message-html="{lang __encode=true}wcf.acp.group.delete.sure{/lang}"></span>
                    {else}
                    <span class="icon icon16 fa-times disabled" title="{lang}wcf.global.button.delete{/lang}"></span>
                    {/if}

                    {event name='rowButtons'}
                </td>
                <td class="columnID columnGroupID">{@$group->groupID}</td>
                <td class="columnTitle columnGroupName">
                    {if $group->isEditable()}
                    <a title="{lang}wcf.acp.group.edit{/lang}" href="{link controller='GuildGroupEdit' id=$group->groupID}{/link}">{lang}{$group->groupName}{/lang}</a>
                    {else}
                    {lang}{$group->groupName}{/lang}
                    {/if}
                </td>
                <td class="columnDigits columnMembers">
                    <a class="jsTooltip" title="{lang}wcf.acp.group.showMembers{/lang}" href="{link controller='UserSearch'}groupID={@$group->groupID}{/link}">{#$group->members}</a>
                </td>
                <td class="columnDigits columnGameRank">{$guild->getRankName($group->gameRank)}</td>

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
            <li><a href="{link controller='GuildGroupAdd'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.acp.group.add{/lang}</span></a></li>
            {/if}

            {event name='contentFooterNavigation'}
            {/content}
        </ul>
    </nav>
    {/hascontent}
</footer>
{include file='footer'}
