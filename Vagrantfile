# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure("2") do |config|
  # The most common configuration options are documented and commented below.
  # For a complete reference, please see the online documentation at
  # https://docs.vagrantup.com.

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://atlas.hashicorp.com/search.
  config.vm.box = "sammaye/ubuntu-16-04-100GB"

  # Disable automatic box update checking. If you disable this, then
  # boxes will only be checked for updates when the user runs
  # `vagrant box outdated`. This is not recommended.
  # config.vm.box_check_update = false

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  # NOTE: This will enable public access to the opened port
  # config.vm.network "forwarded_port", guest: 80, host: 8080

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine and only allow access
  # via 127.0.0.1 to disable public access
  # config.vm.network "forwarded_port", guest: 80, host: 8080, host_ip: "127.0.0.1"
  # config.vm.network "forwarded_port", guest: 443, host: 4343, host_ip: "127.0.0.1"

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  # config.vm.network "private_network", ip: "192.168.33.10"

  # Create a public network, which generally matched to bridged network.
  # Bridged networks make the machine appear as another physical device on
  # your network.
  config.vm.network "public_network", use_dhcp_assigned_default_route: true

  # Share an additional folder to the guest VM. The first argument is
  # the path on the host to the actual folder. The second argument is
  # the path on the guest to mount the folder. And the optional third
  # argument is a set of non-required options.
  # config.vm.synced_folder "../data", "/vagrant_data"

  # Provider-specific configuration so you can fine-tune various
  # backing providers for Vagrant. These expose provider-specific options.
  # Example for VirtualBox:
  #
  # config.vm.provider "virtualbox" do |vb|
  #   # Display the VirtualBox GUI when booting the machine
  #   vb.gui = true
  #
  #   # Customize the amount of memory on the VM:
  #   vb.memory = "1024"
  # end
  #
  # View the documentation for the provider you are using for more
  # information on available options.

  # Define a Vagrant Push strategy for pushing to Atlas. Other push strategies
  # such as FTP and Heroku are also available. See the documentation at
  # https://docs.vagrantup.com/v2/push/atlas.html for more information.
  # config.push.define "atlas" do |push|
  #   push.app = "YOUR_ATLAS_USERNAME/YOUR_APPLICATION_NAME"
  # end

  config.vm.provider "virtualbox" do |v|
    v.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate/v-root", "1"]
  end

  # Enable provisioning with a shell script. Additional provisioners such as
  # Puppet, Chef, Ansible, Salt, and Docker are also available. Please see the
  # documentation for more information about their specific syntax and use.
  config.vm.provision "shell", inline: <<-SHELL

    add-apt-repository ppa:ondrej/php

    apt-get update > /dev/null

    yes | apt-get install zip
    yes | apt-get install pkg-config libssl-dev

    yes | apt-get install php7.2-fpm \
    php7.2-cli \
    php7.2-curl \
    php7.2-gd \
    php7.2-mysql \
    php7.2-mbstring \
    php7.2-xml \
    php7.2-soap \
    php7.2-intl \
	php7.2-oauth \
    php7.2-dev

    # Set the right user for PHP7 ("vagrant" user)
    sed -i -e "s/www-data/vagrant/g" /etc/php/7.2/fpm/pool.d/www.conf

    # Fix error reporting so it is consistent with live server
    sed -i 's/^error_reporting = .*/error_reporting = E_ALL/' /etc/php/7.2/fpm/php.ini
    sed -i 's/^error_reporting = .*/error_reporting = E_ALL/' /etc/php/7.2/cli/php.ini
    sed -i 's/^post_max_size = .*/post_max_size = 100M/' /etc/php/7.2/fpm/php.ini
    sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 100M/' /etc/php/7.2/fpm/php.ini

    yes | apt-get install php-pear
    pecl install xdebug

    # Add xdebug to PHP runtime
    echo 'zend_extension=xdebug.so' >> /etc/php/7.2/fpm/php.ini
    echo 'zend_extension=xdebug.so' >> /etc/php/7.2/cli/php.ini

    pecl install mongodb
    cat > /etc/php/7.2/mods-available/mongodb.ini << 'EOF'
