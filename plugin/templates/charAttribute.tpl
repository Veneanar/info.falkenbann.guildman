<section class="box">
    <h2 class="boxTitle">{lang}wcf.page.gman.arsenal.attributes{/lang}</h2>
    <nav class="boxContent">
        <ol class="boxMenu">
            <li data-id="strength" class="summary-stats-column">
                <span class="name">{lang}wcf.global.gman.item.stat.4{/lang}</span>
                <span class="value">{$viewChar->stats['str']}</span>
                <span class="clear"><!-- --></span>
            </li>
            <li data-id="agi" class="summary-stats-column">
                <span class="name">{lang}wcf.global.gman.item.stat.3{/lang}</span>
                <span class="value">{$viewChar->stats['agi']}</span>
                <span class="clear"><!-- --></span>
            </li>
            <li data-id="int" class="summary-stats-column">
                <span class="name">{lang}wcf.global.gman.item.stat.5{/lang}</span>
                <span class="value">{$viewChar->stats['int']}</span>
                <span class="clear"><!-- --></span>
            </li>
            <li data-id="sta" class="summary-stats-column">
                <span class="name">{lang}wcf.global.gman.item.stat.7{/lang}</span>
                <span class="value">{$viewChar->stats['sta']}</span>
                <span class="clear"><!-- --></span>
            </li>
        </ol>
    </nav>
    <h2 class="boxTitle">{lang}wcf.page.gman.arsenal.attack{/lang}</h2>
    <nav class="boxContent">
        <ol class="boxMenu">
            <li data-id="damage" class="summary-stats-column">
                <span class="name">{lang}wcf.global.gman.item.stat.damage{/lang}</span>
                <span class="value">{$viewChar->stats['mainHandDmgMin']}-{$viewChar->stats['mainHandDmgMax']}</span>
                <span class="clear"><!-- --></span>
            </li>
            <li data-id="damage" class="summary-stats-column">
                <span class="name">{lang}wcf.global.gman.item.stat.attackspeed{/lang}</span>
                <span class="value">{$viewChar->stats['mainHandSpeed']|number_format:2}</span>
                <span class="clear"><!-- --></span>
            </li>
        </ol>
    </nav>
    <h2 class="boxTitle">{lang}wcf.page.gman.arsenal.casts{/lang}</h2>
    <nav class="boxContent">
        <ol class="boxMenu">
            <li data-id="mana5" class="summary-stats-column">
                <span class="name">{lang}wcf.global.gman.item.stat.43{/lang}</span>
                <span class="value">{$viewChar->stats['mana5']}</span>
                <span class="clear"><!-- --></span>
            </li>
        </ol>
    </nav>
    <h2 class="boxTitle">{lang}wcf.page.gman.arsenal.deffence{/lang}</h2>
    <nav class="boxContent">
        <ol class="boxMenu">
            <li data-id="armor" class="summary-stats-column">
                <span class="name">{lang}wcf.global.gman.item.stat.armor{/lang}</span>
                <span class="value">{$viewChar->stats['str']}</span>
                <span class="clear"><!-- --></span>
            </li>
            <li data-id="dodge" class="summary-stats-colum jsTooltip" title="{lang}wcf.global.gman.item.stat.absoluevalue{/lang}:  {$viewChar->stats['dodgeRating']}">
                <span class="name">{lang}wcf.global.gman.item.stat.13{/lang}</span>
                <span class="value">{$viewChar->stats['dodge']|number_format:2}%</span>
                <span class="clear"><!-- --></span>
            </li>
            <li data-id="parry" class="summary-stats-colum jsTooltip" title="{lang}wcf.global.gman.item.stat.absoluevalue{/lang}:  {$viewChar->stats['parryRating']}">
                <span class="name">{lang}wcf.global.gman.item.stat.14{/lang}</span>
                <span class="value">{$viewChar->stats['parry']|number_format:2}%</span>
                <span class="clear"><!-- --></span>
            </li>
            <li data-id="block" class="summary-stats-colum jsTooltip" title="{lang}wcf.global.gman.item.stat.absoluevalue{/lang}:  {$viewChar->stats['blockRating']}">
                <span class="name">{lang}wcf.global.gman.item.stat.15{/lang}</span>
                <span class="value">{$viewChar->stats['block']|number_format:2}%</span>
                <span class="clear"><!-- --></span>
            </li>
        </ol>
    </nav>
    <h2 class="boxTitle">{lang}wcf.page.gman.arsenal.amplification{/lang}</h2>
    <nav class="boxContent">
        <ol class="boxMenu">
            <li data-id="crit" class="summary-stats-colum jsTooltip" title="{lang}wcf.global.gman.item.stat.absoluevalue{/lang}:  {$viewChar->stats['critRating']}">
                <span class="name">{lang}wcf.global.gman.item.stat.32{/lang}</span>
                <span class="value">{$viewChar->stats['crit']|number_format:2}%</span>
                <span class="clear"><!-- --></span>
            </li>
            <li data-id="haste" class="summary-stats-colum jsTooltip" title="{lang}wcf.global.gman.item.stat.absoluevalue{/lang}:  {$viewChar->stats['hasteRating']}">
                <span class="name">{lang}wcf.global.gman.item.stat.36{/lang}</span>
                <span class="value">{$viewChar->stats['haste']|number_format:2}%</span>
                <span class="clear"><!-- --></span>
            </li>
            <li data-id="mastery" class="summary-stats-colum jsTooltip" title="{lang}wcf.global.gman.item.stat.absoluevalue{/lang}:  {$viewChar->stats['masteryRating']}">
                <span class="name">{lang}wcf.global.gman.item.stat.49{/lang}</span>
                <span class="value">{$viewChar->stats['mastery']|number_format:2}%</span>
                <span class="clear"><!-- --></span>
            </li>
            <li data-id="leech" class="summary-stats-colum jsTooltip" title="{lang}wcf.global.gman.item.stat.absoluevalue{/lang}:  {$viewChar->stats['leechRating']}">
                <span class="name">{lang}wcf.global.gman.item.stat.62{/lang}</span>
                <span class="value">{$viewChar->stats['leech']|number_format:2}%</span>
                <span class="clear"><!-- --></span>
            </li>
            <li data-id="versatility" class="summary-stats-colum jsTooltip" title="{lang}wcf.global.gman.item.stat.absoluevalue{/lang}:  {$viewChar->stats['versatility']}">
                <span class="name">{lang}wcf.global.gman.item.stat.40{/lang}</span>
                <span class="value">{$viewChar->stats['versatilityDamageDoneBonus']|number_format:2}%</span>
                <span class="clear"><!-- --></span>
            </li>
            <li data-id="avoidanceRating" class="jsTooltip" title="{lang}wcf.global.gman.item.stat.absoluevalue{/lang}:  {$viewChar->stats['avoidanceRating']}">
                <span class="name">{lang}wcf.global.gman.item.stat.63{/lang}</span>
                <span class="value">{$viewChar->stats['avoidanceRatingBonus']|number_format:2}%</span>
                <span class="clear"><!-- --></span>
            </li>
            <li data-id="speedRating" class="jsTooltip" title="{lang}wcf.global.gman.item.stat.absoluevalue{/lang}:  {$viewChar->stats['speedRating']}">
                <span class="name">{lang}wcf.global.gman.item.stat.61{/lang}</span>
                <span class="value">{$viewChar->stats['speedRatingBonus']|number_format:2}%</span>
                <span class="clear"><!-- --></span>
            </li>
        </ol>
    </nav>
</section>

