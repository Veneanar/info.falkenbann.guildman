<?php
namespace wcf\system\wow\exception;
use wcf\system\exception\LoggedException;

/**
 * Denotes a authentication failure during API access. It should not be retried later.
 *
 * @author	Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
class AuthenticationFailure extends LoggedException { }
