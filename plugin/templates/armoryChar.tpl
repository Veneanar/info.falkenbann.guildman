<!--  /header.tpl-->
{include file='documentHeader'}
{include file='armoryViewSidebar'}
{include file='header'}
            <script data-relocate="true">
                require(['WoltLabSuite/GMan/Ui/TabMenu/Loadable'], function (TabMenuLoadable) {
                    new TabMenuLoadable(document.getElementById('profileContent'), {
                        ajax: {
                            className: 'wcf\\data\\wow\\character\\WowCharacterAction',
                        }
                    })
                });
               require(['WoltLabSuite/GMan/Ui/Item/ItemTooltip'], function (ItemTooltip) {
                        new ItemTooltip('wowItemToolTip');
                });
            </script>
            <div class="box96">
                <div>{@$viewChar->getInset()->getImageTag()}</div>
                <div class="details userInformation">
                    <div class="containerHeadline">
                        <h3>{$viewChar->name}</h3>
                        <p>
                            <small class="separatorLeft"><span>{$viewChar->getLevel()}</span>, <span>{@$viewChar->getRace()->getTag()}</span>, {@$viewChar->getClass()->getTag()}</small>
                        </p>
                        <p>
                            <small><b>{$viewChar->getRank()}</b></small>
                        </p>
                        <p>
                            <small>
                                {lang}wcf.page.gman.char.group{/lang}: <br />
                                {foreach from=$viewChar->getGroups() item=group}
                                <a href="{link controller='GuildGroup' id=$group->groupID}{/link}," class="jsTooltip" title="{$group->groupTeaser}">{$group->groupName}</a> (<a class="jsTooltip" title="{lang}wcf.acp.group.showMembers{/lang}" href="{link controller='ArmoryList'}groupID={@$group->groupID}{/link}">{$group->getMemberList()|count}</a>)
                                {/foreach}
                            </small>
                        </p>
                    </div>
                </div>
            </div>
            <div id="profileContent" data-objectID="{$viewChar->characterID}" class="section tabMenuContainer userProfileContent" data-active="start">
                <nav class="tabMenu">
                    <ul>
                        <li><a href="#start" class="jsTooltip" title="{lang}wcf.page.gman.arsenal.menu.start.description{/lang}">{lang}wcf.page.gman.arsenal.menu.start{/lang}</a></li>
                        <li><a href="#equip" class="jsTooltip" title="{lang}wcf.page.gman.arsenal.menu.equip.description{/lang}">{lang}wcf.page.gman.arsenal.menu.equip{/lang}</a></li>
                        <li><a href="#talents" class="jsTooltip" title="{lang}wcf.page.gman.arsenal.menu.talents.description{/lang}">{lang}wcf.page.gman.arsenal.menu.talents{/lang}</a></li>
                        <li><a href="#stats" class="jsTooltip" title="{lang}wcf.page.gman.arsenal.menu.stats.description{/lang}">{lang}wcf.page.gman.arsenal.menu.stats{/lang}</a></li>
                    </ul>
                </nav>
                <div id="start" class="tabMenuContent" data-menu-item="start">

                    <header class="contentHeader marginTop">
                        <div class="contentHeaderTitle">
                            <h2 class="contentTitle">{lang}wcf.page.gman.arsenal.menu.start{/lang}</h2>
                            <p>{lang}wcf.page.gman.arsenal.menu.start.description{/lang}</p>
                        </div>
                    </header>
                    <div class="section" style="background:url('{@$viewChar->getProfileMain()->getURL()}') no-repeat bottom right; background-attachment:fixed;">
                        <dl>
                            <dt><label for="charName">{lang}wcf.page.gman.arsenal.name{/lang}</label></dt>
                            <dd>
                                {$viewChar->name}
                            </dd>
                        </dl>
                        <dl>
                            <dt><label for="charInfo">{lang}wcf.page.gman.arsenal.info{/lang}</label></dt>
                            <dd>
                                <small class="separatorLeft"><span>{$viewChar->getLevel()}</span>, <span>{@$viewChar->getRace()->getTag()}</span>, {@$viewChar->getClass()->getTag()}</small>
                            </dd>
                        </dl>
                        {if $viewChar->guild['name']|isset}
                        <dl>
                            <dt><label for="cahrGuild">{lang}wcf.page.gman.arsenal.guild{/lang}</label></dt>
                            <dd>
                                {$viewChar->guild['name']}
                            </dd>
                        </dl>
                        {/if}
                        <dl>
                            <dt><label for="charInfo">{lang}wcf.page.gman.arsenal.story{/lang}</label></dt>
                            <dd>
                                {@$viewChar->getChartext()}
                            </dd>
                        </dl>
                        <dl>
                            <dt><label for="charKills">{lang}wcf.page.gman.arsenal.kills{/lang}</label></dt>
                            <dd>
                                
                            </dd>
                        </dl>
                    </div>
                    {include file='_bossKill'}
                </div>
                <div id="equip" class="tabMenuContent" data-menu-item="equip"></div>
                <div id="talents" class="tabMenuContent" data-menu-item="talents">lalala</div>
                <div id="stats" class="tabMenuContent" data-menu-item="stats"></div>
            </div>
            {include file='footer'}