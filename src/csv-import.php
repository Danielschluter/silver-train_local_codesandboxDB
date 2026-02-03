<?php
require 'db.php';

$message = '';
$messageType = '';
$uploaded_file = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $table_name = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['table_name'] ?? 'imported_data');
    

            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            /*background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);*/
            background: #f0f2ff;
            min-height: 100vh;
            padding: 20px;
        }
            button:active {
                transform: translateY(0);
            }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            <?php
            require 'db.php';

            $message = '';
            $messageType = '';
            $uploaded_file = null;

            // Get DB connection (PDO or sqlsrv fallback)
            $db = get_db();
            $is_sqlsrv = is_array($db) && isset($db['type']) && $db['type'] === 'sqlsrv';
            $pdo = $is_sqlsrv ? null : $db;
            $sqlsrv_conn = $is_sqlsrv ? $db['conn'] : null;

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
                $table_name = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['table_name'] ?? 'imported_data');
    
                if (empty($table_name)) {
                    $table_name = 'imported_data';
                }
    
                $file = $_FILES['csv_file'];
    
                // Validate file upload
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $message = 'File upload error: ' . $file['error'];
                    $messageType = 'error';
                } elseif ($file['type'] !== 'text/csv' && pathinfo($file['name'], PATHINFO_EXTENSION) !== 'csv') {
                    $message = 'Please upload a valid CSV file';
                    $messageType = 'error';
                } else {
                    try {
                        $rows = [];
                        $headers = [];
            
                        // Read CSV file
                        if (($handle = fopen($file['tmp_name'], 'r')) !== false) {
                            $row_num = 0;
                            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                                <?php
                                require 'db.php';

                                $message = '';
                                $messageType = '';
                                $uploaded_file = null;

                                // Get DB connection (PDO or sqlsrv fallback)
                                $db = get_db();
                                $is_sqlsrv = is_array($db) && isset($db['type']) && $db['type'] === 'sqlsrv';
                                $pdo = $is_sqlsrv ? null : $db;
                                $sqlsrv_conn = $is_sqlsrv ? $db['conn'] : null;

                                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
                                    $table_name = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['table_name'] ?? 'imported_data');

                                    if (empty($table_name)) {
                                        $table_name = 'imported_data';
                                    }

                                    $file = $_FILES['csv_file'];

                                    // Validate file upload
                                    if ($file['error'] !== UPLOAD_ERR_OK) {
                                        $message = 'File upload error: ' . $file['error'];
                                        $messageType = 'error';
                                    } elseif ($file['type'] !== 'text/csv' && pathinfo($file['name'], PATHINFO_EXTENSION) !== 'csv') {
                                        $message = 'Please upload a valid CSV file';
                                        $messageType = 'error';
                                    } else {
                                        try {
                                            $rows = [];
                                            $headers = [];

                                            // Read CSV file
                                            if (($handle = fopen($file['tmp_name'], 'r')) !== false) {
                                                $row_num = 0;
                                                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                                                    if ($row_num === 0) {
                                                        $headers = $data;
                                                    } else {
                                                        $rows[] = $data;
                                                    }
                                                    $row_num++;
                                                }
                                                fclose($handle);
                                            }

                                            if (empty($headers)) {
                                                throw new Exception('CSV file is empty');
                                            }

                                            if (empty($rows)) {
                                                throw new Exception('CSV file has no data rows');
                                            }

                                            // Sanitize column names
                                            $clean_headers = array_map(function($header) {
                                                return preg_replace('/[^a-zA-Z0-9_]/', '_', trim($header));
                                            }, $headers);

                                            // Infer column types from first few rows
                                            $types = [];
                                            foreach ($clean_headers as $idx => $header) {
                                                $types[$header] = 'TEXT'; // Default to TEXT

                                                // Check first 10 rows to infer type
                                                $sample_rows = array_slice($rows, 0, min(10, count($rows)));
                                                $all_numeric = true;
                                                $all_integer = true;

                                                foreach ($sample_rows as $row) {
                                                    $value = trim($row[$idx] ?? '');
                                                    if ($value === '') continue;

                                                    if (!is_numeric($value)) {
                                                        $all_numeric = false;
                                                        $all_integer = false;
                                                    } elseif (strpos($value, '.') !== false) {
                                                        // Contains a decimal point, so it's not an integer
                                                        $all_integer = false;
                                                    }
                                                }

                                                if ($all_numeric) {
                                                    $types[$header] = $all_integer ? 'INTEGER' : 'DECIMAL(15,2)';
                                                }
                                            }

                                            // Check if table exists
                                            if (!$is_sqlsrv) {
                                                $table_exists_query = "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = ?)";
                                                $stmt = $pdo->prepare($table_exists_query);
                                                $stmt->execute([$table_name]);
                                                $table_exists = $stmt->fetchColumn();
                                            } else {
                                                $table_exists_query = "SELECT COUNT(*) AS cnt FROM information_schema.tables WHERE table_name = ? AND table_schema = 'dbo'";
                                                $res = sqlsrv_query($sqlsrv_conn, $table_exists_query, [$table_name]);
                                                $row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC);
                                                $table_exists = ($row['cnt'] ?? 0) > 0;
                                            }

                                            // Create table if it doesn't exist
                                            if (!$table_exists) {
                                                if (!$is_sqlsrv) {
                                                    $create_sql = "CREATE TABLE $table_name (id SERIAL PRIMARY KEY";
                                                    foreach ($clean_headers as $header) {
                                                        $create_sql .= ", \"$header\" " . $types[$header];
                                                    }
                                                    $create_sql .= ")";

                                                    $pdo->exec($create_sql);
                                                } else {
                                                    // Map types to SQL Server equivalents
                                                    $create_sql = "CREATE TABLE [$table_name] (id INT IDENTITY(1,1) PRIMARY KEY";
                                                    foreach ($clean_headers as $header) {
                                                        $colType = $types[$header];
                                                        if ($colType === 'TEXT') $sqlType = 'NVARCHAR(MAX)';
                                                        elseif ($colType === 'INTEGER') $sqlType = 'INT';
                                                        else $sqlType = $colType; // DECIMAL etc.
                                                        $create_sql .= ", [$header] $sqlType";
                                                    }
                                                    $create_sql .= ")";

                                                    sqlsrv_query($sqlsrv_conn, $create_sql);
                                                }
                                                $message = "Table '$table_name' created successfully. ";
                                            } else {
                                                $message = "Inserting data into existing table '$table_name'. ";
                                            }

                                            // Insert data
                                            if (!$is_sqlsrv) {
                                                $insert_sql = "INSERT INTO $table_name (";
                                                $insert_sql .= implode(', ', array_map(fn($h) => "\"$h\"", $clean_headers));
                                                $insert_sql .= ") VALUES (";
                                                $insert_sql .= implode(', ', array_fill(0, count($clean_headers), '?'));
                                                $insert_sql .= ")";

                                                $stmt = $pdo->prepare($insert_sql);

                                                $inserted = 0;
                                                foreach ($rows as $row) {
                                                    $values = [];
                                                    foreach ($clean_headers as $idx => $header) {
                                                        $value = trim($row[$idx] ?? '');
                                                        $values[] = $value === '' ? null : $value;
                                                    }
                                                    $stmt->execute($values);
                                                    $inserted++;
                                                }
                                            } else {
                                                $insert_sql = "INSERT INTO [$table_name] (" . implode(', ', array_map(fn($h) => "[$h]", $clean_headers)) . ") VALUES (" . implode(', ', array_fill(0, count($clean_headers), '?')) . ")";
                                                $inserted = 0;
                                                foreach ($rows as $row) {
                                                    $values = [];
                                                    foreach ($clean_headers as $idx => $header) {
                                                        $value = trim($row[$idx] ?? '');
                                                        $values[] = $value === '' ? null : $value;
                                                    }
                                                    sqlsrv_query($sqlsrv_conn, $insert_sql, $values);
                                                    $inserted++;
                                                }
                                            }

                                            $message .= "Successfully inserted $inserted rows into '$table_name'.";
                                            $messageType = 'success';
                                            $uploaded_file = [
                                                'name' => htmlspecialchars($file['name']),
                                                'table' => htmlspecialchars($table_name),
                                                'rows' => $inserted,
                                                'headers' => $clean_headers
                                            ];

                                        } catch (Exception $e) {
                                            $message = 'Error: ' . htmlspecialchars($e->getMessage());
                                            $messageType = 'error';
                                        }
                                    }
                                }

                                // Fetch existing tables
                                $existing_tables = [];
                                try {
                                    if (!$is_sqlsrv) {
                                        $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
                                        $existing_tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                    } else {
                                        $res = sqlsrv_query($sqlsrv_conn, "SELECT table_name FROM information_schema.tables WHERE table_schema = 'dbo' ORDER BY table_name");
                                        while ($r = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC)) {
                                            $existing_tables[] = $r['table_name'];
                                        }
                                    }
                                } catch (Exception $e) {
                                    // Silently fail
                                }
        }
        .empty-state {
            text-align: center;
            color: #999;
            font-size: 14px;
            padding: 20px;
        }
    </style>
    <script>
        function handleFileSelect(input) {
            const fileName = input.files[0]?.name || 'No file selected';
            const label = input.parentElement.querySelector('.file-name');
            if (label) {
                label.textContent = fileName;
            }
        }
    </script>
  </head>
  <body>
    <div class="container">
      <header>
        <h1>📊 CSV Database Importer</h1>
        <p>Upload a CSV file and automatically create/populate database tables</p>
      </header>
      
      <div class="content">
        <?php if ($message): ?>
            <div class="alert <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label>Table Name (optional)</label>
            <input type="text" name="table_name" placeholder="Leave blank for 'imported_data'" value="">
          </div>
          
          <div class="form-group">
            <label>CSV File</label>
            <label class="file-label">
              <input type="file" name="csv_file" accept=".csv" onchange="handleFileSelect(this)" required>
              <div style="font-size: 14px; color: #667eea; font-weight: 500;">
                📁 Click to select CSV file or drag & drop
              </div>
              <div class="file-name"></div>
            </label>
          </div>
          
          <button type="submit">Import CSV</button>
        </form>
        
        <?php if ($uploaded_file): ?>
            <div class="result">
              <h3>✅ Import Successful</h3>
              <p><strong>File:</strong> <?php echo $uploaded_file['name']; ?></p>
              <p><strong>Table:</strong> <?php echo $uploaded_file['table']; ?></p>
              <p><strong>Rows Inserted:</strong> <?php echo $uploaded_file['rows']; ?></p>
              <p><strong>Columns:</strong></p>
              <table>
                <thead>
                  <tr>
                    <th>Column Name</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($uploaded_file['headers'] as $header): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($header); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($existing_tables)): ?>
            <div class="tables-section">
              <h3>📋 Existing Tables</h3>
              <div class="table-list">
                <?php foreach ($existing_tables as $table): ?>
                    <div class="table-item"><?php echo htmlspecialchars($table); ?></div>
                <?php endforeach; ?>
              </div>
            </div>
        <?php else: ?>
            <div class="tables-section">
              <div class="empty-state">No tables created yet. Upload a CSV to get started!</div>
            </div>
        <?php endif; ?>
      </div>
    </div>
  </body>
</html>
