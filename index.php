<?php
use wcf\util\DatabaseObjectCreator;

echo "Hello World!";

$DBOCreator = new DatabaseObjectCreator('wcf1_gman_application', 'GuildApplication', 'guild\application', '/sghost/wwwroot/sylvanasgarde.com/garde2015/lib', 'wcf');
$DBOCreator->setAuthor("Veneanar Falkenbann");  // Autor
$DBOCreator->setCopyright("2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info"); // copyright z.B. Firmenname
$DBOCreator->setPackage("info.falkenbann.guildman"); // Paketname
$DBOCreator->setObjectName("Gildenbewerbung");      // Objektname, wird im Kommentar benutzt.
$DBOCreator->setLicense($DBOCreator::LICENSE_GPL); // oder LICENSE_LGPL, LICENSE_GPL, LICENSE_PD (Public Domain) oder string für eigene Lizenz
$DBOCreator->execute($DBOCreator::ALLCLASSES); // oder BASECLASS/ EDITCLASS / ACTIONCLASS / DECORATEDCLASS / LISTCLASS

$DBOCreator = new DatabaseObjectCreator('wcf1_gman_group', 'GuildGroup', 'guild\group', '/sghost/wwwroot/sylvanasgarde.com/garde2015/lib', 'wcf');
$DBOCreator->setAuthor("Veneanar Falkenbann");  // Autor
$DBOCreator->setCopyright("2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info"); // copyright z.B. Firmenname
$DBOCreator->setPackage("info.falkenbann.guildman"); // Paketname
$DBOCreator->setObjectName("Gildenbewerbung");      // Objektname, wird im Kommentar benutzt.
$DBOCreator->setLicense($DBOCreator::LICENSE_GPL); // oder LICENSE_LGPL, LICENSE_GPL, LICENSE_PD (Public Domain) oder string für eigene Lizenz
$DBOCreator->execute($DBOCreator::ALLCLASSES); // oder BASECLASS/ EDITCLASS / ACTIONCLASS / DECORATEDCLASS / LISTCLASS

$DBOCreator = new DatabaseObjectCreator('wcf1_gman_pointtrans', 'PointTransaction', 'guild\point', '/sghost/wwwroot/sylvanasgarde.com/garde2015/lib', 'wcf');
$DBOCreator->setAuthor("Veneanar Falkenbann");  // Autor
$DBOCreator->setCopyright("2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info"); // copyright z.B. Firmenname
$DBOCreator->setPackage("info.falkenbann.guildman"); // Paketname
$DBOCreator->setObjectName("Punktetransaktion");      // Objektname, wird im Kommentar benutzt.
$DBOCreator->setLicense($DBOCreator::LICENSE_GPL); // oder LICENSE_LGPL, LICENSE_GPL, LICENSE_PD (Public Domain) oder string für eigene Lizenz
$DBOCreator->execute($DBOCreator::ALLCLASSES); // oder BASECLASS/ EDITCLASS / ACTIONCLASS / DECORATEDCLASS / LISTCLASS

$DBOCreator = new DatabaseObjectCreator('wcf1_gman_pointtype', 'PointType', 'guild\point\type', '/sghost/wwwroot/sylvanasgarde.com/garde2015/lib', 'wcf');
$DBOCreator->setAuthor("Veneanar Falkenbann");  // Autor
$DBOCreator->setCopyright("2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info"); // copyright z.B. Firmenname
$DBOCreator->setPackage("info.falkenbann.guildman"); // Paketname
$DBOCreator->setObjectName("Punktetyp");      // Objektname, wird im Kommentar benutzt.
$DBOCreator->setLicense($DBOCreator::LICENSE_GPL); // oder LICENSE_LGPL, LICENSE_GPL, LICENSE_PD (Public Domain) oder string für eigene Lizenz
$DBOCreator->execute($DBOCreator::ALLCLASSES); // oder BASECLASS/ EDITCLASS / ACTIONCLASS / DECORATEDCLASS / LISTCLASS

$DBOCreator = new DatabaseObjectCreator('wcf1_gman_wow_character', 'WowCharacter', 'wow\character', '/sghost/wwwroot/sylvanasgarde.com/garde2015/lib', 'wcf');
$DBOCreator->setAuthor("Veneanar Falkenbann");  // Autor
$DBOCreator->setCopyright("2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info"); // copyright z.B. Firmenname
$DBOCreator->setPackage("info.falkenbann.guildman"); // Paketname
$DBOCreator->setObjectName("WoW Charackter");      // Objektname, wird im Kommentar benutzt.
$DBOCreator->setLicense($DBOCreator::LICENSE_GPL); // oder LICENSE_LGPL, LICENSE_GPL, LICENSE_PD (Public Domain) oder string für eigene Lizenz
$DBOCreator->execute($DBOCreator::ALLCLASSES); // oder BASECLASS/ EDITCLASS / ACTIONCLASS / DECORATEDCLASS / LISTCLASS

