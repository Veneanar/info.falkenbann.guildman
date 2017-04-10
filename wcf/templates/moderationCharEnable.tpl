<article class=""
         data-object-id="{@$char->characterID}" data-post-id="{@$char->postID}" data-can-edit="0" 
         data-is-disabled="{if $char->isDisabled}1{else}0{/if}"
         data-can-delete="0" data-can-delete-completely="0" data-can-enable="0" data-can-restore="0"
>
    {assign var=user value=$char->getQueuedOwner(true)}
    <div class="box48">
        <a href="{link controller='User' object=$user}{/link}" title="{$user->username}">{@$user->getAvatar()->getImageTag(48)}</a>

        <div class="details userInformation">
            {include file='userInformation'}
        </div>
    </div>
        <div class="box24 boxMenuLink">
            <a href="{link controller='ArmoryChar' object=$char}{/link}" title="{$char->name}">{@$char->getAvatar()->getImageTag(24)} {$char->name}</a>
            <div class="details userInformation">
                <small>{@$char->getNiceTag(true, true)}</small>
            </div>
        </div>
</article>