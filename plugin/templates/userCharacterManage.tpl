{include file='userMenuSidebar'}

{include file='header' __disableAds=true}

{include file='formError'}
<script data-relocate="true">
            require(['WoltLabSuite/GMan/Character/SetMain'], function (SetMain) {
                new SetMain(document.getElementsByClassName('jsPromoteMain'));
            });
            require(['WoltLabSuite/GMan/Character/Update'], function (UpdateData) {
                new UpdateData(elByClass('jsUpdateButton'));
            });
</script>
{if $mainChar|isset && $mainChar->userID > 0}
    <section class="section">
        <header class="sectionHeader">
            <h2 class="sectionTitle">{lang}wcf.user.gman.char.main{/lang}</h2>
            <p class="sectionDescription">{lang}wcf.user.gman.char.main.description{/lang}</p>
        </header>
        <dl>
            <dd>
                <div class="box96">
                    <div>{@$mainChar->getInset()->getImageTag()}</div>
                    <div class="details userInformation">
                        <div class="containerHeadline">
                            <h3><a href="{$mainChar->getLink()}" class="username userLink" data-char-id="{$mainChar->characterID}">{$mainChar->name}</a> <span class="badge userTitleBadge green">{$mainChar->getRank()}</span></h3>
                            <p>
                                <small class="separatorLeft"><span>{$mainChar->getLevel()}</span>, <span>{@$mainChar->getRace()->getTag()}</span>, {@$mainChar->getClass()->getTag()}</small>
                            </p>
                            <p>
                                <small><b>{$mainChar->getRank()}</b></small>
                            </p>
                            <p>
                                <small>
                                    {lang}wcf.page.gman.char.group{/lang}: <br />
                                    {foreach from=$mainChar->getGroups() item=group}
                                    <a href="{link controller='GuildGroup' id=$group->groupID}{/link}," class="jsTooltip" title="{$group->groupTeaser}">{$group->groupName}</a> (<a class="jsTooltip" title="{lang}wcf.acp.group.showMembers{/lang}" href="{link controller='ArmoryList'}groupID={@$group->groupID}{/link}">{$group->getMemberList()|count}</a>)
                                    {/foreach}
                                </small>
                            </p>
                        </div>
                        <nav class="jsMobileNavigation buttonGroupNavigation" style="opacity: 1" data-char-id="{$mainChar->characterID}">
                            <ul class="buttonList iconList jsOnly">
                                <li>
                                    <a class="jsUpdateButton jsTooltip pointer" title="{lang}wcf.global.button.refresh{/lang}" data-character-id="{$mainChar->characterID}">
                                        <span class="icon icon16 fa-refresh"></span>
                                        <span class="invisible">{lang}wcf.user.char.text{/lang}</span>
                                    </a>
                                <li>
                                    <a class="pointer jsTooltip" href="{link controller='userStory' object=$mainChar}{/link}" title="{lang}wcf.user.char.text{/lang}" data-tooltip="{lang}wcf.user.char.text{/lang}">
                                        <span class="icon icon16 fa-pencil"></span>
                                        <span class="invisible">{lang}wcf.user.char.text{/lang}</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </dd>
        </dl>
        </section>
{/if}
{if $twinks|count}
        <section class="section sectionContainerList">
            <h2 class="sectionTitle">{lang}wcf.page.gman.twinklist.confirmed{/lang}</h2>
            <ol class="containerList userList">
                {foreach from=$twinks item=$twink}
                <li class="jsFollowing" id="wcf3">
                    <div class="box48">
                        <a href="{$twink->getLink()}" title="{$twink->getTitle()}">{@$twink->getAvatar()->getImageTag(48)}</a>
                        <div class="details userInformation">
                            <div class="containerHeadline">
                                <h3>
                                    <a href="{$twink->getLink()}" class="username userLink" data-char-id="{$twink->characterID}">{$twink->name}</a> <span class="badge userTitleBadge green">{$twink->getRank()}</span>
                                </h3>
                            </div>
                            <ul class="inlineList">
                                <li>{$twink->getLevel()}</li>
                                <li>{@$twink->getRace()->getTag()}</li>
                                <li>{@$twink->getClass()->getTag()}</li>
                            </ul>
                            <nav class="jsMobileNavigation buttonGroupNavigation" style="opacity: 1" data-char-id="{$twink->characterID}">
                                <ul class="buttonList iconList jsOnly">
                                    {if $twink->isDisabled}
                                    <li><a class="pointer jsTooltip" name="{$twink->name}_warning" title="{lang}wcf.page.gman.dialog.addchar.moderated{/lang}" data-tooltip="{lang}wcf.page.gman.dialog.addchar.moderated{/lang}"><span class="icon icon16 fa-exclamation-triangle yellow"></span> <span class="invisible">{lang}wcf.page.gman.dialog.addchar.moderated{/lang}</span></a></li>
                                    {else}
                                    <li>
                                        <a class="jsUpdateButton jsTooltip pointer" title="{lang}wcf.global.button.refresh{/lang}" data-character-id="{$twink->characterID}">
                                            <span class="icon icon16 fa-refresh"></span>
                                            <span class="invisible">{lang}wcf.user.char.text{/lang}</span>
                                        </a>
                                    <li>
                                        <a class="pointer jsTooltip" href="{link controller='userStory' object=$twink}{/link}" title="{lang}wcf.user.char.text{/lang}" data-tooltip="{lang}wcf.user.char.text{/lang}"> 
                                            <span class="icon icon16 fa-pencil"></span>
                                            <span class="invisible">{lang}wcf.user.char.text{/lang}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="pointer jsPromoteMain jsTooltip" name="{$twink->name}_PromotMain" title="{lang}wcf.page.gman.twink.setmain{/lang}" data-char-id="{$twink->characterID}" data-user-id="{$mainChar->userID}" data-tooltip="{lang}wcf.page.gman.twinklist.setmain{/lang}">
                                            <span class="icon icon16 fa-star"></span> 
                                            <span class="invisible">{lang}wcf.page.gman.twinklist.setmain{/lang}</span>
                                        </a>
                                    </li>
                                    {/if}
                                </ul>
                            </nav>
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
{if $twinksUN|count}
        {if $__wcf->session->getPermission('user.gman.canAddCharOwner')}
        <script data-relocate="true">
            require(['WoltLabSuite/GMan/Character/SetUser'], function (SetCharToUser) {
                new SetCharToUser(document.getElementsByClassName('jsConfirmButton'));
            });
        </script>
        {/if}
        <section class="section sectionContainerList">
            <h2 class="sectionTitle">{lang}wcf.page.gman.twinklist.unfonfirmed{/lang}</h2>
            <ol class="containerList userList">
                {foreach from=$twinksUN item=$twink}
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
                            <nav class="jsMobileNavigation buttonGroupNavigation" style="opacity: 1" data-char-id="{$twink->characterID}">
                                <ul class="buttonList iconList jsOnly">
                                    <li><a class="pointer jsConfirmButton jsTooltip" title="{lang}wcf.page.gman.twink.confirm{/lang}" data-char-id="{$twink->characterID}" data-user-id="{$mainChar->userID}" data-tooltip="{lang}wcf.page.gman.twinklist.confirm{/lang}"><span class="icon icon16 fa-check-square"></span> <span class="invisible">{lang}wcf.page.gman.twinklist.confirmnow{/lang}</span></a></li>
                                </ul>
                            </nav>
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
{if $__wcf->session->getPermission('user.gman.canAddCharOwner')}
    {assign var=addUser value=$userID}
    {include file='charAdd'}
{/if}
{include file='footer'}

