<?php
require_once("validator.php");

if (isset($_POST) && !empty($_POST)) {
    // var_dump($_POST);
    //validation
    $data = [];
    $data["name"] = $_POST["name"];
    $data["adress"] = $_POST["adress"];
    $data["tel"] = $_POST["tel"];
    $data["email"] = $_POST["email"];

    $error = checkData($data);
}

//save new list into taskList ou updat task
if (isset($_POST) && !empty($_POST) && empty($error)) {
    if (isset($_POST["idToUpdat"]) && $_POST["idToUpdat"] !== "") {
        //updat --> submitTaskForm
        $result = trimData($data);
        $list = [];
        $list["name"] = $result["name"];
        $list["adress"] = $result["adress"];
        $list["tel"] = $result["tel"];
        $list["email"] = $result["email"];

        // $index = trimData($_POST["idToUpdat"]);
        $index = $_POST["idToUpdat"];
        $index = intval($index);
        // var_dump($index);

        //get list from taskList.json
        $listPath = "taskList.json";
        $file = file_get_contents($listPath, true);
        $fileList = json_decode($file, true);
        // var_dump($fileList);
        //updat task
        $fileList[$index] = $list;
        // var_dump($fileList);
        $fileList_json = json_encode($fileList);

        file_put_contents($listPath, $fileList_json);

        //redirection to avoid reload data
        // header("location: index.php");
    } else {
        //save new list
        //pas d'erreur --> on sauvegarde les infos
        $result = trimData($data);
        $list = [];
        $list["name"] = $result["name"];
        $list["adress"] = $result["adress"];
        $list["tel"] = $result["tel"];
        $list["email"] = $result["email"];

        //get list from taskList.json
        $listPath = "taskList.json";
        $file = file_get_contents($listPath, true);
        $fileList = json_decode($file, true);
        //add new task at the end tof array
        $taskCount = count($fileList);
        $fileList[$taskCount] = $list;
        $fileList_json = json_encode($fileList);

        file_put_contents($listPath, $fileList_json);

        //redirection to avoid reload data
        //header("location: index.php");
    }
}

//delete taskList
if (isset($_GET["deteleTask"])) {
    //pas d'erreur --> on mets à jours les infos
    $data = [];
    $data["id"] = $_GET["deteleTask"];
    $result = trimData($data);

    //get list from taskList.json
    $listPath = "taskList.json";
    $file = file_get_contents($listPath, true);
    $fileList = json_decode($file, true);

    // var_dump($fileList);
    //delete task who has id $result["id"]
    unset($fileList[$result["id"]]);

    //on reclasse les elements
    $i = 0;
    $newFileList = [];
    foreach ($fileList as $key => $value) {
        $newFileList[$i] = $value;
        $i++;
    }

    $newFileList_json = json_encode($newFileList);
    file_put_contents($listPath, $newFileList_json);

    //redirection to avoid reload data
    header("location: index.php");
}
//updat taskList
if (isset($_GET["updatTask"])) {
    //pas d'erreur --> on mets à jours les infos
    $data = [];
    $data["id"] = $_GET["updatTask"];
    $resultUpdat = trimData($data);
    $resultUpdat = intval($resultUpdat["id"]);

    //var_dump($resultUpdat);

    //get list from taskList.json
    $listPath = "taskList.json";
    $file = file_get_contents($listPath, true);
    $fileListToUpdat = json_decode($file, true);
    //upload data into form
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TodoList</title>
    <link rel="stylesheet" href="/style.css">
</head>

<body>
    <div id="app">
        <h1>Contacts</h1>
        <div class="todolist">
            <div class="form">
                <form action="index.php" method="POST">
                    <div class="first-line">
                        <input type="text" name="name" id="name" placeholder="name" value="<?php echo isset($fileListToUpdat) && !empty($fileListToUpdat) ? $fileListToUpdat[$resultUpdat]["name"] : null; ?>" required>
                    </div>
                    <div class="error error-name">
                        <?php
                        echo isset($error["name"]) && !empty($error["name"]) ? $error["name"] : null;
                        ?>
                    </div>
                    <div class="second-line">
                        <input type="text" name="adress" id="adress" placeholder="adress" value="<?php echo isset($fileListToUpdat) && !empty($fileListToUpdat) ? $fileListToUpdat[$resultUpdat]["adress"] : null; ?>" required>
                    </div>
                    <div class="error error-adress">
                        <?php
                        echo isset($error["adress"]) && !empty($error["adress"]) ? $error["adress"] : null;
                        ?>
                    </div>
                    <div class="fird-line">
                        <input type="tel" name="tel" id="tel" placeholder="tel" value="<?php echo isset($fileListToUpdat) && !empty($fileListToUpdat) ? $fileListToUpdat[$resultUpdat]["tel"] : null; ?>" required>
                    </div>
                    <div class="error error-tel">
                        <?php
                        echo isset($error["tel"]) && !empty($error["tel"]) ? $error["tel"] : null;
                        ?>
                    </div>
                    <div class="forth-line">
                        <input type="email" name="email" id="email" placeholder="email" value="<?php echo isset($fileListToUpdat) && !empty($fileListToUpdat) ? $fileListToUpdat[$resultUpdat]["email"] : null; ?>" required>
                    </div>
                    <div class="error error-email">
                        <?php
                        echo isset($error["email"]) && !empty($error["email"]) ? $error["email"] : null;
                        ?>
                    </div>
                    <div class="fifth-line">
                        <input type="hidden" name="idToUpdat" <?php echo isset($fileListToUpdat) && !empty($fileListToUpdat) ? 'value=' . $resultUpdat : null; ?>>
                        <input type="submit" value="enregistrer" id="submit-task" name="submitTaskForm">
                    </div>
                </form>
            </div>
            <div class="list">
                <?php
                //get list from taskList.json and display it
                $listPath = "taskList.json";
                $file = file_get_contents($listPath, true);
                $fileList = json_decode($file, true);
                // var_dump($fileList[1]);
                if (isset($fileList) && !empty($fileList)) {
                    for ($i = 0; $i < count($fileList); $i++) {
                        // var_dump($fileList);
                ?>
                        <div class="display-list">
                            <div class="display-list-name">
                                <?php echo '<div class="display-name">' . $fileList[$i]["name"] . '</div> <div class="display-adress">Adress : ' . $fileList[$i]["adress"] . '</div>'; ?>
                            </div>
                            <div class="display-list-tel-mail">
                                <?php echo 'Tél : ' . $fileList[$i]["tel"] . ' Email : ' . $fileList[$i]["email"]; ?>
                            </div>
                            <div class="display-manage-bt">
                                <div class="updat-list">
                                    <form action="index.php" method="GET">
                                        <input type="hidden" name="updatTask" value="<?php echo $i; ?>">
                                        <input type="submit" value="updat">
                                    </form>
                                </div>
                                <div class="delete-list">
                                    <form action="index.php" method="GET">
                                        <input type="hidden" name="deteleTask" value="<?php echo $i; ?>">
                                        <input type="submit" value="delete">
                                    </form>
                                </div>
                            </div>
                        </div>
                <?php

                    }
                } else {
                    echo '<div class="aucune-infos">
                    aucun contact enregistré !
                    </div>';
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>