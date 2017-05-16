<li class="jsFollowing" id="wcf3">
    <div class="box48">
        <a href="{$char->getLink()}" title="{$char->getTitle()}">{@$char->getAvatar()->getImageTag(48)}</a>
        <div class="details userInformation">
            <div class="containerHeadline">
                <h3>
                    <a href="{$char->getLink()}" class="username userLink" data-char-id="{$char->characterID}">{$char->name}</a> <span class="badge userTitleBadge green">{$char->getRank()}</span>
                </h3>
            </div>
            <ul class="inlineList">
                <li>{$char->getLevel()}</li>
                <li>{@$char->getRace()->getTag()}</li>
                <li>{@$char->getClass()->getTag()}</li>
            </ul>
            <nav class="jsMobileNavigation buttonGroupNavigation" style="opacity: 1" data-char-id="{$char->characterID}">
                <ul class="buttonList iconList jsOnly">
                    <li>
                        <a class="jsTooltip pointer" title="{lang}wcf.acp.gman.character.select{/lang}" href="{link controller='guildGroupApplication' object=$application}charID={$char->characterID}{/link}">
                            <span class="icon icon32 fa-check-circle"></span>
                            <span class="invisible">{lang}wcf.acp.gman.character.select{/lang}</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</li>