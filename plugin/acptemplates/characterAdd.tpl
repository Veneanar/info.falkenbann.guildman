{include file='header' pageTitle='{lang}wcf.acp.menu.link.gman.charadd{/lang}'}
<header class="contentHeader">
	<div class="contentHeaderTitle">
	  <h1 class="contentTitle">{lang}wcf.acp.menu.link.gman.charadd{/lang}</h1>
	</div>
</header>

{include file='formError'}
    {if $success|isset}
        <p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
    {/if}
<form id="characterAddForm" method="post" action="{link controller='characterAdd'}{/link}" enctype="multipart/form-data">

    <div class="section">
        <header class="sectionHeader">
            <h2 class="sectionTitle">{lang}wcf.acp.gman.guild.charadd{/lang}</h2>
            <p class="sectionDescription">{lang}wcf.acp.gman.guild.charadd.desc{/lang}</p>
        </header>
        <dl {if $errorField=='realmID'} class="formError" {/if}>
        <dt><label for="realmID">{lang}wcf.acp.gman.guild.realm{/lang}</label></dt>
        <dd>
            <select name="realmID" id="realmID" class="medium">
                <option value="0">{lang}wcf.global.noSelection{/lang}</option>

                {foreach from=$realms item=$realm}
                <option value="{$realm->slug}" {if $realm->slug== $realmID} selected{/if}>{$realm->name}</option>
                {/foreach}
            </select>
            <small>{lang}wcf.acp.gman.guild.realm.desc{/lang}</small>
            {if $errorField == 'realmID'}
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
        <dl {if $errorField=='charName'} class="formError" {/if}>
        <dt><label for="charName">{lang}wcf.acp.gman.guild.charname{/lang}</label></dt>
        <dd>
            <input name="charName" type="text" id="charName" value="{$charName}" class="medium" pattern="{literal}.{4,12}{/literal}" required>
            <small>{lang}wcf.acp.gman.guild.charname.desc{/lang}</small>
            {if $errorField == 'charName'}
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

    </div>

    <div class="formSubmit">
        <input type="hidden">
        <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
        {@SECURITY_TOKEN_INPUT_TAG}
    </div>
</form>

{include file='footer'}
