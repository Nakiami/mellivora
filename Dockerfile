FROM ubuntu:latest

MAINTAINER nakiami <contact@greyboxconcepts.com.au>

# disable interactive functions
ENV DEBIAN_FRONTEND noninteractive

RUN sudo apt-get update && sudo apt-get -y upgrade
RUN sudo apt-get install -y apache2
RUN sudo apt-get install -y mysql-server
RUN sudo apt-get install -y php5 php5-common libapache2-mod-php5 php5-mysqlnd php5-curl php5-pear php5-mbstring php5-mcrypt
RUN phpenmod mcrypt

RUN sudo apt-get autoremove -y

ADD supervisor/conf.d/apache2.conf /etc/supervisor/conf.d/

EXPOSE 80
