{if $noli|isset}{else}<li class="liMoveable">{/if}
    <div class="box48">
        <div class="details userInformation">
            <div class="containerHeadline">
                <h3>
                    <span class="username userLink">{$action->getTitle()}</span>
                </h3>
            </div>
            <dl>
                <dt><label for="actionTrigger">{lang}wcf.gman.app.action.select.trigger{/lang}</label></dt>
                <dd>
                    <select name="actionTrigger[]" class="medium">
                        <option value="1" {if $action->actionTrigger==1}selected{/if}>{lang}wcf.gman.app.action.trigger.open{/lang}</option>
                        <option value="2" {if $action->actionTrigger==2}selected{/if}>{lang}wcf.gman.app.action.trigger.assignOfficer{/lang}</option>
                        <option value="3" {if $action->actionTrigger==3}selected{/if}>{lang}wcf.gman.app.action.trigger.accept{/lang}</option>
                        <option value="4" {if $action->actionTrigger==4}selected{/if}>{lang}wcf.gman.app.action.trigger.probationStart{/lang}</option>   
                        <option value="5" {if $action->actionTrigger==5}selected{/if}>{lang}wcf.gman.app.action.trigger.probationExtension{/lang}</option>
                        <option value="6" {if $action->actionTrigger==6}selected{/if}>{lang}wcf.gman.app.action.trigger.probationShortening{/lang}</option>
                        <option value="7" {if $action->actionTrigger==7}selected{/if}>{lang}wcf.gman.app.action.trigger.probationEnd{/lang}</option>
                        <option value="8" {if $action->actionTrigger==8}selected{/if}>{lang}wcf.gman.app.action.trigger.decline{/lang}</option>
                    </select>
                </dd>
            </dl>
            <dl>
            <dt><label for="actionVariable">{lang}wcf.acp.gman.app.actionVariable{/lang}</label></dt>
            {if $action->actionType < 30 || $action->actionType  > 39}
            {if $action->actionType < 100 || $action->actionType > 150}
            <dd>
                <input name="actionVariable" type="text" id="actionVariable[]" value="{$action->actionVariable}" class="medium" pattern="">
                <small>{lang}wcf.acp.gman.app.actionVariable.description{/lang}</small>
            </dd>
            </dl>
            {/if}
            {/if}
            <input type="hidden" name="actionID[]" value="{$action->actionID}">
            <nav class="jsMobileNavigation buttonGroupNavigation" style="opacity: 1" data-action-id="{$action->actionID}">
                <ul class="buttonList iconList jsOnly">
                    <li>
                        <a class="jsTooltip pointer upbutton" title="{lang}wcf.acp.gman.app.field.moveup{/lang}">
                            <span class="icon icon16 fa-arrow-up"></span>
                            <span class="invisible">{lang}wcf.acp.gman.app.field.select{/lang}</span>
                        </a>
                    </li>
                    <li>
                        <a class="jsTooltip pointer downbutton" title="{lang}wcf.acp.gman.app.field.movedown{/lang}">
                            <span class="icon icon16 fa-arrow-down"></span>
                            <span class="invisible">{lang}wcf.acp.gman.app.field.movedown{/lang}</span>
                        </a>
                    </li>
                    <li>
                        <a class="jsTooltip pointer removebutton" title="{lang}wcf.acp.gman.app.field.remove{/lang}">
                            <span class="icon icon16 fa-times"></span>
                            <span class="invisible">{lang}wcf.acp.gman.app.field.remove{/lang}</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
{if $noli|isset}{else}</li>{/if}