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

## Setup PHPStorm + + Docker + Xdebug + postman
- Open settings by pressing `(cmd + ,)` button
- Under PHP, add a new CLI interpreter

![add-new-cli-interpreter1.png](docs%2Fimages%2Fadd-new-cli-interpreter1.png)

- Select From Docker, Vagrant ... option
- Select Docker Compose, set the configuration file to ./docker-compose.yml and the service select app
![add-new-cli-interpreter2.png](docs%2Fimages%2Fadd-new-cli-interpreter2.png)

- Now your PHP interpreter settings hosuld look like this
![xdebug-php.png](docs%2Fimages%2Fxdebug-php.png)

- Next, you want to set up the test framework, click the plus sign and select PHPUnit by remote interpreter
![test-framework1.png](docs%2Fimages%2Ftest-framework1.png)

- Select the interpreter you just created from the dropdown list
![test-framework2.png](docs%2Fimages%2Ftest-framework2.png)

- Now you can start debugging, set a break point in any controller class and run the test associated with it in debug mode

### Listening for requests from postman
- First step is to set up a server
![server.png](docs%2Fimages%2Fserver.png)
- On postman add this parameter. When postman detects this in a request, it creates a cookie with the value of XDEBUG_SESSION_START. This has an expiry time of 30 minutes so you dont have to include it in your requests all the time.

```angular2html
XDEBUG_SESSION_START=PHPSTORM
```
- Finally, tell postman to listen for PHP Debug Connections
![php-debug-connections.png](docs%2Fimages%2Fphp-debug-connections.png)

- Set a break point in the code called by the endpoint you are consuming on postman, hit send to start debugging
