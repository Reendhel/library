# Library API (Slim PHP API with JWT Authentication)

This repository contains a PHP-based API built using the **Slim Framework**. The API provides basic user management functionality with secure authentication using **JSON Web Tokens (JWT)**.

---

## Library API's Features
1. **User Registration**: Add a new user with a username and hashed password.
2. **User Authentication**: Validate a user's credentials and generate a JWT for session management.
3. **View All Users**: Fetch a list of all registered users (requires valid JWT).
4. **Edit User**: Update a user's details (requires valid JWT).
5. **Delete User**: Remove a user by their ID (requires valid JWT).
6. **Add Book-Author**: Adds a new book-author relationship to the database using a valid JWT token.   
7. **Edit Book-Author**: Updates an existing book-author relationship based on provided IDs and a valid JWT token.
8. **Delete Book-Author**: Deletes a specific book-author relationship from the database using a valid JWT token.
9. **View Book-Author**: Retrieves details of a specific book-author relationship by author ID and book ID with a valid JWT token.
10. **View All Book-Authors**: Retrieves a list of all book-author relationships in the database with a valid JWT token.


---


## Endpoints

 1. **User Registration**
    - **Method**: `POST`
    - **Endpoint**: `http://127.0.0.1/library/public/user/add`

    - **Request**:
        ```json
        {
            "username": "rasejo",
            "password": "john"
        }
        ```

    - **Response**:
        - **Success(200)**
            ```json
            {
                "status": "success",
                "data": null
            }
            ```
        - **Failure**
            ```json
            {
             "status": "fail",
                "data": {
                "title": "Username already exists"
                }
            }
            ```
---


  2. **User Authentication**
     - **Method**: `POST`
     - **Endpoint**: `http://127.0.0.1/library/public/user/auth`

     - **Request**:
        ```json
        {
            "username": "rasejo",
            "password": "john"
        }
        ```

     - **Response**:
        - **Success(200)**
            ```json
            {
                "status": "success",
                "data": null
            }
            ```
        - **Failure**
            ```json
            {
             "status": "fail",
                "data": {
                "title": "Authentication Failed!"
                }
            }
            ```
---
3. **View all Users**
     - **Method**: `POST`
     - **Endpoint**: `http://127.0.0.1/library/public/user/view`

     - **Request**:
        ```json
        {
            "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3Mjk1Nzg5MzUsImV4cCI6MTcyOTU4MjUzNSwiZGF0YSI6eyJ1c2VyaWQiOiI3MSJ9fQ.bdbGx-NduI2ifS5GLtJZmHET1dG2xnjECxtJbl4VS8U"
        }
        ```

     - **Response**:
        - **Success(200)**
            ```json
            {
                "status": "success",
                "data": null
            }
            ```
        - **Failure**
            ```json
            {
                "status": "fail",
                "data": {
                "message": "Token is used"
               }
            }
            ```
---

4. **Edit User**
    - **Method**: `POST`
    - **Endpoint**: `http://127.0.0.1/library/public/user/edit`

    - **Request**:
        ```json
        {
            "userid": "101",
            "username": "king",
            "password": "john",
            "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2VjdXJpdHkub3JnIiwiYXVkIjoiaHR0cDovL3NlY3VyaXR5LmNvbSIsImlhdCI6MTcyOTU3ODk5NiwiZXhwIjoxNzI5NTc5Mjk2LCJkYXRhIjp7InN0YXR1cyI6InVudXNlZCJ9fQ.mPYCTThXMe5RFOWJ-9t4lvTvk4qr-GXVGYpDjcb5794"
        }
        ```

    - **Response**:
        - **Success(200)**
            ```json
            {
                "status": "success",
                "data": null
            }
            ```
        - **Failure**
            ```json
            {
             "status": "fail",
                "data": {
                "title": "Token is used"
                }
            }
            ```
---

5. **Delete User**
    - **Method**: `POST`
    - **Endpoint**: `http://127.0.0.1/library/public/user/del`

    - **Request**:
        ```json
        {
            "userid": "30",
            "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2VjdXJpdHkub3JnIiwiYXVkIjoiaHR0cDovL3NlY3VyaXR5LmNvbSIsImlhdCI6MTcyOTU3ODk1NSwiZXhwIjoxNzI5NTc5MjU1LCJkYXRhIjp7InN0YXR1cyI6InVudXNlZCJ9fQ.VtAWb90oP58M3z3RLlmHvEPFNgIn62AAEieeUN4soEs"
        }
        ```

    - **Response**:
        - **Success(200)**
            ```json
            {
                "status": "success",
                "data": null
            }
            ```
        - **Failure**
            ```json
            {
             "status": "fail",
                "data": {
                "title": "Token is used"
                }
            }
            ```
---

