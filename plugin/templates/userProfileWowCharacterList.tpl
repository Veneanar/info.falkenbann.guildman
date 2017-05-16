<div class="containerPadding">
    <fieldset>
        <legend>{lang}wcf.user.profile.content.gman.charlist.title{/lang}</legend>
        {if $stats > 0}
        <div class="section tabularBox">
            <table class="table">
                <thead>
                    <tr>
                        <th class="columnID columnUserID" colspan="2">{lang}wcf.global.name{/lang}</th>
                        <th class="columnTitle columnCharRace">{lang}wcf.page.gman.wow.race{/lang}</th>
                        <th class="columnTitle columnCharClass"></th>
                        <th class="columnDigits columnCharLevel">{lang}wcf.page.gman.wow.level{/lang}</th>
                        <th class="columnDigits columnCharItemLevel">{lang}wcf.page.gman.wow.averageItemLevel{/lang}</th>
                        <th class="columnTitle columnCharRank">{lang}wcf.page.gman.wow.rank{/lang}</th>
                            {event name='columnHeads'}
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$charList item=character}
                    <tr id="groupContainer{@$character->charID}" class="jsCharacterRow">
                        <td class="columnIcon">{@$character->getAvatar()->getImageTag(24)}</td>
                        <td class="columnID columnCharID">{@$character->name} <small>({$character->realm})</small></td>

                        <td class="columnTitle columnCharRace">
                            {@$character->getRace()->getTag()}
                        </td>
                        <td class="columnDigits columnCharClass">
                            {@$character->getClass()->getTag()}
                        </td>
                        <td class="columnDigits columnCharLevel">
                            {$character->c_level}
                        </td>
                        <td class="columnDigits columnCharItemLevel">

                            {$character->getEquip()->averageItemLevel}
                        </td>
                        <td class="columnTitle columnCharRank">
                            {$guild->getRankName($character->guildRank)}
                        </td>
                        <td class="columnTitle columnCharGroups">
                            <small>
                                {foreach from=$character->getGroups() item=group name=grouplist}
                                {$group->groupName}
                                {if !$tpl.foreach.grouplist.last}, {/if}
                                {/foreach}
                            </small>
                        </td>
                        {event name='columns'}
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        {else}
        <div class="message">{lang}wcf.user.profile.content.gman.charlist.nochar{/lang}</div>
        {/if}
    </fieldset>


</div>