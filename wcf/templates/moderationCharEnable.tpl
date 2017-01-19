<article class="message messageReduced jsMessage wbbPost"
         data-object-id="{@$char->postID}" data-post-id="{@$char->postID}" data-can-edit="0" data-can-edit-inline="1"
         data-is-disabled="{if $char->isDisabled}1{else}0{/if}"
         data-can-delete="{@$__wcf->getSession()->getPermission('mod.board.canDeletePost')}" data-can-delete-completely="{@$__wcf->getSession()->getPermission('mod.board.canDeletePostCompletely')}" data-can-enable="0" data-can-restore="{@$__wcf->getSession()->getPermission('mod.board.canRestorePost')}"
>
	<div class="messageContent">
		<header class="messageHeader">
			<div class="box32 messageHeaderWrapper">
				{if $char->getUserProfile()->userID}
					<a href="{link controller='User' object=$char->userID}{/link}">{@$char->getAvatar()->getImageTag(32)}</a>
				{else}
					<span>{@$char->getUserProfile()->getAvatar()->getImageTag(32)}</span>
				{/if}
				
				<div class="messageHeaderBox">
					<h2 class="messageTitle">
						<a href="{@$char->getLink()}">{$char->getTitle()}</a>
					</h2>
					
					<ul class="messageHeaderMetaData">
						<li>{if $char->userID}<a href="{link controller='User' object=$char->userID}{/link}" class="username">{$char->getUsername()}</a>{else}<span class="username">{$char->getUsername()}</span>{/if}</li>
						<li><span class="messagePublicationTime">{@$char->getTime()|time}</span></li>
						
						{event name='messageHeaderMetaData'}
					</ul>
					
					<ul class="messageStatus">
						{if $char->isDeleted}<li><span class="badge label red jsIconDeleted">{lang}wcf.message.status.deleted{/lang}</span></li>{/if}
						{if $char->isDisabled}<li><span class="badge label green jsIconDisabled">{lang}wcf.message.status.disabled{/lang}</span></li>{/if}
						
						{event name='messageStatus'}
					</ul>
				</div>
			</div>
		</header>
		
		<div class="messageBody">
			{event name='beforeMessageText'}
			
			<div class="messageText">
				{@$char->getFormattedMessage()}
			</div>
			
			{event name='afterMessageText'}
		</div>
		
		<footer class="messageFooter">
			{include file='attachments' objectID=$char->charID}
			
			{event name='messageFooter'}
			
			<div class="messageFooterNotes">
				{if $char->isDisabled && $char->enableTime}
					<p class="messageFooterNote">{lang}wbb.post.delayedPublication{/lang}</p>
				{/if}
				
				{event name='messageFooterNotes'}
			</div>
			
			<div class="messageFooterGroup">
				<ul class="messageFooterButtons buttonList smallButtons">
					{if $char->getThread()->canEditPost($char->getDecoratedObject())}<li><a href="#" title="{lang}wbb.post.edit{/lang}" class="button jsMessageEditButton"><span class="icon icon16 fa-pencil"></span> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>{/if}
					{if LOG_IP_ADDRESS && $char->ipAddress && $__wcf->session->getPermission('admin.user.canViewIpAddress')}<li class="jsIpAddress jsOnly" data-post-id="{@$char->postID}"><a href="#" title="{lang}wbb.post.ipAddress{/lang}" class="button jsTooltip"><span class="icon icon16 fa-globe"></span> <span class="invisible">{lang}wbb.post.ipAddress{/lang}</span></a></li>{/if}
					{if MODULE_USER_INFRACTION && $char->getUserProfile()->userID && $__wcf->session->getPermission('mod.infraction.warning.canWarn') && !$char->getUserProfile()->getPermission('mod.infraction.warning.immune')}<li class="jsWarnPost jsOnly" data-object-id="{@$char->postID}" data-user-id="{@$char->getUserProfile()->userID}"><a href="#" title="{lang}wcf.infraction.warn{/lang}" class="button jsTooltip"><span class="icon icon16 fa-gavel"></span> <span class="invisible">{lang}wcf.infraction.warn{/lang}</span></a></li>{/if}
					{event name='messageFooterButtons'}
				</ul>
			</div>
		</footer>
	</div>
</article>

<script data-relocate="true" src="{@$__wcf->getPath()}js/WCF.Infraction{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@LAST_UPDATE_TIME}"></script>
<script data-relocate="true">
	$(function() {
		WCF.Language.addObject({
			'wbb.post.ipAddress.title': '{lang}wbb.post.ipAddress.title{/lang}',
			'wcf.infraction.warn': '{lang}wcf.infraction.warn{/lang}',
			'wcf.infraction.warn.success': '{lang}wcf.infraction.warn.success{/lang}'
		});
		new WCF.Infraction.Warning.Content('com.woltlab.wbb.warnablePost', '.jsWarnPost');
		{if LOG_IP_ADDRESS && $__wcf->session->getPermission('admin.user.canViewIpAddress')}new WBB.Post.IPAddressHandler();{/if}
	});
</script>
<script data-relocate="true">
	require(['Language', 'WoltLabSuite/Forum/Ui/Post/InlineEditor', 'WoltLabSuite/Forum/Ui/Post/Manager'], function (Language, UiPostInlineEditor, UiPostManager) {
		Language.addObject({
			'wbb.post.edit.close': '{lang}wbb.post.edit.close{/lang}',
			'wbb.post.edit.delete': '{lang}wbb.post.edit.trash{/lang}',
			'wbb.post.edit.delete.confirmMessage': '{lang}wbb.post.edit.delete.confirmMessage{/lang}',
			'wbb.post.edit.deleteCompletely': '{lang}wbb.post.edit.delete{/lang}',
			'wbb.post.edit.open': '{lang}wbb.post.edit.open{/lang}',
			'wbb.post.edit.restore': '{lang}wbb.post.edit.restore{/lang}',
			'wbb.post.edit.trash.confirmMessage': '{lang}wbb.post.edit.trash.confirmMessage{/lang}',
			'wbb.post.edit.trash.reason': '{lang}wbb.post.edit.trash.reason{/lang}',
			'wcf.message.status.deleted': '{lang}wcf.message.status.deleted{/lang}',
			'wcf.message.status.disabled': '{lang}wcf.message.status.disabled{/lang}'
		});
		
		var inlineEdit = new UiPostInlineEditor(0, { disableEdit: true });
		inlineEdit.setPostManager(new UiPostManager());
	});
</script>