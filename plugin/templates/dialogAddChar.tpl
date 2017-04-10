{if success}
{if $msg|isset}
    <p class="warning">{lang}wcf.page.gman.dialog.addchar.moderated{/lang}</p>
{else}
    <p class="success">{lang}wcf.page.gman.dialog.addchar.success{/lang}</p>
{/if}
<ol>
    {include file=charList24}
</ol>
    <p style="text-align: center;"><a href="{$char->getLink()}">{lang}wcf.page.gman.dialog.addchar.linktitle{/lang}</a></p>
{else}
<div class="error">
    <p>{lang}wcf.page.gman.dialog.addchar.failed{/lang}</p>
    {if $msg|isset}
    <p>{lang}wcf.ajax.error.permissionDenied{/lang}</p>
    {/if}
</div>
{/if}
