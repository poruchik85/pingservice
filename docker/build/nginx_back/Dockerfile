FROM nginx

RUN rm /etc/nginx/nginx.conf /etc/nginx/mime.types
COPY nginx.conf /etc/nginx/nginx.conf
COPY mime.types /etc/nginx/mime.types
COPY default.conf /etc/nginx/sites-enabled/default.conf

RUN sed -e 's/^worker_processes.*/worker_processes 5;/' -i /etc/nginx/nginx.conf
