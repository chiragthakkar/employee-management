# Employee Management Web Application

Welcome to the Employee Management Web Application! This is a user-friendly tool designed to help you manage employee data by importing CSV and get average salary calculated per company. 📊📋

## Before you get started, make sure you have **the Docker 🐳** installed

## Getting Started 🚀

**Clone the Repository 📦 :**

   ```
   git clone git@github.com:chiragthakkar/employee-management.git
   ```

**Navigate to the cloned directory:**

   ```
   cd employee-management
   ```

   **Build the Docker Containers**:
   ```
   docker-compose build --no-cache
```


## Running the Application 🏃‍♂️

**Start the Docker Containers**:
```
docker-compose up -d
```

**Access the App by opening your web browser and going to [http://localhost:3030](http://localhost:3030), You can use [Sample.csv](https://github.com/chiragthakkar/employee-management/blob/master/sample.csv) file to import the data.**
   > If this does not work wait for 10 seconds and reload the page again and let docker containers do its magic ✨🪄.


**Stop the Docker Containers**:
```
   docker-compose down
```


## Running Tests 🧪

run `docker-compose exec php /bin/sh` to access the PHP container terminal.
```
vendor/bin/phpunit app/Tests/Controller/ApiControllerTest.php
vendor/bin/phpunit app/Tests/Model/EmployeeModelTest.php
exit
```
run `docker-compose exec react /bin/sh` to access the React container terminal.
```
npm test
q
```

## Feedback and Contributions 🤝

Feel free to provide me with any suggestions, feedback, or improvements you think can enhance the project.
