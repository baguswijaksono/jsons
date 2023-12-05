<?php
$dbConfig = array(
    "servername" => "localhost",
    "username" => "root",
    "password" => " ",
    "dbname" => "jsons"
);

$APP = array(
    "name" => "JSONS",
    "dns" => "https://jsons.com",
);

$conn = new mysqli($dbConfig['servername'], $dbConfig['username'], $dbConfig['password'], $dbConfig['dbname']);

if (isset($_GET['page'])) {
    $page = $_GET['page'];
    if ($page == "migrate") {
        startdbmigrate($conn);
    } elseif ($page == "register") {
        register_page();
    } elseif ($page == "register_handle") {
        user_register_handle($conn);
    } elseif ($page == "login") {
        login_page();
    } elseif ($page == "login_handle") {
        user_login_handle($conn);
    } elseif ($page == "logout_handle") {
        user_logout_handle();
    } elseif ($page == "add") {
        add_json_page();
    } elseif ($page == "add_json_handle") {
        add_json_handle($conn);
    } elseif ($page == "edit") {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            edit_json_page($conn, $id);
        } else {
            nf404page(true);
        }
    } elseif ($page == "json") {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            getJson($conn, $id);
        } else {
            nf404page(true);
        }
    } elseif ($page == "edit_json_handle") {
        update_json_handle($conn);
    } elseif ($page == "delete") {
        delete_json_handle($conn);
    } else {
        homepage($conn);
    }
} else {
    homepage($conn);
}

function getJson($conn, $id)
{
    $sql = "SELECT jsons FROM jsons WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Kesalahan persiapan kueri: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo $row['jsons'];
    } else {
        return "no data";
    }
}



function nf404page($isRequiredNavbar)
{
    bootstrap_head('404 Not Found');
    if ($isRequiredNavbar === true) {
        navbar();
    }

    ?>
    <div class="page-wrap d-flex flex-row align-items-center" style="min-height: 90vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 text-center">
                    <span class="display-1 d-block">404</span>
                    <div class="mb-4 lead">The page you are looking for was not found.</div>
                    <a href="?page=home" class="btn btn-link">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
    <?php
    bootstrap_foot();
}


function bootstrap_head($pagename)
{
    ?>
    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>
            <?php echo $pagename ?>
        </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    </head>

    <body>
        <?php
}

function navbar()
{
    ?>
        <div id="app">
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container">
                    <a class="navbar-brand" href="?page=home">
                        Jsons
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto">

                        </ul>
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Bagus Muhammad Wijaksono
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="?page=add">
                                        Add Json
                                    </a>
                                    <a class="dropdown-item" href="?page=logout_handle" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>



                                    <form id="logout-form" action="?page=logout_handle" method="POST" class="d-none">

                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
        <?php
}

function bootstrap_foot()
{
    ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    </body>

    </html>
    <?php
}

function homepage($conn)
{
    session_start();
    if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
        bootstrap_head('Home');
        navbar();

        $sql = "SELECT id, userid, name, jsons FROM jsons WHERE userid = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Kesalahan persiapan kueri: " . $conn->error);
        }

        $userid = $_SESSION['user_id'];
        $stmt->bind_param("s", $userid);
        $stmt->execute();

        $result = $stmt->get_result();

        ?>
        <div class="container p-4">

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Json</th>
                        <th scope="col">Fetch Url Endpoint</th>
                        <th scope="col">Edit</th>
                        <th scope="col">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <th scope="row">1</th>
                                <td>
                                    <?php echo $row["name"]; ?>
                                </td>

                                <td>
                                    <a href="?page=json&id=<?php echo $row["id"]; ?>" class="btn btn-dark btn-sm">JSON</button>
                                </td>
                                <td>?page=json&id=
                                    <?php echo $row["id"]; ?>
                                </td>
                                <td>
                                    <a href="?page=edit&id=<?php echo $row["id"]; ?>" class="btn btn-primary btn-sm">Edit</a>
                                </td>
                                <td>
                                    <form action="?page=delete" method="POST">
                                        <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this item?')">Delete</button>
                                    </form>

                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "Tidak ada data yang ditemukan";
                    }

                    ?>

                </tbody>
            </table>
        </div>
        <?php
        $stmt->close();
        $conn->close();

        bootstrap_foot();
    } else {
        header("Location: ?page=login");
        exit();
    }

}