; configuration for php mongodb module
; priority=30
extension=mongodb.so
EOF
    ln -s /etc/php/7.2/mods-available/mongodb.ini /etc/php/7.2/fpm/conf.d/30-mongodb.ini
    ln -s /etc/php/7.2/mods-available/mongodb.ini /etc/php/7.2/cli/conf.d/30-mongodb.ini

    service php7.2-fpm restart

    yes | apt-get install -y build-essential
    curl -sL https://deb.nodesource.com/setup_10.x | sudo -E bash -
    yes | sudo apt-get install -y nodejs

    debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
    debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'
    yes | apt-get -y install mysql-server
    mysqladmin -u root -p'root' password ''
    echo '#sql_mode=NO_ENGINE_SUBSTITUTION' >> /etc/mysql/mysql.conf.d/mysqld.cnf
	sed -i -re 's/^bind-address(.*)/#bind-address\1/' /etc/mysql/mysql.conf.d/mysqld.cnf
    service mysql restart
	mysql -u root -e "create user root@'localhost' identified by ''; grant all privileges on *.* to root@'localhost' with grant option;"
    mysql -u root -e "create user root@'%' identified by ''; grant all privileges on *.* to root@'%' with grant option;"
    service mysql restart

    apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv EA312927
    echo "deb http://repo.mongodb.org/apt/ubuntu xenial/mongodb-org/3.2 multiverse" | tee /etc/apt/sources.list.d/mongodb-org-3.2.list
    apt-get update
    yes | apt-get install -y mongodb-org

    # Install the latest nginx from their official repo
    add-apt-repository ppa:nginx/stable
    apt-get update > /dev/null
    yes | apt-get install nginx
    sed -i -e 's/www-data/vagrant/g' /etc/nginx/nginx.conf
    sed -i -e 's/sendfile .*;/sendfile off;/g' /etc/nginx/nginx.conf

    rm /etc/nginx/sites-available/default
    cat > /etc/nginx/sites-available/default << 'EOF'
