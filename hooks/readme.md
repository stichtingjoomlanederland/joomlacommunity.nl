# PHP Codesniffer Pre-Commit Hook for GIT - Modified for PWT

## Based on: 
- https://github.com/s0enke/git-hooks/tree/master/phpcs-pre-commit
- https://gist.github.com/fdemiramon/0423b4308218d417fbf3


## REQUIREMENTS

 * Bash
 * PHP CodeSniffer: http://pear.php.net/package/PHP_CodeSniffer/redirected 
   * Check https://docs.joomla.org/Joomla_CodeSniffer for details
   * https://github.com/joomla/coding-standards/tree/3.x-dev If you want to use the CodeSniffer v3+ (tested on Linux)
  
 
## USAGE

  * Put the script "pre-commit" into your .git/hooks directory or use symbolic links (ln -s ~/path/to/source/perfect-site/hooks/ ~/path/to/target/perfect-site/.git/)
  * OR: add the script to your pre-commit "chain" (you probably know what to do then)
  * Put the Config file "config" into the same dir as the "pre-commit" script and
    edit it to your requirements
  * Ensure that the script is executable. 

## CodeSniffer
  * https://github.com/squizlabs/PHP_CodeSniffer/wiki/Advanced-Usage


## Update
```
# Download using curl
curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar
curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcbf.phar

# Or download using wget
wget https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar
wget https://squizlabs.github.io/PHP_CodeSniffer/phpcbf.phar

# Then test the downloaded PHARs
php phpcs.phar -h
php phpcbf.phar -h
```
