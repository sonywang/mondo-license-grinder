- Use Linux/Unix (untested on other OSs). You will need Subversion (`yum install subversion` on Red Hat, `aptitude install subversion` on Debian/Ubuntu)

- `cd` to where you want the app to be, under your Web server's document root, likely `/var/www/html/` on Red Hat or `/var/www/` on Ubuntu.

- Type: `svn checkout http://mondo-license-grinder.googlecode.com/svn/trunk/ licenses` . This will create a new directory `licences` under the Web root containing the application code.

- `cd licenses`

- `less README` to find out the absolute latest installation hints.

- To upgrade at any time, `cd` to the same directory and do `svn up`. We aren't currently offering a ZIP or tarball at this time because of the anticipated frequency of updates after the initial announcement. Doing a `svn up` is faster and more convenient than downloading and reinstalling the entire application.