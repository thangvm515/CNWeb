<?php
// quiz.php
// Nhúng file dữ liệu trắc nghiệm
require_once 'data.php';

/**
 * Hàm kiểm tra sự bằng nhau của hai mảng (chính xác: cùng số lượng và cùng các phần tử).
 * Logic này đảm bảo đáp án chỉ đúng khi CHỌN ĐÚNG VÀ ĐỦ
 */
function arrays_are_equal($a, $b) {
    // >>> ĐIỀU KIỆN SỐ 1 VÀ QUAN TRỌNG NHẤT: Bắt buộc số lượng đáp án phải khớp.
    if (count($a) !== count($b)) {
        return false; // SAI nếu chọn thiếu hoặc thừa
    }
    
    // Kiểm tra các phần tử có giống nhau không (sau khi sắp xếp)
    sort($a);
    sort($b);
    return $a === $b; // Chỉ TRUE nếu khớp hoàn toàn
}

// Khởi tạo biến
$score = 0;
$showResults = false;
$userAnswers = [];
$totalQuestions = count($quizData);

// Xử lý Form khi người dùng nhấn nút "Kiểm Tra" (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $showResults = true;
    
    // Lấy đáp án của người dùng và tính điểm
    foreach ($quizData as $qIndex => $qItem) {
        $inputName = "q$qIndex";
        $userAnswer = isset($_POST[$inputName]) ? $_POST[$inputName] : null;
        
        // Gán đáp án người dùng
        if ($qItem['type'] === 'multiple' && is_array($userAnswer)) {
            $userAnswers[$qIndex] = $userAnswer;
        } elseif ($qItem['type'] === 'single' && is_string($userAnswer)) {
            $userAnswers[$qIndex] = $userAnswer;
        } else {
            $userAnswers[$qIndex] = null; // Chưa chọn hoặc không hợp lệ
        }

        // Kiểm tra và tính điểm (Logic khớp hoàn toàn)
        $isCorrect = false;
        
        if ($qItem['type'] === 'multiple' && is_array($userAnswers[$qIndex])) {
            // Đáp án Multiple: So sánh hai mảng
            $isCorrect = arrays_are_equal($userAnswers[$qIndex], $qItem['answer']);
            
        } elseif ($qItem['type'] === 'single' && $userAnswers[$qIndex] === $qItem['answer'][0]) {
            // Đáp án Single: So sánh chuỗi
            $isCorrect = true;
        }

        if ($isCorrect) {
            $score++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài Thi Trắc Nghiệm Android (PHP)</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

    <div class="container">
        <h1>📚 Bài Thi Trắc Nghiệm Lập trình Android</h1>
        
        <?php 
        // Hiển thị điểm nếu đã submit
        if ($showResults) {
            echo "<div class='score-display'>Bạn đã trả lời đúng: <strong>$score / $totalQuestions</strong> câu.</div>";
        }
        ?>

        <form method="POST" action="quiz.php">
        
            <?php 
            // Lặp qua mảng dữ liệu để hiển thị từng câu hỏi
            foreach ($quizData as $qIndex => $qItem) {
                $qNumber = $qIndex + 1;
                
                // Quyết định input type và name (Đã sửa lỗi cú pháp ternary operator)
                $inputType = ($qItem['type'] == 'multiple') ? 'checkbox' : 'radio';
                $inputName = ($qItem['type'] == 'multiple') ? ("q$qIndex" . '[]') : "q$qIndex";

                // Chỉ áp dụng 'required' cho Radio Button (single)
                $required = ($qItem['type'] == 'single' && !$showResults) ? 'required' : '';
                // Vô hiệu hóa input nếu đã hiển thị kết quả
                $disabled = $showResults ? 'disabled' : '';

                $userAnswer = isset($userAnswers[$qIndex]) ? $userAnswers[$qIndex] : null;
                $isCorrectAnswered = false;
                
                // Kiểm tra lại đáp án cho mục đích hiển thị
                if ($showResults) {
                    if ($qItem['type'] === 'multiple') {
                        $isCorrectAnswered = is_array($userAnswer) ? arrays_are_equal($userAnswer, $qItem['answer']) : false;
                    } else {
                        $isCorrectAnswered = ($userAnswer === $qItem['answer'][0]);
                    }
                }

                echo "<div class='question-card'>";
                echo "<h3>Câu $qNumber: " . $qItem['question'] . "</h3>";

                // Lặp qua các lựa chọn
                foreach ($qItem['options'] as $key => $value) {
                    $optionId = "q" . $qIndex . "_$key";
                    
                    // Thiết lập trạng thái checked
                    if ($qItem['type'] === 'multiple') {
                        $checked = (is_array($userAnswer) && in_array($key, $userAnswer)) ? 'checked' : '';
                    } else {
                        $checked = ($userAnswer === $key) ? 'checked' : '';
                    }
                    
                    // Thiết lập class để highlight (chỉ khi showResults = true)
                    $class = '';
                    if ($showResults) {
                        $isUserSelected = (is_array($userAnswer) && in_array($key, $userAnswer)) || ($userAnswer === $key);

                        if (in_array($key, $qItem['answer'])) {
                            // Highlight đáp án đúng
                             $class = 'selected-correct';
                        } elseif ($isUserSelected && !in_array($key, $qItem['answer'])) {
                            // Highlight đáp án sai người dùng đã chọn
                            $class = 'selected-incorrect';
                        }
                    }
                    
                    // Tạo input
                    echo "<label for='$optionId' class='option $class'>";
                    echo "<input type='$inputType' name='$inputName' id='$optionId' value='$key' $checked $required $disabled>"; 
                    echo "<strong>$key.</strong> $value";
                    echo "</label>";
                }

                // Hiển thị đáp án đúng nếu đã submit và trả lời sai
                if ($showResults && !$isCorrectAnswered) {
                    $correctAnswersText = implode(', ', $qItem['answer']);
                    echo "<p>Đáp án đúng là: <span class='correct-answer-text'><strong>" . $correctAnswersText . "</strong></span></p>";
                }
                
                echo "</div>";
            }
            ?>
            
            <?php if (!$showResults): ?>
                <button type="submit" class="result-button">Kiểm Tra Đáp Án</button>
            <?php else: ?>
                <button type="submit" class="result-button" formaction="quiz.php" formmethod="GET">Làm Lại Bài Thi</button>
            <?php endif; ?>

        </form>

    </div>
</body>
</html>