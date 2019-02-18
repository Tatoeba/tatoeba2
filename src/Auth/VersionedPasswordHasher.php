<?php
namespace App\Auth;

use Cake\Auth\AbstractPasswordHasher;
use Cake\Core\Configure;
use Cake\Utility\Security;

class VersionedPasswordHasher extends AbstractPasswordHasher {

	const LATEST_VERSION = 1;

	public function hash($password) {
		return '1 '.password_hash($password, PASSWORD_BCRYPT);
	}

	public function isOutdated($hash) {
		$versionedHash = explode(' ', $hash, 2);
		if (count($versionedHash) != 2) {
			return true;
		} else {
			return $versionedHash[0] < self::LATEST_VERSION;
		}
	}

	public function check($plainTextPassword, $storedHash) {
		$versionedHash = explode(' ', $storedHash, 2);
		if (count($versionedHash) != 2) {
			return false;
		}

		list($hashVersion, $storedHash) = $versionedHash;
		$salt = Configure::read('Security.oldSalt', Security::getSalt());
		// help mitigate timing attacks by computing md5 regardless of $hashVersion
		$V0hash = md5($salt . $plainTextPassword);
		if ($hashVersion == 0) {
			$plainTextPassword = $V0hash;
		}
		return password_verify($plainTextPassword, $storedHash);
	}

}
