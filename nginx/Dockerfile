FROM ubuntu:latest

MAINTAINER CodeX Team <github.com/codex-team>

ENV NGINX_VERSION 1.12.2

RUN apt-get update
RUN apt-get install -y \
       wget \
       git \
       gcc \
       build-essential \
       libc6 \
       libpcre3 \
       libpcre3-dev \
       libssl-dev \
       zlib1g \
       zlib1g-dev \
       lsb-base

RUN git clone https://github.com/wandenberg/nginx-push-stream-module.git \
    && wget http://nginx.org/download/nginx-${NGINX_VERSION}.tar.gz \
    && tar xzvf nginx-${NGINX_VERSION}.tar.gz \
    && cd nginx-${NGINX_VERSION} \
    && ./configure --add-module=../nginx-push-stream-module \
    && make \
    && make install

RUN mkdir -p /usr/local/nginx/sites-available/
RUN mkdir -p /usr/local/nginx/sites-enabled/

ADD nginx.conf /usr/local/nginx/conf/
ADD codex.conf /usr/local/nginx/sites-available/
RUN ln -s /usr/local/nginx/sites-available/codex.conf /usr/local/nginx/sites-enabled/codex

#RUN apt-get purge -y \
#    wget \
#    git \
#    gcc \
#    && apt-get clean

RUN usermod -u 1000 www-data

CMD ["/usr/local/nginx/sbin/nginx", "-c", "/usr/local/nginx/conf/nginx.conf"]

EXPOSE 80
