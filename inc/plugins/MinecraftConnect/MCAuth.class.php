<?php
/**
 * Class MCAuth (Minecraft Connect for MyBB)
 *
 * Integrate Minecraft authentication with your MyBB applications.
 * Original MCAuth class created by Mattia Basone. Edited for MyBB
 * integration by Mike V.
 *
 * @author Mattia Basone (mattia.basone@gmail.com)
 * @author Mike V. (https://github.com/squez/)
 * @package MCAuth (Minecraft Connect for MyBB)
 * @version 1.0
 * @copyright 2013-2015 Mattia Basone, 2016 Mike V.
 * @link https://github.com/mattiabasone/MCAuth
 */
class MCAuth {

    #const CLIENT_TOKEN  = '808772fc24bc4d92ba2dc48bfecb375f';          // Removed; client token is the same as account['id']
    const AUTH_URL      = 'https://authserver.mojang.com/authenticate'; // Mojang authentication server URL
    const PROFILE_URL   = 'https://api.mojang.com/users/profiles/minecraft/';     // Profile page
    const HASPAID_URL   = 'https://www.minecraft.net/haspaid.jsp?user='; // Old but gold, check if user is premium
    const USER_AGENT    = 'MinecraftConnect v0.6 (https://github.com/squez/)'; // User Agent used for requests

    public $autherr, $account = array();
    private $curlresp, $curlinfo, $curlerror;
    private $clientToken = '', $usernameInput = '';

    /**
    * Create new object and set $usernameInput.
    *
    * @param  string  $username
    * @return void
    */
    public function __construct($username)
    {
        $this->usernameInput = $username;
    }

    /**
    * Validate username/email input from form.
    *
    * @access public
    * @return bool
    */
    public function validateInput()
    {
        global $lang;

        $username = $this->usernameInput;
        if(strlen($username) < 1)
        {
            $this->autherr = 'Invalid username/email.';
            return false;
        }

        if(!filter_var($username, FILTER_VALIDATE_EMAIL)) // $username is actually a username, not an email
        {
            $check = $this->username2uuid($username);
            if($check != false)
            {
                $this->setClientToken($check);
                return true;
            }
            $this->autherr = $lang->mcc_invalid_username;
            return false;
        }
        else // $username is actually an email so we'll leave the client token blank
        {
            $this->setClientToken('');
            return true;
        }
    }

    /**
    * Set client token.
    *
    * @access public
    * @param  string  $token
    * @return void
    */
    public function setClientToken($token)
    {
        $this->clientToken = $token;
    }

    /**
    * Retrieve client token.
    *
    * @access public
    * @return string
    */
    public function getClientToken()
    {
        return $this->clientToken;
    }

    /**
    * Retrieve access token.
    *
    * @access public
    * @return string
    */
    public function getAccessToken()
    {
        return $this->account['token'];
    }

