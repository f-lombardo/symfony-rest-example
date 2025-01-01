Feature: In order read openapi documentation
  As a user
  I want to download openapi documentation related to this application

  Scenario: I can download the openapi json file
    When a client sends a "GET" request to "/api/openapi.json"
    Then the response HTTP status code should be 200
    And the JSON response should match:
        """JSON
        {
            "openapi": "3.0.0",
            "info": {
                "title": "Books",
                "description": "A CRUD REST API example handling books",
                "version": "1.0.0"
            },
            "paths": {
              "/books": "@json@",
              "/books/{uuid}": "@json@"
            },
            "components": "@json@",
            "tags": [
              {
                "name": "Books",
                "description": "Books"
              }
            ]
        }
        """