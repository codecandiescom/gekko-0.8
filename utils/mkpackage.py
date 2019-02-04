#!/usr/bin/python

# mkpackage.py - The Gekko package creation tool
# Licensed under GNU/GPL
# Written by j. carlos nieto <xiam@users.sourceforge.net>

import os, sys, shutil, string

from getopt import gnu_getopt, GetoptError

def usage():
	print """The Gekko package creation tool

Authors:
	J. Carlos Nieto <xiam@users.sourceforge.net>

Usage """+sys.argv[0]+""" [options]

	-r, --release       Creates distribution packages
	                    example: --release 0.7.1

	-m, --module        Creates a module package
	                    example: --module users --version 0.7

	-i, --iconset       Creates an iconset package
	                    example: --iconset nuvola --version 0.1

	-l, --language      Creates a language distribution
	                    example: --language es --version 1.0
	
	-t, --template		Creates a template distribution (a style 
						named 'default' is mandatory)
						example: --template tao --version 1.0
	
	-c, --smileyset     Creates a smiley set
	                    example: --smileyset ichat --version 0.7
	
	-s, --style         Creates a style package
	                    example: --style blackhat --base tao --version 1.0
	
	-h, --help          You're reading me, baby!
	                    example: You don't need an example, do you ;)?

Bugs:
	This tool will not work under Microsoft Windows.

Fact:
	We don't care ;)
"""

def version():
	print """Version 0.1"""

def getargs():

	s_opt = 'r:i:m:t:l:c:s:b:v:f:h'
	l_opt = ('release=', 'iconset=', 'module=', 'template=', 'language=', 'smileyset=', 'style=', 'base=', 'version=', 'force', 'help')

	try:
		opts, args = gnu_getopt(sys.argv[1:], s_opt, l_opt)
	except GetoptError:
		usage();
		sys.exit(2)

	package = {}

	for opt, arg in opts:
		if opt in ('-h', '--help'):
			usage()
			sys.exit(0)
		if opt in ('-r', '--release'):
			package['release'] = arg
		if opt in ('-f', '--force'):
			package['force'] = True
		if opt in ('-i', '--iconset'):
			package['iconset'] = arg
		if opt in ('-t', '--template'):
			package['template'] = arg
		if opt in ('-m', '--module'):
			package['module'] = arg
		if opt in ('-l', '--language'):
			package['language'] = arg
		if opt in ('-c', '--smileyset'):
			package['smileyset'] = arg
		if opt in ('-b', '--base'):
			package['base'] = arg
		if opt in ('-s', '--style'):
			package['style'] = arg
		if opt in ('-v', '--version'):
			package['version'] = arg

	return package

class package:
	def __init__(self):
		self.enable_debug = False

	def debug(self, string):
		if self.enable_debug:
			print "* "+string

	def compress(self, orig, pkg_name, dest):
		os.popen("cd "+orig+" && tar -czf ../"+pkg_name+" * && mv ../"+pkg_name+" "+dest).read()

	def md5sum(self, file):
		return string.strip(os.popen('md5sum '+file).read())

	def copy(self, orig, dest):
		if os.path.isdir(orig):
			if os.path.basename(orig) != '.svn':
				if os.path.exists(dest) == False:
					self.debug("creating directory "+dest)
					os.mkdir(dest)
				files = os.listdir(orig)
				for file in files:
					self.copy(orig+'/'+file, dest+'/'+file)
			else:
				self.debug("skipping .svn directory")
		else:
			self.debug("copying "+orig+" -> "+dest+"")
			shutil.copy(orig, dest)