server {
    listen 80;
    server_name _;
    root /vagrant/frontend/web;
    index index.php;
	client_max_body_size 100M;

	location /socket.io {
		proxy_pass http://localhost:3000;
		proxy_http_version 1.1;
		proxy_set_header Upgrade $http_upgrade;
		proxy_set_header Connection 'upgrade';
		proxy_set_header Host $host;
		proxy_cache_bypass $http_upgrade;
	}

    location /phpmyadmin {
        root /home/vagrant;
        location ~ \.php$ {
            fastcgi_split_path_info ^(.+\.php)(/.+)$;
            try_files $uri $uri/ =404;
            fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
        }

        location ~ (.*\.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar|mp4|ogg|woff|woff2|ttf))$ {
            try_files $uri =404;
        }
    }

    location ~ ^/system(.*) {
        root /vagrant/backend/web;
        rewrite ^/system/(.*)$ /$1 break;
        try_files $uri /system/index.php?r=$1&$args;

        location ~ \.php$ {
            rewrite ^/system/(.*)$ /$1 break;
            fastcgi_split_path_info ^(.+\.php)(/.+)$;
            try_files $uri $uri/ =404;
            # NOTE: You should have "cgi.fix_pathinfo = 0;" in php.ini
            fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
        }

        location ~ (.*\.(js|css|png|jpg|jpeg|gif|swf|ico|pdf|mov|fla|zip|rar|mp4|ogg|woff|woff2|ttf|eot|html|json|txt))$ {
            rewrite ^/system/(.*)$ /$1 break;
            expires 30d;
            add_header Pragma public;
            add_header Cache-Control "public";
            try_files $uri $uri/ =404;
        }
    }

    location / {
        rewrite /(.*) /index.php?r=$1;
    }

    location ~ (.*\.(js|css|png|jpg|jpeg|gif|swf|ico|pdf|mov|fla|zip|rar|mp4|ogg|woff|woff2|ttf|eot|html|json|txt))$ {
        expires 30d;
        add_header Pragma public;
        add_header Cache-Control "public";
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        try_files $uri $uri/ =404;

        # NOTE: You should have "cgi.fix_pathinfo = 0;" in php.ini
        fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOF
    cat > /etc/nginx/sites-available/cake << 'EOF'
server {
    listen 80;
    server_name _;
    root /vagrant/app/webroot;
    index index.php;
	client_max_body_size 100M;

    location /phpmyadmin {
        root /home/vagrant;
        location ~ \.php$ {
            fastcgi_split_path_info ^(.+\.php)(/.+)$;
            try_files $uri $uri/ =404;
            fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
        }

        location ~ (.*\.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar|mp4|ogg|woff|woff2|ttf))$ {
            try_files $uri =404;
        }
    }

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ (.*\.(js|css|png|jpg|jpeg|gif|swf|ico|pdf|mov|fla|zip|rar|mp4|ogg|woff|woff2|ttf|eot|html|json|txt))$ {
        expires 30d;
        add_header Pragma public;
        add_header Cache-Control "public";
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        try_files $uri $uri/ =404;

        # NOTE: You should have "cgi.fix_pathinfo = 0;" in php.ini
        fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOF
    nginx -s reload

    # Run a last autoremove to get rid of everything
    yes | apt-get autoremove > /dev/null

    cat > /home/vagrant/.ssh/id_private << 'EOF'
-----BEGIN RSA PRIVATE KEY-----
MIIEpAIBAAKCAQEApEHMWRdWwqtKyYlmvtS2PxyePKxGs5LpWC38+etNG3/GVsMI
+sm3isjOF89Lw4W7UfD3z6RMkBg7t6yOR/4WunihdNDToRX1/DAPVsLt+/7DfHAM
dHCbzq9z2KDtHtE4xe2PkSDO2BLxmbQeTjjGNRfiIOzY21yHeFGH50NAZ5AAbpIL
oe31DEHhID6CVKCE6E08Sh7Z0dNGw0ORcUu9QNTEk81nSFDKloV4dkUfkTnGMZ7p
DKFCcnR8qSiE22nOVdiS0eenWQFRWXZ8jSRqxUObTmP3JI5PZMSToVOp0rjt3h6M
RZx0rYqOQxncXxQZpMdPiMtE89DHoIusrfx35QIDAQABAoIBAGVV8qxEKdKPuuP7
UNgKgyUMktL7teKzkCJGvPuyny+H9OUyDigqqoGEwSEPUr6dkqNK9pez1UhQqwb1
/hGMQJlqMrHO48FNuySKwevU9t2wnwn2Vri/gGBS/jV3ktKgYVY66YevpezIQyWA
afH4NNAsm+WUOXWb8DfalO2oH+PDhjr++YKEdOfftfP3Zmz83ee2rb7hMPCqpgg7
O7gqfFbfqXSt3MqJICzcG9yWcI2Omg4xVRhsbQOTcOBNAfm30qOI6m0wGbF1kilY
CjScSD8SKFu+vnDIh7IrpRJb6KurU13QOh/+nV5bKUHacJov9oBgynY6zUOUsEmE
z0WPPAECgYEA1mWv5NZBYRrwA8hBL4Xk9QAxo4ZfSRwRj7bGqfEK2K2P/8DFUScW
PP+db2QEeIACnyNDAYbYn4FuMS13fdVY31Vb6Kz+wxl9UGQ8X+6eJOo8qCadtU7w
JfVNPtG+k2+NRAdC0Q1JGvpVQRXnwRc+8uAy3DypfPfelu2Bs/g6KtECgYEAxCFd
0Uiyz9Zxyixx3Fppo1QSJAZqEHHo4MTBXLC9Xon5SzrS5DvvnsYJgzDvBKYKBfMJ
HdyxzuiDE1YE5PE8hKj8oHtBy2sU8xX8Su/u8ReKzZWLiVwBsR4Yqm1PN8U1mPbz
FS2iFO642YENuOIeMEnaLndbMxMh+2x/bkKvWNUCgYEAnbAI4NydFZjEc2O0Xgmy
zmgoGkfYiWM1n2glUhTRj3CIeukDt55yAsdWufjsONyeEQHUZKkTZq3BqDXyrwBd
71VO1iF293Ql8RzoMv7EHg6SMnLEh/fZNXHoMI4AB1yCoEhe4ndND6STU92SqTg5
ulUf01BisGF2u7dQMFggICECgYEAq1kbRcRls/5920uJDiJPOEoyrxaLg2KBOh2r
cPdX+khAa+EwbgWPCv+pP9x4dW86QohZ5qTxEvs/yJzWGc5IOP4J94q3qKgc3WLP
0AhekHMo6QNFrPOc5siMdBibpPW+Ja1aIZ6EhBUrmZqOCoBCL53V59KO8sg8YBQi
IIpR9yECgYBcht6l1ABj75Rc6Lt9royKIY5KcXy2t4VzzyrIR+2oMojmgcHN3qN+
UwwqFmpcv2O0QzLpsh5p9hA8DA4ypliXhRgszL3vx7SqmkHQBJ8FLEG6raIsqGhy
iFOgjzXOPjN4kuER0KP+K1YzNeTSv+MQ3gZ6/IDMJ/7idnd+ysM4KA==
-----END RSA PRIVATE KEY-----
EOF
    chown vagrant:vagrant /home/vagrant/.ssh/id_private
    chmod 600 /home/vagrant/.ssh/id_private

    cat > /home/vagrant/.ssh/id_public << 'EOF'
ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQCkQcxZF1bCq0rJiWa+1LY/HJ48rEazkulYLfz5600bf8ZWwwj6ybe
KyM4Xz0vDhbtR8PfPpEyQGDu3rI5H/ha6eKF00NOhFfX8MA9Wwu37/sN8cAx0cJvOr3PYoO0e0TjF7Y+RIM7YEvGZtB
5OOMY1F+Ig7NjbXId4UYfnQ0BnkABukguh7fUMQeEgPoJUoIToTTxKHtnR00bDQ5FxS71A1MSTzWdIUMqWhXh2RR+RO
cYxnukMoUJydHypKITbac5V2JLR56dZAVFZdnyNJGrFQ5tOY/ckjk9kxJOhU6nSuO3eHoxFnHStio5DGdxfFBmkx0+I
y0Tz0Megi6yt/Hfl sam.millman@googlemail.com
EOF
    chown vagrant:vagrant /home/vagrant/.ssh/id_public
    chmod 600 /home/vagrant/.ssh/id_public

    cat > /home/vagrant/.ssh/config << 'EOF'
HashKnownHosts no

Host github.com
  User git
  Port 22
  Hostname github.com
  IdentityFile ~/.ssh/id_private
  TCPKeepAlive yes
  IdentitiesOnly yes
  UserKnownHostsFile /dev/null
  StrictHostKeyChecking no

Host bitbucket.org
  Hostname bitbucket.org
  IdentitiesOnly yes
  UserKnownHostsFile /dev/null
  StrictHostKeyChecking no
EOF
    chown vagrant:vagrant /home/vagrant/.ssh/config
    chmod 600 /home/vagrant/.ssh/config

    eval $(ssh-agent -s)
    ssh-add /home/vagrant/.ssh/id_private
  SHELL

  $script = <<-'SCRIPT'
    cd /home/vagrant

	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    php -r "unlink('composer-setup.php');"

    git clone -b STABLE git@github.com:phpmyadmin/phpmyadmin.git
    cd phpmyadmin
	composer install
    cp config.sample.inc.php config.inc.php
    sed -i 's/^\$cfg\['\''Servers'\''\]\[\$i\]\['\''AllowNoPassword'\''\] = .*/\$cfg\['\''Servers'\''\]\[\$i\]\['\''AllowNoPassword'\''\] = true;/' config.inc.php

    cd /vagrant
    php ./composer.phar self-update
    php ./composer.phar install
  SCRIPT
  config.vm.provision "shell", inline: $script, privileged: false
  #config.vm.provision :shell, path: "bootstrap.sh"
end
