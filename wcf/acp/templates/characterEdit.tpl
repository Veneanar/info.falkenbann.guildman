{include file='header' pageTitle='{lang}wcf.acp.menu.link.gman.charedit{/lang}'}
<header class="contentHeader">
	<div class="contentHeaderTitle">
	  <h1 class="contentTitle">{lang}wcf.acp.menu.link.gman.charedit{/lang}</h1>
	</div>
</header>

{include file='formError'}
    {if $success|isset}
        <p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
    {/if}
    {if $failed|isset}
        <p class="error">{$failed}</p>
    {/if}
<script data-relocate="true">
    require(['WoltLabSuite/Core/Ui/User/Search/Input'], function(UiUserSearchInput) {
        new UiUserSearchInput(elBySel('input[name="ownerName"]'));
    });
    require(['WoltLabSuite/GMan/Character/SetMain'], function (SetMain) {
        new SetMain(document.getElementsByClassName('jsPromoteMain'));
    });
    require(['WoltLabSuite/GMan/Character/SetUser'], function (SetCharToUser) {
        new SetCharToUser(document.getElementsByClassName('jsConfirmButton'));
    });
</script>

<form id="guildEditForm" method="post" action="{link controller='characterEdit' object=$charObject}{/link}" enctype="multipart/form-data">

    <div class="section">
        <header class="sectionHeader">
            {if $charObject->userID>0}
            <h2 class="sectionTitle">{lang}wcf.acp.gman.guild.charedit{/lang}</h2>
            <p class="sectionDescription">{lang}wcf.acp.gman.guild.charedit.desc{/lang}</p>
            {else}
            <h2 class="sectionTitle">{lang}wcf.acp.gman.guild.charbind{/lang}</h2>
            <p class="sectionDescription">{lang}wcf.acp.gman.guild.charbind.desc{/lang}</p>
            {/if}
        </header>
        <dl>
            <dt>{lang}wcf.page.gman.charedit.chartoedit{/lang}</dt>
            <dd>
                <div class="box48">
                    <div>{@$charObject->getAvatar()->getImageTag(48)}</div>
                    <div class="details userInformation">
                        <div class="containerHeadline">
                            <h3>{$charObject->name}</h3>
                            <p>
                                <small class="separatorLeft"><span>{$charObject->getLevel()}</span>, <span>{@$charObject->getRace()->getTag()}</span>, {@$charObject->getClass()->getTag()}</small>
                            </p>
                        </div>
                    </div>
                </div>
                <small><b>{$charObject->getRank()}</b></small>
            </dd>
        </dl>  
        {if $charObject->userID>0 && $charObject->isMain==0}
        <dl>
            <dt><label for="syncGuild">{lang}wcf.page.gman.charedit.promotemain{/lang}</label></dt>
            <dd>
                <p class="button jsPromoteMain" name="PromoteMain" id="PromoteMain" data-char-id="{$charObject->characterID}">{lang}wcf.page.gman.charedit.promotemain{/lang}</p>
                <small>{lang}wcf.acp.gman.guild.charedit.promotemain.desc{/lang}</small>
            </dd>
        </dl>
        {/if}
        <dl {if $errorField=='ownerName'} class="formError" {/if}>
        <dt><label for="ownerName">{lang}wcf.acp.gman.guild.username{/lang}</label></dt>
        <dd>
            <input name="ownerName" type="text" id="ownerName" value="{$ownerName}" class="medium">
            <small>{lang}wcf.acp.gman.guild.username.desc{/lang}</small>
            {if $errorField == 'charName'}
            <small class="innerError">
                {if $errorType =='empty'}
                {lang}wcf.global.form.error.empty{/lang}
                {else}
                {lang}wcf.acp.gman.owneradd.error.{@$errorType}{/lang}
                {/if}
            </small>
            {/if}
        </dd>
        </dl>

        {if $guildGroups|count}
        <dl {if $errorField=='groupField'} class="formError" {/if}>
        <dt><label for="realmID">{lang}wcf.acp.gman.guild.groups{/lang}</label></dt>
        <dd>
            <fieldset>
                <ul>
                {foreach from=$guildGroups item=$groups}
                    <li>
                        <label>
                            <input type="checkbox" name="groupField[]" value="{$groups->groupID}" {if $groups->groupID|in_array:$charObject->getGroupIDs()} checked{/if}>
                            {$groups->groupName}
                        </label>
                    </li>
                {/foreach}
                </ul>
                {if $errorField == 'groupField'}
                <small class="innerError">
                    {if $errorType =='empty'}
                    {lang}wcf.global.form.error.empty{/lang}
                    {else}
                    {lang}wcf.acp.gman.groupfield.error.{@$errorType}{/lang}
                    {/if}
                </small>
                {/if}
            </fieldset> 
           <small>{lang}wcf.acp.gman.guild.primarygroup.desc{/lang}</small>
            {if $errorField == 'primaryGroupID'}
            <small class="innerError">
                {if $errorType == 'empty'}
                {lang}wcf.global.form.error.empty{/lang}
                {else}
                {lang}wcf.acp.gman.charadd.error.{@$errorType}{/lang}
                {/if}
            </small>
            {/if}
        </dd>
        </dl>
        {/if}
    </div>
    <div class="formSubmit">
        <input type="hidden">
        <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
        {@SECURITY_TOKEN_INPUT_TAG}
    </div>
    {include file='charList48'}
</form>

{include file='footer'}
