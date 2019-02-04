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

	// connection timeout
	define("GEKKO_REMOTE_CONN_TIMEOUT", 3);

	// response timeout
	define("GEKKO_REMOTE_READ_TIMEOUT", 4);

	// if you're using Gekko's cache.lib.php you should specify how many seconds
	// will Gekko wait before try to connect again
	define("GEKKO_REMOTE_CONN_CACHE_LIFE", 300);

	Class remoteConnection {

		/**
		* TODO: Error handling functions
		*/

		var $socket;

		var $errno;
		var $errstr;

		var $last_response;

		var $status = 0;

		/**
		* remoteConnection(host, port);
		* Opens a connection to the specific port and host
		*/
		function remoteConnection($host, $port) {
			$this->open($host, $port);
		}

		/**
		* open(host, port);
		* @return: true if connection was successful, false otherwise
		*/
		function open($host, $port) {

			$conn_status = 1;

			if (function_exists("cacheAssignKey")) {
				// checkig if we had a successful connection with the same server and the same port
				// whithin the last GEKKO_REMOTE_TIMEOUT seconds
				$key = cacheAssignKey($host, $port);
				if ((($life = cacheCheckLifetime($key)) !== false) && ((time() - $life) < GEKKO_REMOTE_CONN_CACHE_LIFE)) {
					// if we had a successful connection we put a 1 in a cached file, this is for
					// avoiding wasting time trying to connect to a specific server when it is not
					// responding, we can check it later
					cacheRead($key, $conn_status);
				}
			}

			// trying to connect if the last try was successful of if this is the first
			if ($conn_status)
				$this->socket = @fsockopen($host, $port, $this->errno, $this->errstr, GEKKO_REMOTE_CONN_TIMEOUT);
			
			if (!$this->socket)		
				appSetMessage("error", "The connection to ".htmlspecialchars($host).":".htmlspecialchars($port)." couldn't be opened.");

			// saving connection status
			if (function_exists("cacheSave") && ($conn_status != $this->status()))
				cacheSave($key, intval($this->status()));

			return ($conn_status) ? $this->status() : false;
		}

		/**
		* status();
		* @return: true if there is an active connection, false otherwise
		*/
		function status() {
			return is_resource($this->socket);
		}

		/**
		* write(raw_data);
		* @return: -1 if there is no active connection.
		* Sends raw data to the active server
		*/
		function write($data) {

			if (!$this->status())
				return -1;

			$this->debug("C", $data);

			// sending response to server
			return fwrite($this->socket, $data);
		}

		/**
		* debug(id, string);
		* Prints the given string in such a way to help debugging network applications, $id
		* could be "S" or "C" for indicating lines sent by the Server and Client
		* respectively.
		*/
		function debug($id, $string) {
			if (GEKKO_ENABLE_DEBUG  == true)
				appWriteLog("remote.lib.php: $id >> ".trim($string));
		}

		/**
		* last([raw response])
		* Sets or returns the last server response.
		* @return: last stored server response
		*/
		function last($response = null) {
			if ($response)
				$this->last_response = $response;

			return $this->last_response;
		}

		/**
		* read([until_eof]);
		* Reads server responses, if $until_eof is set to true then this function stores
		* what the server is saying until it closes the connection, an error occurrs or
		* there are no more unread bytes.
		* @return: raw server response
		*/
		function read($until_eof = false) {

			// main buffer
			$data = "";

			if ($this->status()) {

				stream_set_timeout($this->socket, GEKKO_REMOTE_READ_TIMEOUT);

				do {
					$buff = fread($this->socket, 1024);

					$data .= $buff;

					$meta = stream_get_meta_data($this->socket);

					if ($until_eof == false && $meta["unread_bytes"] == 0)
						break;
					if ($meta["timed_out"])
						break;

				} while ($meta["eof"] == false);

				$this->debug("S", $this->last($data));
			}

			return $data;

		}

		/**
		* close();
		* Closes a connection
		*/
		function close() {
			if ($this->status())
				fclose($this->socket);
		}
	}
?>
