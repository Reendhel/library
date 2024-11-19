<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require '../src/vendor/autoload.php';

$app = new \Slim\App;

//user registration
$app->post('/user/add', function (Request $request, Response $response, array $args) {

    $data = json_decode($request->getBody());
    $usr = $data->username;
    $pass = $data->password;

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "INSERT INTO users (username, password) VALUES ('".$usr."','".hash('SHA256',$pass)."')";
        $conn->exec($sql);
        $response->getBody()->write(json_encode(array("status"=>"success", "data"=>null)));
    } catch(PDOException $e) {
        $response->getBody()->write(json_encode(array("status"=>"fail","data"=>array("title"=>$e->getMessage()))));
    }
    return $response;
});

$app->post('/user/auth', function (Request $request, Response $response, array $args) {

    $data = json_decode($request->getBody());
    $usr = $data->username;
    $pass = $data->password;

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "SELECT * FROM users WHERE username='".$usr."' AND password = '".hash('SHA256', $pass)."'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $data= $stmt->fetchAll();

        if(count($data)== 1){
            //echo username
            $key = 'server_hack';
            $iat = time();
            $payload = [
                'iss'=> 'http://library.org',
                'aud'=> 'http://library.com',
                'iat'=> $iat,
                'exp'=>$iat + 3600,
                'data'=> array (
                    "userid"=> $data[0]['userid'])];
            $jwt=JWT::encode($payload, $key, 'HS256');

            $insertTokenSql = "INSERT INTO token(token, status) VALUES ('".$jwt."', 'unused')";
            $stmt = $conn->prepare($insertTokenSql);
            $stmt->execute();

            $response->getBody()->write(json_encode(array("status"=>"success","token"=> $jwt, "data"=>null)));
        }else{
            $response->getBody()-> write(json_encode(array("status"=>"fail","data"=>array("title"=>"Authentication Failed!"))));
        }
    }catch(PDOException $e){
        $response->getBody()->write(json_encode(array("status"=>"fail","data"=>array("title"=>$e->getMessage()))));
    }

    return $response;
});

//view all user
$app->post('/user/all', function(Request $request, Response $response, array $args){
    $data = json_decode($request->getBody());

    $jwt = $data -> token;

    $key = 'server_hack';
    $expire = time();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library";


    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_token = "SELECT * FROM token WHERE token = '".$jwt."'";
        $stmt = $conn -> prepare($sql_token);
        $stmt -> execute();
        $stmt -> setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();

        $token_status = $result[0]["status"];

        if(count($result) == 1 && $token_status === "unused"){

            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

            $sql_token_update = "UPDATE token SET status='used' WHERE token='".$jwt."'" ;
            $stmt = $conn -> prepare($sql_token_update);
            $stmt -> execute();

            $sql_user = "SELECT * FROM users";
            $stmt = $conn -> prepare($sql_user);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $payload = [
                'iss' => 'http://security.org',
                'aud' => 'http://security.com',
                'iat' => $expire,
                'exp' => $expire + 300,
                'data' => array(
                    "status" => $token_status,
                )
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');
            $sql_token = "INSERT INTO token(token, status) VALUES ('".$jwt."', 'unused')";
            $stmt = $conn -> prepare($sql_token);
            $stmt -> execute();
            
            $response -> getBody() -> write(json_encode(array(
                "status" => "Success", 
                "data" => array(
                    "users" => $users,
                    "new_token" => $jwt))));

        }else{
            $response -> getBody() -> write(json_encode(array(
                "status" => "fail", 
                "data" => array(
                    "message" => "Token is used"))));
        }

    }catch (PDOException $e){
        $response -> getBody() -> write(json_encode(array(
            "status" => "fail", 
            "data" => array(
                "title" => $e ->getMessage()
        ))));
    }

    $conn = null;

    return $response;

});



