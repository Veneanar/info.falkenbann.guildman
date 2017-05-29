{if $noli|isset}{else}<li class="liMoveable">{/if}
    <div class="box48">
        <div class="details userInformation">
            <div class="containerHeadline">
                <h3>
                    <span class="username userLink">{$field->getTitle()}</span>
                </h3>
            </div>
            <dl>
                <dt><label for="fieldPermission">{lang}wcf.gman.app.field.select.permission{/lang}</label></dt>
                <dd>
                    <select name="fieldPermission[]" class="medium">
                        <option value="1" {if $field->fieldPermission==1}selected{/if}>{lang}wcf.gman.app.field.permission.level1{/lang}</option>
                        <option value="2" {if $field->fieldPermission==2}selected{/if}>{lang}wcf.gman.app.field.permission.level2{/lang}</option>
                        <option value="3" {if $field->fieldPermission==3}selected{/if}>{lang}wcf.gman.app.field.permission.level3{/lang}</option>
                    </select>
                </dd>
            </dl>
            <dl>
                <dd><label><input type="checkbox" name="fieldRequierd[]" value="1" {if $field->fieldRequierd==1}checked{/if}> {lang}wcf.gman.app.field.requiered{/lang}</label></dd>
            </dl>
            <input type="hidden" name="fieldID[]" value="{$field->fieldID}">
            <input type="hidden" class="order" name="fieldOrder[]" value="{$field->getOrder()}">
            <nav class="jsMobileNavigation buttonGroupNavigation" style="opacity: 1" data-char-id="{$field->fieldID}">
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