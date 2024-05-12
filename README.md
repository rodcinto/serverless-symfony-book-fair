# Symfony Serverless Book Fair Talks
This is a proposal of a scalable project that shows how Symfony can be used with Bref on AWS Serverless Lambda functions.

## Use case
During the preparations for a book fair, Organizers create Talks for Authors to prepare them. Once the Talk is assigned to an Author, the Author can modify it as desired, and make it ready. Once a Talk is ready, Guests can subscribe to these talks, furtherly being notified when the talk is about to begin, or has been updated by the Author.

## Roles
  * Organizer
  * Author
  * Guest

## Key Architectural Concepts
  * Serverless (AWS Lambda functions)
  * Role Authorizations
  * Clean Architecture
  * Transaction Scripts
  * Rich Domain Entities
  * IaC

## Observations
I don't feel like making from this a tutorial, but I am happy to share some key points. This is by no means production-ready code, although it might be just a matter of following the same direction. This is my understanding of scalability by the way.

## Setup
### Localhost
1. Setup a Cognito Userbase with hosted UI authentication.
2. Fill up the environment variables.
3. Run the following commands:
```
docker-compose up -d
symfony server:start
```
1. Use the Cognito Hosted UI to acquire the Authentication token and set them as Bearer Token for your requests. With proper AWS credentials, the localhost application can hit Cognito with no trouble.
### AWS
1. `sls deploy`
2. Have fun 🚀

## Utility AWS Commmands

```
aws dynamodb create-table \
    --endpoint http://localhost:8000 \
    --table-name Talks \
    --attribute-definitions \
        AttributeName=id,AttributeType=S \
    --key-schema \
        AttributeName=id,KeyType=HASH \
    --provisioned-throughput \
        ReadCapacityUnits=5,WriteCapacityUnits=5 \
    --table-class STANDARD
```
```
aws dynamodb put-item \
    --endpoint http://localhost:8000 \
    --table-name Talks  \
    --item \
        '{"id": {"S": "asdfqwfeeqwefecqwf"}}'
```
```
aws dynamodb scan \
    --endpoint http://localhost:8000 \
    --table-name Talks
```
```
aws dynamodb delete-item \
    --table-name Talks \
    --endpoint http://localhost:8000 \
    --key '{"id":{"S":"59b51efc-0e40-11ef-a3e4-453e286267bd"}}'
```

```
aws dynamodb delete-table \
    --table-name Talks \
    --endpoint http://localhost:8000
```