//delete user
$app->post('/user/del', function(Request $request, Response $response, array $args){
    $data = json_decode($request->getBody());

    $jwt = $data -> token;
    $userid = $data -> userid;

    $key = 'server_hack';
    $expire = time();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library";

    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_token = "SELECT * FROM token WHERE token = '".$jwt."'";
        $stmt = $conn -> prepare($sql_token);
        $stmt -> execute();
        $stmt -> setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();

        $token_status = $result[0]["status"];

        if(count($result) == 1 && $token_status === "unused"){

            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

            $sql_token_update = "UPDATE token SET status='used' WHERE token='".$jwt."'" ;
            $stmt = $conn -> prepare($sql_token_update);
            $stmt -> execute();

            $sql_user = "DELETE FROM users WHERE userid = '".$userid."'";
            $stmt = $conn -> prepare($sql_user);
            $stmt->execute();

            $payload = [
                'iss' => 'http://security.org',
                'aud' => 'http://security.com',
                'iat' => $expire,
                'exp' => $expire + 300,
                'data' => array(
                    "status" => $token_status,
                )
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');
            $sql_token = "INSERT INTO token(token, status) VALUES ('".$jwt."', 'unused')";
            $stmt = $conn -> prepare($sql_token);
            $stmt -> execute();
            
            $response -> getBody() -> write(json_encode(array(
                "status" => "Success", 
                "data" => array(
                    "new_token" => $jwt))));

        }else{
            $response -> getBody() -> write(json_encode(array(
                "status" => "fail", 
                "data" => array(
                    "message" => "Token is used"))));
        }

    }catch (PDOException $e){
        $response -> getBody() -> write(json_encode(array(
            "status" => "fail", 
            "data" => array(
                "title" => $e->getMessage()
        ))));
    }

    $conn = null;

    return $response;

});

//edit user
$app->post('/user/edit', function(Request $request, Response $response, array $args){
    $data = json_decode($request->getBody());

    $jwt = $data -> token;
    $userid = $data -> userid;
    $usr = $data -> username;
    $pass = $data -> password;

    $key = 'server_hack';
    $expire = time();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library";

    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_token = "SELECT * FROM token WHERE token = '".$jwt."'";
        $stmt = $conn -> prepare($sql_token);
        $stmt -> execute();
        $stmt -> setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();

        $token_status = $result[0]["status"];

        if(count($result) == 1 && $token_status === "unused"){

            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

            $sql_token_update = "UPDATE token SET status='used' WHERE token='".$jwt."'" ;
            $stmt = $conn -> prepare($sql_token_update);
            $stmt -> execute();

            $sql_user = "UPDATE users SET username = '".$usr."', password = '".hash('SHA256', $pass)."' WHERE userid = '".$userid."'";
            $stmt = $conn -> prepare($sql_user);
            $stmt->execute();

            $payload = [
                'iss' => 'http://security.org',
                'aud' => 'http://security.com',
                'iat' => $expire,
                'exp' => $expire + 300,
                'data' => array(
                    "status" => $token_status,
                )
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');

            $sql_token = "INSERT INTO token(token, status) VALUES ('".$jwt."', 'unused')";
            $stmt = $conn -> prepare($sql_token);
            $stmt -> execute();
            
            $response -> getBody() -> write(json_encode(array(
                "status" => "Success", 
                "data" => array(
                    "new_token" => $jwt))));

        }else{
            $response -> getBody() -> write(json_encode(array(
                "status" => "fail", 
                "data" => array(
                    "message" => "Token is used"))));
        }

    }catch (PDOException $e){
        $response -> getBody() -> write(json_encode(array(
            "status" => "fail", 
            "data" => array(
                "title" => $e->getMessage()
        ))));
    }

    $conn = null;

    return $response;

});

//add-books-author
$app->post('/books_author/add', function(Request $request, Response $response, array $args){
    $data = json_decode($request->getBody());

    $author = $data -> author;
    $title = $data -> title;
    $jwt = $data -> token;

    $key = 'server_hack';
    $expire = time();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library";

    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_token = "SELECT * FROM token WHERE token = '".$jwt."'";
        $stmt = $conn -> prepare($sql_token);
        $stmt -> execute();
        $stmt -> setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();

        $token_status = $result[0]["status"];

        if(count($result) == 1 && $token_status === "unused"){

            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

            $sql_token_update = "UPDATE token SET status='used' WHERE token='".$jwt."'" ;
            $stmt = $conn -> prepare($sql_token_update);
            $stmt -> execute();

            $sql_author = "INSERT INTO authors(author) VALUES ('".$author."')";
            $stmt = $conn -> prepare($sql_author);
            $stmt->execute();
            $author_id = $conn -> lastInsertId();

            $sql_book = "INSERT INTO books(title) VALUES ('".$title."')";
            $stmt = $conn -> prepare($sql_book);
            $stmt -> execute();
            $book_id = $conn -> lastInsertId();

            $sql_collection = "INSERT INTO books_authors(bookid, authorid) VALUES ('".$book_id."', '".$author_id."')";
            $stmt = $conn -> prepare($sql_collection);
            $stmt -> execute();

            $payload = [
                'iss' => 'http://security.org',
                'aud' => 'http://security.com',
                'iat' => $expire,
                'exp' => $expire + 300,
                'data' => array(
                    "status" => $token_status,
                )
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');

            $sql_token = "INSERT INTO token(token, status) VALUES ('".$jwt."', 'unused')";
            $stmt = $conn -> prepare($sql_token);
            $stmt -> execute();

            $response -> getBody() -> write(json_encode(array(
                "status" => "Success", 
                "data" => array(
                    "new_token" => $jwt))));

        }else{
            $response -> getBody() -> write(json_encode(array(
                "status" => "fail", 
                "data" => array(
                    "message" => "Token is used"))));
        }

    }catch (PDOException $e){
        $response -> getBody() -> write(json_encode(array(
            "status" => "fail", 
            "data" => array(
                "title" => $e->getMessage()
        ))));
    }

    $conn = null;

    return $response;

});

