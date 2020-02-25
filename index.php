<?php 
    // $path = __DIR__;
    $path = dirname(__DIR__).'\\tester';

    $fileArray = array_diff(scandir($path),['.','..']);
    $data = [];

    foreach($fileArray as $file) {
        $modified = date('Y-m-d H:i:s', filemtime($path.'\\'.$file));
        $size = filesize($path.'\\'.$file);
        
        $data[] = [
            'name' => $file,
            'modified' => $modified,
            'size' => $size
        ];

    }



    function deleteFile($target) {
        // $filePath = $path.'\\'.$target;
        
        if(is_dir($target)) {
            $filesInDir = glob($target.'*', GLOB_MARK);

            foreach($filesInDir as $file) {
                deleteFile($file);
            }

            rmdir($target);
        }else if(is_file($target)) {
            unlink($target);
        }
    }

    if(isset($_GET['del'])) {
        $filePath = $path.'\\'.$_GET['del'];
        deleteFile($filePath);
        header('Location: index.php');
    }



    if(isset($_POST['create'])) {
        if(isset($_POST['createArea'])) {
            $pathCreate = $path.'\\'.$_POST['createArea'];

            if(strpos($_POST['createArea'],'.') !== false) {
                fopen($pathCreate, 'w');
                header('Location: index.php');
            }else{
                mkdir($pathCreate);
                header('Location: index.php');
            }
        }else{
            echo 'input type must not be empty';
        }
    }



    if(isset($_GET['download'])) {
        $fileDownPath = $path.'\\'.$_GET['download'];
        
        if(file_exists($fileDownPath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($fileDownPath).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fileDownPath)); 
            flush();
            readfile($fileDownPath);
            // die();
            header('Location: index.php');
        }else{
            echo 'no file';
        }
        
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>

    <div class="container">    

        <h1 class="text-center mt-5 mb-5">File Manager</h1>

        <label for="createFile">Create a new file or folder</label>
        <form method="POST" action="" class="form-inline mb-5">
            <div class="form-group">
                <input type="text" name="createArea" id="createFile" class="form-control mr-2">
                <input class="btn btn-primary" type="submit" name="create" value="Create">
            </div>
        </form>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Size</th>
                    <th>Modified Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $i = 0;
                    foreach($data as $d) : 
                    $i++;
                ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $d['name'] ?></td>
                        <td><?php 
                            if(!$d['size']) {
                                echo '--';
                            }else{
                                echo $d['size'] . ' bytes';
                            }
                        ?></td>
                        <td><?php echo $d['modified'] ?></td>
                        <td>
                            <?php 
                                if(strpos($d['name'],'.') !== false) : ?>
                                    <a href="?download=<?php echo $d['name'] ?>" class="btn btn-success">Download</a>
                                    <a href="edit.php?name=<?php echo $d['name'] ?>&path=<?php echo $path; ?>" class="btn btn-warning">Edit Files</a>            
                                <?php endif;
                            ?>
                            <a href="?del=<?php echo $d['name'] ?>" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>