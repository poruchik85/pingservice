#!/usr/bin/env bash

DIR=$(readlink -e $(dirname $0))
SUDO_CMD=$(test -z $(id -Gn | xargs -n1 | grep '^docker$') && echo sudo)
NET_NAME=vladis
BACK_DIR="/var/www"
FRONT_DIR="/app"
NPM_HOME=${NPM_HOME:-${DIR}/volumes/npm}
PHP_CONTAINER_NAME=vladis/php
NODE_CONTAINER_NAME=vladis/node

php_container() {
    ${SUDO_CMD} docker run \
        -it \
        --rm \
        -v ${HOME}/.ssh:${HOME}/.ssh \
        -v ${DIR}/..:${BACK_DIR} \
        -v ${DIR}/volumes/data:/opt/data \
        -v ${DIR}/build/php/php.ini:/usr/local/etc/php/php.ini:ro \
        -v ~/.config/psysh:/home/${USER}/.config/psysh \
        -v /etc/passwd:/etc/passwd:ro \
        -v /etc/group:/etc/group:ro \
        -w ${BACK_DIR} \
        -u $(id -u) \
        --network ${NET_NAME} \
        ${PHP_CONTAINER_NAME} \
        $@
}

node_container() {
    local base_dir=$(dirname ${DIR})
    local work_dir=$(pwd | sed "s:${base_dir}:${FRONT_DIR}:")

    if [[ ${work_dir} = $(pwd) ]]; then
        work_dir="${FRONT_DIR}"
    fi

    if [[ ! -d ${NPM_HOME} ]]; then
        mkdir -p ${NPM_HOME}
    fi

    ${SUDO_CMD} docker run \
        -it \
        --rm \
        -v ${NPM_HOME}:/.npm \
        -v ${NPM_HOME}:/home/node/.npm \
        -v ${DIR}/..:${FRONT_DIR} \
        -w ${work_dir} \
        -u $(id -u) \
        -p 3000:3000 \
        --name frontendapp_node \
        ${NODE_CONTAINER_NAME} \
        $@
}