//edit-books-author
$app->post('/books_author/edit', function(Request $request, Response $response, array $args){
    $data = json_decode($request->getBody());

    $authorid = $data -> authorid;
    $author = $data -> author;
    $bookid = $data -> bookid;
    $title = $data -> title;
    $jwt = $data -> token;

    $key = 'server_hack';
    $expire = time();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library";

    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_token = "SELECT * FROM token WHERE token = '".$jwt."'";
        $stmt = $conn -> prepare($sql_token);
        $stmt -> execute();
        $stmt -> setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();

        $token_status = $result[0]["status"];

        if(count($result) == 1 && $token_status === "unused"){

            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

            $sql_token_update = "UPDATE token SET status='used' WHERE token='".$jwt."'" ;
            $stmt = $conn -> prepare($sql_token_update);
            $stmt -> execute();

            $sql_author = "UPDATE authors SET author = '".$author."' WHERE authorid = '".$authorid."'";
            $stmt = $conn -> prepare($sql_author);
            $stmt->execute();

            $sql_book = "UPDATE books SET title = '".$title."' WHERE bookid = '".$bookid."'";
            $stmt = $conn -> prepare($sql_book);
            $stmt -> execute();

            $payload = [
                'iss' => 'http://security.org',
                'aud' => 'http://security.com',
                'iat' => $expire,
                'exp' => $expire + 300,
                'data' => array(
                    "status" => $token_status,
                )
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');

            $sql_token = "INSERT INTO token(token, status) VALUES ('".$jwt."', 'unused')";
            $stmt = $conn -> prepare($sql_token);
            $stmt -> execute();

            $response -> getBody() -> write(json_encode(array(
                "status" => "Success", 
                "data" => array(
                    "new_token" => $jwt))));

        }else{
            $response -> getBody() -> write(json_encode(array(
                "status" => "fail", 
                "data" => array(
                    "message" => "Token is used"))));
        }

    }catch (PDOException $e){
        $response -> getBody() -> write(json_encode(array(
            "status" => "fail", 
            "data" => array(
                "title" => $e->getMessage()
        ))));
    }

    $conn = null;

    return $response;

});

//delete-books-author
$app->post('/books_author/delete', function(Request $request, Response $response, array $args){
    $data = json_decode($request->getBody());

    $authorid = $data -> authorid;
    $bookid = $data -> bookid;
    $jwt = $data -> token;

    $key = 'server_hack';
    $expire = time();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library";

    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_token = "SELECT * FROM token WHERE token = '".$jwt."'";
        $stmt = $conn -> prepare($sql_token);
        $stmt -> execute();
        $stmt -> setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();

        $token_status = $result[0]["status"];

        if(count($result) == 1 && $token_status === "unused"){

            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

            $sql_token_update = "UPDATE token SET status='used' WHERE token='".$jwt."'" ;
            $stmt = $conn -> prepare($sql_token_update);
            $stmt -> execute();

            $sql_author = "DELETE FROM authors WHERE authorid = '".$authorid."'";
            $stmt = $conn -> prepare($sql_author);
            $stmt->execute();

            $sql_book = "DELETE FROM books WHERE bookid = '".$bookid."'";
            $stmt = $conn -> prepare($sql_book);
            $stmt -> execute();

            $sql_delete_books_authors = "DELETE FROM books_authors WHERE authorid = '".$authorid."' AND bookid = '".$bookid."'";
            $stmt = $conn->prepare($sql_delete_books_authors);
            $stmt->execute();

            $payload = [
                'iss' => 'http://security.org',
                'aud' => 'http://security.com',
                'iat' => $expire,
                'exp' => $expire + 300,
                'data' => array(
                    "status" => $token_status,
                )
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');

            $sql_token = "INSERT INTO token(token, status) VALUES ('".$jwt."', 'unused')";
            $stmt = $conn -> prepare($sql_token);
            $stmt -> execute();

            $response -> getBody() -> write(json_encode(array(
                "status" => "Success", 
                "data" => array(
                    "new_token" => $jwt))));

        }else{
            $response -> getBody() -> write(json_encode(array(
                "status" => "fail", 
                "data" => array(
                    "message" => "Token is used"))));
        }

    }catch (PDOException $e){
        $response -> getBody() -> write(json_encode(array(
            "status" => "fail", 
            "data" => array(
                "title" => $e->getMessage()
        ))));
    }

    $conn = null;

    return $response;

});

