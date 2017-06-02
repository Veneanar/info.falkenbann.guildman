<!DOCTYPE html>
<html dir="{@$__wcf->getLanguage()->getPageDirection()}" lang="{@$__wcf->getLanguage()->getFixedLanguageCode()}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">
	<title>{if $pageTitle|isset}{@$pageTitle|language} - {/if}{lang}wcf.global.acp{/lang}{if PACKAGE_ID} - {PAGE_TITLE|language}{/if}</title>
	
	<!-- Stylesheets -->
    <style>
        .liMoveable:first-child .upbutton {
            display: none;
        }

        .liMoveable:last-child .downbutton {
            display: none;
        }
    </style>
	<link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600" rel="stylesheet">
	{@$__wcf->getStyleHandler()->getStylesheet(true)}
	{event name='stylesheets'}
	
	<!-- Icons -->
	<link rel="shortcut icon" href="{@$__wcf->getPath()}images/favicon.ico">
	<link rel="apple-touch-icon" href="{@$__wcf->getPath()}images/apple-touch-icon.png">
	
	<script>
		var SID_ARG_2ND = '';
		var WCF_PATH = '{@$__wcf->getPath()}';
		var WSC_API_URL = '{@$__wcf->getActivePath()}acp/';
		var SECURITY_TOKEN = '{@SECURITY_TOKEN}';
		var LANGUAGE_ID = {@$__wcf->getLanguage()->languageID};
		var LANGUAGE_USE_INFORMAL_VARIANT = {if LANGUAGE_USE_INFORMAL_VARIANT}true{else}false{/if};
		var TIME_NOW = {@TIME_NOW};
		var URL_LEGACY_MODE = false;
	</script>
	
	{js application='wcf' file='require' bundle='WoltLabSuite.Core' core='true'}
	{js application='wcf' file='require.config' bundle='WoltLabSuite.Core' core='true'}
	{js application='wcf' file='require.linearExecution' bundle='WoltLabSuite.Core' core='true'}
	{js application='wcf' file='wcf.globalHelper' bundle='WoltLabSuite.Core' core='true'}
	{js application='wcf' file='closest' bundle='WoltLabSuite.Core' core='true'}
	<script>
		requirejs.config({
			baseUrl: '{@$__wcf->getPath()}js'
			{hascontent}
			, paths: {
				{content}{event name='requirePaths'}{/content}
			}
			{/hascontent}
		});
		{event name='requireConfig'}
	</script>
	<script>
		require(['Language', 'WoltLabSuite/Core/Acp/Bootstrap', 'User'], function(Language, AcpBootstrap, User) {
			Language.addObject({
				'__days': [ '{lang}wcf.date.day.sunday{/lang}', '{lang}wcf.date.day.monday{/lang}', '{lang}wcf.date.day.tuesday{/lang}', '{lang}wcf.date.day.wednesday{/lang}', '{lang}wcf.date.day.thursday{/lang}', '{lang}wcf.date.day.friday{/lang}', '{lang}wcf.date.day.saturday{/lang}' ],
				'__daysShort': [ '{lang}wcf.date.day.sun{/lang}', '{lang}wcf.date.day.mon{/lang}', '{lang}wcf.date.day.tue{/lang}', '{lang}wcf.date.day.wed{/lang}', '{lang}wcf.date.day.thu{/lang}', '{lang}wcf.date.day.fri{/lang}', '{lang}wcf.date.day.sat{/lang}' ],
				'__months': [ '{lang}wcf.date.month.january{/lang}', '{lang}wcf.date.month.february{/lang}', '{lang}wcf.date.month.march{/lang}', '{lang}wcf.date.month.april{/lang}', '{lang}wcf.date.month.may{/lang}', '{lang}wcf.date.month.june{/lang}', '{lang}wcf.date.month.july{/lang}', '{lang}wcf.date.month.august{/lang}', '{lang}wcf.date.month.september{/lang}', '{lang}wcf.date.month.october{/lang}', '{lang}wcf.date.month.november{/lang}', '{lang}wcf.date.month.december{/lang}' ], 
				'__monthsShort': [ '{lang}wcf.date.month.short.jan{/lang}', '{lang}wcf.date.month.short.feb{/lang}', '{lang}wcf.date.month.short.mar{/lang}', '{lang}wcf.date.month.short.apr{/lang}', '{lang}wcf.date.month.short.may{/lang}', '{lang}wcf.date.month.short.jun{/lang}', '{lang}wcf.date.month.short.jul{/lang}', '{lang}wcf.date.month.short.aug{/lang}', '{lang}wcf.date.month.short.sep{/lang}', '{lang}wcf.date.month.short.oct{/lang}', '{lang}wcf.date.month.short.nov{/lang}', '{lang}wcf.date.month.short.dec{/lang}' ],
				'wcf.acp.search.noResults': '{lang}wcf.acp.search.noResults{/lang}',
				'wcf.clipboard.item.unmarkAll': '{lang}wcf.clipboard.item.unmarkAll{/lang}',
				'wcf.date.relative.now': '{lang __literal=true}wcf.date.relative.now{/lang}',
				'wcf.date.relative.minutes': '{capture assign=relativeMinutes}{lang __literal=true}wcf.date.relative.minutes{/lang}{/capture}{@$relativeMinutes|encodeJS}',
				'wcf.date.relative.hours': '{capture assign=relativeHours}{lang __literal=true}wcf.date.relative.hours{/lang}{/capture}{@$relativeHours|encodeJS}',
				'wcf.date.relative.pastDays': '{capture assign=relativePastDays}{lang __literal=true}wcf.date.relative.pastDays{/lang}{/capture}{@$relativePastDays|encodeJS}',
				'wcf.date.dateFormat': '{lang}wcf.date.dateFormat{/lang}',
				'wcf.date.dateTimeFormat': '{lang}wcf.date.dateTimeFormat{/lang}',
				'wcf.date.shortDateTimeFormat': '{lang}wcf.date.shortDateTimeFormat{/lang}',
				'wcf.date.hour': '{lang}wcf.date.hour{/lang}',
				'wcf.date.minute': '{lang}wcf.date.minute{/lang}',
				'wcf.date.timeFormat': '{lang}wcf.date.timeFormat{/lang}',
				'wcf.date.firstDayOfTheWeek': '{lang}wcf.date.firstDayOfTheWeek{/lang}',
				'wcf.global.button.add': '{lang}wcf.global.button.add{/lang}',
				'wcf.global.button.cancel': '{lang}wcf.global.button.cancel{/lang}',
				'wcf.global.button.close': '{lang}wcf.global.button.close{/lang}',
				'wcf.global.button.collapsible': '{lang}wcf.global.button.collapsible{/lang}',
				'wcf.global.button.delete': '{lang}wcf.global.button.delete{/lang}',
				'wcf.global.button.disable': '{lang}wcf.global.button.disable{/lang}',
				'wcf.global.button.disabledI18n': '{lang}wcf.global.button.disabledI18n{/lang}',
				'wcf.global.button.edit': '{lang}wcf.global.button.edit{/lang}',
				'wcf.global.button.enable': '{lang}wcf.global.button.enable{/lang}',
				'wcf.global.button.hide': '{lang}wcf.global.button.hide{/lang}',
				'wcf.global.button.insert': '{lang}wcf.global.button.insert{/lang}',
				'wcf.global.button.next': '{lang}wcf.global.button.next{/lang}',
				'wcf.global.button.preview': '{lang}wcf.global.button.preview{/lang}',
				'wcf.global.button.reset': '{lang}wcf.global.button.reset{/lang}',
				'wcf.global.button.save': '{lang}wcf.global.button.save{/lang}',
				'wcf.global.button.search': '{lang}wcf.global.button.search{/lang}',
				'wcf.global.button.submit': '{lang}wcf.global.button.submit{/lang}',
				'wcf.global.button.upload': '{lang}wcf.global.button.upload{/lang}',
				'wcf.global.confirmation.cancel': '{lang}wcf.global.confirmation.cancel{/lang}',
				'wcf.global.confirmation.confirm': '{lang}wcf.global.confirmation.confirm{/lang}',
				'wcf.global.confirmation.title': '{lang}wcf.global.confirmation.title{/lang}',
				'wcf.global.decimalPoint': '{capture assign=decimalPoint}{lang}wcf.global.decimalPoint{/lang}{/capture}{$decimalPoint|encodeJS}',
				'wcf.global.error.timeout': '{lang}wcf.global.error.timeout{/lang}',
				'wcf.global.error.title': '{lang}wcf.global.error.title{/lang}',
				'wcf.global.form.error.empty': '{lang}wcf.global.form.error.empty{/lang}',
				'wcf.global.form.error.greaterThan': '{lang __literal=true}wcf.global.form.error.greaterThan{/lang}',
				'wcf.global.form.error.lessThan': '{lang __literal=true}wcf.global.form.error.lessThan{/lang}',
				'wcf.global.form.error.multilingual': '{lang}wcf.global.form.error.multilingual{/lang}',
				'wcf.global.loading': '{lang}wcf.global.loading{/lang}',
				'wcf.global.noSelection': '{lang}wcf.global.noSelection{/lang}',
				'wcf.global.select': '{lang}wcf.global.select{/lang}',
				'wcf.page.jumpTo': '{lang}wcf.page.jumpTo{/lang}',
				'wcf.page.jumpTo.description': '{lang}wcf.page.jumpTo.description{/lang}',
				'wcf.global.page.pagination': '{lang}wcf.global.page.pagination{/lang}',
				'wcf.global.page.next': '{capture assign=pageNext}{lang}wcf.global.page.next{/lang}{/capture}{@$pageNext|encodeJS}',
				'wcf.global.page.previous': '{capture assign=pagePrevious}{lang}wcf.global.page.previous{/lang}{/capture}{@$pagePrevious|encodeJS}',
				'wcf.global.pageDirection': '{lang}wcf.global.pageDirection{/lang}',
				'wcf.global.reason': '{lang}wcf.global.reason{/lang}',
				'wcf.global.scrollUp': '{lang}wcf.global.scrollUp{/lang}',
				'wcf.global.success': '{lang}wcf.global.success{/lang}',
				'wcf.global.success.add': '{lang}wcf.global.success.add{/lang}',
				'wcf.global.success.edit': '{lang}wcf.global.success.edit{/lang}',
				'wcf.global.thousandsSeparator': '{capture assign=thousandsSeparator}{lang}wcf.global.thousandsSeparator{/lang}{/capture}{@$thousandsSeparator|encodeJS}',
				'wcf.page.pagePosition': '{lang __literal=true}wcf.page.pagePosition{/lang}'
				{event name='javascriptLanguageImport'}
			});
			
			AcpBootstrap.setup({
				bootstrap: {
					enableMobileMenu: {if $__isLogin|empty}true{else}false{/if}
				}
			});
			
			User.init({@$__wcf->user->userID}, '{@$__wcf->user->username|encodeJS}');
		});
	</script>
	{js application='wcf' lib='jquery'}
	
	<script>
		// prevent jQuery and other libraries from utilizing define()
		__require_define_amd = define.amd;
		define.amd = undefined;
	</script>
	{js application='wcf' lib='jquery-ui'}
	{js application='wcf' lib='jquery-ui' file='touchPunch' bundle='WCF.Combined'}
	{js application='wcf' lib='jquery-ui' file='nestedSortable' bundle='WCF.Combined'}
	{js application='wcf' file='WCF.Assets' bundle='WCF.Combined'}
	{js application='wcf' file='WCF' bundle='WCF.Combined'}
	{js application='wcf' acp='true' file='WCF.ACP'}
	<script>
		define.amd = __require_define_amd;
		$.holdReady(true);
		WCF.User.init({$__wcf->user->userID}, '{@$__wcf->user->username|encodeJS}');
	</script>
	<script>
		$(function() {
			if (jQuery.browser.touch) $('html').addClass('touch');
			
			WCF.System.PageNavigation.init('.pagination');
			
			{if $__wcf->user->userID}
				new WCF.ACP.Search();
			{/if}
			
			{event name='javascriptInit'}
			
			$('form[method=get]').attr('method', 'post');
			});
	</script>
	{event name='javascriptInclude'}
