{if $twinks|isset}

    {if $__wcf->session->getPermission('user.gman.canAddCharOwner')}

    {/if}

<section class="section sectionContainerList">
    <h2 class="sectionTitle">{lang}wcf.page.gman.twinklist{/lang}</h2>
    <ol class="containerList userList">
        {foreach from=$twinks item=$twink}
        <li class="jsFollowing" id="wcf3">
            <div class="box48">
                <a href="{$twink->getLink()}" title="{$twink->getTitle()}">{@$twink->getAvatar()->getImageTag(48)}</a>
                <div class="details userInformation">
                    <div class="containerHeadline">
                        <h3>
                            <a href="{$twink->getLink()}" class="username userLink" data-char-id="{$twink->characterID}">{$twink->name}</a><span class="badge userTitleBadge green">{$twink->getRank()}</span>
                        </h3>
                    </div>
                    <ul class="inlineList">
                        <li>{$twink->getLevel()}</li>
                        <li>{@$twink->getRace()->getTag()}</li>
                        <li>{@$twink->getClass()->getTag()}</li>
                    </ul>
                    {if $twink->userID==0 && $mainChar->userID > 0 && $__wcf->session->getPermission('user.gman.canAddCharOwner')}
                    <nav class="jsMobileNavigation buttonGroupNavigation" style="opacity: 1" data-char-id="{$twink->characterID}">
                        <small class="warning">{lang}wcf.page.gman.twink.unconfirmed{/lang}</small>
                        <ul class="buttonList iconList jsOnly">
                            {if $twink->isEditable()}
                            <li><a class="pointer jsConfirmButton" data-char-id="{$twink->characterID}" data-user-id="{$mainChar->userID}" data-tooltip="{lang}wcf.page.gman.twinklist.confirm{/lang}"><span class="icon icon16 fa-check-square"></span> <span class="invisible">{lang}wcf.page.gman.twinklist.confirmnow{/lang}</span></a></li>
                            {/if}
                        </ul>
                    </nav>
                    {/if}
                    <dl class="plain inlineDataList small">
                        <dt>{lang}wcf.page.gman.twinklist.group{/lang}</dt>
                        {foreach from=$twink->getGroups() item=group}
                            <dd><a href="{link controller='GuildGroupEdit' id=$group->groupID}{/link}," class="jsTooltip" title="{$group->groupTeaser}">{$group->groupName}</a> (<a class="jsTooltip" title="{lang}wcf.acp.group.showMembers{/lang}" href="{link controller='CharacterList'}groupID={@$group->groupID}{/link}">{$group->getMemberList()|count}</a>)</dd>
                        {/foreach}
                    </dl>
                </div>
            </div>
        </li>
        {/foreach}
    </ol>
</section>
{/if}