function basicFormControl($formLabel, $type, $name, $id, $isValueRequired, $value)
{
    ?>
    <div class="mb-3">
        <label for="<?php echo $id ?>" class="form-label">
            <?php echo $formLabel ?>
        </label>
        <input type="<?php echo $type ?>" name="<?php echo $name ?>" class="form-control" id="<?php echo $id ?>" <?php if ($isValueRequired)
                     echo 'value="' . $value . '"' ?>>
        </div>
    <?php
}


function register_page()
{
    bootstrap_head('Register Page');
    session_start();
    ?>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <form action="?page=register_handle" method="post" class="mx-auto" style="width: 500px;">
            <?php
            if (isset($_SESSION['message'])) {
                if ($_SESSION['message'] == 'notfill') {
                    ?>
                    <div class="alert alert-danger" role="alert">
                        Fill All Form!
                    </div>
                    <?php
                } elseif ($_SESSION['message'] == 'fail') {
                    ?>
                    <div class="alert alert-danger" role="alert">
                        Username Already Exsist!
                    </div>
                    <?php

                }
                ?>
                <?php
                unset($_SESSION['message']);
            }
            basicFormControl('Username', 'text', 'fullname', 'fullname', false, null);
            basicFormControl('Email Address', 'email', 'username', 'username', false, null);
            basicFormControl('Password', 'password', 'password', 'password', false, null);
            ?>
            <button type="submit" class="btn btn-dark">Submit</button>
        </form>
    </div>
    <?php
    bootstrap_foot();
}

function login_page()
{
    bootstrap_head('Login Page');
    ?>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <?php
        session_start();
        ?>
        <form action="?page=login_handle" method="post" class="mx-auto" style="width: 500px;">

            <?php
            if (isset($_SESSION['message'])) {

                if ($_SESSION['message'] == 'failpass') {
                    ?>
                    <div class="alert alert-danger" role="alert">
                        Password Incorrect!
                    </div>
                    <?php
                } elseif ($_SESSION['message'] == 'failusr') {
                    ?>
                    <div class="alert alert-danger" role="alert">
                        Username Not Exist!
                    </div>
                    <?php

                } elseif ($_SESSION['message'] == 'notfill') {
                    ?>
                    <div class="alert alert-danger" role="alert">
                        Fill All Form!
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="alert alert-success" role="alert">
                        User Registration Success
                    </div>
                    <?php

                }
                unset($_SESSION['message']);
            }


            basicFormControl('Email Address', 'email', 'username', 'username', false, null);
            basicFormControl('Password', 'password', 'password', 'password', false, null);
            ?>
            <button type="submit" class="btn btn-dark">Submit</button>
        </form>
    </div>
    <?php
    bootstrap_foot();
}

function user_register_handle($conn)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $fullname = $_POST["fullname"];
        $username = $_POST["username"];
        $password = $_POST["password"];

        if (!empty($username) && !empty($password)) {
            $check_query = "SELECT * FROM users WHERE username = '$username'";
            $result = $conn->query($check_query);

            if ($result->num_rows == 0) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert_query = "INSERT INTO users (username, password, fullname) VALUES ('$username', '$hashed_password','$fullname')";

                if ($conn->query($insert_query) === TRUE) {
                    session_start();
                    $_SESSION['message'] = 'succes';
                    header("Location: ?page=home");
                    exit;
                } else {
                    echo "Error: " . $insert_query . "<br>" . $conn->error;
                }
            } else {
                session_start();
                $_SESSION['message'] = 'fail';
                header("Location: ?page=register");
                exit;
            }
        } else {
            session_start();
            $_SESSION['message'] = 'notfill';
            header("Location: ?page=register");
            exit;
        }
    }

}

function user_login_handle($conn)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];

        if (!empty($username) && !empty($password)) {
            $check_query = "SELECT id, username, password, role FROM users WHERE username = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    header("Location: ?page=home");
                    exit();
                } else {
                    session_start();
                    $_SESSION['message'] = 'failpass';
                    header("Location: ?page=login");
                    exit;
                }
            } else {
                session_start();
                $_SESSION['message'] = 'failusr';
                header("Location: ?page=login");
                exit;
            }
        } else {
            session_start();
            $_SESSION['message'] = 'notfill';
            header("Location: ?page=login");
            exit;
        }
    }

}

function user_logout_handle()
{
    session_start();
    session_unset();
    session_destroy();
    header("Location: ?page=home");
    exit();
}