6. **Add book and author**
    - **Method**: `POST`
    - **Endpoint**: `http://127.0.0.1/library/public/books_author/add`

    - **Request**:
        ```json
        {
              "author": "Dhel",
              "title": "the book1",
              "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2VjdXJpdHkub3JnIiwiYXVkIjoiaHR0cDovL3NlY3VyaXR5LmNvbSIsImlhdCI6MTcyOTU3OTAyMywiZXhwIjoxNzI5NTc5MzIzLCJkYXRhIjp7InN0YXR1cyI6InVudXNlZCJ9fQ.OoRPFFy3YIYjYpIBuN114Tbdw3PqGpV2pYKNxK5u1mY"
        }
        ```

    - **Response**:
        - **Success(200)**
            ```json
            {
                "status": "success",
                "data": null
            }
            ```
        - **Failure**
            ```json
            {
             "status": "fail",
                "data": {
                "title": "Token is used"
                }
            }
            ```
---

7. **Edit book and author**
    - **Method**: `POST`
    - **Endpoint**: `http://127.0.0.1/library/public/books_author/edit`

    - **Request**:
        ```json
        {
            "authorid": 8,
            "author": "dhhel",
            "bookid": 8,
            "title": "the king",
            "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2VjdXJpdHkub3JnIiwiYXVkIjoiaHR0cDovL3NlY3VyaXR5LmNvbSIsImlhdCI6MTcyOTU3NzUzMCwiZXhwIjoxNzI5NTc3ODMwLCJkYXRhIjp7InN0YXR1cyI6InVudXNlZCJ9fQ.zELhMCgufKv6ZxtwRFerN-5D2A6WRZBnMB-54leujKM"
        }
        ```

    - **Response**:
        - **Success(200)**
            ```json
            {
                "status": "success",
                "data": null
            }
            ```
        - **Failure**
            ```json
            {
             "status": "fail",
                "data": {
                "title": "Token is used"
                }
            }
            ```
---

8. **Delete book and author**
    - **Method**: `POST`
    - **Endpoint**: `http://127.0.0.1/library/public/books_author/delete`

    - **Request**:
        ```json
        {
            "authorid": 9,
            "bookid": 9 ,
            "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2VjdXJpdHkub3JnIiwiYXVkIjoiaHR0cDovL3NlY3VyaXR5LmNvbSIsImlhdCI6MTcyOTU3OTA5NywiZXhwIjoxNzI5NTc5Mzk3LCJkYXRhIjp7InN0YXR1cyI6InVudXNlZCJ9fQ.obUsWYyUY_0RRnYncOtSEahpd0YGVAb7ecsRH-DByAA"
        }
        ```

    - **Response**:
        - **Success(200)**
            ```json
            {
                "status": "success",
                "data": null
            }
            ```
        - **Failure**
            ```json
            {
             "status": "fail",
                "data": {
                "title": "Token is used"
                }
            }
            ```
---

9. **View book and author**
    - **Method**: `POST`
    - **Endpoint**: `http://127.0.0.1/library/public/books_author/view`

    - **Request**:
        ```json
        {
            "authorid": 9,
            "bookid": 9,
            "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2VjdXJpdHkub3JnIiwiYXVkIjoiaHR0cDovL3NlY3VyaXR5LmNvbSIsImlhdCI6MTcyOTU3OTA1NywiZXhwIjoxNzI5NTc5MzU3LCJkYXRhIjp7InN0YXR1cyI6InVudXNlZCJ9fQ.z6oKRC8SffYJLxqmv2I_8H1YTpO4YhbnKPe9mP5Nj7E"
        }
        ```

    - **Response**:
        - **Success(200)**
            ```json
            {
                "status": "success",
                "data": null
            }
            ```
        - **Failure**
            ```json
            {
             "status": "fail",
                "data": {
                "title": "Token is used"
                }
            }
            ```
---

10. **View all book and author**
    - **Method**: `POST`
    - **Endpoint**: `http://127.0.0.1/library/public/books_author/all`

    - **Request**:
        ```json
        {
            "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2VjdXJpdHkub3JnIiwiYXVkIjoiaHR0cDovL3NlY3VyaXR5LmNvbSIsImlhdCI6MTcyOTU3OTA4MCwiZXhwIjoxNzI5NTc5MzgwLCJkYXRhIjp7InN0YXR1cyI6InVudXNlZCJ9fQ.GA8AlSqMT8YorWMlTQP9Dbha-Z4wo1kJ8YGrRVqOghc"
        }
        ```

    - **Response**:
        - **Success(200)**
            ```json
            {
                "status": "success",
                "data": null
            }
            ```
        - **Failure**
            ```json
            {
             "status": "fail",
                "data": {
                "title": "Token is used"
                }
            }
            ```

---

## Contact Information

If you have any questions feel free to reach me using the provided informations below:

- **Name:** Reendhel John P. Asejo
- **Address:** Tallipugo, Balaoan, La Union
- **School:** Don Mariano Marcos Memorial State University - Mid La Union Campus
- **Email:** rasejo20212@student.dmmmsu.edu.ph
- **Facebook:** Reendhel John Asejo
