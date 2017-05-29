<dl class="wide{if $errorField == 'fieldSelectSecondaryRole'} formError{/if}">
    <dt><label for="fieldSelectSecondaryRole">{lang}wcf.acp.gman.application.field.fieldSelectSecondaryRole{/lang}</label></dt>
    <dd>

        <select name="fieldSelectSecondaryRole" class="medium">
            <option value="1" {if $fieldSelectSecondaryRole==1}selected{/if}>{lang}wcf.gman.app.field.fieldSelectSecondaryRole.tank{/lang}</option>
            <option value="2" {if $fieldSelectSecondaryRole==2}selected{/if}>{lang}wcf.gman.app.field.fieldSelectSecondaryRole.healer{/lang}</option>
            <option value="3" {if $fieldSelectSecondaryRole==3}selected{/if}>{lang}wcf.gman.app.field.fieldSelectSecondaryRole.melee{/lang}</option>
            <option value="4" {if $fieldSelectSecondaryRole==4}selected{/if}>{lang}wcf.gman.app.field.fieldSelectSecondaryRole.range{/lang}</option>

        </select>
        {if $errorField == 'fieldSelectSecondaryRole'}
        <small class="innerError">
            {if $errorType == 'empty'}
            {lang}wcf.global.form.error.empty{/lang}
            {else}
            {lang}wcf.acp.gman.application.field.fieldSelectSecondaryRole.error.{@$errorType}{/lang}
            {/if}
        </small>
        {/if}
        <small>{lang}wcf.acp.gman.application.field.fieldSelectSecondaryRole.description{/lang}</small>
    </dd>
</dl>

<dl>
    <dt>{lang}wcf.acp.gman.application.field.fieldBnet{/lang}</dt>
    <dd>
        <p>{$fieldBnet}</p>
        <small>{lang}wcf.acp.gman.application.field.fieldBnet.description{/lang}</small>
    </dd>
</dl>


<dl{if $errorField == 'fieldBnet'} class="formError"{/if}>
	<dt><label for="fieldBnet">{lang}wcf.acp.gman.application.field.fieldBnet{/lang}</label></dt>
    <dd>
        <input type="text" id="fieldBnet" name="fieldBnet" value="{$fieldBnet}" class="small" {literal} pattern="/^\D.{2,11}#\d{4,6}$/" {/literal}>
        {if $errorField == 'fieldBnet'}
        <small class="innerError">
            {if $errorType == 'empty'}
            {lang}wcf.global.form.error.empty{/lang}
            {else}
            {lang}wcf.acp.gman.application.field.fieldBnet.error.{@$errorType}{/lang}
            {/if}
        </small>
        {/if}
        <small>{lang}wcf.acp.gman.application.field.fieldBnet.description{/lang}</small>
    </dd>
</dl>

<dl>
    <dt>{lang}wcf.acp.gman.application.field. acceptGuildTerms{/lang}</dt>
    <dd>
        <p>{if $ acceptGuildTerms==1}{lang}wcf.acp.option.type.boolean.yes{/lang}{else}{lang}wcf.acp.option.type.boolean.yes{/lang}{/if}</p>
        <small>{lang}wcf.acp.gman.application.field. acceptGuildTerms.description{/lang}</small>
    </dd>
</dl>

<dl{if $errorField == 'acceptGuildTerms'} class="formError"{/if}>
    <dt>{lang}wcf.acp.gman.application.field.acceptGuildTerms{/lang}</dt>
    <dd>
        <label><input type="checkbox" id="acceptGuildTerms" name="acceptGuildTerms" value="1" {if $acceptGuildTerms==1} checked{/if}> {lang}wcf.acp.app.application.field.fieldOver16{/lang}</label>
        <small>{lang}wcf.acp.gman.application.field.acceptGuildTerms.description{/lang}</small>
        {if $errorField == 'fieldBnet'}
        <small class="innerError">
            {if $errorType == 'empty'}
            {lang}wcf.acp.gman.application.field.acceptGuildTerms.empty{/lang}
            {else}
            {lang}wcf.acp.gman.application.field.acceptGuildTerms.error.{@$errorType}{/lang}
            {/if}
        </small>
        {/if}
    </dd>
</dl>


<dl>
    <dt>{lang}wcf.acp.gman.application.field.fieldSelectPrimaryRole{/lang}</dt>
    <dd>
        <p>
            {if $fieldSelectSecondaryRole==1}{lang}wcf.gman.app.field.fieldSelectPrimaryRole.tank{/lang}{/if}>
            {if $fieldSelectSecondaryRole==2}{lang}wcf.gman.app.field.fieldSelectPrimaryRole.healer{/lang}{/if}>
            {if $fieldSelectSecondaryRole==3}{lang}wcf.gman.app.field.fieldSelectPrimaryRole.melee{/lang}{/if}>
            {if $fieldSelectSecondaryRole==4}{lang}wcf.gman.app.field.fieldSelectPrimaryRole.range{/lang}{/if}>}
        </p>
        <small>{lang}wcf.acp.gman.application.field.fieldSelectPrimaryRole.description{/lang}</small>
    </dd>
</dl>

