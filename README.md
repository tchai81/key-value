# Framework & tools

```
API Framework: Laravel
Database: AWS RDS - MySQL (free tier)
Hosting: AWS Elasticbeanstalk (free tier)
Repository hosting: Github
CI/CD: Github Actions + AWS Codepipeline
```

# API Endpoints
```
Creating Key Value Pair
POST: http://assignments.us-east-1.elasticbeanstalk.com/api/key-value
Sample Json Payload: 
{
  "mykey": "myvalue"
}

Getting value by key & timestamp
GET: http://assignments.us-east-1.elasticbeanstalk.com/api/key-value/key?timestamp=1601425620
```

## Getting Started

1.  git clone https://github.com/tchai81/key-value.git <folder-name>
2.  Navigate to the folder

```bash
$ cd <folder-name>
```

3.  Installing dependencies

```bash
$ composer install
```

4.  Replicating .env from .env.example

```bash
$  cp .env.example .env
```

5.  Generate Key and update .env

```bash
$ php artisan key:generate
```

6.  Update APP_KEY attribute in .env file with relevant key
7.  Create a MySQL database on your local and state down all details
8.  Make necessary changes to .env
9.  Execute database migration

```bash
$  php artisan migrate
```

10.  Start the server

```bash
$ php artisan serve --port=8080
```

11.  Execute the unit tests

```bash
$ /vendor/bin/phpunit
```

## CI/CD Workflow:-
1. Upon commit to master branch, Github Actions will trigger the running of unit tests.
2. If unit tests fail, an email will be sent. No deployment to hosting server will be done. 
3. If unit tests succeeded, AWS Codepipeline will be executed to push latest codebase from Github to AWS Elasticbeanstalk.

## Constraints & Assumptions

1. Creation payload only accept a key value pair. 
2. <del>Currently having issues with post deployment script as debugging is very tedious. Changes to codebase may not be reflected on hosting server after deployment depends on what you've changed. It certainly can be fixed but i'll leave it for now due to time constaint.<del>
3. Added post deployment script but still have issues. Post deployment script don't work most likely due to AWS free tier limited memory capacity. The following doesn't work:-
- Database migration script 
- Installing new depencies through composer update
