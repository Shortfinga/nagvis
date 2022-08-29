<?php
/*****************************************************************************
 *
 * CoreLogonMultisite.php - Module for handling cookie based logins as
 *                          generated by multisite
 *
 * Copyright (c) 2004-2016 NagVis Project (Contact: info@nagvis.org)
 *
 * License:
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *
 *****************************************************************************/

class CoreLogonMultisite extends CoreLogonModule {
    private $htpasswdPath;
    private $serialsPath;
    private $secretPath;
    private $authFile;

    public function __construct() {
        $this->htpasswdPath  = cfg('global', 'logon_multisite_htpasswd');
        $this->serialsPath   = cfg('global', 'logon_multisite_serials');
        $this->secretPath    = cfg('global', 'logon_multisite_secret');
        $this->cookieVersion = cfg('global', 'logon_multisite_cookie_version');

        // When the auth.serial file exists, use this instead of the htpasswd
        // for validating the cookie. The structure of the file is equal, so
        // the same code can be used.
        if(file_exists($this->serialsPath)) {
            $this->authFile = 'serial';

        } elseif(file_exists($this->htpasswdPath)) {
            $this->authFile = 'htpasswd';

        } else {
            throw new NagVisException(l('LogonMultisite: The htpasswd file &quot;[HTPASSWD]&quot; or '
                                       .'the authentication serial file &quot;[SERIAL]&quot; do not exist.',
                          array('HTPASSWD' => $this->htpasswdPath, 'SERIAL' => $this->serialsPath)));
        }

        if(!file_exists($this->secretPath)) {
            $this->redirectToLogin();
        }
    }

    private function loadAuthFile($path) {
        $creds = array();
        foreach(file($path) AS $line) {
            if(strpos($line, ':') !== false) {
                list($username, $secret) = explode(':', $line, 2);
                $creds[$username] = rtrim($secret);
            }
        }
        return $creds;
    }

    private function loadSecret() {
        return trim(file_get_contents($this->secretPath));
    }

    private function generateHash($username, $session_id, $user_secret) {
        $secret = $this->loadSecret();
        return hash_hmac("sha256", $username . $session_id. $user_secret, $secret);
    }

    private function generatePre22Hash($username, $session_id, $user_secret) {
        $secret = $this->loadSecret();
        return hash("sha256", $username . $session_id. $user_secret . $secret);
    }

    private function generatePre20Hash($username, $issue_time, $user_secret) {
        $secret = $this->loadSecret();
        return md5($username . $issue_time . $user_secret . $secret);
    }

    private function checkAuthCookie($cookieName) {
        if(!isset($_COOKIE[$cookieName]) || $_COOKIE[$cookieName] == '') {
            throw new Exception();
        }

        // Checkmk 1.6+ may add double quotes round the value in some cases
        // (e.g. when @ signs are found in the value)
        $cookieValue = trim($_COOKIE[$cookieName], '"');

        // 2nd field is "issue time" in pre 2.0 cookies. Now it's the session ID
        list($username, $sessionId, $cookieHash) = explode(':', $cookieValue, 3);

        if($this->authFile == 'htpasswd')
            $users = $this->loadAuthFile($this->htpasswdPath);
        else
            $users = $this->loadAuthFile($this->serialsPath);

        if(!isset($users[$username])) {
            throw new Exception();
        }
        $user_secret = $users[$username];

	if ($this->cookieVersion < 1) {
	    // Older Checkmk versions do not set the cookieVersion, therefore we guess based on the length.

            // Checkmk 2.0 changed the following:
            // a) 2nd field from "issue time" to session ID
            // b) 3rd field from md5 hash to sha256 hash
            // NagVis is used with older and newer Checkmk versions. Be compatible
            // to both cookie formats.
            $is_pre_20_cookie = strlen($cookieHash) == 32;

            if ($is_pre_20_cookie)
                $hash = $this->generatePre20Hash($username, $sessionId, (string) $user_secret);
            else
                $hash = $this->generatePre22Hash($username, $sessionId, (string) $user_secret);
	}
	elseif ($this->cookieVersion == 1) {
            $hash = $this->generateHash($username, $sessionId, (string) $user_secret);
	}
	else {
            throw new NagVisException(l('The Multisite Cookie version is not supported'));
	}

        // Validate the hash
        if ($cookieHash !== $hash) {
            throw new Exception();
        }

        return $username;
    }

    private function checkAuth() {
        // Loop all cookies trying to fetch a valid authentication
        // cookie for this installation
        foreach(array_keys($_COOKIE) AS $cookieName) {
            if(substr($cookieName, 0, 5) != 'auth_') {
                continue;
            }
            try {
                $name = $this->checkAuthCookie($cookieName);

                session_start();
                $_SESSION['multisiteLogonCookie'] = $cookieName;
                session_write_close();

                return $name;
            } catch(Exception $e) {}
        }
        return '';
    }

    private function redirectToLogin() {
        // Do not redirect on ajax calls. Print out errors instead
        if(CONST_AJAX) {
            throw new NagVisException(l('LogonMultisite: Not authenticated.'));
        }
        // FIXME: Get the real path to multisite
        header('Location:../../../check_mk/login.py?_origtarget=' . urlencode($_SERVER['REQUEST_URI']));
    }

    public function check($printErr = true) {
        global $AUTH, $CORE;

        // Try to auth using the environment auth
        $ENV= new CoreLogonEnv();
        if($ENV->check(false) === true) {
            return true;
        }

        $username = $this->checkAuth();
        if($username === '') {
            $this->redirectToLogin();
            return false;
        }

        // Check if the user exists
        if($this->verifyUserExists($username,
                        cfg('global', 'logon_multisite_createuser'),
                        cfg('global', 'logon_multisite_createrole'),
                        $printErr) === false) {
            return false;
        }

        $AUTH->setTrustUsername(true);
        $AUTH->setLogoutPossible(false);
        $AUTH->passCredentials(Array('user' => $username));
        return $AUTH->isAuthenticated();
    }
}

?>