def main():
	if sys.argv[1:]:
		opts = getargs()
		packager = package()
		
		pkg_src = '../src'
		pkg_destdir = os.getcwd()+'/releases'
		pkg_format = '.tgz'
		pkg_tempdir = '/tmp/gekko/'

		if os.path.exists(pkg_tempdir) == False:
			os.mkdir(pkg_tempdir)

		modules = []
		for test in os.listdir(pkg_src+'/modules/'):
			if test[0] != '.' and os.path.isdir(pkg_src+'/modules/'+test):
				modules.append(test)	
		
		if ('release') in opts:
			
			if ('force') not in opts:
				gekko_version = string.strip(os.popen('grep define.*GEKKO_VERSION '+pkg_src+'/conf.php | sed s/[^0-9.]//g').read())
				if gekko_version != opts['release']:
					print "The specified version ("+opts['release']+") doesn't match Gekko's version ("+gekko_version+"). Use --force to override."
					sys.exit(2)

			core_modules = [
				'admin',
				'blocks',
				'conf',
				'groups',
				'menu-editor',
				'modules',
				'packages',
				'users',
				'search'
			]

			distributions = {}

			distributions['blog'] = [
				'backup',
				'blog',
				'categories',
				'comments',
				'contact',
				'gallery',
				'glossary',
				'files',
				'statistics',
				'extensions',
				'pages'
			]

			distributions['portal'] = [
				'backup',
				'blog',
				'categories',
				'comments',
				'contact',
				'downloads',
				'extensions',
				'files',
				'forums',
				'gallery',
				'glossary',
				'calendar',
				'messages',
				'news',
				'pages',
				'polls',
				'statistics'
			]

			distributions['vhost'] = [
				'blog',
				'categories',
				'comments',
				'contact',
				'extensions',
				'gallery',
				'glossary',
				'pages',
				'statistics'
			]
			
			module_packages = {}

			for module in modules:
				if os.path.exists(pkg_src+'/modules/'+module+'/package.xml'):
					module_version = string.strip(os.popen('grep "<version>" '+pkg_src+'/modules/'+module+'/package.xml | sed s/"[^0-9.]"//g').read())
					
					print string.strip(os.popen(sys.argv[0]+" --module "+module+" --version "+module_version).read())
					
					module_packages[module] = module+'-'+module_version
				else:
					print "The given module doesn't has a package.xml file!"
					sys.exit(0)

			pkg_ext = '.tar.gz'
			pkg_name = 'gekko-'+opts['release']
			pkg_dest = pkg_tempdir+pkg_name
			if os.path.exists(pkg_dest):
				shutil.rmtree(pkg_dest)
			os.mkdir(pkg_dest)

			for file in os.listdir(pkg_src+'/../'):
				if os.path.isdir(pkg_src+'/../'+file):
					if file == 'src':
						os.mkdir(pkg_dest+'/src')
						
						for script in os.listdir(pkg_src+'/../src'):
							
							if os.path.isdir(pkg_src+'/../src/'+script):

								if script == 'modules':
									continue

								if script == 'data' or script == 'temp':
									os.mkdir(pkg_dest+'/src/'+script)
									packager.copy(pkg_src+'/../src/'+script+'/.htaccess', pkg_dest+'/src/'+script+'/.htaccess')
									os.chmod(pkg_dest+'/src/'+script, 0777)
									continue

								if script == 'templates':
									
									os.mkdir(pkg_dest+'/src/templates')
									packager.copy(pkg_src+'/../src/templates/index.html', pkg_dest+'/src/templates/index.html')
									packager.copy(pkg_src+'/../src/templates/.htaccess', pkg_dest+'/src/templates/.htaccess')
									
									for i in ('default', 'tao'):

										os.mkdir(pkg_dest+'/src/templates/'+i)
								
										for d in os.listdir(pkg_src+'/../src/templates/'+i):
											if d == '_themes':
												os.mkdir(pkg_dest+'/src/templates/'+i+'/_themes')
												packager.copy(pkg_src+'/../src/templates/'+i+'/_themes/default', pkg_dest+'/src/templates/'+i+'/_themes/default')
											else:
												packager.copy(pkg_src+'/../src/templates/'+i+'/'+d, pkg_dest+'/src/templates/'+i+'/'+d)
									
									continue

								if script == 'media':
									os.mkdir(pkg_dest+'/src/media/')
									for media in os.listdir(pkg_src+'/../src/media'):
										if media == 'icons' or media == 'smileys':
											if os.path.exists(pkg_dest+'/src/media/'+media) == False:
												os.mkdir(pkg_dest+'/src/media/'+media)
											packager.copy(pkg_src+'/../src/media/'+media+'/default', pkg_dest+'/src/media/'+media+'/default')
											continue

										packager.copy(pkg_src+'/../src/media/'+media, pkg_dest+'/src/media/'+media)
									continue

								if script == 'lang':
									os.mkdir(pkg_dest+'/src/lang')
									packager.copy(pkg_src+'/../src/lang/codes.php', pkg_dest+'/src/lang/codes.php')
									packager.copy(pkg_src+'/../src/lang/es', pkg_dest+'/src/lang/es')
									packager.copy(pkg_src+'/../src/lang/en', pkg_dest+'/src/lang/en')
									packager.copy(pkg_src+'/../src/lang/.htaccess', pkg_dest+'/src/lang/.htaccess')
									continue

								if script == 'install':
									os.mkdir(pkg_dest+'/src/install')
									for media in os.listdir(pkg_src+'/../src/install'):
										if media == 'lang':
											os.mkdir(pkg_dest+'/src/install/lang')
											packager.copy(pkg_src+'/../src/install/lang/es', pkg_dest+'/src/install/lang/es')
											packager.copy(pkg_src+'/../src/install/lang/en', pkg_dest+'/src/install/lang/en')
											continue
										packager.copy(pkg_src+'/../src/install/'+media, pkg_dest+'/src/install/'+media)
									continue

								packager.copy(pkg_src+'/../src/'+script, pkg_dest+'/src/'+script)
							else:
								packager.copy(pkg_src+'/../src/'+script, pkg_dest+'/src/'+script)

						continue
					if file == 'utils':
						os.mkdir(pkg_dest+'/utils')
						for util in os.listdir(pkg_src+'/../utils'):
							if util == 'releases':
								os.mkdir(pkg_dest+'/utils/releases')
								continue
							packager.copy(pkg_src+'/../utils/'+util, pkg_dest+'/utils/'+util)
						continue
					if file == 'doc':
						continue
					if file == 'playground':
						continue
				packager.copy(pkg_src+'/../'+file, pkg_dest+'/'+file)

			packager.copy('/dev/null', pkg_dest+'/src/dbconf.php')
			os.chmod(pkg_dest+'/src/dbconf.php', 0777)
			os.chmod(pkg_dest+'/src/temp', 0777)
			os.chmod(pkg_dest+'/src/data', 0777)
			
			sizes = ['16', '48']
			for size in sizes:
				for icon in os.listdir(pkg_dest+'/src/media/icons/default/'+size):
					for module in modules:
						if module == icon[0:len(module)]:
							if os.path.isdir(pkg_dest+'/src/media/icons/default/'+size+'/'+icon):
								shutil.rmtree(pkg_dest+'/src/media/icons/default/'+size+'/'+icon)
							else:
								os.unlink(pkg_dest+'/src/media/icons/default/'+size+'/'+icon)
	
			for module in core_modules:
				os.popen("tar -xzf "+pkg_destdir+"/"+module_packages[module]+".module.tgz -C "+pkg_dest+"/src").read()

			pkg_name = pkg_name+pkg_ext

			os.popen("cd "+pkg_dest+"/src && sed  s/'define(.*GEKKO_ENABLE_DEBUG.*'/'define(\"GEKKO_ENABLE_DEBUG\", false);'/g conf.php > conf.tmp && mv conf.tmp conf.php").read()
			os.popen("cd "+pkg_dest+"/../ && tar -czf "+pkg_name+" "+os.path.basename(pkg_dest)+" && mv "+pkg_name+" "+pkg_destdir).read()
			print packager.md5sum('./releases/'+pkg_name)
			shutil.rmtree(pkg_dest)

			for dist in distributions:
				pkg_dist_name = 'gekko-'+dist+'-'+opts['release']
				
				os.popen("cd "+pkg_destdir+" && rm -rf gekko-"+opts['release']+" && tar -xvzf "+pkg_name+" && mv gekko-"+opts['release']+" "+pkg_dist_name).read()
				
				for module in distributions[dist]:
					os.popen("cd "+pkg_destdir+" && tar -xzf "+module_packages[module]+".module.tgz -C "+pkg_dist_name+"/src").read()
				
				os.popen("cd "+pkg_destdir+" && tar -czf "+pkg_dist_name+pkg_ext+" "+pkg_dist_name).read()
				
				print packager.md5sum('./releases/'+pkg_dist_name+pkg_ext)
				
				shutil.rmtree('./releases/'+pkg_dist_name)

		elif ('version') in opts:
			pkg_name = ''
			
			for opt in opts:
				if opt == 'release' or opt == 'language' or opt == 'module' or opt == 'stylesheet' or opt == 'smileyset' or opt == 'iconset' or opt == 'template' or opt == 'style':
					pkg_name = opts[opt]+'-'+opts['version']
					pkg_ext = '.'+opt+pkg_format
					pkg_target = opt
					pkg_basename = opts[opt]
					pkg_compress = True

			if pkg_name:
				pkg_dest = pkg_tempdir+pkg_name

				if os.path.exists(pkg_dest):
					shutil.rmtree(pkg_dest)
				os.mkdir(pkg_dest)

				if pkg_target == 'language':
					if os.path.exists(pkg_src+'/lang/'+pkg_basename):
						os.mkdir(pkg_dest+'/lang')
						os.mkdir(pkg_dest+'/lang/'+pkg_basename)
						packager.copy(pkg_src+'/lang/'+pkg_basename, pkg_dest+'/lang/'+pkg_basename)
					
						os.mkdir(pkg_dest+'/modules')
						for module in modules:
							os.mkdir(pkg_dest+'/modules/'+module)
							os.mkdir(pkg_dest+'/modules/'+module+'/lang/')
							packager.copy(pkg_src+'/modules/'+module+'/lang/'+pkg_basename, pkg_dest+'/modules/'+module+'/lang/'+pkg_basename)
					else:
						print "Language '"+pkg_basename+"' doesn't exists."
						sys.exit(2)

				elif pkg_target == 'smileyset':
					if os.path.exists(pkg_src+'/media/smileys/'+pkg_basename):
						os.mkdir(pkg_dest+'/media')
						os.mkdir(pkg_dest+'/media/smileys')
						packager.copy(pkg_src+'/media/smileys/'+pkg_basename, pkg_dest+'/media/smileys/'+pkg_basename)
					else:
						print "Smileyset '"+package_basename+"' doesn't exists."
						sys.exit(2)

				elif pkg_target == 'iconset':
					if os.path.exists(pkg_src+'/media/icons/'+pkg_basename):
						os.mkdir(pkg_dest+'/media')
						os.mkdir(pkg_dest+'/media/icons')
						packager.copy(pkg_src+'/media/icons/'+pkg_basename, pkg_dest+'/media/icons/'+pkg_basename)
					else:
						print "Iconset '"+pkg_basename+"' doesn't exists."
						sys.exit(2)

				elif pkg_target == 'style':
					if 'base' in opts:
						if os.path.exists(pkg_src+'/templates/'+opts['base']+'/_themes/'+pkg_basename+'/theme.css'):
							os.mkdir(pkg_dest+'/templates')
							os.mkdir(pkg_dest+'/templates/'+opts['base'])
							os.mkdir(pkg_dest+'/templates/'+opts['base']+'/_themes')

							packager.copy(pkg_src+'/templates/'+opts['base']+'/_themes/'+pkg_basename, pkg_dest+'/templates/'+opts['base']+'/_themes/'+pkg_basename)
							
							pkg_name = opts['base']+'_'+pkg_basename+'-'+opts['version']
						else:
							print "Style doesn't not exists. Please make sure you're using a valid base"
							sys.exit(2)
					else:
						print "You must specify a base template."
						sys.exit(2)

				elif pkg_target == 'module':
					if os.path.exists(pkg_src+'/modules/'+pkg_basename+'/package.xml'):

						if ('force') not in opts:
							module_version = string.strip(os.popen('grep "<version>" '+pkg_src+'/modules/'+pkg_basename+'/package.xml | sed s/"[^0-9.]"//g').read())
							if module_version != opts['version']:
								print "The package.xml module's version ("+module_version+") doesn't match the given version ("+opts['version']+"). Use --force to override."
								sys.exit(2)

						os.mkdir(pkg_dest+'/modules')
						
						index = open(pkg_dest+'/modules/index.html', 'w')
						index.close()

						os.mkdir(pkg_dest+'/modules/'+pkg_basename)
						os.mkdir(pkg_dest+'/modules/'+pkg_basename+'/lang')
						os.mkdir(pkg_dest+'/modules/'+pkg_basename+'/lang/es')
						os.mkdir(pkg_dest+'/modules/'+pkg_basename+'/lang/en')

						for file in os.listdir(pkg_src+'/modules/'+pkg_basename):
							if os.path.isdir(pkg_src+'/modules/'+pkg_basename+'/'+file):
								if file == 'lang':
									packager.copy(pkg_src+'/modules/'+pkg_basename+'/lang/es', pkg_dest+'/modules/'+pkg_basename+'/lang/es')
									packager.copy(pkg_src+'/modules/'+pkg_basename+'/lang/en', pkg_dest+'/modules/'+pkg_basename+'/lang/en')
									continue
							packager.copy(pkg_src+'/modules/'+pkg_basename+'/'+file, pkg_dest+'/modules/'+pkg_basename+'/'+file)
						
						os.mkdir(pkg_dest+'/media')
						os.mkdir(pkg_dest+'/media/icons')
						os.mkdir(pkg_dest+'/media/icons/default')

						sizes = ['16', '48']

						for size in sizes:
							os.mkdir(pkg_dest+'/media/icons/default/'+size)
							for icon in os.listdir(pkg_src+'/media/icons/default/'+size):
								if icon[0:len(pkg_basename)] == pkg_basename:
									packager.copy(pkg_src+'/media/icons/default/'+size+'/'+icon, pkg_dest+'/media/icons/default/'+size+'/'+icon)

					else:
						print "Module '"+pkg_basename+"' doesn't exists!"
						sys.exit(2);

				elif pkg_target == 'template':
					if os.path.exists(pkg_src+'/templates/'+pkg_basename):
						os.mkdir(pkg_dest+'/templates')
						os.mkdir(pkg_dest+'/templates/'+pkg_basename)

						for file in os.listdir(pkg_src+'/templates/'+pkg_basename):

							if os.path.isdir(pkg_src+'/templates/'+pkg_basename+'/'+file):
								if file == '_themes':
									os.mkdir(pkg_dest+'/templates/'+pkg_basename+'/_themes/')
									packager.copy(pkg_src+'/templates/'+pkg_basename+'/_themes/default', pkg_dest+'/templates/'+pkg_basename+'/_themes/default')
									continue
							packager.copy(pkg_src+'/templates/'+pkg_basename+'/'+file, pkg_dest+'/templates/'+pkg_basename+'/'+file)
					else:
						print "Template '"+pkg_name+"' doesn't exists."
						sys.exit(2)

				if pkg_compress:
					packager.compress(pkg_dest, pkg_name+pkg_ext, pkg_destdir)
					print packager.md5sum('./releases/'+pkg_name+pkg_ext)
					shutil.rmtree(pkg_dest)

		else:
			usage()
			sys.exit(2)

	else:
		usage()
		sys.exit(0)

if __name__ == "__main__":
	main()
