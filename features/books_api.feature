Feature: In order to manage a collection of books
    As a REST API user
    I want to send rest calls

    Background:
        Given the database is clean
        And there are "book" database records
            | title                    | author                    | published_date | isbn           | uuid                                 |
            | The Great Gatsby         | Francis Scott Fitzgerald  | 2023-04-23     | 979-8392253876 | e8617343-1dfd-4e80-ab81-d53e06d30ae4 |
            | The Histories            | Publius Cornelius Tacitus | 2008-05-08     | 978-0199540709 | 8afeff05-af03-4ccd-922c-9d7c12cc2e28 |
            | The Annals               | Publius Cornelius Tacitus | 2008-06-12     | 978-0192824219 | a9ab0eb5-1077-436a-bcf6-52f89e95757a |
            | Decameron                | Giovanni Boccaccio        | 2013-04-24     | 978-8817063265 | 546dda5c-900f-4fd5-b382-c9a33571bb4c |
            | Reading Lolita in Tehran | Azar Nafisi               | 2003-12-30     | 978-0812971064 | bfcdead0-85f8-4939-bb14-b70c3d1364ce |

    Scenario: It returns the complete list of books
        When a client sends a "GET" request to "/books"
        Then the response HTTP status code should be 200
        And the JSON response should match:
        """json
        {
          "items": [
            {
              "uuid": "546dda5c-900f-4fd5-b382-c9a33571bb4c",
              "title": "Decameron",
              "author": "Giovanni Boccaccio",
              "published_date": "2013-04-24",
              "isbn": "978-8817063265"
            },
            {
              "uuid": "8afeff05-af03-4ccd-922c-9d7c12cc2e28",
              "title": "The Histories",
              "author": "Publius Cornelius Tacitus",
              "published_date": "2008-05-08",
              "isbn": "978-0199540709"
            },
            {
              "uuid": "a9ab0eb5-1077-436a-bcf6-52f89e95757a",
              "title": "The Annals",
              "author": "Publius Cornelius Tacitus",
              "published_date": "2008-06-12",
              "isbn": "978-0192824219"
            },
            {
              "uuid": "bfcdead0-85f8-4939-bb14-b70c3d1364ce",
              "title": "Reading Lolita in Tehran",
              "author": "Azar Nafisi",
              "published_date": "2003-12-30",
              "isbn": "978-0812971064"
            },
            {
              "uuid": "e8617343-1dfd-4e80-ab81-d53e06d30ae4",
              "title": "The Great Gatsby",
              "author": "Francis Scott Fitzgerald",
              "published_date": "2023-04-23",
              "isbn": "979-8392253876"
            }
          ],
          "total_items": 5,
          "total_pages": 1,
          "current_page": 1,
          "items_per_page": 30
        }
        """

    Scenario: It returns a paginated list of books
        When a client sends a "GET" request to "/books?page=2&itemsPerPage=2"
        Then the response HTTP status code should be 200
        And the JSON response should match:
        """json
        {
          "items": [
            {
              "uuid": "a9ab0eb5-1077-436a-bcf6-52f89e95757a",
              "title": "The Annals",
              "author": "Publius Cornelius Tacitus",
              "published_date": "2008-06-12",
              "isbn": "978-0192824219"
            },
            {
              "uuid": "bfcdead0-85f8-4939-bb14-b70c3d1364ce",
              "title": "Reading Lolita in Tehran",
              "author": "Azar Nafisi",
              "published_date": "2003-12-30",
              "isbn": "978-0812971064"
            }
          ],
          "total_items": 5,
          "total_pages": 3,
          "current_page": 2,
          "items_per_page": 2
        }
        """

    Scenario: It verifies pagination size parameter
        When a client sends a "GET" request to "/books?itemsPerPage=-2"
        Then the response HTTP status code should be 400
        And the JSON response should match:
        """json
        "Max items must be greater than 1 and less than or equal to 100"
        """

    Scenario: It verifies page parameter
        When a client sends a "GET" request to "/books?page=-4"
        Then the response HTTP status code should be 400
        And the JSON response should match:
        """json
        "Current page must be greater than 0"
        """

    Scenario: It returns a single book
        When a client sends a "GET" request to "/books/8afeff05-af03-4ccd-922c-9d7c12cc2e28"
        Then the response HTTP status code should be 200
        And the JSON response should match:
        """json
        {
          "uuid": "8afeff05-af03-4ccd-922c-9d7c12cc2e28",
          "title": "The Histories",
          "author": "Publius Cornelius Tacitus",
          "published_date": "2008-05-08",
          "isbn": "978-0199540709"
        }
        """

    Scenario: It returns a not found HTTP code for a missing book
        When a client sends a "GET" request to "/books/00000000-af03-4ccd-922c-9d7c12cc2e28"
        Then the response HTTP status code should be 404

    Scenario: It verifies uuid parameter
        When a client sends a "GET" request to "/books/not-an-uuid"
        Then the response HTTP status code should be 404