{include file='header' pageTitle='{lang}wcf.acp.gman.guild.edit{/lang} Gilde bearbeiten'}

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

<script data-relocate="true">
	require(['WoltLabSuite/Core/Ui/Page/Search/Input'], function(UiPageSearchInput) {
	    new UiPageSearchInput(elBySel('input[name="contentpage"]'));
	});
</script>

<script data-relocate="true">
	require(['WoltLabSuite/GMan/Ui/Character/Search/Input'], function(UiCharacterSearchInput) {
	    new UiCharacterSearchInput(elBySel('input[name="charsearch"]'));
	});
</script>


<header class="contentHeader">
	<div class="contentHeaderTitle">
	  <h1 class="contentTitle">{lang}wcf.acp.gman.guild.edit{/lang} bearbeiten</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link controller='GuildList'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}wcf.acp.menu.link.guild.list{/lang}</span></a></li>
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{include file='formError'}
    {if $success|isset}
        <p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
    {/if}
    
    {if $firstjob|isset}
        <p class="success">{lang}wcf.acp.gman.guild.datasuccess{/lang} Daten wurden geladen und verarbeitet.</p>
    {/if}


<form id="guildEditForm" method="post" action="{link controller='GuildEdit' id=$guild->guildID}{/link}" enctype="multipart/form-data">
	<div class="section">
     	<section class="section">
		<header class="sectionHeader">
			<h2 class="sectionTitle">{lang}wcf.acp.gman.guild.bnetdata{/lang} Battle.net Daten</h2>
			<p class="sectionDescription">{lang}wcf.acp.gman.guild.bnetdata.desc{/lang} Daten aus dem battle.net</p>
		</header>
		<dl>
			<dt>{lang}wcf.acp.gman.guild.name{/lang} Name</dt>
			<dd>
				<p>{$guild->name} ({$guild->side})</p>
                <small>{lang}wcf.acp.gman.guild.name.desc{/lang} Name, Fraktion </small>
			</dd>
		</dl>  
		<dl>
			<dt>{lang}wcf.acp.gman.guild.realm Realm{/lang}</dt>
			<dd>
				<p>{$guild->getRealm()->name} 
                {if $guild->getRealm()->getConnetedRealmCount()>0}
                 <br />
                 <small>{lang}wcf.page.gman.guild.realm.connected{/lang} Verbunden mit [CODE EINBAUEN] </small>
                {/if}
               
            </p>
                <small>{lang}wcf.acp.gman.guild.realm.desc{/lang} Realm u. verbundene Realms</small>
			</dd>
		</dl>  
		<dl>
			<dt>{lang}wcf.page.gman.guildAchievementPoints{/lang} achievementPoints</dt>
			<dd>
				<p>{$guild->achievementPoints}</p>
                <small>{lang}wcf.page.gman.guildAchievementPoints.desc{/lang} Punkte</small>
			</dd>
		</dl>     
        <dl>
			<dt>wcf standart? Letzte Ändeurng</dt>
			<dd>
				<p>{$guild->lastModified} / </p>
                <small>{lang}wcf.acp.gman.lastChangeAndUpdate{/lang} Letzte Änderung im Spiel und letztes Update Homepage</small>
			</dd>
		</dl>  
        <dl>
			<dt>{lang}wcf.page.gman.guildleader{/lang} Gildenleiter</dt>
			<dd>
				<p>{$guild->getLeader()->charname}</p> 
                <small>{lang}wcf.page.gman.guildleader.desc{/lang}</small>
			</dd>
		</dl>                                        
        </section>

     	<section class="section">
		<header class="sectionHeader">
			<h2 class="sectionTitle">{lang}wcf.acp.gman.guild.guildinfo{/lang} Gildeninformationen</h2>
			<p class="sectionDescription">{lang}wcf.acp.gman.guild.guildinfo.desc{/lang}  weitere Gildeninfos</p>
		</header>
			{if $__wcf->session->getPermission('admin.content.cms.canUseMedia')}
				<dl{if $errorField == 'image'} class="formError"{/if}>
					<dt><label for="image">{lang}wcf.acp.gman.guild.image{/lang}</label></dt>
					<dd>
						<div id="imageDisplay" class="selectedImagePreview">
							{if $images[0]|isset}
								{@$images[0]->getThumbnailTag('small')}
							{/if}
						</div>
						<p class="button jsMediaSelectButton" data-store="imageID0" data-display="imageDisplay">{lang}wcf.media.chooseImage{/lang}</p>
						<input type="hidden" name="imageID[0]" id="imageID0"{if $imageID[0]|isset} value="{@$imageID[0]}"{/if}>
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
        
		<dl{if $errorField == 'birthtime'} class="formError"{/if}>
			<dt><label for="birthtime">{lang}wcf.acp.gman.guild.birthdate{/lang}</label></dt>
			<dd>
				<input type="datetime" id="birthtime" name="birthtime" value="{$time}" class="medium">
				{if $errorField == 'time'}
					<small class="innerError">
						{if $errorType == 'empty'}
							{lang}wcf.global.form.error.empty{/lang}
						{else}
							{lang}wcf.acp.article.time.error.{@$errorType}{/lang}
						{/if}
					</small>
				{/if}
			</dd>
		</dl>
             
    </section>
 	<section class="section">
		<header class="sectionHeader">
			<h2 class="sectionTitle">{lang}wcf.acp.gman.guild.data{/lang} Daten</h2>
			<p class="sectionDescription">{lang}wcf.acp.gman.guild.data.desc{/lang} Daten auslesen</p>
		</header>
		<dl>
			<dt>{lang}wcf.page.gman.guildmember{/lang} Gildenmember</dt>
			<dd>
				[BUTTON GILDENMEMBER]
			</dd>
		</dl>    
		<dl>
			<dt>{lang}wcf.page.guildacm{/lang} Gildenerfolge</dt>
			<dd>
				[BUTTON GILDENACMS]
			</dd>
		</dl>        
		<dl>
			<dt>{lang}wcf.acp.gman.guild.data.sync{/lang}Automatische Synchroniersung erlauben</dt>
			<dd>
				<label><input name="autosync" type="checkbox" id="autosync" value="0">{lang}wcf.acp.gman.guild.data.sync.desc{/lang} Bitte erst einstellen, wenn die Daten erstmals manuell übertragen wurden.</label>
			</dd>
		</dl>
	</section>
    </div>
	<div class="formSubmit">
    	<input type="hidden">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}
