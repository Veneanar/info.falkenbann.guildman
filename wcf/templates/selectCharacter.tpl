{include file='header' pageTitle='wcf.acp.gman.character.list' __disableAds=true}
<script data-relocate="true">
            require(['WoltLabSuite/GMan/Character/SetMain'], function (SetMain) {
                new SetMain(document.getElementsByClassName('jsPromoteMain'));
            });
            require(['WoltLabSuite/GMan/Character/Update'], function (UpdateData) {
                new UpdateData(elByClass('jsUpdateButton'));
            });
</script>

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{$application->appTitle}</h1>
        <p class="contentDescription">
            {@$application->appDescription}
            {if $application->getArticle()!==null}
            <a href="{$application->getArticle()->getLink()}" target="_blank" class="">{lang}wcf.acp.gman.application.getArticle{/lang}</a>
            {/if}
        </p>
    </div>
</header>

{if $step < 2}
    <section class="section loginFormRegister">
        <h2 class="sectionTitle">{if $application->requireUser==0}{lang}wcf.acp.gman.application.step1.optional{/lang}{else}{lang}wcf.acp.gman.application.step1{/lang}{/if}: {lang}wcf.user.login.register{/lang}</h2>
        {lang}wcf.acp.gman.application.step1.description{/lang}
        <div class="userLoginButtons">
            <!-- Funktioniert: -->
            <a href="{link controller='Login'}url={link controller='SelectCharacter' object=$application}{/link}{/link}" class="button loginFormRegisterButton">{lang}wcf.user.login.login{/lang}</a>
            <!-- Funktioniert nicht: -->
            <a href="{link controller='Register'}url={link controller='SelectCharacter' object=$application}{/link}{/link}" target="_blank" class="button loginFormRegisterButton">{lang}wcf.user.login.register.registerNow{/lang}</a>

            <a href="{link controller='SelectCharacter' object=$application}step=2{/link}" target="_blank" class="button">{lang}wcf.acp.gman.application.ignorelogin{/lang}</a>

        </div>
    </section>
    {hascontent}
    <section class="section loginFormThirdPartyLogin">
        <h2 class="sectionTitle">{lang}wcf.user.login.3rdParty{/lang}</h2>
        <dl>
            <dt></dt>
            <dd>
                <ul class="buttonList smallButtons">
                    {content}
                    {if GITHUB_PUBLIC_KEY !== '' && GITHUB_PRIVATE_KEY !== ''}
                    <li id="githubAuth" class="thirdPartyLogin">
                        <a href="{link controller='GithubAuth'}{/link}" class="button thirdPartyLoginButton githubLoginButton"><span class="icon icon16 fa-github"></span> <span>{lang}wcf.user.3rdparty.github.login{/lang}</span></a>
                    </li>
                    {/if}

                    {if TWITTER_PUBLIC_KEY !== '' && TWITTER_PRIVATE_KEY !== ''}
                    <li id="twitterAuth" class="thirdPartyLogin">
                        <a href="{link controller='TwitterAuth'}{/link}" class="button thirdPartyLoginButton twitterLoginButton"><span class="icon icon16 fa-twitter"></span> <span>{lang}wcf.user.3rdparty.twitter.login{/lang}</span></a>
                    </li>
                    {/if}

                    {if FACEBOOK_PUBLIC_KEY !== '' && FACEBOOK_PRIVATE_KEY !== ''}
                    <li id="facebookAuth" class="thirdPartyLogin">
                        <a href="{link controller='FacebookAuth'}{/link}" class="button thirdPartyLoginButton facebookLoginButton"><span class="icon icon16 fa-facebook"></span> <span>{lang}wcf.user.3rdparty.facebook.login{/lang}</span></a>
                    </li>
                    {/if}

                    {if GOOGLE_PUBLIC_KEY !== '' && GOOGLE_PRIVATE_KEY !== ''}
                    <li id="googleAuth" class="thirdPartyLogin">
                        <a href="{link controller='GoogleAuth'}{/link}" class="button thirdPartyLoginButton googleLoginButton"><span class="icon icon16 fa-google-plus"></span> <span>{lang}wcf.user.3rdparty.google.login{/lang}</span></a>
                    </li>
                    {/if}

                    {event name='3rdpartyButtons'}
                    {/content}
                </ul>
            </dd>
        </dl>
    </section>
    {/hascontent}

{else}
    {include file='formError'}
     <section class="section sectionContainerList">
         <h2 class="sectionTitle">{lang}wcf.acp.gman.application.step2{/lang}</h2>
         {lang}wcf.acp.gman.application.step2.description{/lang}
                <ol class="containerList userList" id="characterList">
                    {foreach from=$chars item=$char}
                        {include file="_charSelection"}
                    {/foreach}
                </ol>
            </section>
        {assign var=addUser value=$userID}
    <script data-relocate="true">
        require(['WoltLabSuite/GMan/Ui/Character/Validation/Input'], function (UiCharacterValidInput) {
            var validator = new UiCharacterValidInput(document.getElementById('charNamebnet'), document.getElementById('realmID'), document.getElementById('sendCharUpdate'));
            validator._validate(document.getElementById('charNamebnet').value)
        });
        require(['WoltLabSuite/GMan/Character/CreateAndAdd'], function (CreateCharAndAdd) {
            new CreateCharAndAdd([document.getElementById('sendCharUpdate')], {$userID}, document.getElementById('characterList'), {$application->appID});
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
                <input name="charNamebnet" type="text" id="charNamebnet" value="" data-realmslug="" class="medium" pattern="{literal}.{4,12}{/literal}" required>
                <small>{lang}wcf.acp.gman.guild.charname.desc{/lang}</small>
            </dd>
        </dl>
    </div>
    <div class="formSubmit">
        <input type="hidden">
        <input type="button" id="sendCharUpdate" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" disabled="disabled">
        {@SECURITY_TOKEN_INPUT_TAG}
    </div>
{/if}

{include file='footer'}

