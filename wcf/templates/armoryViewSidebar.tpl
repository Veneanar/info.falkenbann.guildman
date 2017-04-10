{capture assign='sidebarLeft'}
<script data-relocate="true">
		$(function() {
			// mobile safari hover workaround
			if ($(window).width() <= 800) {
				$('.sidebar').addClass('mobileSidebar').hover(function() { });
			}
		});
		require(['WoltLabSuite/GMan/Character/Update'], function (UpdateData) {
		    new UpdateData(elByClass('jsUpdateButton'));
		});
</script>

<section class="box">
    {if $user}
    <h2 class="boxTitle">{lang}wcf.page.gman.owner{/lang}</h2>
    <nav class="boxContent">
        <ol class="boxMenu">
            {include file='userListItem'}
        </ol>
    </nav>
    {/if}

    <h2 class="boxTitle">{lang}wcf.page.gman.arsenal.links{/lang}</h2>
    <nav class="boxContent">
        <ol class="boxMenu">
            <li>
                <a class="jsUpdateButton pointer boxMenuLink" data-character-id="{$viewChar->characterID}"><span class="boxMenuLinkTitle">{lang}wcf.page.gman.arsenal.links.refresh{/lang}</span></a>
            </li>
            <li>
                <a href="{$viewChar->getWowArsenalLink()}" class="boxMenuLink" target="_blank"><span class="boxMenuLinkTitle">{lang}wcf.page.gman.arsenal.links.armory{/lang}</span></a>
            </li>
            <li>
                <a href="{$viewChar->getWowProgressLink()}" class="boxMenuLink" target="_blank"><span class="boxMenuLinkTitle">{lang}wcf.page.gman.arsenal.links.wowprogress{/lang}</span></a>
            </li>
            <li>
                <a href="{$viewChar->getWarcraftlogsLink()}" class="boxMenuLink" target="_blank"><span class="boxMenuLinkTitle">{lang}wcf.page.gman.arsenal.links.logs{/lang}</span></a>
            </li>
        </ol>
    </nav>

    <h2 class="boxTitle">{lang}wcf.page.gman.twinklist{/lang}</h2>
    <nav class="boxContent">
        <ol class="boxMenu">
                {foreach from=$twinks item=$char}
                    {include file='charList24'}
                {/foreach}
        </ol>
    </nav>

</section>
{/capture}
