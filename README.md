# API template

## Installation

## Usage

### Routes for collection listing  

One of the most common endpoints are collection listing with paginating and filtering. Here you can find a list of all
parameters which are able to use via GET method.

* offset
* limit
* fields
* order
* filters
* with
 
---
#### LIMIT

An example configuration in a json route file:
```json
...
"limit": {
  "type": "integer",
  "default": 100,
  "minimum": 0,
  "maximum": 999999999999,
  "sanitizers": [
    "toInteger"
  ],
  "errorMessages": {
    "required": {
      "3000": "Parameter `limit` is required"
    },
    "type": {
      "3001": "Parameter `limit` has invalid type"
    },
    "minimum": {
      "3002": "Minimum `limit` is 0"
    },
    "maximum": {
      "3003": "Maximum `limit` is 999999999999"
    }
  }
},
...
```
---
#### OFFSET

An example configuration in a json route file:
```json
...
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
              "2000": "Parameter `offset` is required"
            },
            "type": {
              "2001": "Parameter `offset` has invalid type"
            },
            "minimum": {
              "2002": "Minimum `offset` is 0"
            },
            "maximum": {
              "2003": "Maximum `offset` is 999999999999"
            }
          }
        },
...
```
---

#### ORDER

If you want to have a sorted dataset by some rule, you have to define the `order` section as a object type. Properties
of this object became a order types. Each of order type should be defined as a string type with enum values 
['asc','desc'].   

An example configuration in a json route file:
```json
...
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
      "4000": "Incorrect `order` values"
    },
    "type": {
      "4001": "Incorrect `order` types"
    },
    "enum": {
      "4002": "Incorrect `order` values"
    },
    "additionalProperties": {
      "4003": "Incorrect `order` properties"
    }
  },
  "additionalProperties": false
},
...
```

The multiply order condition should be like this: `order[id]=asc&order[datetime]=desc`

**CAUTION** Sanitizer `toObject` should be always set. Orders are set in a query string as an array so casting to the 
object type is required.

**CAUTION** The best practice is to set property `additionalProperties` to `false`. It means that only defined values 
are valid. Otherwise the 400 http response code (bad request) will be returned.   


---

#### FIELDS

The collection endpoints return only entity ids as a default. If you want to have more entity data, you should 
define the `fields` array with entity property names.    

An example configuration in a json route file:
```json
...
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
      "5000": "Incorrect `order` values"
    },
    "type": {
      "5001": "Incorrect `order` types"
    },
    "enum": {
      "5002": "Incorrect `order` values"
    }
  }
},
...
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

#### FILTERS 

You can use the filtering section to define an additional conditions for the requested data set. In this case the object
type should be defined in the route json file. For each of the filter you can also set all available values but it is 
not required.  

An example configuration in a json route file:
```json
...
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
      "7000": "Incorrect `filters` values"
    },
    "type": {
      "7001": "Incorrect `filters` types"
    },
    "enum": {
      "7002": "Incorrect `filters` values"
    },
    "additionalProperties": {
      "7003": "Incorrect `filters` properties"
    }
  },
  "additionalProperties": false
}
...
```

If you want to have a collection of products which have active status and their names are started from `Addi` 
you should set parameter like this: `filters[query]=Addi&filters[active]=1`  

**CAUTION** Sanitizer `toObject` should be always set. Filters are set in a query string as an array so casting to the 
object type is required.

**CAUTION** The best practice is to set property `additionalProperties` to `false`. It means that only defined values 
are valid. Otherwise the 400 http response code (bad request) will be returned.   

---

#### WITH 

You are able to get a collection list with an additional mapping to the external resources like 
shops, photos, brands, etc. Thanks to this you have an access to more necessary data in one http request only. Remember 
to define all available bindings in `items.enum` section. 

An example configuration in a json route file:
```json
...
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
      "6001": "Incorrect `with` type"
    },
    "enum": {
      "6002": "Incorrect `with` values"
    }
  }
},
...
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




For instance if you would like to get list of first 100 products from one brand ordered by id  


An example url
```text
http://api-local.miinto.net/test/location/1-m!i!s-041-1010?offset=0&limit=100&order[id]=asc&order[datetime]=desc&fields[]=datetime&fields[]=id&filters[active]=1&filters[query]=Addid&with[]=brands
```
