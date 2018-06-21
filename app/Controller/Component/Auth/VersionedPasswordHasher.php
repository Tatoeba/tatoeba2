<?php
App::uses('AbstractPasswordHasher', 'Controller/Component/Auth');
App::uses('Security', 'Utility');

class VersionedPasswordHasher extends AbstractPasswordHasher {

	const LATEST_VERSION = 1;

	public function hash($password) {
		return '1 '.Security::hash($password, 'blowfish');
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
		if ($hashVersion == 0) {
			$plainTextPassword = md5(Configure::read('Security.salt') . $plainTextPassword);
		}
		$calculatedHash = Security::hash($plainTextPassword, 'blowfish', $storedHash);
		return $storedHash === $calculatedHash;
	}

}
