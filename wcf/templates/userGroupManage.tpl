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
<section class="section sectionContainerList">
    <h2 class="sectionTitle">{lang}wcf.page.gman.groupList.groups{/lang}</h2>
        <ol class="containerList userList">
        {foreach from=$allGroups item=$group}
            {assign var=isMember value=false}
        <li class="" id="">
            <div class="box48">
                <a href="{$group->getLink()}" title="{$group->getTitle()}">{@$group->getIcon()->getThumbnailTag()}</a>
                <div class="details userInformation">
                    <div class="containerHeadline">
                        <h3>
                            <a href="{$group->getLink()}" class="username userLink" data-char-id="{$group->characterID}">{$group->groupName}</a>
                        </h3>
                     </div>
                    {assign var=isMember value=$group->isMember(null, $user)}
                     <ul class="inlineList">
                        <li>{$group->groupTeaser}</li>
                        <li><a class="jsTooltip" title="{lang}wcf.acp.group.showMembers{/lang}" href="{link controller='ArmoryList'}groupID={$group->groupID}{/link}">{lang}wcf.page.gman.groupList.membercount{/lang} {$group->getMemberList()|count}</a></li>
                        <li>{if $isMember}{lang}wcf.page.gman.groupList.memberwith{/lang} {$group->getMemberNameFromUser($user)->name}{else}{lang}wcf.page.gman.groupList.notmember{/lang}{/if}</li>
                    </ul>
                            <nav class="jsMobileNavigation buttonGroupNavigation" style="opacity: 1" data-goup-id="{$group->groupID}">
                                <ul class="buttonList iconList jsOnly">
                               {if $isMember}
                                    <li>
                                        <a class="jsTooltip pointer" title="{lang}wcf.page.gman.groupList.gototforum{/lang}" href="">
                                            <span class="icon icon16 fa-users"></span>
                                            <span class="invisible">{lang}wcf.page.gman.groupList.gototforum{/lang}</span>
                                        </a>
                                    </li>
                               {else}
                                    <li>
                                        <a class="pointer jsGroupApply jsTooltip" title="{lang}wcf.page.gman.groupList.signup{/lang}" data-groupID="{$group->groupID}" data-tooltip="{lang}wcf.page.gman.groupList.signup{/lang}">
                                            <span class="icon icon16 fa-sign-in"></span>
                                            <span class="invisible">{lang}wcf.page.gman.groupList.signup{/lang}</span>
                                        </a>
                                    </li>
                                {/if}
                                {if $group->isLeader()}
                                    <li>
                                        <a class="pointer jsTooltip" title="{lang}wcf.page.gman.groupList.editgroup{/lang}" href="">
                                            <span class="icon icon16 fa-pencil"></span>
                                            <span class="invisible">{lang}wcf.page.gman.groupList.editgroup{/lang}</span>
                                        </a>
                                    </li>
                                {/if}
                                </ul>
                            </nav>
                        </div>
                    </div>
                </li>
                {/foreach}
            </ol>
        </section>

<section class="section sectionContainerList">
    <h2 class="sectionTitle">{lang}wcf.page.gman.groupList.applications{/lang}</h2>
    <ol class="containerList userList">
        {foreach from=$applications item=$application}
        {assign var=isMember value=false}
        <li class="" id="">
            <div class="box48">
                <a href="{$application->getGroup()->getLink()}" title="{$application->getGroup()->getTitle()}">{@$application->getGroup()->getIcon()->getThumbnailTag()}</a>
                <div class="details userInformation">
                    <div class="containerHeadline">
                        <h3>
                            <a href="{$application->getGroup()->getLink()}" class="username userLink" data-char-id="{$application->getGroup()->groupID}">{lang}wcf.page.gman.groupList.applicationfor{/lang} {$application->getGroup()->groupName}</a>
                        </h3>
                    </div>
                    <ul class="">
                        <li>{$application->getGroup()->groupTeaser}</li>
                        <li>{@$application->getStateTag()}</li>
                    </ul>

                    <ul id="articleCommentList" class="commentList containerList">
                        <li class="comment">
                            <div class="box32">
                                <a href="{link controller='ArmoryChar' object=$application->getApplicant()}{/link}" title="{$application->getApplicant()->charname}">
                                    {@$application->getApplicant()->getAvatar()->getImageTag(32)}
                                </a>
                                <div itemprop="comment" itemscope itemtype="http://schema.org/Comment">
                                    <div class="commentContent">
                                        <meta itemprop="dateCreated" content="{@$application->lastAppUpdate|date:'c'}">
                                        <div class="containerHeadline">
                                            <h4 itemprop="author" itemscope itemtype="http://schema.org/Person">
                                                <a href="{link controller='ArmoryChar' object=$application->getApplicant()}{/link}" class="userLink" data-user-id="{@$application->getApplicant()->userID}" itemprop="url">
                                                    <span itemprop="name">{$application->getApplicant()->charname}</span>
                                                </a>
                                                <small class="separatorLeft">{@$application->lastAppUpdate|time}</small>
                                            </h4>
                                        </div>
                                        <div class="userMessage" itemprop="text">{@$application->getApplicationText()}</div>
                                    </div>
                                    {if $application->assignedOfficier > 0}
                                    <ul class="containerList commentResponseList">
                                        <li class="commentResponse jsCommentResponse">
                                            <div class="box32">
                                                <a href="{link controller='ArmoryChar' object=$application->getOfficier()}{/link}" title="{$application->getOfficier()->charname}">
                                                    {@$application->getOfficier()->getAvatar()->getImageTag(32)}
                                                </a>
                                                <div class="commentContent commentResponseContent" itemprop="comment" itemscope itemtype="http://schema.org/Comment">
                                                    <meta itemprop="dateCreated" content="{@$application->lastOffUpdate|date:'c'}">
                                                    <div class="containerHeadline">
                                                        <h4 itemprop="author" itemscope itemtype="http://schema.org/Person">
                                                            <a href="{link controller='ArmoryChar' object=$application->getOfficier()}{/link}" class="userLink" data-user-id="{@$application->getOfficier()->userID}" itemprop="url">
                                                                <span itemprop="name">{$application->getOfficier()->charname}</span>
                                                            </a>
                                                            <small class="separatorLeft">{@$application->lastOffUpdate|time}</small>
                                                        </h4>
                                                    </div>
                                                    <div class="userMessage" itemprop="text">{@$application->getAnswerText()}</div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                    {/if}
                                    </div>
                                </div>
                            </li>
                    </ul>
        <nav class="jsMobileNavigation buttonGroupNavigation" style="opacity: 1" data-goup-id="{$application->getGroup()->groupID}">
            <ul class="buttonList iconList jsOnly">
                <li>
                    <a class="jsTooltip pointer" title="{lang}wcf.page.gman.groupList.remove{/lang}" href="">
                        <span class="icon icon16 fa-remove"></span>
                        <span class="invisible">{lang}wcf.page.gman.groupList.remove{/lang}</span>
                    </a>
                </li>
            </ul>
        </nav>
        </div>
        </div>
        </li>
        {/foreach}
    </ol>
</section>

{/if}


{include file='footer'}

