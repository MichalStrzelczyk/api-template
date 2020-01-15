# API template

This repository has the whole code to create an api component template. The api application is based on Phalcon 4.0
framework. You can find the Phalcon documentation here: https://docs.phalcon.io/4.0/en/introduction. 

## Installation

```shell script
  composer install miinto/template-api
```

## Usage

### Routes for the collection listing  

One of the most common endpoints is data collection listing. Most likely there is a possibility to paginate, sort and
filter the data. For these functionalities you can use parameters: 

- offset    (pagination)
- limit     (pagination)
- order     (sorting)
- fields    (mapping)
- filters   (filtering)
- with      (binding)
 
An example url:
```text
http://api-local.miinto.net/test/location/1-m!i!s-041-1010?offset=0&limit=100&order[id]=asc&order[datetime]=desc&fields[]=datetime&fields[]=id&filters[active]=1&filters[query]=Addid&with[]=brands
```

---
##### OFFSET

The offset parameter is a basic param necessary for the pagination which informs about the number of data batch. 
The offset should always has an integer type. In the configuration route file, you can define minimum and maximum 
criteria. 
    
An example configuration in a json route file:
```json
{
  "offset": {
    "type": "integer",
    "default": 0,
    "minimum": 0,
    "maximum": 999999999999,
    "sanitizers": [
      "toInteger"
    ],
    "errorMessages": {
      "required": {
        "code": 2000,
        "message": "Parameter `offset` is required"
      },
      "type": {
        "code": 2001,
        "message": "Parameter `offset` has invalid type"
      },
      "minimum": {
        "code": 2002,
        "message": "Minimum `offset` is 0"
      },
      "maximum": {
        "code": 2003,
        "message": "Maximum `offset` is 999999999999"
      }
    }
  }
}
```

**CAUTION** Sanitizer `toInteger` should be always set. The limit parameter is set in a query string so casting to the 
integer type is required.

---


---
##### LIMIT

The limit parameter is a second basic param necessary for the pagination which gives us an information how many 
results we receive in response data. The limit should always has an integer type. In the configuration route file, 
you can define minimum and maximum criteria for limit.    

An example configuration in a json route file:
```json
{
    "limit": {
      "type": "integer",
      "default": 100,
      "minimum": 0,
      "maximum": 1000,
      "sanitizers": [
        "toInteger"
      ],
      "errorMessages": {
        "required": {
          "code": 3000,
          "message": "Parameter `limit` is required"
        },
        "type": {
          "code": 3001,
          "message": "Parameter `limit` has invalid type"
        },
        "minimum": {
          "code": 3002,          
          "message": "Minimum `limit` is 0"
        },
        "maximum": {
          "code": 3003,
          "message": "Maximum `limit` is 999999999999"
        }
      }
    }
}
```
**CAUTION** Sanitizer `toInteger` should be always set. The limit parameter is set in a query string so casting to the 
integer type is required.

##### ORDER

If you want to have a sorted dataset by some rule, you have to define the `order` section as an object type. Properties
of this object become order types. Each of order type should be defined as a string type with enum values  
['asc','desc'].   

An example configuration in a json route file:
```json
{
    "order": {
      "type": "object",
      "default": {
        "id": "desc"
      },
      "properties": {
        "id": {
          "type": "string",
          "enum": [
            "asc",
            "desc"
          ]
        },
        "datetime": {
          "type": "string",
          "enum": [
            "asc",
            "desc"
          ]
        }
      },
      "sanitizers": [
        "toObject"
      ],
      "errorMessages": {
        "required": {
          "code": 4000,
          "message": "Incorrect `order` values"
        },
        "type": {
          "code": 4001,
          "message": "Incorrect `order` types"
        },
        "enum": {
          "code": 4002,
          "message": "Incorrect `order` values"
        },
        "additionalProperties": {
          "code": 4003,
          "message": "Incorrect `order` properties"
        }
      },
      "additionalProperties": false
    }
}
```

