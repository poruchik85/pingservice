#!/usr/bin/env bash

DIR=$(readlink -e $(dirname $0))

. $(dirname $0)/func.sh

set -e

if [[ -z "$(${SUDO_CMD} docker network inspect ${NET_NAME} > /dev/null 2>&1 && echo 1)" ]]; then
    ${SUDO_CMD} docker network create ${NET_NAME}
fi

if [[ ! -f "${DIR}/docker-compose.yml" ]]; then
    cp "${DIR}/docker-compose.yml.dist" "${DIR}/docker-compose.yml"
fi

bash -c "cd ${DIR} && mkdir -p volumes/data && ${SUDO_CMD} chmod -R 775 volumes/data"
bash -c "cd ${DIR} && mkdir -p volumes/npm && ${SUDO_CMD} chmod -R 775 volumes/npm"

bash -c "${DIR}/up"
