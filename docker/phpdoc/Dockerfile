FROM phusion/baseimage:0.9.15

# mount the code base in /data
# mount the output dir in /ouput

# Let the container know that there is no tty
ENV DEBIAN_FRONTEND noninteractive

# Common environment variables
ENV COMPOSER_HOME /.composer
ENV CONF_DIR_PHP5_CLI /etc/php5/cli

# All our dependencies, in alphabetical order (to ease maintenance)
RUN apt-get update && apt-get install -y --no-install-recommends \
        curl \
        git \
        wget \
        php5-cli \
        php5-curl \
        php5-gd \
        php5-imagick \
        php5-intl \
        php5-json \
        php5-ldap \
        php5-mcrypt \
        php5-mhash \
        php5-mysql \
        php5-pgsql \
        php5-sqlite && \

    # Remove cache
    apt-get clean && rm -rf /var/lib/apt/lists/*



# mount the code to phpdoc in /data
RUN mkdir /data

WORKDIR /data

VOLUME ["/output"]

ADD sed.sh /.cravler/sed.sh
ADD php5-set-config.sh /.cravler/php5-set-config.sh
ADD php5-cli-config.sh /.cravler/php5-cli-config.sh

RUN apt-get update
RUN apt-get install -y graphviz

RUN wget -nv -O /usr/local/bin/phpdoc http://www.phpdoc.org/phpDocumentor.phar \
  && chmod +x /usr/local/bin/phpdoc

ENTRYPOINT ["/usr/bin/php", "/usr/local/bin/phpdoc"]
