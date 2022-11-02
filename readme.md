# Prerequisites
- Download and install composer globally https://getcomposer.org/download/
- Add an alias to your bash or zsh profile

```angular2html
alias composer='/usr/local/bin/composer'
```
**Remember to source the file.**

- Ensure php is correctly installed on your machine. You can use brew, follow the instructions to update your httpd.conf file and restart apache.

# Installation

Use the make command to setup the project like so:

```angular2html
make install
```

This command spins up containers, install project dependencies, creates a barebone  .env file which you can update as needed, sets the app key etc
For a full glossary of all the make commands, look in the `Makefile` in the project root.

# Contributing
- style guide (TBD)

## Testing
- setup phpunit in phpstorm

#### Requirements
- xdebug, php


