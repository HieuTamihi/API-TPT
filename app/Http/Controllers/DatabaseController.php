<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    /**
     * Hiển thị trang quản lý database
     */
    public function index()
    {
        return view('database.index', [
            'title' => 'Quản lý Database',
            'activeGroup' => 'systemFirst',
            'activeName' => 'database',
            'dbName' => config('database.connections.mysql.database')
        ]);
    }

    /**
     * Export database ra file SQL và download
     */
    public function export(Request $request)
    {
        try {
            $host = config('database.connections.mysql.host');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $port = config('database.connections.mysql.port', 3306);

            // Tên file backup
            $filename = 'backup_' . $database . '_' . date('d-m-Y_His') . '.sql';
            $tempPath = storage_path('app/temp');

            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }

            $filePath = $tempPath . '/' . $filename;

            // Đường dẫn mysqldump (XAMPP)
            $mysqldumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';

            // Fallback nếu không tìm thấy mysqldump
            if (!file_exists($mysqldumpPath)) {
                $mysqldumpPath = 'mysqldump';
            }

            // Xây dựng command
            $command = sprintf(
                '"%s" --host=%s --port=%s --user=%s %s %s > "%s"',
                $mysqldumpPath,
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                $password ? '--password=' . escapeshellarg($password) : '',
                escapeshellarg($database),
                $filePath
            );

            // Thực thi command
            $output = [];
            $returnVar = 0;
            exec($command . ' 2>&1', $output, $returnVar);

            if ($returnVar !== 0 || !file_exists($filePath) || filesize($filePath) === 0) {
                // Thử phương pháp thay thế: Export qua PHP
                return $this->exportViaPhp($filename, $database);
            }

            // Download file và xóa sau khi download
            return response()->download($filePath, $filename, [
                'Content-Type' => 'application/sql',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return redirect()->route('database.index')->with('warning', 'Lỗi export: ' . $e->getMessage());
        }
    }

    /**
     * Export database qua PHP (phương pháp thay thế)
     */
    private function exportViaPhp($filename, $database)
    {
        try {
            $tempPath = storage_path('app/temp');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }

            $filePath = $tempPath . '/' . $filename;

            $tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' . $database;

            $sql = "-- Database Backup\n";
            $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Database: " . $database . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
            $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
            $sql .= "SET time_zone = \"+00:00\";\n\n";

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;

                // Get create table statement
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= "-- Structure for table `{$tableName}`\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

                // Get table data
                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $sql .= "-- Data for table `{$tableName}`\n";

                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $values = array_map(function ($value) {
                            if (is_null($value)) {
                                return 'NULL';
                            }
                            return "'" . addslashes($value) . "'";
                        }, $rowArray);

                        $columns = array_map(function ($col) {
                            return "`{$col}`";
                        }, array_keys($rowArray));

                        $sql .= "INSERT INTO `{$tableName}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $sql .= "\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            file_put_contents($filePath, $sql);

            return response()->download($filePath, $filename, [
                'Content-Type' => 'application/sql',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return redirect()->route('database.index')->with('warning', 'Lỗi export: ' . $e->getMessage());
        }
    }

    /**
     * Import database từ file SQL
     */
    public function import(Request $request)
    {
        $request->validate([
            'sql_file' => 'required|file|max:102400', // Max 100MB
        ], [
            'sql_file.required' => 'Vui lòng chọn file SQL để import.',
            'sql_file.file' => 'File không hợp lệ.',
            'sql_file.max' => 'File không được vượt quá 100MB.',
        ]);

        try {
            $file = $request->file('sql_file');
            $filename = $file->getClientOriginalName();

            // Lưu file tạm
            $tempPath = storage_path('app/temp');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }

            $file->move($tempPath, 'import_temp.sql');
            $filePath = $tempPath . '/import_temp.sql';

            $host = config('database.connections.mysql.host');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $port = config('database.connections.mysql.port', 3306);

            // Đường dẫn mysql (XAMPP)
            $mysqlPath = 'C:\\xampp\\mysql\\bin\\mysql.exe';

            if (!file_exists($mysqlPath)) {
                $mysqlPath = 'mysql';
            }

            // Xây dựng command
            $command = sprintf(
                '"%s" --host=%s --port=%s --user=%s %s %s < "%s"',
                $mysqlPath,
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                $password ? '--password=' . escapeshellarg($password) : '',
                escapeshellarg($database),
                $filePath
            );

            // Thực thi command
            $output = [];
            $returnVar = 0;
            exec($command . ' 2>&1', $output, $returnVar);

            // Xóa file tạm
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            if ($returnVar !== 0) {
                // Thử phương pháp thay thế: Import qua PHP
                return $this->importViaPhp($request);
            }

            return redirect()->route('database.index')->with('msg', 'Import database thành công từ file: ' . $filename);
        } catch (\Exception $e) {
            return redirect()->route('database.index')->with('warning', 'Lỗi import: ' . $e->getMessage());
        }
    }

    /**
     * Import database qua PHP (phương pháp thay thế)
     */
    private function importViaPhp(Request $request)
    {
        try {
            $file = $request->file('sql_file');
            $filename = $file->getClientOriginalName();
            $sql = file_get_contents($file->getRealPath());

            // Tắt foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Tách các câu lệnh SQL
            $statements = $this->parseSqlStatements($sql);

            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement) && !$this->isComment($statement)) {
                    try {
                        DB::unprepared($statement);
                    } catch (\Exception $e) {
                        // Bỏ qua lỗi và tiếp tục
                        \Log::warning('SQL Import Warning: ' . $e->getMessage());
                    }
                }
            }

            // Bật lại foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return redirect()->route('database.index')->with('msg', 'Import database thành công từ file: ' . $filename);
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return redirect()->route('database.index')->with('warning', 'Lỗi import: ' . $e->getMessage());
        }
    }

    /**
     * Tách các câu lệnh SQL
     */
    private function parseSqlStatements($sql)
    {
        // Loại bỏ comments
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

        $statements = [];
        $currentStatement = '';
        $inString = false;
        $stringChar = '';

        $length = strlen($sql);
        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];

            if (!$inString) {
                if ($char === '"' || $char === "'") {
                    $inString = true;
                    $stringChar = $char;
                } elseif ($char === ';') {
                    $statements[] = $currentStatement;
                    $currentStatement = '';
                    continue;
                }
            } else {
                if ($char === $stringChar && ($i === 0 || $sql[$i - 1] !== '\\')) {
                    $inString = false;
                }
            }

            $currentStatement .= $char;
        }

        if (!empty(trim($currentStatement))) {
            $statements[] = $currentStatement;
        }

        return $statements;
    }

    /**
     * Kiểm tra xem dòng có phải là comment không
     */
    private function isComment($statement)
    {
        $statement = trim($statement);
        return strpos($statement, '--') === 0 || strpos($statement, '#') === 0;
    }
}
