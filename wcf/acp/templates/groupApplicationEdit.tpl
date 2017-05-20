{include file='header' pageTitle='wcf.acp.gman.app.'|concat:$action}

{include file='aclPermissions'}
<script data-relocate="true">
</script>

{include file='multipleLanguageInputJavascript' elementIdentifier='description' forceSelection=false}
{include file='multipleLanguageInputJavascript' elementIdentifier='title' forceSelection=false}

{if $appID|isset}
	{include file='aclPermissionJavaScript' containerID='userPermissionsContainer' categoryName='user.*' objectID=$boardID}
	{include file='aclPermissionJavaScript' containerID='moderatorPermissionsContainer' categoryName='mod.*' objectID=$boardID}
{else}
	{include file='aclPermissionJavaScript' containerID='userPermissionsContainer' categoryName='user.*'}
	{include file='aclPermissionJavaScript' containerID='moderatorPermissionsContainer' categoryName='mod.*'}
{/if}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.gman.app.{$action}{/lang}</h1>
        {if $action == 'edit'}<p class="contentHeaderDescription">{$application->title|language}</p>{/if}
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
        <li><a href="{link controller='GroupApplicationList'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}wbb.acp.menu.link.gman.app{/lang}</span></a></li>
        {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{include file='formError'}

{if $success|isset}
<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<form method="post" action="{if $action == 'add'}{link controller='GroupApplicationAdd'}{/link}{else}{link controller='GroupApplicationEdit' id=$appID}{/link}{/if}">
    <div class="section tabMenuContainer" data-active="{$activeTabMenuItem}" data-store="activeTabMenuItem">
        <nav class="tabMenu">
            <ul>
                <li><a href="{@$__wcf->getAnchor('general')}">{lang}wcf.acp.gman.app.name{/lang}</a></li>
                <li><a href="{@$__wcf->getAnchor('fields')}">{lang}wcf.acp.gman.app.fields{/lang}</a></li>
                <li><a href="{@$__wcf->getAnchor('actions')}">{lang}wcf.acp.gman.app.actions{/lang}</a></li>
                <li><a href="{@$__wcf->getAnchor('userPermissions')}">{lang}wbb.acp.gman.app.userPermissions{/lang}</a></li>
                <li><a href="{@$__wcf->getAnchor('moderatorPermissions')}">{lang}wbb.acp.gman.app.moderatorPermissions{/lang}</a></li>
                {event name='tabMenuTabs'}
            </ul>
        </nav>

        <div id="general" class="tabMenuContent">
            <div class="section">
                <dl{if $errorField=='title' } class="formError" {/if}>
                <dt><label for="title">{lang}wcf.global.title{/lang}</label></dt>
                <dd>
                    <input type="text" id="title" name="title" value="{$i18nPlainValues['title']}" autofocus class="medium">
                    {if $errorField == 'title'}
                    <small class="innerError">
                        {if $errorType == 'empty'}
                        {lang}wcf.global.form.error.empty{/lang}
                        {elseif $errorType == 'multilingual'}
                        {lang}wcf.global.form.error.multilingual{/lang}
                        {else}
                        {lang}wbb.acp.board.title.error.{@$errorType}{/lang}
                        {/if}
                    </small>
                    {/if}
                </dd>
                </dl>

                <dl{if $errorField=='description'} class="formError" {/if}>
                <dt><label for="description">{lang}wcf.global.description{/lang}</label></dt>
                <dd>
                    <textarea id="description" name="description" cols="30" rows="8">{$i18nPlainValues[description]}</textarea>
                    {if $errorField == 'description'}
                    <small class="innerError">
                        {if $errorType == 'empty'}
                        {lang}wcf.global.form.error.empty{/lang}
                        {else}
                        {lang}wbb.acp.board.description.error.{@$errorType}{/lang}
                        {/if}
                    </small>
                    {/if}
                </dd>
                </dl>
                {event name='dataFields'}
            </div>
            <section class="section">
                <h2 class="sectionTitle">{lang}wbb.acp.board.category.position{/lang}</h2>
                {hascontent}
                <dl{if $errorField=='parentID' } class="formError" {/if}>
                <dt><label for="parentID">{lang}wbb.acp.board.parentID{/lang}</label></dt>
                <dd>
                    <select name="parentID" id="parentID">
                        <option value="0">{lang}wcf.global.noSelection{/lang}</option>
                        {content}
                        {foreach from=$boardNodeList item=boardNode}
                        {if !$boardNode->getBoard()->isExternalLink()}
                        <option value="{@$boardNode->getBoard()->boardID}" {if $boardNode->getBoard()->boardID == $parentID} selected{/if}>{if $boardNode->getDepth() > 1}{@"&nbsp;&nbsp;&nbsp;&nbsp;"|str_repeat:($boardNode->getDepth() - 1)}{/if}{$boardNode->getBoard()->title|language}</option>
                        {/if}
                        {/foreach}
                        {/content}
                    </select>
                    {if $errorField == 'parentID'}
                    <small class="innerError">
                        {lang}wbb.acp.board.parentID.error.{@$errorType}{/lang}
                    </small>
                    {/if}
                </dd>
                </dl>
                {/hascontent}
                {event name='positionFields'}
            </section>

            <section class="section" id="propertiesContainer">
                <h2 class="sectionTitle">{lang}wbb.acp.board.properties{/lang}</h2>
                <dl>
                    <dd>
                        <label><input type="checkbox" id="isClosed" name="isClosed" value="1" {if $isClosed} checked{/if}> {lang}wbb.acp.board.isClosed{/lang}</label>
                        {event name='properties'}
                    </dd>
                </dl>
            </section>
            {event name='sections'}
        </div>

        <div id="appFields" class="tabMenuContent">
            <div class="section">
                <dl id="appFieldsContainer">
                    <dt>appFields</dt>
                    <dd></dd>
                </dl>
            </div>
        </div>

        <div id="appActions" class="tabMenuContent">
            <div class="section">
                <dl id="appActionsContainer">
                    <dt>appActions</dt>
                    <dd></dd>
                </dl>
            </div>
        </div>

        <div id="moderatorPermissions" class="tabMenuContent">
            <div class="section">
                <dl id="moderatorPermissionsContainer">
                    <dt>{lang}wcf.acl.user.permissions{/lang}</dt>
                    <dd></dd>
                </dl>
            </div>
        </div>
        {event name='tabMenuContents'}
    </div>

    <div class="formSubmit">
        <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
        {@SECURITY_TOKEN_INPUT_TAG}
    </div>
</form>

{include file='footer'}
