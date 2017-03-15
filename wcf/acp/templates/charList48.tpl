{if $twinks|isset}

    {if $__wcf->session->getPermission('user.gman.canAddCharOwner')}
    <script data-relocate="true">
        require(['WoltLabSuite/Core/Ajax'], function(Ajax) {
            "use strict";
            function GuildUpdate() {};
            GuildUpdate.prototype = {
                setup: function() {
                    var buttons = document.getElementsByClassName('jsConfirmButton');
                    for (var i = 0, length = buttons.length; i < length; i++) {
                        buttons[i].addEventListener('click', this._click.bind(this));
                    }
                },
                _click: function(event) {
                    Ajax.api(this, {
                        objectIDs: [event.currentTarget.getAttribute('data-char-id')],
                        parameters: {
                            userID: event.currentTarget.getAttribute('data-user-id')
                        }
                    });
                },
                _ajaxSetup: function() {
                    return {
                        data: {
                            actionName: 'setUser',
                            className: 'wcf\\data\\wow\\character\\WowCharacterAction'
                        }
                    };
                },
                _ajaxSuccess: function(data) {
                    location.reload();
                }
            };
            return new GuildUpdate().setup();
        });
    </script>
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
                            <a href="{$twink->getLink()}" class="username userLink" data-char-id="{$twink->characterID}" id="wcf1">{$twink->name}</a><span class="badge userTitleBadge green">{$twink->getRank()}</span>
                        </h3>
                    </div>
                    <ul class="inlineList">
                        <li>{$twink->getLevel()}</li>
                        <li>{@$twink->getRace()->getTag()}</li>
                        <li>{@$twink->getClass()->getTag()}</li>
                    </ul>
                    {if $twink->userID==0 && $mainChar|isset && $__wcf->session->getPermission('user.gman.canAddCharOwner')}
                    <nav class="jsMobileNavigation buttonGroupNavigation" style="opacity: 1" data-char-id="{$twink->characterID}">
                        <small class="warning">{lang}wcf.page.gman.twinklist.unconfirmed{/lang}</small>
                        <ul class="buttonList iconList jsOnly">
                            {if $twink->isEditable()}
                            <li><a class="pointer jsConfirmButton" data-char-id="{$twink->characterID}" data-user-id="{$mainChar->userID}" data-tooltip="{lang}wcf.page.gman.twinklist.confirm{/lang}"><span class="icon icon16 fa-check-square"></span> <span class="invisible">{lang}wcf.page.gman.twinklist.confirmnow{/lang}</span></a></li>
                            {/if}
                        </ul>
                    </nav>
                    {/if}
                    <dl class="plain inlineDataList small">
                        {foreach from=$twink->getGroups() item=group}
                            <dt><a href="{$group->getLink()}," class="" data-tooltip="{lang}wcf.page.gman.twinklist.groupdesc{/lang}">{lang}wcf.page.gman.twinklist.group{/lang}</a></dt>
                            <dd>{$group->name}</dd>
                        {/foreach}
                    </dl>
                </div>
            </div>
        </li>
        {/foreach}
    </ol>
</section>
{/if}

