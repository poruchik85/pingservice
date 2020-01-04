Проект представляет из себя тестовое задание:
- написать веб-сервис, позволяющий создавать/удалять/редактировать группы хостов и хосты с IP-адресами и/или именами, пинговать хосты, хранить историю пингов;
- язык - PHP;
- не использовать PHP-фреймворки;

# 1. Описание

Проект состоит из трёх частей:

#### 1.1. Серверная часть.
Папка *./back*.

Реализована на PHP 7.3, не используются фреймворки. 

Из сторонних библиотек - только [phpunit](https://phpunit.de/) и [phpab](https://github.com/theseer/Autoload/).
* *./back/src/* - код проекта;
* *./back/tests/* - тесты;
* *./back/config.json* - конфиг.

#### 1.2. Клиентская часть.
Папка *./front*. 

Реализована на Vuejs.

Стандартный проект, созданный с **[vue-cli 3](https://cli.vuejs.org/)**. Также используются [vuex](https://vuex.vuejs.org/), [vuetify](https://vuetifyjs.com/) и [material icons](https://material.io/tools/icons/).

#### 1.3. Инфраструктура.
Папка *./docker*.

Папка содержит конфиги [докера](https://www.docker.com/) и [докер-композа](https://docs.docker.com/compose/), а также скрипты для управления проектом и простого доступа к контейнерам.

* *./docker/build/* - Dockerfile-ы и конфиги для отдельных сервисов;
* *./docker/logs/* - логи из контейнеров;
* *./docker/volumes/* - сохраняемые данные контейнеров;
* *./docker/docker-compose.yml* - конфиг [docker-compose](https://docs.docker.com/compose/);
* *./back/func.sh* - вспомогательный скрипт, содержит общие константы и функции для других скриптов, напрямую не вызывается;
* *./docker/install.sh* - инициализирующий скрипт. Создаёт необходимые папки, конфиги, пулит сторонние библиотеки ([phpunit](https://phpunit.de/), [phpab](https://github.com/theseer/Autoload/)). Должен выполняться первым, сразу после `git pull` проекта;
* *./docker/up* - поднимает контейнеры;
* *./docker/down* - выключает контейнеры.

Далее следует группа скриптов, работающих с контейнерами.
* *./docker/php* - доступ к консоли **php** (например, `./docker/php -v`);
* *./docker/npm* - доступ к консоли **npm** (например, `./docker/npm --version`);
* *./docker/vue* - доступ к консоли **vue** (например, `./docker/vue --version`);
* *./docker/runtests* - запускает все тесты. Можно указать файл или класс с тестами, чтобы прогнать только его - например, `./docker/runtests QueryBuilderTest`;
* *./docker/autoloaderupdate* - формирует автолоадер для тестов. Нужно выполнять, когда добавляется новый тестируемый класс.

---

# 2. Установка и запуск

В этом разделе по шагам описывается процесс запуска на свежеустановленной **[Ubuntu server 18.04.2 amd64](https://ubuntu.com/download/server/thank-you?country=RU&version=18.04.2&architecture=amd64)**. Разумеется, можно использовать любой другой дистрибутив линукс, но процесс установки может сильно отличаться, если дистрибутив не **[debian-based](https://www.debian.org/)**.

Я использовал виртуальную машину, созданную в **[VMware Workstation Player](https://www.vmware.com/ru/products/workstation-player/workstation-player-evaluation.html) 12.5.9 build-7535481** (сейчас на сайте уже новее, но, думаю, это не важно) и запущенную локально c 1024MB памяти и 10GB HDD.  

#### 2.1. Установка убунты.
Просто стандартная установка, все параметры по умолчанию, на вопрос про **open ssh** нужно поставить галочку **Install OpenSSH server**, чтобы после установки с этим не заморачиваться, никаких дополнительных пакетов, кроме **OpenSSH**, добавлять не нужно. **Важно: не надо менять язык во время установки. Локализация на сервере может вызвать непредсказуемые катаклизмы, и вообще не комильфо.**

После установки необходимо проверить доступность сервера по *ssh* любым клиентом, например *putty* (IP сервера можно узнать в консоли VM командой `ifconfig`). Все последующие пункты по умолчанию выполняются в ssh-консоли в домашней папке пользователя, созданного при установке.
Также крайне желательно выполнить команду 
```bash
sudo bash -c "echo 127.0.0.1 localhost $(hostname) >> /etc/hosts"
```
это уберёт фризы при использовании **sudo**.

Проверка:
```bash
sudo lsb_release -a
```
 
#### 2.2. Установка **[git](https://git-scm.com/)**
Установка:
```bash
sudo apt update
sudo apt install git
```
Настройка:
```bash
git config --global user.name "Your Name"
git config --global user.email "youremail@domain.com"
```
Проверка:
```bash
git --version
```
#### 2.3. Установка **[docker](https://docs.docker.com/install/linux/docker-ce/ubuntu/)**
```bash
sudo apt update
sudo apt install -y \
    apt-transport-https \
    ca-certificates \
    curl \
    gnupg-agent \
    software-properties-common
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
sudo apt-key fingerprint 0EBFCD88
sudo add-apt-repository \
     "deb [arch=amd64] https://download.docker.com/linux/ubuntu \
     $(lsb_release -cs) \
     stable"
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io
```

Проверка:
```bash
docker -v
```

После проверки, если всё нормально, можно сразу настроить своего пользователя, чтобы он мог заходить в контейнеры и читать логи:
```bash
sudo usermod -aG docker "${USER}"
newgrp docker
sudo service docker restart
```

#### 2.4. Установка **[docker-compose](https://docs.docker.com/compose/install/)**
Установка:
```bash
sudo curl -L "https://github.com/docker/compose/releases/download/1.24.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

Проверка:
```bash
docker-compose --version
```

#### 2.5. Клонирование и запуск проекта:
Клонирование:
```bash
git clone https://github.com/poruchik85/pingservice.git
```
Проект склонируется в папку **pingservice**, сразу переходим в неё - `cd pingservice` - далее все команды выполняются из этой папки.

Запуск:
```bash
docker/install.sh
```
Первоначальный процесс запуска будет достаточно долгим (докер будет выкачивать контейнеры) - можно сходить покурить, или даже пообедать. 

После того, как все контейнеры скачаются, запустятся и освободят консоль, нужно будет убедиться, что собрался фронт. Он может собираться от нескольких десятков секунд до нескольких минут. Посмотреть, что там у него происходит, можно командой 
```bash
docker logs vladis_node -f
```
Вывод в конце должен быть примерно таким:
```
App running at:
- Local:   http://localhost:8080/

It seems you are running Vue CLI inside a container.
Access the dev server via http://localhost:<your container's external mapped port>/

Note that the development build is not optimized.
To create a production build, run npm run build.
```

Поднимаем контейнеры, которые не запускались во время инсталла:
```bash
docker/up
```

#### 2.6. hosts
На локальной машине добавляем в **hosts** запись
```bash
ip_сервера      pingservice.local
```
 Открываем в браузере [pingservice.local](http://pingservice.local)
 
---

Этот проект исключительно демонстрационный, содержит кучу уязвимостей, и почти не содержит никаких валидаций.
