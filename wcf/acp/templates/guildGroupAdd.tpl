{include file='header' pageTitle='{lang}wcf.acp.menu.link.gman.group{/lang}'}

{if $__wcf->session->getPermission('admin.content.cms.canUseMedia')}
<script data-relocate="true">
		{include file='mediaJavaScript'}

		require(['WoltLabSuite/Core/Media/Manager/Select'], function(MediaManagerSelect) {
			new MediaManagerSelect({
				dialogTitle: '{lang}wcf.media.chooseImage{/lang}',
				imagesOnly: 1
			});
		});
</script>
{/if}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.menu.link.gman.group{/lang}</h1>
    </div>
</header>

{include file='formError'}
    {if $success|isset}
<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
    {/if}
{$errorField}
<form id="guildEditForm" method="post" action="{if $action == 'add'}{link controller='GuildGroupAdd'}{/link}{else}{link controller='GuildGroupEdit' object=$guildGroupObject}{/link}{/if}" enctype="multipart/form-data">
    <div class="section">
        <header class="sectionHeader">
            <h2 class="sectionTitle">{lang}wcf.acp.gman.group.groupmain{/lang}</h2>
            <p class="sectionDescription">{lang}wcf.acp.gman.group.groupmain.desc{/lang}</p>
        </header>

        <dl {if $errorField=='groupName'} class="formError" {/if}>
        <dt><label for="groupName">{lang}wcf.acp.gman.group.name{/lang}</label></dt>
        <dd>
            <input name="groupName" type="text" id="groupName" value="{$groupName}" class="medium" pattern="{literal}[a-Z0-9 _]{4,50}{/literal}" required>
            <small>{lang}wcf.acp.gman.group.name.description{/lang}</small>
            {if $errorField == 'groupName'}
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

        <dl {if $errorField =='groupTeaser'} class="formError" {/if}>
        <dt><label for="groupTeaser">{lang}wcf.acp.gman.group.teaser{/lang}</label></dt>
        <dd>
            <input name="groupTeaser" type="text" id="groupTeaser" value="{$groupTeaser}" class="long" maxlength="250">
            <small>{lang}wcf.acp.gman.group.teaser.description{/lang}</small>
            {if $errorField == 'groupTeaser'}
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

        <dl {if $errorField=='groupWcfID'} class="formError" {/if}>
        <dt><label for="groupWcfID">{lang}wcf.acp.gman.group.wcfgroup{/lang}</label></dt>
        <dd>
            <select name="groupWcfID" id="groupWcfID" class="medium">
                <option value="0">{lang}wcf.global.noSelection{/lang}</option>

                {foreach from=$wcfGroups item=$group}
                <option value="{$group->groupID}" {if $group->groupID == $groupWcfID} selected{/if}>{$group->getTitle()}</option>
                {/foreach}
            </select>
            <small>{lang}wcf.acp.gman.group.wcfgroup.description{/lang}</small>
            {if $errorField == 'groupWcfID'}
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

        <dl{if $errorField=='gameRank'} class="formError" {/if}>
        <dt><label for="gameRank">{lang}wcf.acp.gman.group.gamerank{/lang}</label></dt>
        <dd>
            <select name="gameRank" id="gameRank" class="medium">
                <option value="11">{lang}wcf.global.noSelection{/lang}</option>

                {foreach from=$rankList item=$rank}
                <option value="{$rank['rankID']}" {if $rank['rankID'] == $gameRank} selected{/if}>{$rank['rankName']}</option>
                {/foreach}
            </select>
            <small>{lang}wcf.acp.gman.group.gamerank.description{/lang}</small>
            {if $errorField == 'gameRank'}
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

    </div>

    <div class="section">
        <header class="sectionHeader">
            <h2 class="sectionTitle">{lang}wcf.acp.gman.group.calendar{/lang}</h2>
            <p class="sectionDescription">{lang}wcf.acp.group.calendar.description{/lang}</p>
        </header>

        <dl{if $errorField=='showCalender'} class="formError" {/if}>
        <dd>
            <label><input type="checkbox" name="showCalender" value="1" {if $showCalender} checked{/if}> {lang}wcf.acp.gman.group.showcalendar{/lang}</label>
            <small>{lang}wcf.acp.gman.group.showcalendar.description{/lang}</small>
            {if $errorField == 'showCalender'}
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

   <div id="calendarDetails">
       <dl{if $errorField=='calendarTitle'} class="formError" {/if}>
       <dt><label for="calendarTitle">{lang}wcf.acp.gman.group.calendartitle{/lang}</label></dt>
       <dd>
           <input name="calendarTitle" id="calendarTitle" type="text" value="{$calendarTitle}" class="medium" pattern={literal}"[a-Z0-9 _]{4,50}"{/literal}>
           <small>{lang}wcf.acp.gman.group.calendartitle.description{/lang}</small>
           {if $errorField == 'calendarTitle'}
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

       <dl{if $errorField=='calendarText'} class="formError" {/if}>
       <dt><label for="calendarText">{lang}wcf.acp.gman.group.calendartext{/lang}</label></dt>
       <dd>
           <input name="calendarText" id="calendarText" type="text" value="{$calendarText}" class="long" maxlength="250">
           <small>{lang}wcf.acp.gman.group.calendartext.description{/lang}</small>
           {if $errorField == 'calendarText'}
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

       <dl{if $errorField=='calendarQuery'} class="formError" {/if}>
       <dt><label for="calendarQuery">{lang}wcf.acp.gman.calendarquery.name{/lang}</label></dt>
       <dd>
           <input name="calendarQuery" id="calendarQuery" type="text" value="{$calendarQuery}" class="medium" pattern={literal}"[a-Z0-9 _-]{4,50}"{/literal}>
           <small>{lang}wcf.acp.gman.group.calendarquery.description{/lang}</small>
           {if $errorField == 'calendarQuery'}
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

       <dl{if $errorField=='calendarCategoryID'} class="formError" {/if}>
       <dt><label for="calendarCategoryID">{lang}wcf.acp.gman.group.calcat{/lang}</label></dt>
       <dd>
           <select name="calendarCategoryID" id="calendarCategoryID" class="medium">
               <option value="0">{lang}wcf.global.noSelection{/lang}</option>

               {foreach from=$categoryList item=$category}
               <option value="{$category->categoryID}" {if $category->categoryID == $calendarCategoryID} selected{/if}>{$category->getTitle()}</option>
               {/foreach}
           </select>
           <small>{lang}wcf.acp.gman.group.calcat.description{/lang}</small>
           {if $errorField == 'calendarCategoryID'}
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
   </div>
  </div>




    <div class="section">
        <header class="sectionHeader">
            <h2 class="sectionTitle">{lang}wcf.acp.gman.group.appearance{/lang}</h2>
            <p class="sectionDescription">{lang}wcf.acp.gman.guild.appearance.desc{/lang}</p>
        </header>
        {if $__wcf->session->getPermission('admin.content.cms.canUseMedia')}
        <dl{if $errorField=='image'} class="formError" {/if}>
        <dt><label for="image">{lang}wcf.acp.gman.group.image{/lang}</label></dt>
        <dd>
            <div id="imageDisplay" class="selectedImagePreview">
                {if $images[0]|isset}
                {@$images[0]->getThumbnailTag('small')}
                {/if}
            </div>
            <p class="button jsMediaSelectButton" data-store="imageID0" data-display="imageDisplay">{lang}wcf.media.chooseImage{/lang}</p>
            <input type="hidden" name="imageID[0]" id="imageID0" {if $imageID[0]|isset} value="{@$imageID[0]}" {/if}>
            {if $errorField == 'image'}
            <small class="innerError">{lang}wcf.acp.article.image.error.{@$errorType}{/lang}</small>
            {/if}
        </dd>
        </dl>
        {elseif $action == 'edit' && $images[0]|isset}
        <dl>
            <dt>{lang}wcf.acp.article.image{/lang}</dt>
            <dd>
                <div id="imageDisplay">{@$images[0]->getThumbnailTag('small')}</div>
            </dd>
        </dl>
        {/if}
        <dl{if $errorField=='articleID'} class="formError" {/if}>
        <dt><label for="articleID">{lang}wcf.acp.gman.group.article{/lang}</label></dt>
        <dd>
            <select name="articleID" id="articleID" class="medium">
                <option value="0">{lang}wcf.global.noSelection{/lang}</option>

                {foreach from=$articleList item=$article}
                <option value="{$article->articleID}" {if $article->articleID == $articleID} selected{/if}>{$article->getTitle()}</option>
                {/foreach}
            </select>
            <small>{lang}wcf.acp.gman.group.article.description{/lang}</small>
            {if $errorField == 'articleID'}
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
        <dl{if $errorField=='boardID'} class="formError" {/if}>
        <dt><label for="boardID">{lang}wcf.acp.gman.guild.board{/lang}</label></dt>
        <dd>
            <select name="boardID" id="boardID" class="medium">
                <option value="0">{lang}wcf.global.noSelection{/lang}</option>

                {foreach from=$boardList item=$board}
                <option value="{$board->boardID}" {if $board->boardID == $boardID} selected{/if}>{$board->getTitle()}</option>
                {/foreach}
            </select>
            <small>{lang}wcf.acp.gman.group.board.description{/lang}</small>
            {if $errorField == 'boardID'}
            <small class="innerError">
                {if $errorType == 'empty'}
                {lang}wcf.global.form.error.empty{/lang}
                {else}
                {lang}wcf.user.username.error.{@$errorType}{/lang}
                {/if}
            </small>
            {/if}
        </dd>
        </dl>
    </div>

    <dl{if $errorField=='threadID'} class="formError" {/if}>
    <dt><label for="threadID">{lang}wcf.acp.gman.group.thread{/lang}</label></dt>
    <dd>
        <input name="threadID" type="number" id="threadID" value="{$threadID}" class="small">
        <small>{lang}wcf.acp.gman.group.thread.description{/lang}</small>
        {if $errorField == 'threadID'}
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

    <dl{if $errorField=='orderNo'} class="formError" {/if}>
    <dt><label for="orderNo">{lang}wcf.acp.gman.group.order{/lang}</label></dt>
    <dd>
        <input name="orderNo" type="number"  id="orderNo" value="{$orderNo}" class="tiny">
        <small>{lang}wcf.acp.gman.group.order.description{/lang}</small>
        {if $errorField == 'orderNo'}
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
    <div class="section">
        <header class="sectionHeader">
            <h2 class="sectionTitle">{lang}wcf.acp.gman.group.raid{/lang}</h2>
            <p class="sectionDescription">{lang}wcf.acp.gman.group.raid.desc{/lang}</p>
        </header>
        <dl{if $errorField=='isRaidgruop'} class="formError" {/if}>
        <dd>
            <label><input type="checkbox" name="isRaidgruop" value="1" {if $isRaidgruop} checked{/if}> {lang}wcf.acp.gman.group.israidgruop{/lang}</label>
            <small>{lang}wcf.acp.gman.group.israidgruop.description{/lang}</small>
            {if $errorField == 'isRaidgruop'}
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
        <dl{if $errorField=='fetchWCL'} class="formError" {/if}>
        <dd>
            <label><input type="checkbox" name="fetchWCL" value="1" {if $fetchWCL} checked{/if}> {lang}wcf.acp.gman.group.fetchwcl{/lang}</label>
            <small>{lang}wcf.acp.gman.group.fetchwcl.description{/lang}</small>
            {if $errorField == 'fetchWCL'}
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
        <dl{if $errorField=='wclQuery'} class="formError" {/if}>
        <dt><label for="calendarQuery">{lang}wcf.acp.gman.wclquery.name{/lang}</label></dt>
        <dd>
            <input name="wclQuery" type="text" id="wclQuery" value="{$wclQuery}" class="medium" pattern={literal}"[a-Z0-9 _-]{4,50}"{/literal}>
            <small>{lang}wcf.acp.gman.group.wclquery.description{/lang}</small>
            {if $errorField == 'wclQuery'}
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

    </div>
    <div class="formSubmit">
        <input type="hidden">
        <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
        {@SECURITY_TOKEN_INPUT_TAG}
    </div>
</form>
