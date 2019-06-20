FROM node:10

WORKDIR /app

RUN npm config -g set user root
RUN npm install -g @vue/cli

EXPOSE 8080

CMD npm install && npm run serve
