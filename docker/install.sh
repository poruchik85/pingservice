#!/usr/bin/env bash

DIR=$(readlink -e $(dirname $0))
TESTUTILS_DIR="${DIR}/../back/tests/utils"

. $(dirname $0)/func.sh

set -e

if [[ -z "$(${SUDO_CMD} docker network inspect ${NET_NAME} > /dev/null 2>&1 && echo 1)" ]]; then
    ${SUDO_CMD} docker network create ${NET_NAME}
fi

if [[ ! -f "${DIR}/docker-compose.yml" ]]; then
    cp "${DIR}/docker-compose.yml.dist" "${DIR}/docker-compose.yml"
fi

if [[ ! -f "${DIR}/../back/config.json" ]]; then
    cp "${DIR}/../back/config.json.dist" "${DIR}/../back/config.json"
fi

bash -c "cd ${DIR} && mkdir -p volumes/data && ${SUDO_CMD} chmod -R 775 volumes/data"

bash -c "${DIR}/up"

if [[ ! -d ${TESTUTILS_DIR} ]]; then
    mkdir -p ${TESTUTILS_DIR}
fi

bash -c "wget -O ${TESTUTILS_DIR}/phpab https://github.com/theseer/Autoload/releases/download/1.25.6/phpab-1.25.6.phar"
bash -c "chmod +x ${TESTUTILS_DIR}/phpab"

bash -c "wget -O ${TESTUTILS_DIR}/phpunit https://phar.phpunit.de/phpunit-8.phar"
bash -c "chmod +x ${TESTUTILS_DIR}/phpunit"

bash -c "cd ${TESTUTILS_DIR} && git clone https://github.com/bovigo/vfsStream.git"
