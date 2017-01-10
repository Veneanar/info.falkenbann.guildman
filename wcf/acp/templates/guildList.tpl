{include file='header' pageTitle='wcf.page.gman.admin.guild.title'}

<script data-relocate="true">
	$(function() {
		new WCF.Action.Delete('wcf\\data\\credit\\GuildAction', '.jsMenuRow');
	});
</script>

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.page.gman.admin.guild.secondTitle{/lang}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li>
                <div id="uploadButton"></div>
            </li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{include file='formError'}

{hascontent}
<div class="paginationTop">
    {content}
			{assign var='linkParameters' value=''}
			{pages print=true assign=pagesLinks controller="GuildList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder$linkParameters"}
		{/content}
</div>
{/hascontent}
{if $objects|count}
<div class="section tabularBox">
    <table class="table jsClipboardContainer" data-type="com.gman.wcf.guild">
        <thead>
            <tr>
                <th class="columnMark">
                    <label>
                        <input type="checkbox" class="jsClipboardMarkAll" />
                    </label>
                </th>
                <th class="columnID columnCaID{if $sortField == 'caID'} active {@$sortOrder}{/if}" colspan="2">
                    <a href="{link controller='CreditList'}pageNo={@$pageNo}&sortField=mediaID&sortOrder={if $sortField == 'caID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.global.objectID{/lang}</a>
                </th>
                <th class="columnTitel columnName{if $sortField == 'name'} active {@$sortOrder}{/if}">
                    <a href="{link controller='CreditList'}pageNo={@$pageNo}&sortField=filesize&sortOrder={if $sortField == 'name' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}Name{/lang}</a>
                </th>
                {event name='columnHeads'}
            </tr>
        </thead>

        <tbody>
            {foreach from=$objects item=guild}
            <tr class="jsMenuRow jsClipboardObject">
                <td class="columnMark">
                    <input type="checkbox" class="jsClipboardItem" data-object-id="{@$credittapp->caID}" />
                </td>
                <td class="columnIcon">
					<a href="{link controller='CreditEdit' id=$credittapp->caID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon24 fa-pencil"></span></a>                    
                    <span class="icon icon24 fa-times jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$guild->guildID}" data-confirm-message-html="{lang title=$guild->name} wcf.media.delete.confirmMessage{/lang}"></span>

                    {event name='rowButtons'}
                </td>
                <td class="columnID columnCaID">{@$guild->guildID}</td>
                <td class="columnDate columnName">{$guild->name}</td>
                {event name='columns'}
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>

<footer class="contentFooter">
    {hascontent}
    <div class="paginationBottom">
        {content}{@$pagesLinks}{/content}
    </div>
    {/hascontent}

    <nav class="contentFooterNavigation">
        <ul>
            {event name='contentFooterNavigation'}
        </ul>
    </nav>
</footer>
{else}
<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
