<li data-object-id="{$char->characterID}">
    <div class="box24 boxMenuLink">
        <a href="{link controller='ArmoryChar' object=$char}{/link}" title="{$char->name}">{@$char->getAvatar()->getImageTag(24)} {$char->name}</a>

        <div class="details userInformation">
            <small>{@$char->getNiceTag(true, true)}</small>
        </div>
    </div>
</li>