<script data-relocate="true">
    require(['WoltLabSuite/GMan/Ui/Character/Validation/Input'], function (UiCharacterValidInput) {
        var validator = new UiCharacterValidInput(document.getElementById('charNamebnet'), document.getElementById('realmID'), document.getElementById('sendCharUpdate'));
        validator._validate(document.getElementById('charNamebnet').value)
    });
    require(['WoltLabSuite/GMan/Character/Create'], function (CreateChar) {
        new CreateChar([document.getElementById('sendCharUpdate')], {if $addUser|isset && $addUser|count}{$addUser}{/if} );
    });
</script>
    <div class="section">
        <header class="sectionHeader">
            <h2 class="sectionTitle">{lang}wcf.acp.gman.guild.charadd{/lang}</h2>
            <p class="sectionDescription">{lang}wcf.acp.gman.guild.charadd.desc{/lang}</p>
        </header>
        <dl>
        <dt><label for="realmID">{lang}wcf.acp.gman.guild.realm{/lang}</label></dt>
        <dd>
            <select name="realmID" id="realmID" class="medium">
                <option value="0">{lang}wcf.global.noSelection{/lang}</option>

                {foreach from=$realms item=$realm}
                <option value="{$realm->slug}" {if $realm->slug== $realmID} selected{/if}>{$realm->name}</option>
                {/foreach}
            </select>
            <small>{lang}wcf.acp.gman.guild.realm.desc{/lang}</small>
        </dd>
        </dl>
        <dl>
        <dt><label for="charNamebnet">{lang}wcf.acp.gman.guild.charname{/lang}</label></dt>
        <dd>
            <input name="charNamebnet" type="text" id="charNamebnet" value="{$charName}" data-realmslug="" class="medium" pattern="{literal}.{4,12}{/literal}" required>
            <small>{lang}wcf.acp.gman.guild.charname.desc{/lang}</small>
        </dd>
        </dl>
    </div>
    <div class="formSubmit">
        <input type="hidden">
        <input type="button" id="sendCharUpdate" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" disabled="disabled">
        {@SECURITY_TOKEN_INPUT_TAG}
    </div>
