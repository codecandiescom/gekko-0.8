<?php

	/*
	*	Gekko - Open Source Web Development Framework
	*	------------------------------------------------------------------------
	*	Copyright (C) 2004-2006, J. Carlos Nieto <xiam@users.sourceforge.net>
	*	This program is Free Software.
	*
	*	@package	Gekko
	*	@license	http://www.gnu.org/copyleft/gpl.html GNU/GPL License 2.0
	*	@author		J. Carlos Nieto <xiam@users.sourceforge.net>
	*	@link		http://www.gekkoware.org
	*/


	if (!defined("IN-GEKKO")) die("Get a life!");

	require_once "remote.lib.php";

	/*
		*** ftpSession basic usage example ***
		$ftp = new ftpSession("localhost", 21, "user", "pass");
		$ftp->connect();
		$ftp->login();
		if ($ftp->conn->status()) {

			$ftp->cd("public_html");
			$ftp->chmod("conf.php", 0777);

			header("content-type: image/png");
			echo $ftp->get("me.png");

			$ftp->bye();
		}
	*/

	class ftpSession {

		var $info = array();
		var $conn = false;
		var $logged = false;
		var $error_text = "";

		function ftpSession($host, $port = 21, $user = null, $pass = null) {
			$this->info["host"] = $host;
			$this->info["port"] = $port ? $port : 21;
			$this->info["user"] = $user ? $user : "anonymous";
			$this->info["pass"] = $pass ? $pass : "anonymous@example.com";
		}

		function connect() {
			$this->conn = new remoteConnection($this->info["host"], $this->info["port"]);
			return $this->conn->status();
		}

		// talks with server, expects responses, etc...
		function chat($expects, $answers = null, $avoid_errors = true) {
			$data = $this->conn->read();
			if (substr(trim($data), 0, 3) == $expects) {
				if ($answers) {
					$this->conn->write($answers);
				}
				return true;
			} else {
				if ($avoid_errors) return false;
				trigger_error("S: \"".trim($data)."\", expecting: \"$expects\"", E_USER_ERROR);
			}
		}
		function login() {
			$this->chat("220", "USER ".$this->info["user"]."\r\n");
			$this->chat("331", "PASS ".$this->info["pass"]."\r\n");
			$this->chat("230") or $this->error('FTP_WRONG_PASSWORD');
			return !$this->error();
		}
		function auth() {
			return $this->logged;
		}
		function mkdir($dirname, $mod = "0775") {
			$this->conn->write("MKD $dirname\r\n");
			return ($this->chat("257") && $this->chmod($dirname, $mod));
		}
		function chmod($filename, $mod) {
			$this->conn->write("SITE CHMOD $mod $filename\r\n");
			return $this->chat("200");
		}
		function cd($dirname) {
			$this->conn->write("CWD ".rtrim($dirname, "/")."/\r\n");
			return $this->chat("250");
		}
		function delete($filename) {
			$this->conn->write("DELE $filename\r\n");
			return $this->chat("250");
		}
		function rmdir($dirname) {
			$this->conn->write("RMD $dirname/\r\n");
			return $this->chat("250");
		}
		function mtime($file) {
			$this->conn->write("MDTM $file\r\n");
			if ($this->chat("213")) {
				return substr($this->conn->last(), 4);
			}
			return false;
		}
		function pwd() {
			$this->conn->write("PWD\r\n");

			if ($this->chat("257")) {
				return substr($this->conn->last(), 4);
			}
			return false;
		}
		function rename($oldfile, $newfile) {
			$this->conn->write("RNFR $oldfile\r\n");
			if ($this->chat("350")) {
				$this->conn->write("RNTO $newfile\r\n");
				return $this->chat("250");
			}
			return true;
		}
		// passive mode (server opens a port to data transfer)
		function pasv(&$dataConn) {
			$this->conn->write("PASV\r\n");
			$this->chat("227");

			// where to connect?
			preg_match("/\(([\d,]*)\)/", $this->conn->last(), $match);
			$match = explode(",", $match[1]);

			if (isset($match[5])) {
				$host = "{$match[0]}.{$match[1]}.{$match[2]}.{$match[3]}";
				$port = $match[4]*256 + $match[5];

				$dataConn = new remoteConnection($host, $port);
				if ($dataConn->status()) {
					return true;
				} else {
					trigger_error("Couldn't open data connection to $host:$port.", E_USER_ERROR);
					return false;
				}
			} else {
				gdebug($match);
			}
		}
		function type($type) {
			$this->conn->write("TYPE $type\r\n");
			return $this->chat("200");
		}
		function put($orig, $dest = null) {

			if (!$dest)
				$dest = basename($orig);

			if (file_exists($orig)) {

				//$dataConn = new Object();
				$success = $this->pasv($dataConn);

				if ($success) {


					$cdir = "";
					$dirs = explode("/", dirname($dest));
					foreach ($dirs as $dir) {
						$cdir .= "$dir/";
						$this->chmod($cdir, "0755");
					}

					$this->conn->write("STOR $dest\r\n");
					$this->chat("150");

					$fh = fopen($orig, "r");

					do {
						$dataConn->write(fread($fh, 1024*8));
					} while (!feof($fh));

					$dataConn->close();

					$this->chat("226");

					return true;
				} else {

					trigger_error("Couldn't put file '$orig'", E_USER_ERROR);
					return false;

				}
			} else {
				trigger_error("File $orig doest not exists.", E_USER_ERROR);
			}
		}
		function get($file) {

			// binary transfers
			$this->type("I");

			// passive mode
			$success = $this->pasv($dataConn);

			if ($success) {

				$this->conn->write("RETR $file\r\n");
				$this->chat("150");

				$data = $dataConn->read();
				$dataConn->close();

				return $data;
			} else {

				trigger_error("Couldn't get file '$file'", E_USER_ERROR);

			}
			return false;
		}
		function error($err = null) {
			if ($err) {
				switch ($err) {
					case 'FTP_WRONG_PASSWORD':
						$this->error_text = "Wrong username or password.";
					break;
					case 'FTP_DATA_CONNECTION_ERROR':
						$this->error_text = "Data connection error.";
					break;
					case 'FTP_NO_FILE':
						$this->error_text = "No such file or directory.";
					break;
					$this->error_text = "Unknown error";
				}
			} else {
				return $this->error_text;
			}
		}
		function bye() {
			$this->conn->write("QUIT\r\n");
			$this->conn->close();
		}
	}

	/*
		*** httpSession basic usage example ***

		if (($http = new httpSession("example.com", "80")) !== false) {
			header("content-type: image/png");
			echo $http->get("/somedir/somefile.png");
			$http->close();
		}
	*/
	class httpSession {

		var $userAgent = "Gekko/0.5";
		var $conn, $info, $body, $header, $head;

		function status() {
			return $this->conn->status();
		}

		function httpSession($host, $port = 80, $user = null, $pass = null) {
			$this->info["host"] = $host;
			$this->info["port"] = $port ? $port : 80;
			$this->info["user"] = $user;
			$this->info["pass"] = $pass;
			return $this->open($this->info["host"], $this->info["port"]);
		}
		function open($host, $port) {
			if (($this->conn = new remoteConnection($host, $port)) !== false) {
				return true;
			}
			return false;
		}
		function parseResponse(&$buff) {

			preg_match("/^(.*?)\r\n\r\n(.*?)$/s", $buff, $match);

			if (isset($match[2])) {
				$this->header = $match[1];
				$this->body = $match[2];

				$headlines = explode("\n", $this->header);

				// this->head
				$status = false;
				//$stop = false;
				foreach ($headlines as $header) {
					if (!$status) {
						$status = $header;
						// expecting HTTP/1.1 200 OK
						if (!strpos($status, "200")) {
							return false;
						}
					} else {
						preg_match("/^([^:]*?):\s*(.*?)[\r]$/i", $header, $htmp);
						if (isset($htmp[2]))
							$this->head[strtolower($htmp[1])] = $htmp[2];
					}
				}

				// inflating gzip'ed pages
				if ((isset($this->head["content-encoding"]) && ($this->head["content-encoding"] == "gzip")) || (isset($this->head["vary"]) && strtolower($this->head["vary"]) == "accept-encoding")) {
					// Read http://www.php.net/manual/en/function.gzinflate.php
					$this->body = gzinflate(substr($this->body, 10));
				}
			}
		}
		function sendCommonHeaders() {
			$this->conn->write("Host: ".$this->info["host"].":".$this->info["port"]."\r\n");
			$this->conn->write("User-Agent: ".$this->userAgent."\r\n");
			if ($this->info["user"])
				$this->conn->write("Authorization: Basic ".base64_encode($this->info["user"].":".$this->info["pass"])."\r\n");
			$this->conn->write("Accept: */*\r\n");
			$this->conn->write("Accept-Encoding: gzip,deflate\r\n");
		}
		function post($where, $variables) {

			if (!$this->status())
				return false;

			$buff = "";
			foreach ($variables as $field => $value)
				$buff[] = "$field=".urlencode($value);
			$variables = implode("&", $buff);

			$this->conn->write("POST $where HTTP/1.1\r\n");
			$this->conn->write("Content-Length: ".strlen($variables)."\r\n");
			$this->conn->write("Content-Type: application/x-www-form-urlencoded\r\n");
			$this->sendCommonHeaders();
			$this->conn->write("\r\n");
			$this->conn->write($variables);

			$buff = $this->conn->read(true);

			$this->parseResponse($buff);

			return $this->body;

		}
		function getHeaders() {
			$buff = "";
			do {
				$line = fgets($this->conn->socket);
				$buff .= $line;
				if ($line == "\r\n")
					return $buff;
			} while (!feof($this->conn->socket));
		}
		function get($what) {

			if (!$this->status())
				return false;

			$this->conn->write("GET $what HTTP/1.0\r\n");
			$this->sendCommonHeaders();
			$this->conn->write("\r\n");

			$buff = $this->conn->read(true);

			$this->parseResponse($buff);

			return $this->body;
		}
		function close() {
			$this->conn->close();
		}
	}

	class smtpSession {
		var $conn;
		var $info;

		// Initializing Class
		function smtpSession($host, $port = "25", $user = null, $pass = null) {
			$this->info["host"] = $host;
			$this->info["port"] = $port ? $port : "25";
			$this->info["user"] = $user;
			$this->info["pass"] = $pass;

			if (class_exists("conf")) {
				$this->info["from-mail"] = conf::getkey("core", "site.contact_mail");
				$this->info["from-name"] = conf::getkey("core", "site.contact_mail");
			} else {
				$this->info["from-mail"] = GEKKO_SMTP_FROM_EMAIL;
				$this->info["from-name"] = GEKKO_SMTP_FROM_NAME;
			}

			return $this->connect();
		}
		// Opens a connection with SMTP server
		function connect() {
			$this->conn = new remoteConnection($this->info["host"], $this->info["port"]);
			if ($this->conn->status()) {
				// connected, now saying hello!
				return $this->login();
			} else {
				// there was a connection error for some strange reason
				trigger_error("Couldn't open connection to SMTP server.",E_USER_ERROR);
				return false;
			}
		}

		// Sending creentials
		function login() {

			// Please read rfc0821 (or if you're too lazy just sniff a conversation between your e-mail client
			// and one random smtp server)
			$this->chat("220", "EHLO ".$this->info["host"]."\r\n");
			$ehlo = $this->conn->read();

			// getting server supported auth methods (read rfc2554)
			// http://www.technoids.org/saslmech.html
			if ($this->info["user"] && preg_match_all("/\d{3}-AUTH\s(.*)/", $ehlo, $match) && isset($match[1][0])) {
				$methods = explode(" ", $match[1][0]);

				if (in_array("LOGIN", $methods)) {
					$this->conn->write("AUTH LOGIN\r\n");
					$this->chat("334", base64_encode($this->info["user"])."\r\n");
					$this->chat("334", base64_encode($this->info["pass"])."\r\n");
				} else {
					trigger_error("Unsupported SMTP AUTH type.", E_USER_ERROR);
				}

				if (!$this->chat("235", "", true))
					trigger_error("Incorrect SMTP Username or Password.", E_USER_ERROR);
			} else {
				$this->chat("220", "HELO ".$this->info["host"]."\r\n");
				$this->chat("250");
			}

			return true;
		}
  		// Sends an e-mail
		function send($to, $subject, $message, $content_type = "text/plain", $headers = null) {

			//
			$this->conn->write("MAIL FROM: <".$this->info["from-mail"].">\r\n");

			// can handle multiple recipients sepparated by commas
			$ato = explode(",", $to);
			foreach ($ato as $addr) {
				$this->chat("250", "RCPT TO: <".trim($addr).">\r\n");
			}

			// telling server that the following data is a message
			$this->chat("250", "DATA\r\n");
			$this->chat("354");

			// common headers
			$this->conn->write("Mime-Version: 1.0\r\n");
			$this->conn->write("Content-Type: $content_type\r\n");
			$this->conn->write("Subject: $subject\r\n");
			$this->conn->write("From: ".$this->info["from-name"]." <".$this->info["from-mail"].">\r\n");
			$this->conn->write("To: <".$to.">\r\n");
			$this->conn->write("Date: ".date("r")."\r\n");
			$this->conn->write("X-Mailer: Gekko/".GEKKO_VERSION."\r\n");
			$this->conn->write("X-Gekko-Tag: ".(isset($GLOBALS["USER"]["id"]) ? $GLOBALS["USER"]["id"] : "?")."@".IP_ADDR."\r\n");

			if (GEKKO_SMTP_EXTRA_HEADERS)
				$headers .= GEKKO_SMTP_EXTRA_HEADERS;

			if ($headers)
				$this->conn->write($headers);

			// message body
			$this->conn->write("\r\n");
			$this->conn->write($message);
			$this->conn->write("\r\n.\r\n");
			// expecting confirmation
			$this->chat("250");
		}

		/**
		* chat(expecting, answer, hide_errors);
		* Sends $answer to server after receiving the expected answer
		*/
		function chat($expecting, $answer = null, $hide_errors = false) {
			// reading what server is saying
			$data = $this->conn->read();
			// checking if this was an expected response code (first 3 numbers)
			if (substr(trim($data), 0, 3) == $expecting) {
				if ($answer)
					$this->conn->write($answer);
				return true;
			} else {
				if ($hide_errors)
					return false;
				trigger_error("S: \"".trim($data)."\", expecting: \"$expecting\"", E_USER_ERROR);
			}
		}
		// closing connection
		function bye() {
			$this->conn->write("QUIT\r\n");
			$this->conn->close();
		}
	}
	function sendmail($to, $subject, $message, $content_type = "text/plain", $headers = null) {
		// preventing possible spam attacks
		$to = trim(preg_replace("/[\r|\n](.*?)/", "", $to));
		$subject = trim(preg_replace("/[\r|\n](.*?)/", "", $subject));
		$message = trim(preg_replace("/[\r|\n]\.[\r|\n](.*?)/", "", $message));

		if (conf::getkey("core", "smtp.enable")) {

			$smtp = new smtpSession(conf::getkey("core", "smtp.host"), conf::getkey("core", "smtp.port"), conf::getkey("core", "smtp.user"), conf::getkey("core", "smtp.pass"));
			if ($smtp->conn->status()) {
				$smtp->send($to, $subject, $message, $content_type, $headers);
			}
			$smtp->bye();

		} else {

			if (!$headers)
				$headers = "";

			$headers .= "Mime-Version: 1.0\r\n";
			$headers .= "Content-Type: $content_type\r\n";
			$headers .= "From: ".conf::getkey("core", "site.name")." <".conf::getkey("core", "site.contact_mail").">\r\n";
			$headers .= "X-Gekko-Tag: ".(isset($GLOBALS["USER"]["id"]) ? $GLOBALS["USER"]["id"] : "0")."@".getIP()."\r\n";
			$headers .= "Date: ".date("r")."\r\n";

			mail($to, $subject, $message, trim($headers));
		}

	}

?>
