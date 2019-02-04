#!/usr/bin/env python

import optparse

class subdomains:
	snippets = {
		"vhost": """<VirtualHost %hostname>
	DocumentRoot %documentroot
</VirtualHost>\r\n"""
	}
	files = {
		"vhost.conf": "/etc/apache2/vhosts.d/00_default_vhost.conf"
	}

def main():
	opt = optparse.OptionParser()
	opt.add_option("-s", "--source", dest="directory", help="Gekko source directory", default="../src")
	opt.add_option("-c", "--check", help="Checks if subdomain mode is enabled, returns a boolean value")
	opt.add_option("-e", "--enable", help="Enables subdomain mode")
	opt.add_option("-d", "--disable", help="Disables subdomain mode")
	opt.add_option("-a", "--add", dest="subdomain", help="Adds a new subdomain")
	opt.add_option("-r", "--remove", dest="subdomain", help="Removes an existing subdomain")

	(options, args) = opt.parse_args()


if __name__ == "__main__":
	main()
