<?php
// upload.php - Xử lý file, phân tích và lưu vào CSDL

// Bao gồm cấu hình và kết nối CSDL
require 'config.php';

// Hàm phân tích nội dung từ file Quiz.txt
function parse_quiz_content($content) {
    $questions = [];
    // Phân tách nội dung thành từng khối câu hỏi
    $blocks = preg_split('/\r\n\r\n|\n\n/', trim($content));
    
    foreach ($blocks as $block) {
        $lines = array_filter(explode("\n", trim($block)));
        if (empty($lines)) continue;

        $question_text = array_shift($lines); // Dòng đầu tiên là câu hỏi
        $options = [];
        $answer_line = null;

        // Tách các lựa chọn và dòng đáp án
        foreach ($lines as $line) {
            if (strpos($line, 'ANSWER:') === 0) {
                $answer_line = $line;
            } elseif (preg_match('/^([A-Z]\.)\s*(.*)$/', $line, $matches)) {
                $key = trim($matches[1], '. ');
                $text = trim($matches[2]);
                $options[$key] = $text;
            }
        }

        if (empty($options) || $answer_line === null) continue;

        // Xử lý dòng đáp án
        $answer_keys = [];
        if (preg_match('/ANSWER:\s*([A-Z, ]+)/', $answer_line, $matches)) {
            $answer_keys = array_map('trim', explode(',', $matches[1]));
            $answer_keys = array_filter($answer_keys);
        }

        if (empty($answer_keys)) continue;

        $type = (count($answer_keys) > 1) ? 'multiple' : 'single';

        $questions[] = [
            'question_text' => $question_text,
            'options' => $options,
            'answer_keys' => $answer_keys,
            'question_type' => $type
        ];
    }
    return $questions;
}

// Hàm xóa dữ liệu cũ và chèn dữ liệu mới vào CSDL
function insert_quiz_data($pdo, $parsed_data) {
    
    try {
        // BƯỚC 1: Xóa dữ liệu cũ (TRUNCATE) - THỰC HIỆN NGOÀI GIAO DỊCH
        // Các lệnh DDL này không được phép trong giao dịch hoặc gây Implicit Commit
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->exec("TRUNCATE TABLE options;");
        $pdo->exec("TRUNCATE TABLE questions;");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        // BƯỚC 2: Chèn dữ liệu mới (INSERT) - THỰC HIỆN TRONG GIAO DỊCH (Transaction)
        $pdo->beginTransaction(); 
        
        // Chuẩn bị các lệnh chèn (Prepared Statements)
        $stmt_q = $pdo->prepare("INSERT INTO questions (question_text, question_type) VALUES (?, ?)");
        $stmt_o = $pdo->prepare("INSERT INTO options (question_id, option_key, option_text, is_correct) VALUES (?, ?, ?, ?)");

        foreach ($parsed_data as $q) {
            $stmt_q->execute([$q['question_text'], $q['question_type']]);
            $question_id = $pdo->lastInsertId();

            foreach ($q['options'] as $key => $text) {
                $is_correct = in_array($key, $q['answer_keys']) ? 1 : 0;
                $stmt_o->execute([$question_id, $key, $text, $is_correct]);
            }
        }
        
        $pdo->commit();
        return true;
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) { 
            $pdo->rollBack();
        }
        
        error_log("Lỗi chèn CSDL: " . $e->getMessage()); 
        
        return false;
    }
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['quiz_file'])) {
    $file = $_FILES['quiz_file'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = "Lỗi upload file: " . $file['error'];
    } 
    else if (pathinfo($file['name'], PATHINFO_EXTENSION) !== 'txt') {
        $message = "Chỉ chấp nhận file có định dạng .txt.";
    } 
    else {
        $content = file_get_contents($file['tmp_name']);
        
        $parsed_data = parse_quiz_content($content);
        
        if (empty($parsed_data)) {
            $message = "Lỗi: Không tìm thấy dữ liệu câu hỏi hợp lệ trong file.";
        } else {
            // 2. Chèn vào CSDL
            if (insert_quiz_data($pdo, $parsed_data)) {
                $count = count($parsed_data);
                $message = "Tải lên thành công! Đã lưu trữ $count câu hỏi vào cơ sở dữ liệu.";
            } else {
                $message = "Lỗi khi lưu trữ dữ liệu vào cơ sở dữ liệu. Vui lòng kiểm tra Log hoặc kết nối CSDL.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tải Lên Dữ Liệu Bài Thi</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <div class="container">
        <h1>⬆️ Tải Lên Dữ Liệu Bài Thi (CSDL)</h1>
        <p>Vui lòng tải lên file `Quiz.txt` (hoặc file .txt theo định dạng chuẩn) để cập nhật dữ liệu câu hỏi vào cơ sở dữ liệu. **Mọi dữ liệu cũ sẽ bị xóa.**</p>

        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="upload.php" enctype="multipart/form-data" class="upload-form">
            <label for="quiz_file">Chọn file Quiz (.txt):</label>
            <input type="file" name="quiz_file" id="quiz_file" accept=".txt" required>
            <button type="submit">Tải Lên và Lưu CSDL</button>
        </form>

        <p style="margin-top: 20px;"><a href="quiz.php">→ Chuyển đến trang Bài Thi</a></p>
    </div>
</body>
</html>