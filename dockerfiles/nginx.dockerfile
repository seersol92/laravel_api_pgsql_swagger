FROM nginx:latest

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

# MacOS staff group's gid is 20
RUN delgroup dialout

# Create laravel group
RUN addgroup --gid ${GID} --system laravel

# Create laravel user
RUN adduser --system --uid ${UID} --ingroup laravel --home /home/laravel --shell /bin/sh laravel


RUN sed -i "s/user  nginx/user laravel/g" /etc/nginx/nginx.conf

ADD ./nginx/default.conf /etc/nginx/conf.d/

RUN mkdir -p /var/www/html