$DBOCreator = new DatabaseObjectCreator('wcf1_gman_wow_character_feed', 'WowCharacterFeed', 'wow\character', '/sghost/wwwroot/sylvanasgarde.com/garde2015/lib', 'wcf');
$DBOCreator->setAuthor("Veneanar Falkenbann");  // Autor
$DBOCreator->setCopyright("2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info"); // copyright z.B. Firmenname
$DBOCreator->setPackage("info.falkenbann.guildman"); // Paketname
$DBOCreator->setObjectName("WoW Charackter mit Feed");      // Objektname, wird im Kommentar benutzt.
$DBOCreator->setLicense($DBOCreator::LICENSE_GPL); // oder LICENSE_LGPL, LICENSE_GPL, LICENSE_PD (Public Domain) oder string für eigene Lizenz
$DBOCreator->execute($DBOCreator::DECORATEDCLASS); // oder BASECLASS/ EDITCLASS / ACTIONCLASS / DECORATEDCLASS / LISTCLASS

$DBOCreator = new DatabaseObjectCreator('wcf1_gman_wow_character_item', 'WowCharacterItem', 'wow\character', '/sghost/wwwroot/sylvanasgarde.com/garde2015/lib', 'wcf');
$DBOCreator->setAuthor("Veneanar Falkenbann");  // Autor
$DBOCreator->setCopyright("2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info"); // copyright z.B. Firmenname
$DBOCreator->setPackage("info.falkenbann.guildman"); // Paketname
$DBOCreator->setObjectName("WoW Charackter mit Items");      // Objektname, wird im Kommentar benutzt.
$DBOCreator->setLicense($DBOCreator::LICENSE_GPL); // oder LICENSE_LGPL, LICENSE_GPL, LICENSE_PD (Public Domain) oder string für eigene Lizenz
$DBOCreator->execute($DBOCreator::DECORATEDCLASS); // oder BASECLASS/ EDITCLASS / ACTIONCLASS / DECORATEDCLASS / LISTCLASS

$DBOCreator = new DatabaseObjectCreator('wcf1_gman_wow_classes', 'WowClass', 'wow\class', '/sghost/wwwroot/sylvanasgarde.com/garde2015/lib', 'wcf');
$DBOCreator->setAuthor("Veneanar Falkenbann");  // Autor
$DBOCreator->setCopyright("2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info"); // copyright z.B. Firmenname
$DBOCreator->setPackage("info.falkenbann.guildman"); // Paketname
$DBOCreator->setObjectName("WoW Klassen");      // Objektname, wird im Kommentar benutzt.
$DBOCreator->setLicense($DBOCreator::LICENSE_GPL); // oder LICENSE_LGPL, LICENSE_GPL, LICENSE_PD (Public Domain) oder string für eigene Lizenz
$DBOCreator->execute($DBOCreator::BASECLASS); // oder BASECLASS/ EDITCLASS / ACTIONCLASS / DECORATEDCLASS / LISTCLASS

$DBOCreator = new DatabaseObjectCreator('wcf1_gman_wow_races', 'WowClass', 'wow\class', '/sghost/wwwroot/sylvanasgarde.com/garde2015/lib', 'wcf');
$DBOCreator->setAuthor("Veneanar Falkenbann");  // Autor
$DBOCreator->setCopyright("2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info"); // copyright z.B. Firmenname
$DBOCreator->setPackage("info.falkenbann.guildman"); // Paketname
$DBOCreator->setObjectName("WoW Rassen");      // Objektname, wird im Kommentar benutzt.
$DBOCreator->setLicense($DBOCreator::LICENSE_GPL); // oder LICENSE_LGPL, LICENSE_GPL, LICENSE_PD (Public Domain) oder string für eigene Lizenz
$DBOCreator->execute($DBOCreator::BASECLASS); // oder BASECLASS/ EDITCLASS / ACTIONCLASS / DECORATEDCLASS / LISTCLASS

$DBOCreator = new DatabaseObjectCreator('wcf1_gman_wow_realm', 'WowClass', 'wow\class', '/sghost/wwwroot/sylvanasgarde.com/garde2015/lib', 'wcf');
$DBOCreator->setAuthor("Veneanar Falkenbann");  // Autor
$DBOCreator->setCopyright("2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info"); // copyright z.B. Firmenname
$DBOCreator->setPackage("info.falkenbann.guildman"); // Paketname
$DBOCreator->setObjectName("WoW Realms");      // Objektname, wird im Kommentar benutzt.
$DBOCreator->setLicense($DBOCreator::LICENSE_GPL); // oder LICENSE_LGPL, LICENSE_GPL, LICENSE_PD (Public Domain) oder string für eigene Lizenz
$DBOCreator->execute($DBOCreator::BASECLASS); // oder BASECLASS/ EDITCLASS / ACTIONCLASS / DECORATEDCLASS / LISTCLASS
$DBOCreator->execute($DBOCreator::LISTCLASS); // oder BASECLASS/ EDITCLASS / ACTIONCLASS / DECORATEDCLASS / LISTCLASS







    /**
	 * name of the base table
	 * @var	string
	 */
    private $baseTable = "gman_wow_character_feed";

    public function __construct(DatabaseObject $object) {
        parent::__construct($object);

    }

    public function update($feed) {
        $sql = "UPDATE  wcf".WCF_N."_example
        SET     bar = ?
        WHERE   exampleID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$feed]);

    }



























?>