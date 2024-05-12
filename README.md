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
