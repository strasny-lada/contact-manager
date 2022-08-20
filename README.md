# Contact Manager

A simple web application for managing your contacts. 

## How to install the Contact Manager

1. Clone the repository

      ```sh
      git clone git@github.com:strasny-lada/contact-manager.git
      ```
   
1. Install PHP libraries by Composer

      ```sh
      composer install
      ```

1. Create `/.env.local` file and add the `DATABASE_URL` variable according to your configuration

      ```sh
      DATABASE_URL='mysql://root@localhost/contact_manager'
      ```

1. Create a database and execute migrations

      ```sh
      php bin/console doctrine:database:create
      php bin/console doctrine:migrations:migrate
      ```

1. Install JavaScript dependencies by Yarn

      ```sh
      yarn install
      ```
 
1. Build JavaScript application

      ```sh
      yarn build
      ```

1. Create a webserver virtualhost with the `DocumentRoot` directive set to the absolute path of the `/public` directory

1. Restart the webserver and enjoy the app :)

## TODO

* hold the pagination state 
* add wysiwyg editor to edit the "notice" field
* improve flash messages of disappearances at certain intervals