//view-books-author
$app->post('/books_author/view', function(Request $request, Response $response, array $args){
    $data = json_decode($request->getBody());

    $authorid = $data -> authorid;
    $bookid = $data -> bookid;
    $jwt = $data -> token;

    $key = 'server_hack';
    $expire = time();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library";

    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_token = "SELECT * FROM token WHERE token = '".$jwt."'";
        $stmt = $conn -> prepare($sql_token);
        $stmt -> execute();
        $stmt -> setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();

        $token_status = $result[0]["status"];

        if(count($result) == 1 && $token_status === "unused"){

            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

            $sql_token_update = "UPDATE token SET status='used' WHERE token='".$jwt."'" ;
            $stmt = $conn -> prepare($sql_token_update);
            $stmt -> execute();

            $sql_author = "SELECT 
                                books.title AS book_title, 
                                authors.author AS author_author
                            FROM 
                                books_authors
                            INNER JOIN 
                                books ON books.bookid = books_authors.bookid
                            INNER JOIN 
                                authors ON authors.authorid = books_authors.authorid
                            ";
            $stmt = $conn -> prepare($sql_author);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $payload = [
                'iss' => 'http://security.org',
                'aud' => 'http://security.com',
                'iat' => $expire,
                'exp' => $expire + 300,
                'data' => array(
                    "status" => $token_status,
                )
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');
            $sql_token = "INSERT INTO token(token, status) VALUES ('".$jwt."', 'unused')";
            $stmt = $conn -> prepare($sql_token);
            $stmt -> execute();

            $response -> getBody() -> write(json_encode(array(
                "status" => "Success", 
                "data" => array(
                    "user" => $user,
                    "new_token" => $jwt))));

        }else{
            $response -> getBody() -> write(json_encode(array(
                "status" => "fail", 
                "data" => array(
                    "message" => "Token is used"))));
        }

    }catch (PDOException $e){
        $response -> getBody() -> write(json_encode(array(
            "status" => "fail", 
            "data" => array(
                "title" => $e->getMessage()
        ))));
    }

    $conn = null;

    return $response;

});

//view all books-authors
$app->post('/book_author/all', function(Request $request, Response $response, array $args){
    $data = json_decode($request->getBody());

    $jwt = $data -> token;

    $key = 'server_hack';
    $expire = time();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library";

    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_token = "SELECT * FROM token WHERE token = '".$jwt."'";
        $stmt = $conn -> prepare($sql_token);
        $stmt -> execute();
        $stmt -> setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();

        $token_status = $result[0]["status"];

        if(count($result) == 1 && $token_status === "unused"){

            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

            $sql_token_update = "UPDATE token SET status='used' WHERE token='".$jwt."'" ;
            $stmt = $conn -> prepare($sql_token_update);
            $stmt -> execute();


            $sql_user = "SELECT
                            books.title AS book_title,
                            authors.author AS author_author
                        FROM 
                            books_authors
                        INNER JOIN 
                            books ON books.bookid = books_authors.bookid
                        INNER JOIN 
                            authors ON authors.authorid = books_authors.authorid;";
            $stmt = $conn -> prepare($sql_user);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $payload = [
                'iss' => 'http://security.org',
                'aud' => 'http://security.com',
                'iat' => $expire,
                'exp' => $expire + 300,
                'data' => array(
                    "status" => $token_status,
                )
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');

            $sql_token = "INSERT INTO token(token, status) VALUES ('".$jwt."', 'unused')";
            $stmt = $conn -> prepare($sql_token);
            $stmt -> execute();
            
            $response -> getBody() -> write(json_encode(array(
                "status" => "Success", 
                "data" => array(
                    "users" => $users,
                    "new_token" => $jwt))));

        }else{
            $response -> getBody() -> write(json_encode(array(
                "status" => "fail", 
                "data" => array(
                    "message" => "Token is used."))));
        }

    }catch (PDOException $e){
        $response -> getBody() -> write(json_encode(array(
            "status" => "fail", 
            "data" => array(
                "title" => $e->getMessage()
        ))));
    }

    $conn = null;

    return $response;

});

$app->run();
?>