function startdbmigrate($conn)
{
    function create_user_table($conn)
    {
        $sql = "CREATE TABLE users (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(20) NOT NULL DEFAULT 'user',
            fullname VARCHAR(100) 
        )";

        if ($conn->query($sql) === TRUE) {
            echo "Table 'users' created successfully";
        } else {
            echo "Error creating table: " . $conn->error;
        }

    }

    function create_jsons_table($conn)
    {
        $sql = "CREATE TABLE jsons (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            userid INT(6) UNSIGNED NOT NULL,
            name VARCHAR(50) NOT NULL,
            jsons LONGTEXT,
            FOREIGN KEY (userid) REFERENCES users(id)
        )";

        if ($conn->query($sql) === TRUE) {
            echo "Table 'jsons' created successfully";
        } else {
            echo "Error creating table: " . $conn->error;
        }

    }
    create_user_table($conn);
    create_jsons_table($conn);

    header("Location: /betta");
    exit;

}

function add_json_page()
{
    bootstrap_head('Add JSON Data Page');
    navbar();
    ?>
    <div class="container p-4">
        <form action="?page=add_json_handle" method="post" enctype="multipart/form-data">
            <div class="container">
                <?php
                session_start();
                if (isset($_SESSION['message'])) {
                    if ($_SESSION['message'] == 'notfill') {
                        ?>
                        <div class="alert alert-danger" role="alert">
                            Fill All Form!
                        </div>
                        <?php
                    } 
                    unset($_SESSION['message']);
                }

                basicFormControl('Name', 'text', 'name', 'name', false, null);
                ?>
                <div class="mb-3">
                    <label class="form-label">Json</label>
                    <textarea class="form-control" name="json" rows="6"></textarea>
                </div>
                <button type="submit" class="btn btn-dark">Submit</button>
            </div>
        </form>
    </div>
    <?php
    bootstrap_foot();
}


function add_json_handle($conn)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'];
        $json = $_POST['json'];
        session_start();
        $userId = $_SESSION['user_id'];

        if (!empty($name) && !empty($json)) {
            $sql = "INSERT INTO jsons (id, name, jsons, userid) VALUES (NULL, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $name, $json, $userId);

            if ($stmt->execute() === TRUE) {
                header("Location: ?page=home");
                exit;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            $stmt->close();
        } else {
            session_start();
            $_SESSION['message'] = 'notfill';
            header("Location: ?page=add");
            exit;

        }
        $conn->close();
        header("Location: ?page=add");
        exit;
    }
}



function edit_json_page($conn, $id)
{

    bootstrap_head('Edit Fish Data Page');
    navbar();

    $sql = "SELECT * FROM jsons WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="container p-4">
                        <form action="?page=edit_json_handle" method="post">
                            <div class="container">
                                <input type="hidden" name="id" value="<?php echo $id ?>">
                                <?php
                                basicFormControl('Name', 'text', 'name', 'name', true, $row["name"]);

                                ?>
                                <div class="mb-3">
                                    <label class="form-label">Json</label>
                                    <textarea class="form-control" name="json" rows="3"> <?php echo $row["jsons"] ?> </textarea>
                                </div>
                                <button type="submit" class="btn btn-dark">Submit</button>
                            </div>
                        </form>
                    </div>
                    <?php
                }
            } else {
                nf404page(false);
            }
        } else {
            echo "Error in fetching results";
        }
        $result->close();
    } else {
        echo "Error in preparing statement: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
    bootstrap_foot();

}

function update_json_handle($conn)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $json = $_POST['json'];

        if (!empty($id) && !empty($name) && !empty($json)) {
            $sql = "UPDATE jsons SET name = ?, jsons = ? WHERE id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $name, $json, $id);

            if ($stmt->execute() === TRUE) {
                header("Location: ?page=home");
                exit;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            $stmt->close();
        } else {
            echo "Please fill in all fields!";
        }

    }
}


function delete_json_handle($conn)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = $_POST['id'];

        if (!empty($id)) {
            $sql = "DELETE FROM jsons WHERE id = ?";

            $stmt = $conn->prepare($sql);

            // Bind parameter
            $stmt->bind_param("i", $id);

            if ($stmt->execute() === TRUE) {
                header("Location: ?page=home");
                exit;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            $stmt->close();
        } else {
            echo "Please provide an ID to delete";
        }
        $conn->close();
        header("Location: ?page=home");
        exit;
    }
}


?>