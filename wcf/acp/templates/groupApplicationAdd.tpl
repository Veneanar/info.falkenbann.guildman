{capture assign='stylesheets'}
<style>
.liMoveable:first-child .upbutton {
    display: none;
}
.liMoveable:last-child .downbutton {
    display: none;
}
</style>
{/capture}
{include file='header' pageTitle='wcf.acp.gman.app.'|concat:$action}

{include file='aclPermissions'}
<script data-relocate="true">
    $(document.body).on('click', '.upbutton', function () {
        console.log("click uP");
        var hook = $(this).closest('.liMoveable').prev('.liMoveable');
        var order = $(this).closest('.liMoveable').find('.order');
        console.log("oder val (old): " + order.val());
        order.val(order.val() - 1);
        console.log("oder val (new): " + order.val());
        var elementToMove = $(this).closest('.liMoveable').detach();
        hook.before(elementToMove);
    });
    $(document.body).on('click', '.downbutton', function () {
        var hook = $(this).closest('.liMoveable').next('.liMoveable');
        var order = $(this).closest('.liMoveable').find('.order');
        order.val(order.val() + 1);
        var elementToMove = $(this).closest('.liMoveable').detach();
        hook.after(elementToMove);
    });
    require(['WoltLabSuite/GMan/Ui/Ul/UladdElement'], function (UladdElement) {
        new UladdElement(document.getElementById('fieldAdd'), document.getElementById('avaibleFieldList'), document.getElementById('fieldList'), {
            ajax: {
                className: 'wcf\\data\\guild\\group\\application\\field\\ApplicationFieldAction',
            }
        })
    });
</script>