The multiply order condition should look like this: `order[id]=asc&order[datetime]=desc`.

**CAUTION** Sanitizer `toObject` should be always set. Orders are set in a query string as an array so casting to the 
object type is required.

**CAUTION** The best practice is to set property `additionalProperties` to `false`. It means that only defined values 
are valid. Otherwise the 400 http response code (bad request) will be returned.   


---

##### FIELDS

The collection endpoints return only entity ids as a default. If you want to have more entity data, you should 
define the `fields` array with entity property names.    

An example configuration in a json route file:
```json
{
    "fields": {
      "type": "array",
      "default": [
        "id"
      ],
      "items": {
        "type": "string",
        "enum": [
          "id",
          "name"            
        ]
      },
      "errorMessages": {
        "required": {
          "code": 5000,
          "message": "Incorrect `order` values"
        },
        "type": {
          "code": 5001,
          "message": "Incorrect `order` types"
        },
        "enum": {
          "code": 5002,
          "message": "Incorrect `order` values"
        }
      }
    }
}
```

You should put `fields[]=name&fields[]=id` in query string to get names and ids of products.
```json
[
    {
        "id": 100,
        "name": ""
    },
    {
        "id": 120,
        "name": ""
    } 
]
```

---

##### FILTERS 

You can use the filtering section to define an additional conditions for the requested data set. In this case the object
type should be defined in the route json file. For each of the filter you can also set all available values but it is 
not required.  

An example configuration in a json route file:
```json
{
    "filters": {
      "type": "object",
      "default": {
        "active": "1",
        "query": ""
      },
      "properties": {
        "query": {
          "type": "string"
        },
        "active": {
          "type": "string",
          "enum": [
            "1", "0"
          ]
        }
      },
      "sanitizers": [
        "toObject"
      ],
      "errorMessages": {
        "required": {
          "code": 6000,
          "message": "Incorrect `filters` values"
        },
        "type": {
          "code": 6001,
          "message": "Incorrect `filters` types"
        },
        "enum": {
          "code": 6002,
          "message": "Incorrect `filters` values"
        },
        "additionalProperties": {
          "code": 6003,
          "message": "Incorrect `filters` properties"
        }
      },
      "additionalProperties": false
    }
}
```

If you want to have a collection of products which have active status and their names are started from `Addi` 
you should set parameter like this: `filters[query]=Addi&filters[active]=1`  

**CAUTION** Sanitizer `toObject` should be always set. Filters are set in a query string as an array so casting to the 
object type is required.

**CAUTION** The best practice is to set property `additionalProperties` to `false`. It means that only defined values 
are valid. Otherwise the 400 http response code (bad request) will be returned.   

---

##### WITH 

You are able to get a collection list with an additional mapping to the external resources like 
shops, photos, brands, etc. Thanks to this you have an access to more necessary data in one http request only. Remember 
to define all available bindings in `items.enum` section. 

An example configuration in a json route file:
```json
{
    "with": {
      "type": "array",
      "default": [],
      "items": {
        "type": "string",
        "enum": [
          "brands",
          "photos"
        ]
      },
      "errorMessages": {
        "type": {
          "code": 7000,
          "message": "Incorrect `with` type"
        },
        "enum": {
          "code": 7001,
          "message": "Incorrect `with` values"
        }
      }
    }
}
```

An example response with the parameter `with[]=photos`
```json
[
  {
    "id": 100,
    "photos": [
      {
        "id": 8947,
        "url": "https://cdn1.miinto.net/aaa/bbb/ccc/12.jpg"
      },
      {
        "id": 785,
        "url": "https://cdn1.miinto.net/aaa/bbbdasdasdsadsad.jpg"
      },
      {
        "id": 15842,
        "url": "https://cdn1.miinto.net/a.jpg"
      }
    ]
  },
  {
    "id": 120,
    "photos": [
      {
        "id": 999,
        "url": "https://cdn1.miinto.net/aaa/asdasdas.jpg"
      }
    ]
  }
]
```
---