    /**
     * Generic function for cURL requests
     *
     * @access private
     * @param string $url
     * @return bool
     */
    private function curl_request($url) {
        $request = curl_init();
        curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($request, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($request, CURLOPT_URL, $url);
        $response = curl_exec($request);
        $this->curlinfo = curl_getinfo($request);
        $this->curlerror = curl_error($request);
        $this->curlresp = (string) $response;
        curl_close($request);
        if ($this->curlinfo['http_code'] == '200') {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Execute a POST request with JSON data
     *
     * @access private
     * @param $url
     * @param $array
     * @return bool
     */
    private function curl_json($url, $array) {
        $request = curl_init();
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($request, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($request, CURLOPT_HTTPHEADER , array('Content-Type: application/json'));
        curl_setopt($request, CURLOPT_POST, TRUE);
        curl_setopt($request, CURLOPT_POSTFIELDS, json_encode($array));
        curl_setopt($request, CURLOPT_URL, $url);
        $response = curl_exec($request);
        $this->curlinfo = curl_getinfo($request);
        $this->curlerror = curl_error($request);
        $this->curlresp = json_decode($response);
        curl_close($request);
        if ($this->curlinfo['http_code'] == '200') {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Allowed characters for username
     *
     * @access private
     * @param $username
     * @return bool
     */
    private function valid_username($username) {
        if (preg_match('#[^a-zA-Z0-9_]+#', $username)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Check if a string is an email address
     *
     * @access private
     * @param $email
     * @return bool
     */
    public function valid_email($email) {
        if ( preg_match('#^[a-zA-Z0-9\.\_\%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,8}$#', $email) == 1 ) {
            return false;
        }
        return true;
    }

    /**
     * Check if username is premium
     *
     * @access public
     * @param $username
     * @return bool
     */
    public function check_premium($username) {
        if ($this->curl_request(self::HASPAID_URL.$username)) {
            if ($this->curlresp == 'true') {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Authentication with given credentials
     *
     * @access public
     * @param $username
     * @param $password
     * @return bool
     */
    public function authenticate($username, $password) {
        // json array for POST authentication
        $json = array();
        $json['agent']['name'] = 'Minecraft';
        $json['agent']['version'] = 1;
        $json['username'] = $username;
        $json['password'] = $password;
        #$json['clientToken'] = self::CLIENT_TOKEN;
        $json['clientToken'] = $this->clientToken;
        if ($this->curl_json(self::AUTH_URL, $json)) {
            if (!isset($this->curlresp->error) AND isset($this->curlresp->selectedProfile->name)) {
                $this->account['id'] = $this->curlresp->selectedProfile->id;
                $this->account['username'] = $this->curlresp->selectedProfile->name;
                $this->account['token'] = $this->curlresp->accessToken;
                $this->autherr = 'OK';
                if(strlen($this->clientToken) != 32)
                    $this->setClientToken($this->account['id']);

                return TRUE;
            } else {
                $this->autherr = $this->curlresp->errorMessage . '('.$this->curlresp->error.')';
            }
        } else {
            if (isset($this->curlresp->error)) {
                $this->autherr = $this->curlresp->errorMessage . '('.$this->curlresp->error.')';
            } else {
                if (isset($this->curlerror)) {
                    $this->autherr = $this->curlerror;
                } else {
                    $this->autherr = 'Server unreacheable';
                }
            }
        }
        return FALSE;
    }

    /**
    * Log the user into MyBB with their MC credentials.
    * Must be authenticated with authenticate() first!
    *
    * @access public
    * @param  $username
    * @return bool
    */
    public function login($username)
    {
        global $mybb, $db, $session;

        if(!isset($username))
            $username = $this->getUsername();

        $q = $db->simple_select('users', '*', "mcc_username = '$username'");
        if($db->num_rows($q) == 1)
        {
            $user = $db->fetch_array($q);
            if(!$user['uid'])
                return false;

            // Delete all the old sessions from user's IP address
            $db->delete_query('sessions', "ip='".$db->escape_string($session->ipaddress)."' AND sid != '{$session->sid}'");

            // Create a new session
            $db->update_query('sessions', array('uid'=>$user['uid']), "sid='{$session->sid}'");
            
            // Set login cookies
            my_setcookie('mybbuser', $user['uid'] . '_' . $user['loginkey'], null, true);
            my_setcookie('sid', $session->sid, -1, true);
            
            return true;
        }
        return false;
    }

    /**
     * Utility: Get correct username and minecraft id from username (NOT email, case insensitive)
     *
     * @access public
     * @param $username
     * @return bool
     */
    public function get_user_info($username) {
        if ($this->valid_username($username)) {
            if ($this->curl_request(self::PROFILE_URL.urlencode($username))) {
                $response = json_decode($this->curlresp, true);
                if (isset($response['id']) && isset($response['name'])) {
                    $this->account['id'] = $response['id'];
                    $this->account['username'] = $response['username'];
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Utility: Convert username to UUID
     *
     * @access public
     * @param $username
     * @return bool
     */
    public function username2uuid($username) {
        if ($this->get_user_info($username)) {
            return $this->account['id'];
        }
        return false;
    }

    /**
    * Retrieve the user's username.
    *
    * @access public
    * @return string
    */
    public function getUsername()
    {
        return $this->account['username'];
    }

    /**
    * Retrieve the user's id (same as client token)
    *
    * @access public
    * @return string
    */
    public function getID()
    {
        return $this->account['id'];
    }

    /**
    * Retrieve any authentication/curl errors.
    *
    * @access public
    * @return string
    */
    public function getErr()
    {
        #return $this->autherr . 'client token: ##' . $this->clientToken .'##' . ' account token: @@' . $this->account['token'] . '@@';
        return $this->autherr;
    }
}