{include file='multipleLanguageInputJavascript' elementIdentifier='description' forceSelection=false}
{include file='multipleLanguageInputJavascript' elementIdentifier='title' forceSelection=false}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.gman.app.{$action}{/lang}</h1>
        {if $action == 'edit'}<p class="contentHeaderDescription">{$application->title|language}</p>{/if}
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
        <li><a href="{link controller='GroupApplicationList'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}wcf.acp.menu.link.gman.app{/lang}</span></a></li>
        {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{if $application|isset}
{$applicationObject|var_dump}
{/if}

{if $appID|isset}
	{include file='aclPermissionJavaScript' containerID='userPermissionsContainer' categoryName='user.*' objectID=$applicationObject->appID}
	{include file='aclPermissionJavaScript' containerID='moderatorPermissionsContainer' categoryName='mod.*' objectID=$applicationObject->appID}
{else}
	{include file='aclPermissionJavaScript' containerID='userPermissionsContainer' categoryName='user.*'}
	{include file='aclPermissionJavaScript' containerID='moderatorPermissionsContainer' categoryName='mod.*'}
{/if}

{include file='formError'}

{if $success|isset}
<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<form method="post" action="{if $action == 'add'}{link controller='GroupApplicationAdd'}{/link}{else}{link controller='GroupApplicationEdit' object=$applicationObject}{/link}{/if}">
    <div class="section tabMenuContainer" data-active="{$activeTabMenuItem}" data-store="activeTabMenuItem">
        <nav class="tabMenu">
            <ul>
                <li><a href="{@$__wcf->getAnchor('general')}">{lang}wcf.acp.gman.app.general{/lang}</a></li>
                <li><a href="{@$__wcf->getAnchor('fields')}">{lang}wcf.acp.gman.app.fields{/lang}</a></li>
                <li><a href="{@$__wcf->getAnchor('actions')}">{lang}wcf.acp.gman.app.actions{/lang}</a></li>
                <li><a href="{@$__wcf->getAnchor('userPermissions')}">{lang}wcf.acp.gman.app.userPermissions{/lang}</a></li>
                <li><a href="{@$__wcf->getAnchor('moderatorPermissions')}">{lang}wcf.acp.gman.app.moderatorPermissions{/lang}</a></li>
                {event name='tabMenuTabs'}
            </ul>
        </nav>

        <div id="general" class="tabMenuContent">

            <section class="section">
                <dl{if $errorField=='title'} class="formError" {/if}>
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
                        {lang}wcf.acp.board.title.error.{@$errorType}{/lang}
                        {/if}
                    </small>
                    {/if}
                </dd>
                </dl>

                <dl{if $errorField=='description'} class="formError" {/if}>
                <dt><label for="description">{lang}wcf.global.description{/lang}</label></dt>
                <dd>
                    <textarea id="description" name="description" cols="40" rows="10">{$i18nPlainValues[description]}</textarea>
                    {if $errorField == 'description'}
                    <small class="innerError">
                        {if $errorType == 'empty'}
                        {lang}wcf.global.form.error.empty{/lang}
                        {else}
                        {lang}wcf.acp.board.description.error.{@$errorType}{/lang}
                        {/if}
                    </small>
                    {/if}
                </dd>
                </dl>
                <dl{if $errorField=='appGroupID'} class="formError" {/if}>
                <dt><label for="appGroupID">{lang}wcf.acp.gman.app.group{/lang}</label></dt>
                <dd>
                    <select name="appGroupID" id="appGroupID" class="medium">
                        <option value="0">{lang}wcf.global.noSelection{/lang}</option>

                        {foreach from=$groupList item=$group}
                        <option value="{$group->groupID}" {if $group->groupID == $appGroupID} selected{/if}>{$group->getTitle()}</option>
                        {/foreach}
                    </select>
                    <small>{lang}wcf.acp.gman.app.group.description{/lang}</small>
                    {if $errorField == 'appGroupID'}
                    <small class="innerError">
                        {if $errorType == 'empty'}
                        {lang}wcf.global.form.error.empty{/lang}
                        {else}
                        {lang}wcf.acp.app.group.error.{@$errorType}{/lang}
                        {/if}
                    </small>
                    {/if}
                </dd>
                </dl>

                {event name='dataFields'}
            </section>
            <section class="section">
                <h2 class="sectionTitle">{lang}wcf.acp.gman.app.details{/lang}</h2>
                <dl{if $errorField=='appArticleID'} class="formError" {/if}>
                <dt><label for="appArticleID">{lang}wcf.acp.gman.app.article{/lang}</label></dt>
                <dd>
                    <select name="appArticleID" id="appArticleID" class="medium">
                        <option value="0">{lang}wcf.global.noSelection{/lang}</option>

                        {foreach from=$articleList item=$article}
                        <option value="{$article->articleID}" {if $article->articleID == $appArticleID} selected{/if}>{$article->getTitle()}</option>
                        {/foreach}
                    </select>
                    <small>{lang}wcf.acp.gman.app.article.description{/lang}</small>
                    {if $errorField == 'appArticleID'}
                    <small class="innerError">
                        {if $errorType == 'empty'}
                        {lang}wcf.global.form.error.empty{/lang}
                        {else}
                        {lang}wcf.acp.article.category.error.{@$errorType}{/lang}
                        {/if}
                    </small>
                    {/if}
                </dd>
                </dl>
                <dl{if $errorField=='appForumID'} class="formError" {/if}>
                <dt><label for="appForumID">{lang}wcf.acp.gman.app.board{/lang}</label></dt>
                <dd>
                    <select name="appForumID" id="appForumID" class="medium">
                        <option value="0">{lang}wcf.global.noSelection{/lang}</option>

                        {foreach from=$boardList item=$board}
                        <option value="{$board->boardID}" {if $board->boardID == $appForumID} selected{/if}>{$board->getTitle()}</option>
                        {/foreach}
                    </select>
                    <small>{lang}wcf.acp.gman.app.board.description{/lang}</small>
                    {if $errorField == 'boardID'}
                    <small class="innerError">
                        {if $errorType == 'empty'}
                        {lang}wcf.global.form.error.empty{/lang}
                        {else}
                        {lang}wcf.acp.gman.app.bord.error.{@$errorType}{/lang}
                        {/if}
                    </small>
                    {/if}
                </dd>
                </dl>
                {event name='positionFields'}
            </section>

            <section class="section" id="propertiesContainer">
                <h2 class="sectionTitle">{lang}wcf.acp.app.properties{/lang}</h2>
                <dl>
                    <dd>
                        <label><input type="checkbox" id="isActive" name="isActive" value="1" {if $isActive} checked{/if}> {lang}wcf.acp.app.isactive{/lang}</label>
                        <label><input type="checkbox" id="hasPoll" name="hasPoll" value="1" {if $hasPoll} checked{/if}> {lang}wcf.acp.app.hasPoll{/lang}</label>
                        <label><input type="checkbox" id="isCommentable" name="isCommentable" value="1" {if $isCommentable} checked{/if}> {lang}wcf.acp.app.isCommentable{/lang}</label>

                        {event name='properties'}
                    </dd>
                </dl>
            </section>
            {event name='sections'}
        </div>
        <div id="fields" class="tabMenuContent">
            <section class="section">
                <h2 class="sectionTitle">{lang}wcf.acp.gman.app.fieldAdd{/lang}</h2>
                <dl>
                    <dt><label for="avaibleFieldList">{lang}wcf.gman.app.field.select{/lang}</label></dt>
                    <dd>
                        <select name="avaibleFieldList" id="avaibleFieldList" class="medium">
                            {foreach from=$avaibleFieldList item=$field}
                                <option value="{$field->fieldID}">{$field->getTitle()}</option>
                            {/foreach}
                        </select>
                    </dd>
                </dl>
                <dl>
                     <dt></dt>
                     <dd><p class="button jsFieldAdd" id="fieldAdd" name="fieldAdd">{lang}wcf.acp.gman.app.field.add{/lang} </p></dd>
               </dl>
            </section>
            <section class="section">
                <h2 class="sectionTitle">{lang}wcf.acp.gman.app.fieldList{/lang}</h2>
                <ol class="containerList userList" id="fieldList">
                {foreach from=$applicationFieldList item=field}
                    {include file="_applicationField"}
                {/foreach}
                </ol>

            </section>

        </div>

        <div id="actions" class="tabMenuContent">
            <div class="section">
                <dl id="appActionsContainer">
                    <dt>appActions</dt>
                    <dd></dd>
                </dl>
            </div>
        </div>

        <div id="userPermissions" class="tabMenuContent">
            <div class="section">
                <dl id="userPermissionsContainer">
                    <dt>{lang}wcf.acl.user.permissions{/lang}</dt>
                    <dd></dd>
                </dl>
            </div>
        </div>

        <div id="moderatorPermissions" class="tabMenuContent">
            <div class="section">
                <dl id="moderatorPermissionsContainer">
                    <dt>{lang}wcf.acl.mod.permissions{/lang}</dt>
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