</head>

<body id="tpl{$templateName|ucfirst}" data-template="{$templateName}" data-application="{$templateNameApplication}" class="wcfAcp">
	<a id="top"></a>
	
	<div id="pageContainer" class="pageContainer">
		{event name='beforePageHeader'}
		
		{include file='pageHeader'}
		
		{event name='afterPageHeader'}
		
		<div id="acpPageContentContainer" class="acpPageContentContainer">
			{include file='pageMenu'}
			
			<section id="main" class="main" role="main">
				<div class="layoutBoundary">
					<div id="content" class="content">
<!-- 
    // header.tpl 
    include file='header' pageTitle='wcf.acp.gman.app.'|concat:$action
-->


{include file='aclPermissions'}
<script data-relocate="true">
    $(document.body).on('click', '.remove', function () {
        $(this).closest('.liMoveable').remove();
    });
    $(document.body).on('click', '.upbutton', function () {
        console.log("click uP");
        var hook = $(this).closest('.liMoveable').prev('.liMoveable');
        var order = $(this).closest('.liMoveable').find('.order');
        order.val(order.val() - 1);
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
    require(['WoltLabSuite/GMan/Ui/Ul/UladdElement'], function (UladdElement) {
        new UladdElement(document.getElementById('actionAdd'), document.getElementById('avaibleActionList'), document.getElementById('actionList'), {
            ajax: {
                className: 'wcf\\data\\guild\\group\\application\\action\\ApplicationActionAction',
            }
        })
    });

    {if $applicationObject|isset}
        var appID = {$applicationObject->appID};
    {else}
        var appID = 0;
    {/if}

    require(['WoltLabSuite/GMan/Appfields/RemoveAppElement'], function (RemoveAppElement) {
        new RemoveAppElement('jsRemoveField', appID,{
            ajax: {
                actionName: 'removeField',
            }
        })
    });
    require(['WoltLabSuite/GMan/Appfields/RemoveAppElement'], function (RemoveAppElement) {
        new RemoveAppElement('jsRemoveAction', appID,{
            ajax: {
                actionName: 'removeAction',
            }
        })
    });
</script>

{include file='multipleLanguageInputJavascript' elementIdentifier='description' forceSelection=false}
{include file='multipleLanguageInputJavascript' elementIdentifier='title' forceSelection=false}
{include file='multipleLanguageInputJavascript' elementIdentifier='pollDescription' forceSelection=false}
{include file='multipleLanguageInputJavascript' elementIdentifier='pollTitle' forceSelection=false}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.gman.app.{$action}{/lang}</h1>
        {if $action == 'edit'}<p class="contentHeaderDescription">{$application->title|language}</p>{/if}
    </div>
</header>

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

<form method="post" action="{if $action == 'add'}{link controller='GuildGroupApplicationAdd'}{/link}{else}{link controller='GuildGroupApplicationEdit' object=$applicationObject}{/link}{/if}">
    <div class="section tabMenuContainer" data-active="{$activeTabMenuItem}" data-store="activeTabMenuItem">
        <nav class="tabMenu">
            <ul>
                <li><a href="{@$__wcf->getAnchor('general')}">{lang}wcf.acp.gman.app.general{/lang}</a></li>
                <li><a href="{@$__wcf->getAnchor('poll')}">{lang}wcf.acp.gman.app.poll{/lang}</a></li>
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
                <dt><label for="title">{lang}wcf.acp.gman.app.title{/lang}</label></dt>
                <dd>
                    <input type="text" id="title" name="title" value="{$i18nPlainValues['title']}" autofocus class="medium">
                    <small>{lang}wcf.acp.gman.app.title.description{/lang}</small>
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
                <dt><label for="description">{lang}wcf.acp.gman.app.description{/lang}</label></dt>
                <dd>
                    <textarea id="description" name="description" cols="40" rows="10">{$i18nPlainValues[description]}</textarea>
                    <small>{lang}wcf.acp.gman.app.description.description{/lang}</small>
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
                <h2 class="sectionTitle">{lang}wcf.acp.gman.app.properties{/lang}</h2>
                <dl>
                    <dd>
                        
                        <label><input type="checkbox" id="requireUser" name="requireUser" value="1" {if $requireUser} checked{/if}> {lang}wcf.acp.gman.app.requireUser{/lang}</label>
                        <label><input type="checkbox" id="isActive" name="isActive" value="1" {if $isActive} checked{/if}> {lang}wcf.acp.gman.app.isactive{/lang}</label>
                        <label><input type="checkbox" id="isCommentable" name="isCommentable" value="1" {if $isCommentable} checked{/if}> {lang}wcf.acp.gman.app.isCommentable{/lang}</label>

                        {event name='properties'}
                    </dd>
                </dl>
            </section>
            {event name='sections'}
        </div>
        <div id="poll" class="tabMenuContent">
            <section class="section" id="propertiesContainer">
                <h2 class="sectionTitle">{lang}wcf.acp.gman.app.poll{/lang}</h2>
                <dl>
                    <dd>
                        <label><input type="checkbox" id="hasPoll" name="hasPoll" value="1" {if $hasPoll} checked{/if}> {lang}wcf.acp.gman.app.hasPoll{/lang}</label>
                    </dd>
                </dl>
            </section>

            <section class="section">
                <dl{if $errorField =='pollTitle'} class="formError" {/if}>
                <dt><label for="title">{lang}wcf.global.title{/lang}</label></dt>
                <dd>
                    <input type="text" id="pollTitle" name="pollTitle" value="{$i18nPlainValues['pollTitle']}" autofocus class="medium">
                    {if $errorField == 'pollTitle'}
                    <small class="innerError">
                        {if $errorType == 'empty'}
                        {lang}wcf.global.form.error.empty{/lang}
                        {elseif $errorType == 'multilingual'}
                        {lang}wcf.global.form.error.multilingual{/lang}
                        {else}
                        {lang}wcf.acp.gman.app.pollTitle.error.{@$errorType}{/lang}
                        {/if}
                    </small>
                    {/if}
                </dd>
                </dl>

                <dl{if $errorField=='pollDescription'} class="formError" {/if}>
                <dt><label for="pollDescription">{lang}wcf.global.description{/lang}</label></dt>
                <dd>
                    <textarea id="pollDescription" name="pollDescription" cols="40" rows="10">{$i18nPlainValues[description]}</textarea>
                    {if $errorField == 'pollDescription'}
                    <small class="innerError">
                        {if $errorType == 'empty'}
                        {lang}wcf.global.form.error.empty{/lang}
                        {else}
                        {lang}wcf.acp.gman.app.pollDescription.error.{@$errorType}{/lang}
                        {/if}
                    </small>
                    {/if}
                </dd>
                </dl>
        </section>
        </div>

        <div id="fields" class="tabMenuContent">
            <section class="section">
                <h2 class="sectionTitle">{lang}wcf.acp.gman.app.fieldAdd{/lang}</h2>
                <dl>
                    <dt><label for="avaibleFieldList">{lang}wcf.acp.gman.app.field.select{/lang}</label></dt>
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
            <section class="section">
                <h2 class="sectionTitle">{lang}wcf.acp.gman.app.action.actionAdd{/lang}</h2>
                <dl>
                    <dt><label for="avaibleActionList">{lang}wcf.acp.gman.app.action.select{/lang}</label></dt>
                    <dd>
                        <select name="avaibleActionList" id="avaibleActionList" class="medium">
                            {foreach from=$avaibleActionList item=$action}
                            <option value="{$action->actionID}">{$action->getTitle()}</option>
                            {/foreach}
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt></dt>
                    <dd><p class="button jsActionAdd" id="actionAdd" name="actionAdd">{lang}wcf.acp.gman.app.action.add{/lang} </p></dd>
                </dl>
            </section>
            <section class="section">
                <h2 class="sectionTitle">{lang}wcf.acp.gman.app.actionlist{/lang}</h2>
                <ol class="containerList userList" id="actionList">
                    {foreach from=$applicationActionList item=action}
                    {include file="_applicationAction"}
                    {/foreach}
                </ol>

            </section>
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
                    <dt>{lang}wcf.acp.gman.app.moderatorPermissions{/lang}</dt>
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
