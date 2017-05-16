{include file='userMenuSidebar'}

{include file='header' __disableAds=true}

{include file='formError'}

{if $success|isset}
<p class="success">{lang}wcf.global.success.edit{/lang}</p>
{/if}

{if $charObject|isset && $charObject->userID > 0}
<section class="section">
    <header class="sectionHeader">
        <h2 class="sectionTitle">{lang}wcf.user.char.text.head{/lang}</h2>
        <p class="sectionDescription">{lang}wcf.user.char.text.head.description{/lang}</p>
    </header>
    <dl>

        <dd>
            <div class="box96">
                <div>{@$charObject->getInset()->getImageTag()}</div>
                <div class="details userInformation">
                    <div class="containerHeadline">
                        <h3>{$charObject->name}</h3>
                        <p>
                            <small class="separatorLeft"><span>{$charObject->getLevel()}</span>, <span>{@$charObject->getRace()->getTag()}</span>, {@$charObject->getClass()->getTag()}</small>
                        </p>
                        <p>
                            <small><b>{$charObject->getRank()}</b></small>
                        </p>
                        <p>
                            <small>
                                {lang}wcf.page.gman.char.group{/lang}: <br />
                                {foreach from=$charObject->getGroups() item=group}
                                <a href="{link controller='GuildGroupEdit' id=$group->groupID}{/link}," class="jsTooltip" title="{$group->groupTeaser}">{$group->groupName}</a> (<a class="jsTooltip" title="{lang}wcf.acp.group.showMembers{/lang}" href="{link controller='CharacterList'}groupID={@$group->groupID}{/link}">{$group->getMemberList()|count}</a>)
                                {/foreach}
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        </dd>
    </dl>
</section>
{/if}
<form method="post" action="{link controller='userStory' object=$charObject}{/link}">
    <section class="section">
        <h2 class="sectionTitle">{lang}wcf.user.char.text.current{/lang}</h2>

        <div class="htmlContent">{@$charObject->getChartext()}</div>
    </section>
    <section class="section" id="signatureContainer">
        <h2 class="sectionTitle">{lang}wcf.user.char.text.new{/lang}</h2>

        <dl class="wide{if $errorField == 'text'} formError{/if}">
            <dt><label for="text">{lang}wcf.user.char.text.new{/lang}</label></dt>
            <dd>
                <textarea id="text" class="wysiwygTextarea" name="text" rows="20" cols="40"
                          data-disable-attachments="false"
                          data-disable-media="false">
                    {$text}
                </textarea>
                {if $errorField == 'text'}
                <small class="innerError">
                    {if $errorType == 'empty'}
                    {lang}wcf.global.form.error.empty{/lang}
                    {elseif $errorType == 'tooLong'}
                    {lang}wcf.message.error.tooLong{/lang}
                    {elseif $errorType == 'censoredWordsFound'}
                    {lang}wcf.message.error.censoredWordsFound{/lang}
                    {elseif $errorType == 'disallowedBBCodes'}
                    {lang}wcf.message.error.disallowedBBCodes{/lang}
                    {else}
                    {lang}wcf.user.signature.error.{@$errorType}{/lang}
                    {/if}
                </small>
                {/if}
            </dd>
        </dl>

        {event name='fields'}
    </section>

    {event name='sections'}
    {include file='messageFormTabs'}

    <div class="formSubmit">
        <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
        {@SECURITY_TOKEN_INPUT_TAG}
    </div>
</form>

{include file='wysiwyg'}
{include file='footer' __disableAds=true}
