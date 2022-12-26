# Prerequisites
- Download and install composer globally https://getcomposer.org/download/
- Add an alias to your bash or zsh profile

```angular2html
open ~/.bashrc OR
open ~/.zshrc

# add this line
alias composer='/usr/local/bin/composer'

# remember to source the file.
e.g
source ~/.bashrc OR
source ~/.zshrc
```

- Ensure php is correctly installed on your machine. You can use brew, follow the instructions to update your httpd.conf file and restart apache.
- install the docker desktop app suitable for your hardware https://www.docker.com/products/docker-desktop/

# Installation

Use the make command to setup the project like so:

```angular2html
make install
```

This command spins up the containers, install project dependencies, creates a barebone  .env file which you can update as needed, sets the app key etc
For a full glossary of all the make commands, look in the `Makefile` in the project root.

Now you can access the app via:

```angular2html
http://localhost:8080/api/v1/ping
```

To run a command directly in the "app" container, you could use `docker-compose exec app [command]`, for example
```
docker-compose exec app php artisan env
# which prints the application environment
```
# Contributing
1) Always rebase your branch against the base branch `git rebase -i --autosquash origin/staging`
2) Strictly adhere to branch naming rules `e.g feature/descriptive-branch-name, bugfix/descriptive-branch-name etc (hotfix, chore)`

## Testing in dev
- Follow this guide to learn how to setup phpunit in phpstorm.
- You can also use any of the following make cmds to run tests

```angular2html
make test
make test_unit
make test_api
```

# Help
To see a list of all the make commands, type `make` in the project root. It will dispplay the full list of available commands you can use.

Examples:

![Alt text](docs/images/help.png?raw=true "help")

